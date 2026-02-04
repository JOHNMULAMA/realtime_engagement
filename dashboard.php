<?php
/**
 * Real-time Engagement Dashboard page.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

require_once('../../config.php');
require_once('lib.php');
require_once('classes/output/dashboard_page.php');
require_once('classes/output/renderer.php');

$courseid = required_param('courseid', PARAM_INT);
$timeperiod = optional_param('timeperiod', '24h', PARAM_ALPHANUMEXT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = 
    context_course::instance($courseid, MUST_EXIST);

require_login($course);
require_capability('local/realtime_engagement:viewdashboard', $context);

// Set up page and navigation
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url(new 
    moodle_url('/local/realtime_engagement/dashboard.php', ['courseid' => $courseid, 'timeperiod' => $timeperiod]));
$PAGE->set_title(get_string('dashboardtitle', 'local_realtime_engagement') . ' - ' . format_string($course->fullname));
$PAGE->set_heading(get_string('dashboardtitle', 'local_realtime_engagement'));

// Add dashboard styles
$PAGE->requires->css(new 
    moodle_url('/local/realtime_engagement/styles.css'));

// Generate output
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('dashboardtitle', 'local_realtime_engagement'));

$output = $PAGE->get_renderer('local_realtime_engagement');
$dashboardpage = new 
    
    local_realtime_engagement\output\dashboard_page(
        $courseid,
        $timeperiod
    );

echo $output->render_dashboard_page($dashboardpage);

echo $OUTPUT->footer();
