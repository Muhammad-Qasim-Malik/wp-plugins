<?php 
// Define the plugin directory if not already defined
if (!defined('CHALLAN_PLUGIN_DIR')) {
    define('CHALLAN_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

require_once CHALLAN_PLUGIN_DIR . 'includes/admin.php';

function challan_search_admin_menu() {
    // Dashboard Menu - Show data
    add_menu_page(
        'Challan Dashboard',                
        'Challan Dashboard',                
        'manage_options',                   
        'challan_display_page',                
        'challan_display_data',           
        'dashicons-admin-site-alt',              
        6                                   
    );

    // Add submenu pages under 'challan_display_page'
    add_submenu_page(
        'challan_display_page',              
        'Challan Add',              
        'Challan Add',              
        'manage_options',                  
        'challan-add',              
        'challan_add_form'         
    );

    

    
    add_submenu_page(
        'challan_display_page',               
        'Challan Upload',                  
        'Challan Upload',                  
        'manage_options',                  
        'challan-upload',                  
        'challan_upload_page'              
    );

    add_submenu_page(
        'challan_display_page',              
        'Challan Shortcodes',             
        'Challan Shortcodes',             
        'manage_options',                  
        'challan-shortcodes',             
        'challan_shortcodes_page'         
    );
}

add_action('admin_menu', 'challan_search_admin_menu');

