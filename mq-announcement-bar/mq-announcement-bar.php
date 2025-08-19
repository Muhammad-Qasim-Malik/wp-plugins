<?php
/**
 * Plugin Name: MQ Simple Announcement Bar
 * Description: A lightweight plugin to display a customizable announcement bar on your WordPress site.
 * Version: 1.0.0
 * Author: Muhammad Qasim
 * Text Domain: mq-simple-announcement-bar
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MQAB_FILE', __FILE__ );
define( 'MQAB_DIR', plugin_dir_path( __FILE__ ) );
define( 'MQAB_URL', plugin_dir_url( __FILE__ ) );

require_once MQAB_DIR . 'includes/admin.php';
require_once MQAB_DIR . 'includes/frontend.php';

\MQAB\Admin::init();
\MQAB\Frontend::init();
