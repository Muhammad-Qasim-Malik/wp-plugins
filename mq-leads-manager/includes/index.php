<?php
/**
 * Autoload required files
 */
$mq_leads_files = [
    'includes/database.php',
    'includes/menu.php',
    'includes/helper.php',
    'includes/admin/dashboard.php',
    'includes/admin/leads.php',
    'includes/admin/settings.php',
    'includes/admin/templates.php',
    'includes/admin/send-email.php',
    'includes/admin/email-history.php',
];

foreach ($mq_leads_files as $file) {
    $path = MQ_LEADS_PLUGIN_DIR . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}