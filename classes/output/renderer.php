<?php
/**
 * Renderer for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

namespace local_realtime_engagement\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;

/**
 * Renderer for the Real-time Engagement plugin.
 */
class renderer extends plugin_renderer_base {

    /**
     * Renders the real-time engagement dashboard.
     *
     * @param 
     * @return string HTML for the dashboard.
     */
    public function render_dashboard_page($page) {
        $data = $page->export_for_template($this);
        // Load historical data view link if applicable
        $data->historical_data_url = new 
            moodle_url('/local/realtime_engagement/historical.php', ['courseid' => $data->courseid]);
        return $this->render_from_template('local_realtime_engagement/dashboard', $data);
    }

    /**
     * Renders a single student engagement row for the dashboard.
     *
     * @param 
     * @return string HTML for a student row.
     */
    public function render_student_engagement_row($studentdata) {
        $data = $studentdata->export_for_template($this);
        return $this->render_from_template('local_realtime_engagement/student_row', $data);
    }
}
