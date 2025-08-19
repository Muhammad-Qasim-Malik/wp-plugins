# MQ Simple Announcement Bar

A lightweight WordPress plugin to display a customizable announcement bar on your site. It allows you to display a message at the top or bottom of your pages with a configurable background color, text color, and the ability to dismiss the bar.

---

## ✨ Features

- **Customizable Announcement Bar**: Display an announcement message at the top or bottom of your website.
- **Admin Settings**: Configure text, background color, text color, position (top/bottom), and enable/disable the dismiss button.
- **Responsive and Lightweight**: Bar adjusts to screen sizes and doesn't affect page load performance.
- **Dismiss Feature**: Users can close the bar, and it won’t show again unless the cache is cleared.
- **WP Color Picker**: Admins can easily pick custom colors for the background and text.

---

## 📂 Installation

1. Upload the `mq-simple-announcement-bar` folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → MQ Announcement Bar** to configure the plugin's settings.

---

## ⚙️ Admin Settings

- **Announcement Text**: Enter the message you want to display in the announcement bar.
- **Background Color**: Choose the background color of the bar.
- **Text Color**: Choose the text color of the bar.
- **Position**: Choose whether the bar should appear at the **Top** or **Bottom** of the page.
- **Dismiss Button**: Option to allow users to dismiss the announcement bar. If enabled, users can click on the "×" to hide it.

---

## 🖼️ Example

If your announcement text is "Big Sale Now On!" and the background color is set to `#FF5733`, the bar will display:

Big Sale Now On! ✖

This bar will appear at the **top** of the screen with the selected colors and a dismiss button.

---

## 📌 Notes

- The bar will only show on **single pages/posts**. It doesn’t show on the homepage or archive pages unless configured.
- If the **dismiss** option is enabled, the bar will hide for the user once dismissed, using local storage.
- The color options allow for complete customization via the **WP Color Picker** in the settings.

---

---

## 📜 License

This plugin is open-source and distributed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
