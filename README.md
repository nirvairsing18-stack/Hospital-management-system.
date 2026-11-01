# Hospital Management System

A DBMS mini-project demonstrating a simple hospital administration website using **HTML, CSS, PHP, and MySQL**.

## Features
- Dashboard cards and recent appointments
- Add and view patient records
- Add and view doctors, connected to departments
- Book appointments and update appointment status
- MySQL trigger automatically creates a ₹500 pending bill when an appointment changes to `Completed`
- Billing page to mark pending bills as paid
- Foreign keys, unique appointment-time constraint, check constraints, indexes, and a `pending_bills` view

## Requirements
- PHP 8.1+ with PDO MySQL enabled
- MySQL 8.0+
- A local server package such as XAMPP, WAMP, or MAMP

## Setup (XAMPP example)
1. Install and open XAMPP. Start **Apache** and **MySQL**.
2. Copy this project folder into XAMPP's `htdocs` directory, e.g. `C:\xampp\htdocs\hospital_management_system_project`.
3. Open `http://localhost/phpmyadmin` in a browser.
4. Select **Import**, choose `database.sql`, and import it. The script creates the `hospital_management_system` database and sample records.
5. Open `config.php`. The defaults use MySQL user `root` with a blank password. Change these if your setup uses different credentials.
6. Visit `http://localhost/hospital_management_system_project/`.

## How to test
1. Add a patient on the Patients page.
2. Add a doctor on the Doctors page.
3. Book a future appointment on the Appointments page.
4. Change its status to **Completed**. The MySQL trigger creates a pending bill automatically.
5. Open Billing and mark the bill as paid.

## Database tables
- `Department`
- `Doctor`
- `Patient`
- `Appointment`
- `Bill`

## Important notes
- This is an educational demonstration, not a production-ready medical system. It has no login, roles, audit trail, or production security hardening. Do not enter real patient data.
- The database sample data contains fictional people and phone numbers.
- The interface uses a Google Fonts import; if offline, it falls back to local sans-serif fonts.
