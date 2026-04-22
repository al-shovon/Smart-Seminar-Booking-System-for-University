# Smart-Seminar-Booking-System-for-University

## Project Overview
The Smart Seminar Booking System is a web-based application designed to help universities manage seminar registrations efficiently. Students can view upcoming seminars and book seats online, while administrators can create and manage seminar events and monitor participant registrations.

This system helps improve organization, prevents duplicate bookings, and allows easy management of seminar events.

---

## Features

- View upcoming seminars
- Online seminar seat booking
- Real-time seat availability
  
- Booking history for users
- Admin dashboard for managing seminars
- Create, update, and delete seminar events

---

## Technology Stack

### Frontend
- HTML
- CSS
- JavaScript

### Backend
- php

### Database
- MySQL

---

## System Modules

### User Module
- View seminar list
- Register for seminars
- Cancel bookings
- View booking history

### Admin Module
- Create seminars
- Update seminar information
- Delete seminars
- Manage participant lists

---

## Database Tables

- users
- seminars
- bookings
- admins

---

## Project Structure

Smart-Seminar-Booking-System-for-University/
│
├── admin/                     # Admin panel
│   ├── login.php             # Admin login page
│   ├── dashboard.php         # Admin dashboard
│   ├── add_seminar.php       # Add new seminar
│   ├── edit_seminar.php      # Edit seminar details
│   ├── delete_seminar.php    # Delete seminar
│   └── ...                   # অন্যান্য admin-related files
│
├── assets/                   # Static assets
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   ├── js/
│   │   └── main.js           # JavaScript functionality
│   └── images/               # Image resources
│
├── includes/                 # Reusable backend components
│   ├── db.php                # Database connection
│   ├── header.php            # Common header
│   ├── footer.php            # Common footer
│   └── ...                   # Other includes
│
├── index.php                 # Homepage
├── booking.php               # Seminar booking logic
├── seminar_detail.php        # Seminar details page
├── process_booking.php       # Handles booking submission
├── logout.php                # Logout functionality
│
├── database/ (optional)      # Database files
│   └── seminar_db.sql        # SQL dump file
│
└── README.md                 # Project documentation

---

## Future Improvements

- Email notification for seminar registration
- QR code based attendance system
- Seminar reminders
- Certificate generation for participants

  

---

## Author

- Abdullah Al Shovon
- Pial Hasan Tutul
- Swapnil Roy
