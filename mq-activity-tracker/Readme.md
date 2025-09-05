# MQ Activity Tracker

## Description

The MQ Activity Tracker plugin allows WordPress administrators to track activity on the site. It logs key actions that occur, such as user logins, content edits, and other significant events. Administrators can view detailed logs, filter them by actions or search terms, and even export logs to CSV for further analysis. Additionally, the plugin provides an option to clear activity logs.

## Features

- **Activity Logging**: Track user activity, including actions, messages, IP addresses, and objects involved.
- **Admin Dashboard**: A dedicated admin page to view and filter the activity log.
- **Search and Filter**: Filter logs by action, keyword, or IP.
- **Export Logs**: Export logs to CSV for reporting or backup purposes.
- **Log Management**: Clear logs with a single button click.
- **Pagination**: Paginate through logs for easy browsing.

## Installation

1. Download the `mq-activity-tracker` plugin zip file.
2. From the WordPress admin dashboard, go to **Plugins > Add New > Upload Plugin**.
3. Click **Choose File**, select the zip file, and click **Install Now**.
4. Once installed, click **Activate** to activate the plugin.

## Usage

Once activated, the plugin adds an "Activity Log" menu item to the WordPress admin menu. To view the activity log:

1. Navigate to **Activity Log** in the admin menu.
2. You can filter the logs by:
   - Action (e.g., logins, content updates)
   - Search term (e.g., keywords, IP addresses)
3. To export the logs to a CSV file, click the **Export CSV** button.
4. To clear the logs, click **Clear Logs**.

## How It Works

- The plugin tracks various activities on the site, storing information in a custom database table (`mq_activity_log`).
- The admin can filter logs by action type or search terms (e.g., user, message, object).
- Data can be exported into a CSV file for external analysis or record-keeping.

### Database Table

The plugin creates a custom table in the database to store activity logs:

- `mq_activity_log`: This table contains details like time, user ID, IP address, action, object type, and message.

### Logs Table Columns

- **id**: The unique ID of the log entry.
- **logged_at**: The time the activity occurred.
- **user_id**: The ID of the user who performed the action.
- **ip**: The IP address of the user.
- **action**: The type of action performed (e.g., "edit_post").
- **object_type**: The type of object (e.g., "post", "page").
- **object_id**: The ID of the object involved in the action.
- **message**: A detailed message about the action.

## Hooks

- **admin_enqueue_scripts**: Enqueues the admin styles and scripts to be used on the plugin's admin page.

## Security

- The plugin uses WordPress nonces to secure the export and log clearing actions to prevent unauthorized access.
- Only administrators with the `manage_options` capability can access the activity log page.

## Changelog

### 1.0.0
- Initial release of the MQ Activity Tracker plugin with basic logging functionality, filtering, export, and log management.

## Developer Notes

The plugin defines constants and includes necessary files, including an admin panel for viewing and managing activity logs. Developers can extend the plugin by adding custom actions or modifying the log structure.

## Author

Muhammad Qasim

## License

This plugin is licensed under the GPL-2.0 license.

