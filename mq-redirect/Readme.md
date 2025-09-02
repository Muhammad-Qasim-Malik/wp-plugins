Got it ✅ — here’s a **professional `README.md`** for your **MQ Redirect** plugin, modeled after the style you showed for *MQ Booking Manager*:

---

````markdown
# MQ Redirect

**MQ Redirect** is a lightweight WordPress plugin that lets you easily create and manage URL redirects from the WordPress admin dashboard.  
It’s built to simplify redirect management without requiring third-party plugins or complex configurations.  

---

## 🚀 Features
- Manage redirects directly from the WordPress Admin.
- Add, edit, and delete redirects from a simple dashboard.
- Import multiple redirects at once via CSV upload.
- Clean and modern admin UI using **WordPress widefat tables**.
- AJAX-powered editing (no page reloads).
- DataTables integration for client-side search, sort, and pagination.
- Stores redirects in a custom database table (`wp_redirects`).

---

## ⚙️ Installation
1. Download or clone the plugin.
2. Upload the `mq-redirect` folder into your WordPress `wp-content/plugins/` directory.
3. Activate **MQ Redirect** from **WordPress Admin > Plugins**.
4. A new **Redirects** menu will appear in the admin dashboard.

---

## 🛠️ Usage

### Admin Dashboard
- Navigate to **MQ Redirects** in the admin sidebar.  
- From here you can:
  - **Add New Redirect** → Define an old URL and a new URL.  
  - **Edit Redirect** → Update existing redirects inline.  
  - **Delete Redirect** → Remove unwanted redirects.  
  - **Upload CSV** → Bulk import redirects (must contain `old_url,new_url` headers).  

### Example Workflow
1. Click **Add New** to create a redirect from `/old-page` → `/new-page`.  
2. Upload a CSV with multiple redirects for batch imports.  
3. Use the **Edit** button to update redirect targets.  

---

## 📊 CSV Upload Format
Your CSV file should have **two columns**:  

old_url,new_url
/old-page,/new-page
/legacy-page,/modern-page
---

## 🤝 Contributing

Pull requests are welcome!
For major changes, please open an issue first to discuss what you’d like to improve.

---

## 📄 License

This project is licensed under the **GPL-2.0 License** – see the LICENSE file for details.

---

## 👨‍💻 Author

**Muhammad Qasim**
📧 [kingqasimmalik@gmail.com](mailto:kingqasimmalik@gmail.com)

