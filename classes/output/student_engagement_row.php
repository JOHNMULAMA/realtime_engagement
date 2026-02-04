<?php
/**
 * Student engagement row class for the Real-time Engagement plugin.
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
    core_user;

/**
 * Represents a single student's engagement data for display in the dashboard.
 */
class student_engagement_row implements renderable, templatable {
    /** @var 
     * stdClass $user The user object. */
    public $user;

    /** @var int $courseid The course ID. */
    public $courseid;

    /** @var int $score The engagement score. */
    public $score;

    /** @var int $lastactivity The timestamp of the last activity. */
    public $lastactivity;

    /**
     * Constructor for student_engagement_row.
     *
     * @param 
     * stdClass $user The user object.
     * @param int $courseid The course ID.
     * @param int $score The engagement score.
     * @param int $lastactivity The timestamp of the last activity.
     */
    public function __construct(
        
        stdClass $user,
        int $courseid,
        int $score,
        int $lastactivity
    ) {
        $this->user = $user;
        $this->courseid = $courseid;
        $this->score = $score;
        $this->lastactivity = $lastactivity;
    }

    /**
     * Export this data for a Mustache template.
     *
     * @param 
     * plugin_renderer_base $output The renderer.
     * @return object
     */
    public function export_for_template(
        
        plugin_renderer_base $output
    ) : 
        object {

        $context = new 
            stdClass();
        $context->userid = $this->user->id;
        $context->fullname = fullname($this->user);
        $context->profileimageurl = new 
            core_user($this->user->id))->get_profile_image(
                true
            )->get_url();
        $context->engagement_score = $this->score;
        $context->last_activity_time = $this->lastactivity > 0 ? 
            userdate($this->lastactivity) : 
            get_string('no_activity', 'local_realtime_engagement');
        $context->courseid = $this->courseid;

        // Add some basic visual representation based on score
        if ($this->score >= 70) {
            $context->score_class = 'engagement-high';
        } elseif ($this->score >= 40) {
            $context->score_class = 'engagement-medium';
        } else {
            $context->score_class = 'engagement-low';
        }

        return $context;
    }
}
