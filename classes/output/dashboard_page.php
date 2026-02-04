<?php
/**
 * Dashboard page class for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_realtime_engagement\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use templatable;
use 
    local_realtime_engagement\external\realtime_engagement_external;

/**
 * Renders the Real-time Engagement dashboard page.
 */
class dashboard_page implements renderable, templatable {
    /** @var int $courseid The course ID. */
    public $courseid;

    /** @var array $students Array of student engagement data. */
    public $students;

    /** @var array $alerts Array of generated AI alerts. */
    public $alerts;

    /** @var int $refreshinterval Dashboard refresh interval in seconds. */
    public $refreshinterval;

    /** @var string $timeperiod Current time period for filtering (e.g., '24h', '7d'). */
    public $timeperiod;

    /**
     * Constructor for dashboard_page.
     *
     * @param int $courseid The course ID.
     * @param string $timeperiod The selected time period for filtering.
     */
    public function __construct(int $courseid, string $timeperiod = '24h') {
        $this->courseid = $courseid;
        $this->timeperiod = $timeperiod;
        $this->refreshinterval = (int)get_config('local_realtime_engagement', 'refreshinterval');
        $this->load_dashboard_data();
    }

    /**
     * Loads all necessary data for the dashboard.
     */
    private function load_dashboard_data() {
        global $DB, $CFG;

        // Determine time range based on $this->timeperiod
        $timestart = 0;
        $timeend = time();
        switch ($this->timeperiod) {
            case '12h':
                $timestart = strtotime('-12 hours');
                break;
            case '24h':
                $timestart = strtotime('-24 hours');
                break;
            case '7d':
                $timestart = strtotime('-7 days');
                break;
            case 'all':
                $timestart = 0; // All time
                break;
            default:
                $timestart = strtotime('-24 hours');
                break;
        }

        // Fetch students enrolled in the course.
        $context = 
            context_course::instance($this->courseid, MUST_EXIST);
        $enrolledusers = get_enrolled_users($context, 'mod/forum:viewforum', 0, 'u.id, u.firstname, u.lastname, u.picture');

        $this->students = [];
        $this->alerts = []; // Placeholder for AI-driven alerts - for now, just disengagement.

        if (!empty($enrolledusers)) {
            foreach ($enrolledusers as $user) {
                // Ensure students do not see their own or other students' detailed engagement.
                if (has_capability('local/realtime_engagement:viewdashboard', $context, $user->id, false)) {
                    continue; // Skip teachers/managers already handled via capability
                }
                if (is_guest($user->id)) {
                    continue; // Skip guest users
                }

                // Calculate engagement score for each student.
                $engagement_score = local_realtime_engagement_calculate_score($user->id, $this->courseid, $timestart, $timeend);

                // Get last activity time.
                $lastactivity = local_realtime_engagement_get_last_activity_time($user->id, $this->courseid, $timestart, $timeend);

                $this->students[] = (
                    new student_engagement_row(
                        $user,
                        $this->courseid,
                        $engagement_score,
                        $lastactivity
                    )
                );

                // Check for disengagement alerts
                if (get_config('local_realtime_engagement', 'alertnotifications')) {
                    $disengagementthreshold = (int)get_config('local_realtime_engagement', 'disengagementthreshold');
                    if ($engagement_score < $disengagementthreshold) {
                        $a = new 
                            stdClass();
                        $a->studentname = fullname($user);
                        $a->score = $engagement_score;
                        $a->lastactive = userdate($lastactivity, get_string('strftimedatetime', 'langconfig'));
                        $this->alerts[] = get_string('disengaged_alert', 'local_realtime_engagement', $a);
                    }
                }
            }
        }
    }

    /**
     * Export this data for a Mustache template.
     *
     * @param 
     * @return stdClass
     */
    public function export_for_template(
        
        plugin_renderer_base $output
    ) : 
        stdClass {

        $context = new 
            stdClass();
        $context->courseid = $this->courseid;
        $context->students = array_map(function ($student) use ($output) {
            return $student->export_for_template($output);
        }, $this->students);
        $context->alerts = $this->alerts;
        $context->refreshinterval = $this->refreshinterval;
        $context->refresh = true; // Indicate that refreshing is enabled.
        $context->timeperiod = $this->timeperiod;

        // Populate time period filter options
        $context->timefilteroptions = [];
        $context->timefilteroptions[] = (
            (object)['value' => '12h', 'name' => get_string('last_12_hours', 'local_realtime_engagement'), 'selected' => ($this->timeperiod === '12h')]
        );
        $context->timefilteroptions[] = (
            (object)['value' => '24h', 'name' => get_string('last_24_hours', 'local_realtime_engagement'), 'selected' => ($this->timeperiod === '24h')]
        );
        $context->timefilteroptions[] = (
            (object)['value' => '7d', 'name' => get_string('last_7_days', 'local_realtime_engagement'), 'selected' => ($this->timeperiod === '7d')]
        );
        $context->timefilteroptions[] = (
            (object)['value' => 'all', 'name' => get_string('all_time', 'local_realtime_engagement'), 'selected' => ($this->timeperiod === 'all')]
        );

        return $context;
    }
}
