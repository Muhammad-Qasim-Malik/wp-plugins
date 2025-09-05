<?php

/* ------------------------------------------------------------
 * Admin page: viewer, filter, search, CSV export, clear
 * ---------------------------------------------------------- */

if(!function_exists('mqat_register_admin_menu')){
    function mqat_register_admin_menu() {
        add_menu_page(
            'MQ Activity Log',
            'Activity Log',
            'manage_options',
            'mq-activity-log',
            'mqat_render_admin_page',
            'dashicons-list-view',
            65
        );
    }
    add_action('admin_menu', 'mqat_register_admin_menu');
}