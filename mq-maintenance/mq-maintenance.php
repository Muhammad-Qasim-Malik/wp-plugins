<?php 
/**
 * Plugin Name: MQ Maintenance
 * Description: Maintenance plugin by MQ
 * Author: Muhammad Qasim
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('MQ_MAINTENANCE_FILE', __FILE__);
define('MQ_MAINTENANCE_URI', plugin_dir_url(__FILE__));
define('MQ_MAINTENANCE_PATH', plugin_dir_path(__FILE__));

if(!function_exists('mq_maintenance_enqueue_scripts')) {
    function mq_maintenance_enqueue_scripts(){
        wp_enqueue_style('mq-maintenance-style', MQ_MAINTENANCE_URI . '/includes/assets/css/style.css');
        wp_enqueue_script('mq-maintenance-script', MQ_MAINTENANCE_URI . '/includes/assets/js/script.js', ['jquery']);
        wp_localize_script('mq-maintenance-script', 'mq_maintenance_script', [
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }
    add_action('wp_enqueue_scripts', 'mq_maintenance_enqueue_scripts');
}


if(!function_exists('mq_maintenance_enqueue_admin_scripts')) {
    function mq_maintenance_enqueue_admin_scripts(){
        wp_enqueue_style('mq-maintenance-style', MQ_MAINTENANCE_URI . '/admin/assets/css/style.css');
        wp_enqueue_script('mq-maintenance-script', MQ_MAINTENANCE_URI . '/admin/assets/js/script.js', ['jquery']);
    }
    add_action('admin_enqueue_scripts', 'mq_maintenance_enqueue_admin_scripts');
}

$includes = [
    'admin/menu.php',
    'admin/dashboard.php',
    'admin/helper.php',
    'includes/helper.php',
    'includes/maintenance.php',
];
foreach ($includes as $include) {
    require_once(MQ_MAINTENANCE_PATH . $include);
}
?>