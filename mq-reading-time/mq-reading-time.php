<?php
/**
 * Plugin Name: MQ Reading Time
 * Description: Automatically displays estimated reading time before posts.
 * Version: 1.0.0
 * Author: Muhammad Qasim
 * Test Domain: mq-reading-time
 */

if ( ! defined( 'ABSPATH' ) ) exit;


define( 'MQRT_DIR', plugin_dir_path( __FILE__ ) );
define( 'MQRT_URL', plugin_dir_url( __FILE__ ) );

// Includes
require_once MQRT_DIR . 'includes/admin.php';
require_once MQRT_DIR . 'includes/frontend.php';

// Init
\MQRT\Admin::init();
\MQRT\Frontend::init();
