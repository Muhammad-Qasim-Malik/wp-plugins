<?php
$includes = [
    '/includes/helper.php',
    '/includes/shortcode.php',
];
foreach($includes as $include){
    require_once(MQBM_DIR_PATH . $include);
}