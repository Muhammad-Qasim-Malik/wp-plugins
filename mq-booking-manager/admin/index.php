<?php
$includes = [
    '/admin/helper.php',
    '/admin/menu.php',
    '/admin/database.php',
    '/admin/dashboard.php',
    '/admin/settings.php',
];
foreach($includes as $include){
    require_once(MQBM_DIR_PATH . $include);
}