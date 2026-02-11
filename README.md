# 🍔 RoyalSpot Café - Reservation System

A full-stack web application designed for a café to manage table bookings seamlessly. This project handles the entire flow from a customer-facing frontend to a secure MySQL backend.

## 🚀 Features
* **Dynamic Booking Form:** Captures guest details, date, time, and seating preferences (Indoor/Outdoor, Smoking/Non-Smoking).
* **PHP Processing:** Backend script that validates data and prevents unauthorized request methods.
* **Database Integration:** Stores all reservations in a structured MySQL database via Laragon.
* **Automated Notifications:** Triggers an email summary for every new reservation.
* **Responsive Design:** Optimized for both desktop and mobile users.

## 🛠️ Tech Stack
* **Frontend:** HTML5, CSS3, JavaScript
* **Backend:** PHP 7.4+
* **Database:** MySQL 5.7
* **Server Environment:** Laragon / Apache

## 📸 Preview
*Insert a screenshot of your reservation page here*

## ⚙️ Setup
1. Clone the repository into your `laragon/www` folder.
2. Import the provided SQL schema into your MySQL database.
3. Configure your database connection in `booking_processing.php`.
4. Access via `http://localhost:8080/Royal/`.