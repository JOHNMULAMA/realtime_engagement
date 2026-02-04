<?php
/**
 * Main library file for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the global navigation for courses to include the Real-time Engagement dashboard.
 *
 * @param navigation_node $navigation The navigation node to extend.
 */
function local_realtime_engagement_extend_navigation(navigation_node $navigation) {
    global $COURSE;

    $course = $COURSE;
    $context = context_course::instance($course->id);

    if (has_capability('local/realtime_engagement:viewdashboard', $context)) {
        $url = new moodle_url('/local/realtime_engagement/dashboard.php', ['courseid' => $course->id]);
        $navigation->add(
            get_string('dashboardtitle', 'local_realtime_engagement'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'realtimeengagementdashboard',
            new pix_icon('i/report', get_string('dashboardtitle', 'local_realtime_engagement'))
        );
    }
}

/**
 * Tracks student engagement events.
 *
 * @param string $eventname The event name.
 * @param array $eventdata Array with 'userid', 'courseid', 'component', 'action'.
 */
function local_realtime_engagement_track_event(string $eventname, array $eventdata) {
    global $DB;

    if (empty($eventdata['userid']) || empty($eventdata['courseid'])) {
        return;
    }

    $record = new stdClass();
    $record->userid = (int)$eventdata['userid'];
    $record->courseid = (int)$eventdata['courseid'];
    $record->component = !empty($eventdata['component']) ? $eventdata['component'] : 'core';
    $record->action = !empty($eventdata['action']) ? $eventdata['action'] : $eventname;
    $record->timecreated = time();

    try {
        $DB->insert_record('local_realtime_engagement_events', $record);
    } catch (moodle_exception $e) {
        error_log("Real-time Engagement: Failed to track event: " . $e->getMessage());
    }
}

/**
 * Calculates engagement score for a student in a course over a time period.
 *
 * @param int $userid
 * @param int $courseid
 * @param int $timestart
 * @param int $timeend
 * @return int
 */
function local_realtime_engagement_calculate_score(int $userid, int $courseid, int $timestart, int $timeend): int {
    global $DB;

    // Get weights from plugin settings
    $weights = [
        'quiz'   => (int)get_config('local_realtime_engagement', 'quizweight') ?: 1,
        'forum'  => (int)get_config('local_realtime_engagement', 'forumweight') ?: 1,
        'lesson' => (int)get_config('local_realtime_engagement', 'lessonweight') ?: 1,
        'video'  => (int)get_config('local_realtime_engagement', 'videoweight') ?: 1,
    ];
    $totalweight = array_sum($weights) ?: 1;

    $params = ['userid' => $userid, 'courseid' => $courseid, 'timestart' => $timestart, 'timeend' => $timeend];

    // Count activities
    $counts = [];
    $counts['quiz'] = $DB->count_records_sql("
        SELECT COUNT(e.id) FROM {local_realtime_engagement_events} e
        WHERE e.userid = :userid AND e.courseid = :courseid
        AND e.timecreated BETWEEN :timestart AND :timeend
        AND e.component = 'mod_quiz' AND e.action = 'attempted'", $params);

    $counts['forum'] = $DB->count_records_sql("
        SELECT COUNT(e.id) FROM {local_realtime_engagement_events} e
        WHERE e.userid = :userid AND e.courseid = :courseid
        AND e.timecreated BETWEEN :timestart AND :timeend
        AND e.component = 'mod_forum' AND e.action = 'posted'", $params);

    $counts['lesson'] = $DB->count_records_sql("
        SELECT COUNT(e.id) FROM {local_realtime_engagement_events} e
        WHERE e.userid = :userid AND e.courseid = :courseid
        AND e.timecreated BETWEEN :timestart AND :timeend
        AND e.component = 'mod_lesson' AND e.action = 'viewed'", $params);

    $counts['video'] = $DB->count_records_sql("
        SELECT COUNT(e.id) FROM {local_realtime_engagement_events} e
        WHERE e.userid = :userid AND e.courseid = :courseid
        AND e.timecreated BETWEEN :timestart AND :timeend
        AND e.component = 'mod_resource' AND e.action = 'video_watched'", $params);

    // Calculate weighted score
    $rawscore = 0;
    foreach ($counts as $key => $count) {
        $score = match($key) {
            'quiz' => min($count * 10, 100),
            'forum' => min($count * 5, 100),
            'lesson' => min($count * 2, 100),
            'video' => min($count * 8, 100),
        };
        $rawscore += $score * $weights[$key];
    }
    $engagement_score = (int)round($rawscore / $totalweight);

    // Store score
    $lastactivity = local_realtime_engagement_get_last_activity_time($userid, $courseid, $timestart, $timeend);
    $scoredata = (object)[
        'userid' => $userid,
        'courseid' => $courseid,
        'score' => $engagement_score,
        'lastactivity' => $lastactivity,
        'timeupdated' => time()
    ];

    if ($existing = $DB->get_record('local_realtime_engagement_scores', ['userid' => $userid, 'courseid' => $courseid])) {
        $scoredata->id = $existing->id;
        $DB->update_record('local_realtime_engagement_scores', $scoredata);
    } else {
        $DB->insert_record('local_realtime_engagement_scores', $scoredata);
    }

    // Send alerts if enabled
    if (get_config('local_realtime_engagement', 'alertnotifications') &&
        $engagement_score < (int)get_config('local_realtime_engagement', 'disengagementthreshold')) {
        local_realtime_engagement_alert_disengaged_student($userid, $courseid, $engagement_score, $lastactivity);
    }

    return $engagement_score;
}

/**
 * Get last activity timestamp.
 */
function local_realtime_engagement_get_last_activity_time(int $userid, int $courseid, int $timestart, int $timeend): int {
    global $DB;
    $time = $DB->get_field_sql("
        SELECT MAX(timecreated) FROM {local_realtime_engagement_events}
        WHERE userid = :userid AND courseid = :courseid
        AND timecreated BETWEEN :timestart AND :timeend", 
        ['userid'=>$userid,'courseid'=>$courseid,'timestart'=>$timestart,'timeend'=>$timeend]
    );
    return (int)$time;
}

/**
 * Alert disengaged student to teachers.
 */
function local_realtime_engagement_alert_disengaged_student(int $userid, int $courseid, int $score, int $lastactivity) {
    global $DB;

    $student = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    $course  = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
    $context = context_course::instance($courseid);

    $teachers = get_users_by_capability($context, 'moodle/course:update', 'u.id, u.email', '', '', '', '', '', false, false, true);
    if (empty($teachers)) return;

    $a = (object)[
        'studentname' => fullname($student),
        'coursename' => format_string($course->fullname),
        'score' => $score,
        'lastactive' => userdate($lastactivity, get_string('strftimedatetime', 'langconfig'))
    ];

    $message = get_string('disengaged_alert', 'local_realtime_engagement', $a);

    foreach ($teachers as $teacher) {
        message_send([
            'component' => 'local_realtime_engagement',
            'name' => 'alert',
            'userfrom' => \core_user::get_noreply_user(),
            'userto' => $teacher,
            'subject' => get_string('disengaged_student', 'local_realtime_engagement'),
            'fullmessage' => strip_tags($message),
            'fullmessageformat' => FORMAT_HTML,
            'fullmessagehtml' => '<p>'.$message.'</p>',
            'smallmessage' => '',
            'notification' => 1
        ]);
    }
}

/**
 * Feature support.
 */
function local_realtime_engagement_supports($feature, $default = null) {
    $supported = [
        FEATURE_BACKUP_MOODLE2 => false,
        FEATURE_GRADE_EXPORT => false,
        FEATURE_GRADE_IMPORT => false,
        FEATURE_COMPLETION_TRACKING => false
    ];
    return $supported[$feature] ?? $default;
}

/**
 * Messaging providers.
 */
function local_realtime_engagement_get_message_providers(array $providers = []) {
    $providers['local_realtime_engagement'] = [
        'alert' => [
            'defaults' => [
                'multiple' => true,
                'loggedout' => true,
                'popup' => true,
                'email' => true,
                'instant' => true,
                'offline' => true
            ],
            'capability' => 'local/realtime_engagement:viewdashboard'
        ]
    ];
    return $providers;
}
