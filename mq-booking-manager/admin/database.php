<?php

if(!function_exists('mqbm_create_database')){
    function mqbm_create_database(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'bookings';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            service_name varchar(255) NOT NULL,
            customer_name varchar(255) NOT NULL,
            customer_email varchar(255) NOT NULL,
            customer_phone VARCHAR(20) NOT NULL,
            customer_message TEXT DEFAULT NULL,
            booking_date date NOT NULL,
            booking_time time NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY unique_booking (booking_date, booking_time) 
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    register_activation_hook(MQBM_FILE, 'mqbm_create_database');
}