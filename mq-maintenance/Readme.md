Here’s a `README.md` you can ship with your **MQ Maintenance** plugin. It covers description, features, installation, usage, and customization in a clear structure:

---

````markdown
# MQ Maintenance

A simple and customizable WordPress maintenance mode plugin by **Muhammad Qasim**.  
This plugin allows site administrators to easily enable a maintenance screen with a heading, paragraph, background image, and optional subscription form.

---

## 📦 Features

- Enable/disable **Maintenance Mode** with a single checkbox.
- Customizable **Heading** and **Paragraph** for the maintenance page.
- Upload a **Background Image** for the overlay.
- Optional **Subscribe Form** to collect user subscriptions.
- Fully responsive overlay with modern UI (centered, flexbox-based).
- Separate **Admin** and **Frontend** CSS/JS enqueueing.
- Lightweight and easy to use.

---

## 🔧 Installation

1. Download or clone this repository into your WordPress `wp-content/plugins/` directory.

2. Activate **MQ Maintenance** from the WordPress Admin → Plugins screen.
3. Navigate to **MQ Maintenance** in the WordPress Admin menu to configure settings.

---

## ⚙️ Usage

1. Go to **MQ Maintenance** in the WordPress dashboard.

2. Configure the following options:

   * ✅ **Enable Maintenance Mode**
     Toggle site-wide maintenance overlay.
   * 📝 **Maintenance Heading**
     Add a custom heading message for visitors.
   * 📄 **Maintenance Paragraph**
     Add a descriptive message (e.g., downtime details).
   * 🖼️ **Background Image**
     Upload a background image for the maintenance overlay.
   * ✉️ **Show Subscribe Form**
     Optionally display a subscription form for users.

3. Save settings.
   When enabled, non-logged-in visitors will see the maintenance overlay.

---

## 📂 File Structure

mq-maintenance/
├── admin/
│   ├── assets/
│   │   ├── css/style.css
│   │   └── js/script.js
│   ├── menu.php
│   ├── dashboard.php
│   └── helper.php
├── includes/
│   ├── assets/
│   │   ├── css/style.css
│   │   └── js/script.js
│   ├── helper.php
│   └── maintenance.php
├── mq-maintenance.php   # Main plugin file
└── README.md

---

## 🎨 Customization

* Modify `includes/assets/css/style.css` to adjust frontend overlay styling.
* Add custom JavaScript in `includes/assets/js/script.js`.
* Admin panel styles/scripts can be modified inside `admin/assets/`.

---

## 📜 Changelog

### 1.0.0

* Initial release with:

  * Maintenance Mode toggle
  * Custom heading and paragraph
  * Background image support
  * Subscription form option

---

## 👨‍💻 Author

**Muhammad Qasim**