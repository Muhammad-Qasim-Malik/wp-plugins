<?php

/* ------------------------------------------------------------
 * Activation: create table
 * ---------------------------------------------------------- */
if(!function_exists('mqat_create_table')){
    function mqat_create_table () {
        global $wpdb;
        $collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE " . MQAL_TABLE . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            logged_at DATETIME NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            ip VARCHAR(45) DEFAULT NULL,
            action VARCHAR(50) NOT NULL,
            object_type VARCHAR(30) DEFAULT NULL,
            object_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
            message TEXT DEFAULT NULL,
            meta LONGTEXT DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY action_idx (action),
            KEY user_idx (user_id),
            KEY date_idx (logged_at)
        ) $collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    register_activation_hook(MQAT_FILE, 'mqat_create_table');
}

