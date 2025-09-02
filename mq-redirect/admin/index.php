<?php
$includes_admin = [
    '/admin/helper.php',
    '/admin/menu.php',
    '/admin/dashboard.php',
    '/admin/database.php',
];

foreach ($includes_admin as $include) {
    require_once(MQ_REDIRECT_DIR . $include);

}