# Work Log System (Laravel Task Assignment)

## Project Setup Guide

Follow the steps below to run the project locally.

-----------------------------------------------


```bash


### 1) Get the Source Code

Clone the repository inside your web server directory (XAMPP/WAMP/LAMP).

cd C:\xampp\htdocs
git clone https://github.com/Saurabh2511s/Work-Log-System.git
cd Work-Log-System


### 2) Configure Environment

Create and configure the environment file:

Update database credentials in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=worklog
DB_USERNAME=root
DB_PASSWORD=



### 3) Run Migrations & Seeders


This will create tables and insert default data:

php artisan migrate --seed


### 4) Start Development Server

php artisan serve

Application URL:  http://127.0.0.1:8000



### 5) Troubleshooting

If any error occurs during setup, clear caches and retry:

php artisan optimize:clear
php artisan serve