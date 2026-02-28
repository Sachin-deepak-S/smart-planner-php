# 🚀 Smart Planner – Full Stack Productivity Web App

Smart Planner is a modern full-stack productivity web application built with PHP, MySQL, JavaScript, and FullCalendar.  
It helps students and professionals manage notes, schedule events, track reminders, and receive email notifications — all in one clean and powerful interface.

🌐 Live Demo: http://sachindeepak-planner.gt.tc/

---

## ✨ Features

### 🔐 Authentication
- Secure user registration & login
- Password hashing (PHP `password_hash`)
- Session-based authentication
- Profile management (update name & password)

### 📝 Notes Manager
- Add, edit, and view notes
- Mark notes as important 🔥
- Search & filter notes
- Real-time statistics
- Clean dark UI layout

### 📅 Event Scheduler
- Create one-time & yearly repeating events
- Date & time scheduling
- Notification toggle (1-day before)
- Event filtering (All / Today / Upcoming / Yearly)

### 🗓 Calendar Integration
- FullCalendar integration
- Monthly / Weekly / Daily views
- Dynamic color-coded events
- Interactive event click details

### 🔔 Smart Reminders
- Database-driven reminder checking
- Real-time notification count
- Dynamic notification bell

### 📊 Dashboard Analytics
- Total notes counter
- Important notes counter
- Today’s events counter
- Upcoming events counter
- Live statistics from database

### 🎨 Modern UI / UX
- Dark theme design
- Sidebar navigation layout
- Smooth animations
- Loading spinners
- Toast notifications
- Responsive layout

---

## 🛠 Tech Stack

**Frontend**
- HTML5
- CSS3
- JavaScript
- FullCalendar.js

**Backend**
- PHP (Core PHP)
- MySQL

**Other Tools**
- Google Search Console (SEO)
- Sitemap.xml integration
- Email reminders (SMTP)

---

## 📂 Project Structure


/auth
/config
/events
/notes
/reminder
calendar.php
dashboard.php
profile.php
index.php
sitemap.xml


---

## 🔧 Installation Guide

1. Clone the repository:


git clone https://github.com/yourusername/smart-planner.git


2. Import the database:
- Create a MySQL database
- Import the provided `.sql` file

3. Configure database connection:
Edit:

config/database.php


4. Run the project:
- Place inside `htdocs` (XAMPP) or hosting server
- Open in browser

---

## 🔐 Security Features

- Password hashing
- Session protection
- SQL injection prevention (`mysqli_real_escape_string`)
- Authentication guard on protected pages

---

## 🚀 Future Improvements

- Email verification system
- Two-factor authentication
- PWA version
- REST API integration
- Cloud deployment (AWS / Vercel + API backend)

---

## 👨‍💻 Author

Sachin Deepak  
Full Stack Developer (Learning & Building Daily)

LinkedIn: https://linkedin.com/in/yourprofile  
GitHub: https://github.com/yourusername  

---

## 📄 License

This project is licensed under the MIT License.

---

⭐ If you found this useful, consider giving it a star!
