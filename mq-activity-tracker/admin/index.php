<?php
$includes = [
    '/admin/database.php',
    '/admin/menu.php',
    '/admin/admin-page.php',
    '/admin/helper.php',
    '/admin/logs.php',
    '/admin/csv-export.php',
];

foreach($includes as $include) {
    require_once(MQAT_DIR_PATH . $include);
}
 ?>