<?php 

if(!function_exists('mqbm_register_menu')){
    function mqbm_register_menu(){
        add_menu_page(
            'MQBM',
            'MQBM',
            'manage_options',
            'mqbm-dashboard',
            'mqbm_dashboard',
            'dashicons-chart-pie'
        );
        add_submenu_page(
            'mqbm-dashboard',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'mqbm-dashboard',
            'mqbm_dashboard',
        );
        add_submenu_page(
            'mqbm-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'mqbm-settings',
            'mqbm_settings',
        );
    }
    add_action('admin_menu', 'mqbm_register_menu');
}

