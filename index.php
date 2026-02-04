<?php
/**
 * Main page for local_realtime_engagement
 *
 * @package    local_realtime_engagement
 * @copyright  2026 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

require_login();
$context = context_system::instance();
require_capability('local/realtime_engagement:view', $context);

$PAGE->set_context($context);
$PAGE->set_url('/local/realtime_engagement/index.php');
$PAGE->set_title(get_string('pluginname', 'local_realtime_engagement'));
$PAGE->set_heading(get_string('pluginname', 'local_realtime_engagement'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_realtime_engagement'));

echo html_writer::tag('p', get_string('pluginname_desc', 'local_realtime_engagement'));

echo $OUTPUT->footer();
