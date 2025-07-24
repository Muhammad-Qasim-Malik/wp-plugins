<?php
$mqcz_shortcodes = [
    'includes/shortcodes/mqcz-header-button.php',
    'includes/shortcodes/mqcz-login.php',
    'includes/shortcodes/mqcz-signup.php',
    'includes/shortcodes/mqcz-dashboard.php',
    'includes/shortcodes/mqcz-like-button.php',
];

foreach ( $mqcz_shortcodes as $file ) {
    require_once MQCZ_PATH . $file;
}