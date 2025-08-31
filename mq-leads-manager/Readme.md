# MQ Leads Manager

Manage Google Maps scraped data, upload leads in bulk, and send personalized or bulk emails directly from WordPress.

![WordPress](https://img.shields.io/badge/WordPress-Plugin-blue)
![License](https://img.shields.io/badge/License-GPLv2-orange)
![Version](https://img.shields.io/badge/Version-1.0.0-green)

---

## 📌 Features

- 📥 **Bulk Upload** – Import Google Maps scraped leads (CSV/Excel).
- 📊 **Lead Management** – Manage and organize leads directly in WordPress.
- 📧 **Email Sending** – Send emails individually or in bulk.
- 📝 **Email Templates** – Create and manage templates with **dynamic variables** like `{name}`, `{email}`, `{business}`, etc.
- ⏰ **Scheduled Emails** – Automate sending via WordPress Cron Jobs.
- ⚡ **DataTables Integration** – Searchable & sortable lead management interface.
- 🔒 **Secure & Lightweight** – Built with WordPress best practices.

---

## 🚀 Installation

1. Download the Plugin

2. Upload the plugin folder (`mq-leads-manager`) to:

   ```
   /wp-content/plugins/
   ```
3. Activate **MQ Leads Manager** from the WordPress **Plugins** menu.
4. Go to **MQ Leads Manager** in your WordPress dashboard to get started.

---

## ⚙️ Usage

### 1. Upload Leads

* Import your scraped data (CSV/Excel) into the system.
* Data will be displayed in a searchable DataTable.

### 2. Create Email Templates

* Add reusable templates with variables like `{name}`, `{email}`, `{business}`.

### 3. Send Emails

* Select leads and send emails individually or in bulk.
* Use WordPress Cron to automate sending campaigns.

---

## 📸 Screenshots (planned)

1. Lead management table with DataTables
2. Bulk upload form for leads
3. Email template editor
4. Cron job settings for scheduled emails

---

## 🔧 Requirements

* WordPress **5.0+**
* PHP **7.4+**
<!-- * Recommended: [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) for better email delivery -->

---

## 🛠️ Developer Notes

If you’re extending this plugin:

* Use the defined constants:

  ```php
  MQ_LEADS_VERSION
  MQ_LEADS_PLUGIN_DIR
  MQ_LEADS_PLUGIN_URL
  MQ_LEADS_BASENAME
  ```
* Hook into email sending process via custom actions/filters (to be documented in future releases).
* Database tables are created automatically on activation (`mq_create_tables`).

---

## 📝 Changelog

### v1.0.0

* Initial release
* Bulk data upload
* Email template management with dynamic variables
* Send emails individually or in bulk
* Cron job integration

---

## 📜 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

👨‍💻 Developed by **Muhammad Qasim**
