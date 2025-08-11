# Challan Search Plugin

The **Challan Search Plugin** allows WordPress administrators to upload, manage, and search challan data. It includes features for importing XLSX files, auto-updating challan statuses (including marking them as "Overdue" when the due date has passed and they're unpaid), generating PDFs for each challan, and providing a public-facing search form and results.

## Features

- **Admin Upload Functionality**: Admins can upload challan data via XLSX files.
- **Public Search Page**: Users can search for challan data using a registration number.
- **Shortcodes**: Two shortcodes for displaying a search form (`[challan_form]`) and showing search results (`[challan_result]`).
- **Auto Status Update**: Cron job that automatically updates challans to **Overdue** if they are unpaid and the due date has passed.
- **PDF Generation**: Generates a PDF for each challan with a "Print Preview" button.
- **Edit and Add Challans**: Admins can manually add or edit challan records.
- **Search by Registration Number**: Users can search by their registration number (challan ID).
- **Search Results**: Displays relevant challan details including due amount, payment status, and due date.

## Installation

### 1. Download and Install the Plugin
- Download the plugin files.
- Upload the plugin folder to the `/wp-content/plugins/` directory of your WordPress installation.

### 2. Activate the Plugin
- Go to your WordPress dashboard.
- Navigate to **Plugins** > **Installed Plugins**.
- Find **Challan Search Plugin** and click **Activate**.

### 3. Requirements
This plugin requires:
- **WordPress 5.0 or higher**.
- **PHP 7.0 or higher**.
- **Elementor** (if you want to use Elementor widgets functionality).

### 4. Admin: Import Challan Data via XLSX
Admins can upload an XLSX file with challan data from the WordPress admin panel. The plugin processes the file, inserts the data into the database, and makes it available for public search. The file should follow the format required by the plugin (e.g., columns for registration number, student name, due date, fees, etc.).

## Usage

### Admin: Uploading Challan Data
Once activated, the admin can upload an XLSX file containing challan data. The plugin will process and store the data in the database for future searches.

### Shortcodes for Displaying the Search Form and Results

1. **Search Form**: To display the challan search form on any page or post, use the following shortcode:

   ```bash
   [challan_form]
2. **Search Results**: To display the challan search Results on the same page where Form has been used, use the following shortcode:

   ```bash
   [challan_result]

