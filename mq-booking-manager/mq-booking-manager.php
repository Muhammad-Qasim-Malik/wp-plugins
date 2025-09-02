<?php 
/**
 * Plugin Name: MQ Booking Manager
 * Description: Creating a plugin for Noman Nadeem
 * Author: Muhammad Qasim
 * Version: 1.0.0
 */


if(!defined('ABSPATH')) exit;

/**
 * Define Constrants
 */

define('MQBM_HOME', home_url());
define('MQBM_DIR_PATH', plugin_dir_path(__FILE__) );
define('MQBM_DIR_URL', plugin_dir_url(__FILE__) );
define('MQBM_FILE', __FILE__);
define('MQBM_VERSION', '1.0.0');


/**
 * Enqueue Scripts
 */
if(!function_exists('mqbm_enqueue_scripts')) {
    function mqbm_enqueue_admin_scripts(){
        wp_enqueue_style('mqbm-admin-style', MQBM_DIR_URL . '/admin/assets/css/style.css');
        wp_enqueue_script('mqbm-admin-script', MQBM_DIR_URL . '/admin/assets/js/script.js', ['jquery']);

         wp_enqueue_style('fullcalendar-css', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css');
        wp_enqueue_script('fullcalendar-js', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js', [], null, true);

        wp_localize_script('mqbm-admin-script', 'mqbm_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
    add_action('admin_enqueue_scripts', 'mqbm_enqueue_admin_scripts');
}

if(!function_exists('mqbm_enqueue_scripts')) {
    function mqbm_enqueue_scripts(){
        wp_enqueue_style('mqbm-style', MQBM_DIR_URL . '/includes/assets/css/style.css');
        wp_enqueue_script('mqbm-script', MQBM_DIR_URL . '/includes/assets/js/script.js', ['jquery']);

        wp_localize_script('mqbm-script', 'mqbm_user_ajax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('mqbm_user_nonce')
        ]);
    }
    add_action('wp_enqueue_scripts', 'mqbm_enqueue_scripts');
}

/**
 * Include Files
 */

$includes = [
    '/admin/index.php',
    '/includes/index.php',
];
foreach($includes as $include){
    require_once(MQBM_DIR_PATH . $include);
}
