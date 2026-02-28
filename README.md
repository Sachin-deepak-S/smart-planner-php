<div align="center">

# 🚀 Smart Planner

**A modern full-stack productivity web app for students and professionals.**

Manage notes, schedule events, track reminders, and receive email notifications — all in one clean, powerful interface.

[![Live Demo](https://img.shields.io/badge/🌐_Live_Demo-Visit_Site-blue?style=for-the-badge)](http://sachindeepak-planner.gt.tc/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-Backend-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)

</div>

---

## ✨ Features

### 🔐 Authentication
- Secure registration & login with PHP `password_hash`
- Session-based authentication with protected route guards
- Profile management — update name and password

### 📝 Notes Manager
- Create, edit, and view notes
- Mark notes as important 🔥
- Search & filter notes in real time
- Live statistics dashboard

### 📅 Event Scheduler
- One-time and yearly repeating events
- Date & time scheduling with notification toggles
- 1-day-before email reminders
- Filter events by: All / Today / Upcoming / Yearly

### 🗓️ Calendar Integration
- Powered by **FullCalendar.js**
- Monthly, Weekly, and Daily views
- Color-coded events with click-to-view details

### 🔔 Smart Reminders
- Database-driven reminder engine
- Real-time notification bell with unread count
- SMTP-powered email delivery

### 📊 Dashboard Analytics
- Live counters: total notes, important notes, today's events, upcoming events
- All stats fetched dynamically from the database

### 🎨 Modern UI / UX
- Dark theme with sidebar navigation
- Smooth animations, loading spinners, and toast notifications
- Fully responsive layout

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript, FullCalendar.js |
| Backend | PHP (Core) |
| Database | MySQL |
| Email | SMTP |
| SEO | Google Search Console, Sitemap.xml |

---

## 📂 Project Structure

```
smart-planner/
├── auth/           # Login, register, logout
├── config/         # Database connection
├── events/         # Event CRUD & filtering
├── notes/          # Notes CRUD & search
├── reminder/       # Reminder check & email logic
├── calendar.php    # FullCalendar view
├── dashboard.php   # Analytics & overview
├── profile.php     # User profile management
├── index.php       # Entry point
└── sitemap.xml
```

---

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/smart-planner.git
   ```

2. **Import the database**
   - Create a MySQL database
   - Import the provided `.sql` file

3. **Configure the connection**

   Edit `config/database.php` with your database credentials.

4. **Run the project**
   - Place the folder inside `htdocs` (XAMPP) or your hosting root
   - Open in your browser

---

## 🔐 Security

- Password hashing via `password_hash` / `password_verify`
- Session-based authentication guards
- SQL injection prevention with `mysqli_real_escape_string`
- Protected pages redirect unauthenticated users

---

## 🚀 Roadmap

- [ ] Email verification on registration
- [ ] Two-factor authentication (2FA)
- [ ] Progressive Web App (PWA) support
- [ ] REST API layer
- [ ] Cloud deployment (AWS / Railway / Vercel + API backend)

---

## 👨‍💻 Author

**Sachin Deepak**

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-0077B5?style=flat&logo=linkedin)](https://www.linkedin.com/in/sachin-deepak-s/)

---

## 📄 License

This project is licensed under the [MIT License](LICENSE).

---

<div align="center">

⭐ **If you found this useful, consider giving it a star!**

</div>
