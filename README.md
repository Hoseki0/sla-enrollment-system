# Sta. Lucia Academy Enrollment System

A modern, web-based Enrollment Management System and Student Portal built specifically for **Sta. Lucia Academy**. This application streamlines the enrollment process for students and provides school administrators with a comprehensive dashboard to manage records, sections, and academic school years.

![SLA Enrollment System Theme](https://img.shields.io/badge/Theme-Blue%20%26%20White-00509e)
![Framework](https://img.shields.io/badge/Framework-Laravel-FF2D20?logo=laravel)
![Database](https://img.shields.io/badge/Database-MySQL-4479A1?logo=mysql)
![Frontend](https://img.shields.io/badge/Frontend-Bootstrap%205-7952B3?logo=bootstrap)

---

## 🌟 Key Features

### 🎓 For Students (Student Portal)
- **Account Registration & Login**: Secure account creation requiring verification (Student ID + Last Name).
- **Online Enrollment Application**: A dynamic, multi-step enrollment form supporting both Junior High School (JHS) and Senior High School (SHS).
- **Track & Strand Selection**: Dynamically loads applicable academic tracks and strands for SHS enrollees.
- **Real-time Status Tracking**: Students can log in to check if their enrollment is Pending, Enrolled, Cancelled, or Dropped.
- **Profile Management**: Keep personal, address, and guardian information up to date.

### 🛡️ For Administrators (Admin Portal)
- **Analytics Dashboard**: Get a birds-eye view of total enrollments, pending applications, and student populations.
- **Enrollment Processing**: Review incoming enrollment applications and easily approve or decline them.
- **Student Records**: Maintain a centralized, searchable database of all enrolled students using DataTables.
- **Section Management**: Create and assign students to specific grade sections and adviser classes.
- **School Year Configuration**: Toggle active academic school years to ensure data is properly categorized.

---

## 🎨 UI/UX Design
The system utilizes a premium **Blue and White** aesthetic. The design includes:
- Modern glassmorphism effects and clean solid white containers.
- Smooth CSS keyframe entrance animations (e.g., slide-ins, pop-ins) for a dynamic feel.
- Responsive layout that ensures the system is accessible on both mobile phones and desktop computers.

---

## 🚀 Installation & Setup

Follow these steps to run the project locally on your machine using **XAMPP**.

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL (via XAMPP)
- Git

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/sla-enrollment.git
cd sla-enrollment
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
Copy the sample environment file and generate your application key.
```bash
copy .env.example .env
php artisan key:generate
```
Open the `.env` file and configure your database connection:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sla
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database Setup
Make sure your Apache and MySQL servers are running in XAMPP. Create a database named `sla` in phpMyAdmin, then run the migrations and seeders:
```bash
php artisan migrate:fresh --seed
```
*(Optional)* You can also run the custom demo seeder to populate sample data:
```bash
php artisan db:seed --class=DemoSeeder
```
*(Or navigate to `http://localhost/sla-enrollment/seed_demo.php` if configured).*

### 5. Run the Application
Start the Laravel development server:
```bash
php artisan serve
```
The application will be accessible at: `http://localhost:8000`

---

## 📁 System Architecture
- **Backend Framework**: Laravel
- **Frontend**: Blade Templating, Vanilla CSS, Bootstrap 5.3, Bootstrap Icons
- **Database Tables**:
  - `users` / `admins`
  - `students`
  - `enrollments`
  - `school_years`
  - `sections`

---

## 🛠️ Automated Updates
A custom batch script (`update.bat`) is included in the root directory. To quickly push changes to GitHub:
1. Double click `update.bat`.
2. Enter your commit message.
3. The script will automatically add, commit, and push your code to the remote repository.

---
*Developed for Sta. Lucia Academy.*
