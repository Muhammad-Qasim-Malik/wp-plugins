# Custom Sitemap Generator

**Custom Sitemap Generator** is a powerful and flexible WordPress plugin designed to manage post URLs, sitemaps, and their relationships. It enables users to create custom sitemaps and maintain efficient relationships between different posts. This plugin allows you to generate, add, and manage sitemaps tailored to your site's needs.

## Features

- **Custom Sitemap Creation**: Easily create custom sitemaps for posts, pages, or any custom post type.
- **Post URL Management**: Maintain and manage the URLs of your posts in the sitemap, ensuring all links are up-to-date.
- **Flexible Relationships**: The plugin allows you to define relationships between posts and include them in different sitemaps.
- **Last Modified Tracking**: Automatically tracks the last modification date of posts and updates the sitemap accordingly.
- **AJAX Support**: Includes AJAX functionality to process the sitemap generation in the background without disrupting the admin interface.
- **Custom Sitemap Structure**: Allows you to create sitemaps with custom fields and naming conventions.

## Installation

1. **Download the Plugin**:
   - Download the plugin ZIP file and unzip it.

2. **Upload the Plugin**:
   - Upload the plugin folder to the `/wp-content/plugins/` directory on your WordPress installation.

3. **Activate the Plugin**:
   - Go to the **Plugins** section in your WordPress admin dashboard, find **Custom Sitemap Generator**, and click **Activate**.

4. **Database Table Creation**:
   - Upon activation, the plugin will automatically create a custom database table to store sitemap links. This will track each post's URL, post type, and last modified date.

## How It Works

- **Sitemap Table**: When the plugin is activated, a custom table (`wp_sitemap_links`) is created in the database. This table stores post URLs, post types, sitemap names, and last modified dates. It is used to efficiently manage the URLs in your custom sitemaps.
  
- **Post Relationship**: The plugin allows you to create relationships between posts and assign them to custom sitemaps. It also ensures that each URL is updated when a post is modified.

- **AJAX Functionality**: The plugin uses AJAX for seamless sitemap creation without the need to reload the page. This ensures a smooth user experience, especially when generating large sitemaps.

## Admin Features

- **Sitemap Dashboard**: The plugin provides an easy-to-use admin dashboard where users can create, manage, and download their sitemaps. This interface makes it simple to view and edit the sitemap links.
  
- **Menu Integration**: The plugin adds a custom menu item in the WordPress admin, allowing you to access the sitemap management features with just a click.

- **Add Post URLs to Sitemap**: You can easily add individual post URLs to specific sitemaps, ensuring that only the necessary content is included.

- **Generate Sitemaps**: You can generate custom sitemaps for any set of posts. The plugin will update the sitemap dynamically based on the changes you make to your posts.

## Shortcodes

Currently, the **Custom Sitemap Generator** plugin does not include any built-in shortcodes. However, you can manage and display your custom sitemaps from the WordPress admin dashboard.

## Admin Settings

- **Custom Sitemap Menu**: After activating the plugin, a new menu item called **Custom Sitemap Generator** will appear in the admin dashboard.
- **Dashboard Overview**: This section allows you to view all the created sitemaps, their links, and the last modification dates.
- **Sitemap Creation**: Use the **Create Sitemap** option to create a new custom sitemap. You can choose which posts to include and customize the sitemap name.

## How to Use

1. **Create a New Sitemap**:
   - Go to **Custom Sitemap Generator > Dashboard** in the WordPress admin.
   - Click **Create New Sitemap** and select the posts you want to include.
   - Specify the sitemap name and customize any options if needed.

2. **Add Post URLs to Sitemap**:
   - From the dashboard, you can add or remove individual post URLs to/from specific sitemaps.
   - This can be useful for categorizing your posts into different sitemaps.

3. **Track Last Modified Date**:
   - The plugin automatically tracks when a post is updated and updates the sitemap accordingly.
   - Each entry in the sitemap includes the **last modified** date of the post, ensuring accurate timestamps for each URL.

4. **Download Sitemap**:
   - After generating a sitemap, you can download it directly from the admin interface.
   - You can use the generated sitemaps for SEO purposes or submit them to search engines.

## Example Usage

To create a custom sitemap for specific posts:

1. Navigate to **Custom Sitemap Generator > Dashboard**.
2. Click on **Create New Sitemap**.
3. Select the posts you want to include and customize the sitemap name.
4. Generate the sitemap, which will be saved in the database.

## Enqueuing Scripts

The plugin includes a custom script (`handler.js`) that is enqueued in the WordPress admin:

- **AJAX Script**: The script is used to handle AJAX requests for generating the sitemap in the background without reloading the page.
  
```php
function sitemap_generator_enqueue_scripts() {
    wp_enqueue_script('sitemap-generator-ajax', plugin_dir_url(__FILE__) . 'assets/js/handler.js', array('jquery'), null, true);

    wp_localize_script('sitemap-generator-ajax', 'sitemap_gen', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security_nonce' => wp_create_nonce('create_sitemap_nonce') 
    ));
}
add_action('admin_enqueue_scripts', 'sitemap_generator_enqueue_scripts');
