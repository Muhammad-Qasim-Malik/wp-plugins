<?php
function create_sitemap_file($sitemap_name) {
    global $wpdb;

    $sitemap_data = $wpdb->get_results(
        $wpdb->prepare("SELECT link, last_modified FROM {$wpdb->prefix}sitemap_links WHERE sitemap_name = %s", $sitemap_name)
    );

    // Use site root directory directly
    $sitemap_dir = trailingslashit(ABSPATH);

    $file_path = $sitemap_dir . sanitize_title($sitemap_name) . '.xml';

    // URL to site root
    $site_root_url = trailingslashit(site_url());

    $xsl_file = $site_root_url . 'sitemap.xsl'; // Assuming XSL file is in root

    $xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml_content .= '<?xml-stylesheet type="text/xsl" href="' . esc_url($xsl_file) . '"?>' . "\n";
    $xml_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($sitemap_data as $row) {
        $last_modified = $row->last_modified ? date('c', strtotime($row->last_modified)) : date('c');
        $xml_content .= '<url>' . "\n";
        $xml_content .= '<loc>' . esc_url($row->link) . '</loc>' . "\n";
        $xml_content .= '<lastmod>' . esc_html($last_modified) . '</lastmod>' . "\n";
        $xml_content .= '<changefreq>daily</changefreq>' . "\n";
        $xml_content .= '<priority>0.5</priority>' . "\n";
        $xml_content .= '</url>' . "\n";
    }

    $xml_content .= '</urlset>';

    file_put_contents($file_path, $xml_content);

    return $file_path;
}

function create_parent_sitemap_on_admin_init() {
    global $wpdb;

    $sitemap_dir = trailingslashit(ABSPATH);

    $xsl_path = $sitemap_dir . 'sitemap.xsl';

    if (!file_exists($xsl_path)) {
        $plugin_xsl_path = plugin_dir_path(__FILE__) . 'sitemap.xsl';
        if (file_exists($plugin_xsl_path)) {
            copy($plugin_xsl_path, $xsl_path);
        } else {
            error_log('sitemap.xsl not found in plugin folder: ' . $plugin_xsl_path);
        }
    }

    $sitemap_names = $wpdb->get_results("SELECT DISTINCT sitemap_name FROM {$wpdb->prefix}sitemap_links");

    $file_path = $sitemap_dir . 'sitemap.xml';

    $site_root_url = trailingslashit(site_url());
    $xsl_file = $site_root_url . 'sitemap.xsl';

    $xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml_content .= '<?xml-stylesheet type="text/xsl" href="' . esc_url($xsl_file) . '"?>' . "\n";
    $xml_content .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($sitemap_names as $sitemap) {
        $child_sitemap_url = $site_root_url . sanitize_title($sitemap->sitemap_name) . '.xml';

        $xml_content .= '<sitemap>' . "\n";
        $xml_content .= '<loc>' . esc_url($child_sitemap_url) . '</loc>' . "\n";
        $xml_content .= '</sitemap>' . "\n";

        create_sitemap_file($sitemap->sitemap_name);
    }

    $xml_content .= '</sitemapindex>';

    file_put_contents($file_path, $xml_content);

    return $file_path;
}

add_action('admin_init', 'create_parent_sitemap_on_admin_init');
?>
