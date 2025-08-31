<?php
function mq_create_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $leads_table     = $wpdb->prefix . 'mq_leads';
    $templates_table = $wpdb->prefix . 'mq_email_templates';
    $logs_table      = $wpdb->prefix . 'mq_email_logs';

    // Leads table
    $sql1 = "CREATE TABLE IF NOT EXISTS $leads_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        business_name varchar(255) NOT NULL,
        email varchar(255),
        phone varchar(50),
        website varchar(255),
        niche varchar(150),
        address varchar(255),
        city varchar(150),
        country varchar(150),
        status varchar(50) NOT NULL DEFAULT 'New',
        date_added datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Email templates table
    $sql2 = "CREATE TABLE IF NOT EXISTS $templates_table (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        template_name varchar(255) NOT NULL,
        subject varchar(255) NOT NULL,
        body longtext NOT NULL,
        date_created datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Email logs table
    $sql3 = "CREATE TABLE IF NOT EXISTS $logs_table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        lead_id BIGINT(20) UNSIGNED NOT NULL,
        template_id BIGINT(20) UNSIGNED NOT NULL,
        recipient VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'pending',
        error_message TEXT NULL,
        sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY lead_id (lead_id),
        KEY template_id (template_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    dbDelta($sql2);
    dbDelta($sql3);
}
