<?php
/**
 * External services definitions for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_realtime_engagement_get_dashboard_data' => array(
        'classname'   => 'local_realtime_engagement\external\realtime_engagement_external',
        'methodname'  => 'get_dashboard_data',
        'classpath'   => 'local/realtime_engagement/classes/external/realtime_engagement_external.php',
        'description' => 'Returns real-time engagement dashboard data for a course.',
        'type'        => 'read',
        'ajax'        => true,
    ),
);

$services = array(
    'Real-time Engagement AJAX Service' => array(
        'functions' => array('local_realtime_engagement_get_dashboard_data'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);
