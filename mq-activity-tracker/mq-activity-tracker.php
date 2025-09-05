<?php 
/**
 * Plugin Name: MQ Activity Tracker
 * Description: A plugin for admins to track activity about the work.
 * Author: Muhammad Qasim
 */

if(!defined('ABSPATH')) exit;

// --------------------------------
// -------Define Constrants
// --------------------------------
if(!defined('MQAT_FILE')){
    define('MQAT_FILE', __FILE__);
}
if(!defined('MQAT_DIR_URL')){
    define('MQAT_DIR_URL', plugin_dir_url(__FILE__));
}
if(!defined('MQAT_DIR_PATH')){
    define('MQAT_DIR_PATH', plugin_dir_path(__FILE__));
}
if(!defined('MQAL_TABLE')){
    global $wpdb;
    define('MQAL_TABLE', $wpdb->prefix . 'mq_activity_log');
}



// --------------------------------
// -------Enqueue Admin Scripts
// --------------------------------
if(!function_exists('mqat_admin_enqueue_scripts')){
    function mqat_admin_enqueue_scripts(){
        wp_enqueue_style( 'mqat-admin-style',MQAT_DIR_URL . '/admin/assets/css/style.css');
        wp_enqueue_script( 'mqat-admin-script',MQAT_DIR_URL . '/admin/assets/js/script.js', ['jquery']);
    }
}
add_action('admin_enqueue_scripts', 'mqat_admin_enqueue_scripts');

// --------------------------------
// -------Include Files
// --------------------------------

require_once(MQAT_DIR_PATH . '/admin/index.php');