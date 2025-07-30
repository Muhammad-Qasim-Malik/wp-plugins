<?php 

add_action('admin_menu', 'backstage_admin_page');

function backstage_admin_page() {
    add_menu_page(
        'Backstage Settings',
        'Backstage',
        'manage_options',
        'backstage-settings',
        'backstage_admin_page_html',
        'dashicons-image-filter',
        6
    );

    add_submenu_page(
        'backstage-settings',
        'Backstage Settings',
        'Backstage Settings',
        'manage_options',
        'backstage-settings',
        'backstage_admin_page_html'
    );

    add_submenu_page(
        'backstage-settings',
        'Backstage Upload',
        'Upload File',
        'manage_options',
        'backstage-upload',
        'backstage_upload_page_html'
    );
}
