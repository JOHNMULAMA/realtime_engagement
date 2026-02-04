<?php
/**
 * Admin settings for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

if (has_capability('local/realtime_engagement:managesettings', context_system::instance())) {

    $settings = new admin_settingpage('local_realtime_engagement_settings',
        get_string('pluginname', 'local_realtime_engagement'));

    // General Settings Header
    $settings->add(new admin_setting_heading(
        'local_realtime_engagement/general_settings_heading',
        get_string('realtime_data', 'local_realtime_engagement'),
        get_string('config_refreshinterval_desc', 'local_realtime_engagement')
    ));

    // Dashboard Refresh Interval
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/refreshinterval',
        get_string('config_refreshinterval', 'local_realtime_engagement'),
        get_string('config_refreshinterval_desc', 'local_realtime_engagement'),
        30, // Default to 30 seconds
        PARAM_INT
    ));

    // Engagement Scoring Weights Header
    $settings->add(new admin_setting_heading(
        'local_realtime_engagement/scoring_weights_heading',
        get_string('engagement_scoring_weights', 'local_realtime_engagement'),
        null
    ));

    // Quiz Weight
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/quizweight',
        get_string('config_quizweight', 'local_realtime_engagement'),
        get_string('config_quizweight_desc', 'local_realtime_engagement'),
        20, // Default weight
        PARAM_INT
    ));

    // Forum Weight
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/forumweight',
        get_string('config_forumweight', 'local_realtime_engagement'),
        get_string('config_forumweight_desc', 'local_realtime_engagement'),
        25, // Default weight
        PARAM_INT
    ));

    // Lesson Weight
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/lessonweight',
        get_string('config_lessonweight', 'local_realtime_engagement'),
        get_string('config_lessonweight_desc', 'local_realtime_engagement'),
        15, // Default weight
        PARAM_INT
    ));

    // Video Weight
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/videoweight',
        get_string('config_videoweight', 'local_realtime_engagement'),
        get_string('config_videoweight_desc', 'local_realtime_engagement'),
        40, // Default weight
        PARAM_INT
    ));

    // Alert Settings Header
    $settings->add(new admin_setting_heading(
        'local_realtime_engagement/alert_settings_heading',
        get_string('alert_settings', 'local_realtime_engagement'),
        null
    ));

    // Disengagement Threshold
    $settings->add(new admin_setting_configtext(
        'local_realtime_engagement/disengagementthreshold',
        get_string('config_disengagementthreshold', 'local_realtime_engagement'),
        get_string('config_disengagementthreshold_desc', 'local_realtime_engagement'),
        30, // Default threshold
        PARAM_INT
    ));

    // Enable AI Alert Notifications
    $settings->add(new admin_setting_configcheckbox(
        'local_realtime_engagement/alertnotifications',
        get_string('config_alertnotifications', 'local_realtime_engagement'),
        get_string('config_alertnotifications_desc', 'local_realtime_engagement'),
        1 // Default to enabled
    ));

    $ADMIN->add('localplugins', $settings);
}
