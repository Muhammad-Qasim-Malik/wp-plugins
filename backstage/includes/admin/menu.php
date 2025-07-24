<?php

add_action('admin_menu', 'backstage_admin_page');

function backstage_admin_page() {
    add_menu_page(
        'Backstage Settings', 
        'Backstage', 
        'manage_options', 
        'backstage-settings', 
        'backstage_admin_page_html', 
        'dashicons-image-filter'
    );
}