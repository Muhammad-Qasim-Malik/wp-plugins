<?php
function sitemap_plugin_menu() {
    add_menu_page('Sitemap Generator', 'Sitemap Generator', 'manage_options', 'sitemap-generator', 'sitemap_generator_page', 'dashicons-admin-site', 20);
    add_submenu_page('sitemap-generator', 'Create Sitemap', 'Create Sitemap', 'manage_options', 'create-sitemap', 'create_sitemap_page');
    add_submenu_page('sitemap-generator', 'Manage Links', 'Manage Links', 'manage_options', 'manage-links', 'manage_links_page');
    add_submenu_page('sitemap-generator','Add Link to Sitemap','Add Link',  'manage_options','add-link', 'add_link_to_sitemap_page'
    );
}
add_action('admin_menu', 'sitemap_plugin_menu');

