<?php 
/**
 * Plugin Name: MQ Redirect
 * Author: Muhammad Qasim
 * Author URI: mailto:kingqasimmalik@gmail.com
 * Description: Simple plugin to create and manage redirects
 * Version: 1.0.0
 */


// !allowed to ABSPATH

if(!defined('ABSPATH')) exit;

/**
 * Define Constrants
 */

define('MQ_REDIRECT_SITE_URL', home_url());
define('MQ_REDIRECT_URL', plugin_dir_url(__FILE__));
define('MQ_REDIRECT_DIR', plugin_dir_path(__FILE__));
define('MQ_REDIRECT_VERSION', '1.0.0');

/**
 * Enqueue Scripts for Admin
 */

function mq_redirect_admin_enqueue_scripts(){
    wp_enqueue_style('mq-redirect-admin-style', MQ_REDIRECT_URL . '/admin/assets/css/style.css');
    wp_enqueue_script('mq-redirect-admin-script', MQ_REDIRECT_URL . '/admin/assets/js/script.js', ['jquery']);
    wp_enqueue_style( 'datatables-css', 'https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css' );
    wp_enqueue_script( 'datatables-js', 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', array( 'jquery' ), null, true );
    wp_localize_script( 'mq-redirect-admin-script', 'mq_redirect_admin_script',
		array( 
			'ajaxurl' => admin_url( 'admin-ajax.php'),
		)
	);
}

add_action('admin_enqueue_scripts', 'mq_redirect_admin_enqueue_scripts');


/**
 * Include Files
 */

$includes = [
    '/admin/index.php'
];

foreach ($includes as $include){
    require_once(MQ_REDIRECT_DIR . $include);
}

/**
 * Activation Hook
 */

register_activation_hook(__FILE__, 'mq_redirect_create_db');