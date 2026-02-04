<?php
/**
 * Privacy provider for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_realtime_engagement\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\request\approved_context_list;
use core_privacy\local\request\context_aware_provider;
use core_privacy\local\request\data_request;
use core_privacy\local\request\transform;
use local_realtime_engagement\event\student_quiz_attempted;
use moodle_exception;

/**
 * Privacy provider for the Real-time Engagement plugin.
 *
 * This class defines how user data is handled for privacy requests (data export/deletion).
 */
class provider implements context_aware_provider {

    private static $component = 'local_realtime_engagement';

    /**
     * Returns the content of the privacy statement for this plugin.
     *
     * @param int $contextid The context ID.
     * @return 
     * string The privacy statement HTML.
     */
    public static function get_privacy_statement(int $contextid):
        string {

        return html_writer::tag('p', get_string('privacy:statement', self::$component));
    }

    /**
     * Returns the list of contexts for a user where data is stored.
     *
     * @param int $userid The ID of the user.
     * @return 
     * approved_context_list The list of contexts.
     */
    public static function get_contexts_for_userid(int $userid):
        approved_context_list {

        global $DB;

        $contextlist = new 
            approved_context_list();

        // Gather contexts from engagement events
        $eventcontexts = $DB->get_records_sql(
            "SELECT DISTINCT ctx.id FROM {local_realtime_engagement_events} e
             JOIN {course} c ON e.courseid = c.id
             JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :coursecontextlevel
             WHERE e.userid = :userid",
            ['coursecontextlevel' => CONTEXT_COURSE, 'userid' => $userid]
        );

        foreach ($eventcontexts as $context) {
            $contextlist->add_context(new 
                
                
                
                context(
                    CONTEXT_COURSE,
                    $DB->get_field('context', 'instanceid', ['id' => $context->id])
                )
            );
        }

        // Gather contexts from engagement scores
        $scorecontexts = $DB->get_records_sql(
            "SELECT DISTINCT ctx.id FROM {local_realtime_engagement_scores} s
             JOIN {course} c ON s.courseid = c.id
             JOIN {context} ctx ON c.id = ctx.instanceid AND ctx.contextlevel = :coursecontextlevel
             WHERE s.userid = :userid",
            ['coursecontextlevel' => CONTEXT_COURSE, 'userid' => $userid]
        );

        foreach ($scorecontexts as $context) {
            $contextlist->add_context(new 
                
                
                
                context(
                    CONTEXT_COURSE,
                    $DB->get_field('context', 'instanceid', ['id' => $context->id])
                )
            );
        }

        return $contextlist;
    }

    /**
     * Export all user data stored in the plugin for a given data request.
     *
     * @param data_request $request The data request object.
     * @return 
     * array Exported data.
     */
    public static function export_data(data_request $request):
        array {

        global $DB;

        $data = [];

        // Data from local_realtime_engagement_events
        $events = $DB->get_records('local_realtime_engagement_events', [
            'userid' => $request->getUserid(),
            'courseid' => $request->getContextinstanceid() // Assuming course context
        ], 'id ASC', 'id, courseid, component, action, timecreated');

        if (!empty($events)) {
            foreach ($events as $event) {
                $data['engagement_events'][] = [
                    'courseid' => $event->courseid,
                    'component' => $event->component,
                    'action' => $event->action,
                    'timecreated' => userdate($event->timecreated),
                ];
            }
        }

        // Data from local_realtime_engagement_scores
        $scores = $DB->get_records('local_realtime_engagement_scores', [
            'userid' => $request->getUserid(),
            'courseid' => $request->getContextinstanceid() // Assuming course context
        ], 'id ASC', 'id, courseid, score, lastactivity, timeupdated');

        if (!empty($scores)) {
            foreach ($scores as $score) {
                $data['engagement_scores'][] = [
                    'courseid' => $score->courseid,
                    'score' => $score->score,
                    'lastactivity' => userdate($score->lastactivity),
                    'timeupdated' => userdate($score->timeupdated),
                ];
            }
        }

        return $data;
    }

    /**
     * Delete all user data stored in the plugin for a given data request.
     *
     * @param data_request $request The data request object.
     */
    public static function delete_data(data_request $request) {
        global $DB;

        // Delete from local_realtime_engagement_events
        $DB->delete_records('local_realtime_engagement_events', [
            'userid' => $request->getUserid(),
            'courseid' => $request->getContextinstanceid() // Assuming course context
        ]);

        // Delete from local_realtime_engagement_scores
        $DB->delete_records('local_realtime_engagement_scores', [
            'userid' => $request->getUserid(),
            'courseid' => $request->getContextinstanceid() // Assuming course context
        ]);

        // No data to delete at system level for a specific user ID for now.
    }

    /**
     * Delete all data in all contexts for the specified user, if necessary.
     *
     * @param int $userid The ID of the user.
     */
    public static function delete_all_user_data(int $userid) {
        global $DB;

        // Delete from local_realtime_engagement_events across all courses
        $DB->delete_records('local_realtime_engagement_events', ['userid' => $userid]);

        // Delete from local_realtime_engagement_scores across all courses
        $DB->delete_records('local_realtime_engagement_scores', ['userid' => $userid]);
    }

    /**
     * Does the plugin keep any user data?
     *
     * @return bool True if the plugin keeps user data.
     */
    public static function has_personal_data():
        bool {

        return true;
    }


    /**
     * Is the plugin able to handle a delete_all_user_data request?
     *
     * @return bool
     */
    public static function can_delete_all_user_data():
        bool {

        return true;
    }
}
