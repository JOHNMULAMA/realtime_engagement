<?php
/**
 * Version information for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_realtime_engagement';
$plugin->version = 2024111500;
$plugin->requires = 2023042400; // Moodle 4.3 minimum
$plugin->maturity = MATURITY_STABLE;
$plugin->release = 'v1.0.0';
$plugin->author = 'John Mulama - Senior Software Engineer';
$plugin->author_email = 'johnmulama001@gmail.com';
$plugin->supportedfeatures = [
    'local_realtime_engagement_dashboards' => true, // Indicates support for engagement dashboards
];
