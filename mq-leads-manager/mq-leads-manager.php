<?php
/**
 * Plugin Name: MQ Leads Manager
 * Description: Manage Google Maps scraped data and send bulk emails from WordPress.
 * Version: 1.0.0
 * Author: Muhammad Qasim
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
if (!defined('MQ_LEADS_VERSION')) {
    define('MQ_LEADS_VERSION', '1.0.0');
}
if (!defined('MQ_LEADS_PLUGIN_DIR')) {
    define('MQ_LEADS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('MQ_LEADS_PLUGIN_URL')) {
    define('MQ_LEADS_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('MQ_LEADS_BASENAME')) {
    define('MQ_LEADS_BASENAME', plugin_basename(__FILE__));
}


add_action('admin_enqueue_scripts', 'mq_enqueue_admin_assets');
function mq_enqueue_admin_assets() {
    wp_enqueue_style('mq-admin-style', MQ_LEADS_PLUGIN_URL . 'assets/css/admin-style.css', array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/admin-style.css'));
    wp_enqueue_script('jquery');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array('jquery'), null, true);
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
    wp_enqueue_script('mq-admin-script', MQ_LEADS_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery','datatables-js'), filemtime(plugin_dir_path(__FILE__) . 'assets/js/admin-script.js'), true);
}

require_once MQ_LEADS_PLUGIN_DIR . 'includes/index.php';

// Activation hook - From database
register_activation_hook(__FILE__, 'mq_create_tables');
