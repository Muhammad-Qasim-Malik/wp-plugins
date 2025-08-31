# MQ Booking Manager

**MQ Booking Manager** is a WordPress plugin for managing service bookings with a weekly availability schedule and a calendar dashboard.  
---

## 🚀 Features

- Define weekly availability (per day start/end times or mark as off).
- Users can book services directly on the frontend.
- Prevents double booking using unique constraints.
- Admin dashboard with **calendar view** to display all bookings.
- FullCalendar integration with day/week/month views.
- AJAX-powered booking system (no page reloads).
- Lightweight & easy to customize.

---

## ⚙️ Installation

1. Download the plugin.
2. Upload the `mq-booking-manager` folder into your WordPress `wp-content/plugins/` directory.
3. Activate the plugin from **WordPress Admin > Plugins**.
4. A new **Bookings** menu will appear in the admin dashboard.

---

## 🛠️ Usage

### Admin

* Set weekly availability under **Bookings > Settings**.
* View and manage bookings in the **Bookings > Dashboard** calendar.

### User

* Use `[mq_booking]` shortcode to display the booking form on any page.
* Users can select a date/time and book an available slot.
* If a slot is already booked, it won’t be available.

---

## 🤝 Contributing

Pull requests are welcome!
For major changes, please open an issue first to discuss what you’d like to improve.

---

## 📄 License

This project is licensed under the **GPL-2.0 License** – see the [LICENSE](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) file for details.

---

👨‍💻 **Author:** Muhammad Qasim