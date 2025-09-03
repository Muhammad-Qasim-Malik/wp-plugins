<?php
if(!function_exists('mq_maintenance_admin_menu')) {
    function mq_maintenance_admin_menu(){
        add_menu_page(
            'MQ Maintenance',
            'MQ Maintenance',
            'manage_options',
            'mq-maintenance',
            'mq_maintenance_dashboard',
            'dashicons-hammer',
            6
        );
    }
    add_action('admin_menu', 'mq_maintenance_admin_menu');
}