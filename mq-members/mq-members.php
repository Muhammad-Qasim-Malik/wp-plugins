<?php
/**
 * Plugin Name: MQ Members
 * Author: Muhammad Qasim
 * Description: Provides signup/login shortcodes, Stripe integration, and a user dashboard for Muay-Thai posts.
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('MQ_MEMBERS_PATH', plugin_dir_path(__FILE__));
define('MQ_MEMBERS_URL', plugin_dir_url(__FILE__));
define('MQ_MEMBERS_INC', MQ_MEMBERS_PATH . 'includes/');
define('MQ_MEMBERS_ASSETS', MQ_MEMBERS_URL . 'assets/');
define('STRIPE_SECRET_KEY', 'Your Stripe Secret Key');
define('STRIPE_PUBLISHABLE_KEY', 'Your Stripe Publishable Key');

$autoload_path = MQ_MEMBERS_PATH . 'vendor/autoload.php'; 
require_once $autoload_path; 
require_once  MQ_MEMBERS_PATH . 'functions.php';
require_once(ABSPATH . 'wp-admin/includes/file.php');

// Include required files
$mq_members_includes = [
    'mq-signup.php',
    'mq-login.php',
    'mq-button.php',
    'mq-single.php',
    'mq-dashboard.php'
];

foreach ($mq_members_includes as $file) {
    $path = MQ_MEMBERS_INC . $file;
    if (file_exists($path)) {
        include_once $path;
    }
}

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('mq-members-style', MQ_MEMBERS_ASSETS . 'style.css', [], '1.0');
    wp_enqueue_script('mq-members-script', MQ_MEMBERS_ASSETS . 'script.js', ['jquery'], '1.0', true);
});

register_activation_hook(__FILE__, function () {
    add_role('paid_member', 'Paid Member', ['read' => true]);
    add_role('free_member', 'Free Member', ['read' => true]);
});
