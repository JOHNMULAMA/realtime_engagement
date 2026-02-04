<?php
/**
 * External API for Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_realtime_engagement\external;

defined('MOODLE_INTERNAL') || die();

use external_api;
use external_function;
use external_param;
use external_value;

/**
 * External API for the Real-time Engagement dashboard data.
 */
class realtime_engagement_external extends external_api {

    /**
     * Returns the definition of the function to get dashboard data.
     *
     * @return external_function
     */
    public static function get_dashboard_data_parameters() {
        return new 
            external_function(
                'get_dashboard_data',
                'Returns real-time engagement dashboard data for a course.',
                new 
                    external_parameters(
                        new 
                            external_param('courseid', PARAM_INT, 'The ID of the course.'),
                        new 
                            external_param('timeperiod', PARAM_ALPHANUMEXT, 'The time period to filter data (e.g., 24h, 7d, all).', VALUE_DEFAULT, '24h')
                    ),
                new 
                    external_single_value(new 
                        external_array(
                            new 
                                external_object(
                                    [
                                        'userid' => new 
                                            external_value(PARAM_INT, 'The ID of the user.'),
                                        'fullname' => new 
                                            external_value(PARAM_RAW, 'The full name of the user.'),
                                        'profileimageurl' => new 
                                            external_value(PARAM_RAW, 'URL of the user profile image.'),
                                        'engagement_score' => new 
                                            external_value(PARAM_INT, 'The calculated engagement score.'),
                                        'last_activity_time' => new 
                                            external_value(PARAM_RAW, 'The formatted time of last activity.'),
                                        'score_class' => new 
                                            external_value(PARAM_RAW, 'CSS class for styling the score.')
                                    ],
                                    'Student engagement data.'
                                )
                        ),
                        'An array of student engagement data.'
                    )
            );
    }

    /**
     * Fetches real-time engagement dashboard data for a given course.
     *
     * @param int $courseid The ID of the course.
     * @param string $timeperiod The time period to filter data (e.g., '24h', '7d', 'all').
     * @return array An array of student engagement data.
     * @throws moodle_exception
     */
    public static function get_dashboard_data(int $courseid, string $timeperiod = '24h'):
        array {

        self::validate_parameters(self::get_dashboard_data_parameters(), ['courseid' => $courseid, 'timeperiod' => $timeperiod]);

        $context = 
            
            
            context_course::instance($courseid, MUST_EXIST);
        self::require_capability('local/realtime_engagement:viewdashboard', $context);

        $dashboard = new 
            
            
            local_realtime_engagement\output\dashboard_page(
                $courseid,
                $timeperiod
            );
        $studentsdata = [];
        if (!empty($dashboard->students)) {
            foreach ($dashboard->students as $student) {
                $studentsdata[] = [
                    'userid' => $student->user->id,
                    'fullname' => fullname($student->user),
                    'profileimageurl' => new 
                        core_user(
                            $student->user->id
                        ))->get_profile_image(true)->get_url()->out(),
                    'engagement_score' => $student->score,
                    'last_activity_time' => $student->lastactivity > 0 ? 
                        userdate(
                            $student->lastactivity
                        ) : 
                        get_string('no_activity', 'local_realtime_engagement'),
                    'score_class' => ($student->score >= 70) ? 
                        'engagement-high' : 
                        (($student->score >= 40) ? 
                            'engagement-medium' : 
                            'engagement-low'),
                ];
            }
        }

        return $studentsdata;
    }
}
