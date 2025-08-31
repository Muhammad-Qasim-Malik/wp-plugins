<?php
add_action('admin_menu', 'mq_admin_menu');
function mq_admin_menu() {
    // Main menu
    add_menu_page(
        'MQ Leads Manager',       
        'Leads Manager',         
        'manage_options',         
        'mq-dashboard',           
        'mq_dashboard_page',      
        'dashicons-groups',       
        25                        
    );

    // 1. Dashboard
    add_submenu_page(
        'mq-dashboard',           
        'Dashboard',              
        'Dashboard',              
        'manage_options',         
        'mq-dashboard',           
        'mq_dashboard_page'       
    );

    // 2. Settings
    add_submenu_page(
        'mq-dashboard',
        'Email Settings',
        'Email Settings',
        'manage_options',
        'mq-settings',
        'mq_settings_page'
    );

    // 3. Leads
    add_submenu_page(
        'mq-dashboard',
        'Manage Leads',
        'Manage Leads',
        'manage_options',
        'mq-leads',
        'mq_leads_page'
    );

    // 3. Email Templates
    add_submenu_page(
        'mq-dashboard',
        'Email Templates',
        'Email Templates',
        'manage_options',
        'mq-templates',
        'mq_templates_page'
    );

    // 4. Email Templates
    add_submenu_page(
        'mq-dashboard',
        'Send Emails',
        'Send Emails',
        'manage_options',
        'mq-send-emails',
        'mq_send_email_page'
    );

    // 4. Email History
    add_submenu_page(
        'mq-dashboard',
        'Email History',
        'Email History',
        'manage_options',
        'mq-email-history',
        'mq_email_page'
    );
}