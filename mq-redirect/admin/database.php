<?php 

function mq_redirect_create_db(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'redirects'; 

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        old_url varchar(255) NOT NULL,
        new_url varchar(255) NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY old_url (old_url)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// add_action('init', 'mq_redirect_create_db');