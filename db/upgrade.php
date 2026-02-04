<?php
/**
 * Upgrade code for the Real-time Engagement plugin.
 *
 * @package    local_realtime_engagement
 * @copyright  2024 John Mulama <johnmulama001@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     John Mulama - Senior Software Engineer (johnmulama001@gmail.com)
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function for local_realtime_engagement.
 * @param int $oldversion The old version of the plugin.
 * @return bool True if upgrade was successful.
 */
function xmldb_local_realtime_engagement_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024111500) {

        // Define table local_realtime_engagement_events
        $table = new xmldb_table('local_realtime_engagement_events');

        if (!$dbman->table_exists($table)) {
            // Adding fields to table local_realtime_engagement_events
            $table->add_field('id', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_TRUE, null, null);
            $table->add_field('userid', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('courseid', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('component', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '');
            $table->add_field('action', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '');
            $table->add_field('timecreated', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

            // Adding keys to table local_realtime_engagement_events
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('userid_courseid', XMLDB_KEY_FOREIGN, array('userid'), 'user', 'id');
            $table->add_key('courseid_fk', XMLDB_KEY_FOREIGN, array('courseid'), 'course', 'id');

            // Adding indexes to table local_realtime_engagement_events
            $table->add_index('idx_userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));
            $table->add_index('idx_courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
            $table->add_index('idx_timecreated', XMLDB_INDEX_NOTUNIQUE, array('timecreated'));
            $table->add_index('idx_component_action', XMLDB_INDEX_NOTUNIQUE, array('component', 'action'));

            // Launch create table for local_realtime_engagement_events
            $dbman->create_table($table);
        }

        // Define table local_realtime_engagement_scores
        $table = new xmldb_table('local_realtime_engagement_scores');

        if (!$dbman->table_exists($table)) {
            // Adding fields to table local_realtime_engagement_scores
            $table->add_field('id', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_TRUE, null, null);
            $table->add_field('userid', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('courseid', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('score', XMLDB_TYPE_INT, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('lastactivity', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
            $table->add_field('timeupdated', XMLDB_TYPE_INT, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

            // Adding keys to table local_realtime_engagement_scores
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('userid_courseid_unique', XMLDB_KEY_UNIQUE, array('userid', 'courseid'));
            $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, array('userid'), 'user', 'id');
            $table->add_key('courseid_fk_score', XMLDB_KEY_FOREIGN, array('courseid'), 'course', 'id');

            // Adding indexes to table local_realtime_engagement_scores
            $table->add_index('idx_userid_score', XMLDB_INDEX_NOTUNIQUE, array('userid'));
            $table->add_index('idx_courseid_score', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
            $table->add_index('idx_score_value', XMLDB_INDEX_NOTUNIQUE, array('score'));

            // Launch create table for local_realtime_engagement_scores
            $dbman->create_table($table);
        }

        // Real-time Engagement savepoint reached
        upgrade_plugin_savepoint(true, 2024111500, 'local_realtime_engagement');
    }

    return true;
}
