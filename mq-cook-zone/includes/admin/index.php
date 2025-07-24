<?php
$mqcz_admin = [
    'includes/admin/mqcz-chef-approval.php',
    'includes/admin/restrict-admin-access.php',
];

foreach ( $mqcz_admin as $file ) {
    require_once MQCZ_PATH . $file;
}