<?php

namespace LiveForms;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\LiveForms\Installer')):

    class Installer
    {

        private $dbVersion = 2.5;

        function __construct()
        {

        }

        public static function dbVersion()
        {
            $inst = new Installer();
            return $inst->dbVersion;
        }

        public static function dbUpdateRequired()
        {
            return (Installer::dbVersion() !== (double)get_option('__wplf_db_version'));
        }

        public static function init()
        {
            self::updateDB();
        }

        public static function updateDB()
        {

            global $wpdb;

            delete_option('wplf_latest');

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_conreqs` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `fid` int(11) NOT NULL,
                    `uid` int(11) NOT NULL,
                    `data` text NOT NULL,
                    `reply_for` int(11) NOT NULL,
                    `status` varchar(20) NOT NULL,
                    `token` varchar(20) NOT NULL,
                    `time` int(11) NOT NULL,
                    `agent_id` int(11) NOT NULL,
                    `replied_by` varchar(500) NOT NULL,
                    PRIMARY KEY (`id`))";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_stats` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `fid` int(11) NOT NULL,
                    `author_id` int(11) NOT NULL,
                    `action` varchar(20) NOT NULL,
                    `ip` varchar(30) NOT NULL,
                    `time` int(11) NOT NULL,
                    PRIMARY KEY (`id`)    
                    )";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_addons` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `addons` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
                    `form_id` bigint(20) unsigned NOT NULL,
                    `list_id` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `fields` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
                    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY `id` (`id`)
                  ) ;";


            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_addons_active` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `uid` int(11) NOT NULL,
                    `key` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `clientid` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `addons` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
                    `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
                    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY `id` (`id`)
                  ) ;";
            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_addons_form_details` (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `uid` int(11) NOT NULL,
                    `addonid` int(11) NOT NULL,
                    `addons` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
                    `form_id` bigint(20) unsigned NOT NULL,
                    `list_id` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `fields` longtext COLLATE utf8_unicode_ci NOT NULL,
                    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY `id` (`id`)
                  ) ;";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_payments` (
                    `id` bigint(20) UNSIGNED NOT NULL,
                      `form_id` int(11) NOT NULL,
                      `entry_id` int(11) NOT NULL,
                      `amount` double NOT NULL,
                      `currency` varchar(10) NOT NULL,
                      `payment_method` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                      `payment_status` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
                      `date` int(11) NOT NULL,
                      `transaction_id` varchar(200) NOT NULL,
                      `payment_data` text NOT NULL,
                    UNIQUE KEY `id` (`id`)
                  ) ;";

            $sqls[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}liveforms_replies` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `form_id` int(11) NOT NULL,
                      `entry_id` int(11) NOT NULL,
                      `user_email` varchar(250) NOT NULL,
                      `message` text NOT NULL,
                      `time` int(11) NOT NULL,
                      `type` varchar(20) NOT NULL,
                      PRIMARY KEY (`id`)
                    )";


            foreach ($sqls as $qry) {
                $wpdb->query($qry);
            }

            $installer = new Installer();

            $installer->addColumn('liveforms_payments', 'currency', 'VARCHAR(10)');
            $installer->addColumn('liveforms_payments', 'transaction_id', 'VARCHAR(200)');
            $installer->addColumn('liveforms_payments', 'payment_data', 'TEXT');

            $ach = get_option("__wplf_activation_history", array());
            $ach = maybe_unserialize($ach);
            $ach[] = time();
            update_option("__wplf_activation_history", $ach, false);
            update_option('__wplf_db_version', $installer->dbVersion, false);

        }


        function addColumn($table, $column, $type_n_default = 'TEXT NOT NULL')
        {
            global $wpdb;
            $result = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}{$table}` LIKE '$column'");
            $exists = count($result) > 0;
            if (!$exists)
                $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD `{$column}` {$type_n_default}");
        }

        function changeColumn($table, $column, $newName, $type_n_default = 'TEXT NOT NULL')
        {
            global $wpdb;
            $result = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}{$table}` LIKE '$newName'");
            $exists = count($result) > 0;
            if ($exists)
                $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` CHANGE `{$column}` `{$newName}` {$type_n_default}");
        }

        function primaryKey($table, $column)
        {
            global $wpdb;
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD PRIMARY KEY(`{$column}`)");
        }

        function uniqueKey($table, $column)
        {
            global $wpdb;
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}{$table}` ADD UNIQUE(`{$column}`)");
        }
    }

endif;

