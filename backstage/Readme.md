# Backstage by MQ

**Backstage by MQ** is a WordPress plugin that simplifies image management for your website. It allows you to bulk upload image URLs via an XLSX file, set a global background image, and display post-specific images or titles with a customizable logo using an Elementor widget. This streamlines design updates and eliminates the need to manage individual product images.

## Description

The plugin provides a robust solution for managing images across your WordPress site. You can upload an XLSX file containing post slugs and image URLs, which are stored as post meta in the database. A global background image can be set via the admin dashboard, and an Elementor widget lets you display either a post-specific image (if available) or the post title, along with the site logo, over the background image. All elements are fully customizable via Elementor’s interface, making it easy to adjust positioning, styling, and more.

## Features

### Bulk Image URL Upload
- Upload an XLSX file with two columns: post slugs (first column) and image URLs (second column).
- Image URLs are saved as post meta (_image_url) for each corresponding post.

### Global Background Image
- Set a single background image in the admin dashboard, applied across all widget instances.

### Elementor Widget
- Displays a post-specific image (if _image_url exists in the database) or the post title over the global background image.
- Optionally includes the site logo with adjustable position, size, opacity, and hover effects.
- Offers full styling controls in Elementor for container size, image positioning, title typography, text color, text shadow, and more.

### Fallback Display
- If no image URL is stored for a post, the widget displays the post title over the background image.

### Efficient Design Updates
- Update the global background image or bulk-uploaded URLs to change the site’s appearance without modifying individual posts.

## Installation

### Download the Plugin:
- Download the `backstage-by-mq` plugin ZIP file and unzip it.

### Upload the Plugin:
- Upload the `backstage-by-mq` folder to the `/wp-content/plugins/` directory of your WordPress installation.

### Activate the Plugin:
- Go to **Plugins** in your WordPress admin dashboard, find **Backstage by MQ**, and click **Activate**.

## Usage

### Set the Global Background Image:
1. Navigate to **Backstage > Backstage Settings** in the WordPress admin dashboard.
2. Upload a background image to be used across all widget instances.

### Bulk Upload Image URLs:
1. Go to **Backstage > Backstage Upload Settings** in the admin dashboard.
2. Upload an XLSX file with two columns:
    - First column: Post slugs (e.g., `my-post-title`).
    - Second column: Image URLs (e.g., `https://example.com/image.jpg`).
   
   The plugin saves each image URL as post meta (`_image_url`) for the corresponding post.

### Add the Elementor Widget:
1. Open a page or post in the Elementor editor.
2. Search for the **Backstage Image widget** in the Elementor panel and drag it onto the page.
3. Customize the widget settings:
   - **Container**: Adjust width, height, and alignment.
   - **Site Logo**: Set position, height, opacity, and hover effects.
   - **Content**: Position the image or title, adjust image size and object-fit, or customize title typography, color, and text shadow.
   
   The widget displays the post’s image (if `_image_url` exists) or the post title over the global background image.

## Requirements

- WordPress 5.0 or higher.
- Elementor plugin (free or pro version) for widget functionality.
- PHP 7.4 or higher for compatibility with the PhpOffice\PhpSpreadsheet library (used for XLSX processing).
- Write permissions for file uploads in the WordPress environment.

## Notes

- **XLSX File Format**: Ensure the XLSX file has exactly two columns (slug, image URL) to avoid processing errors.
- **Optional Components**: The global background image and logo are optional. The widget functions with only post-specific images or titles.
- **Custom CSS**: Styling is managed via Elementor, but custom CSS can be added to your theme or plugin for advanced tweaks.

## Support

For issues or feature requests, contact the plugin developer through the WordPress support forum or GitHub repository (replace with your repository URL).
