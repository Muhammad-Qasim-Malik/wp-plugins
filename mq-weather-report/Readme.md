# MQ Weather Report – WordPress Plugin

A lightweight WordPress plugin that displays current weather data from **OpenWeatherMap** using a shortcode.  
It provides an **Admin Settings page** for your API key, and all API requests are proxied through WordPress (your API key is never exposed to visitors).

---

## 📦 Features
- Admin Settings page to store your **OpenWeatherMap API key**
- Shortcode `[mq_weather_report]` usable anywhere (posts, pages, widgets)
- AJAX proxy for secure API calls
- Lightweight JavaScript (no React, no external dependencies)
- Customizable **CSS styling**

---

## 📁 File Structure
mq-weather-report/
├─ mq-weather-report.php # Main plugin file
├─ includes/
│ ├─ admin.php # Admin settings page
│ ├─ shortcode.php # Shortcode for the weather report
│ └─ helper.php # AJAX proxy
├─ assets/
│ ├─ style.css # Customizable CSS styling
│ └─ script.js 
└─ README.md # This file

---

## ⚙️ Installation
1. Upload the **`mq-weather-report`** folder to your WordPress `wp-content/plugins/` directory.
2. Go to **WordPress Admin → Plugins** and activate **MQ Weather Report**.
3. Navigate to **Settings → MQ Weather Report** and enter your **OpenWeatherMap API key**.

---

## 🖥️ Usage
Insert the shortcode anywhere in posts, pages, or widgets:

```text
[mq_weather_report]