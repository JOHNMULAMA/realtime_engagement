<?php
/**
 * Event for tracking when a student views a lesson page.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/share/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_realtime_engagement\event;

defined('MOODLE_INTERNAL') || die();

use core\event\base;

class student_lesson_viewed extends base {
    protected function init() {
        $this->data['objecttable'] = 'lesson_pages';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = '1';
    }

    public static function get_name() {
        return get_string('event_lesson_viewed', 'local_realtime_engagement');
    }

    public function get_description() {
        return get_string('event_lesson_viewed_desc', 'local_realtime_engagement', $this->contextinstanceid);
    }

    public function get_url() {
        return new 
            moodle_url('/mod/lesson/view.php', ['id' => $this->objectid]);
    }
}
