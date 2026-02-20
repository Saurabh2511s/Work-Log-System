#  Get the Source Code

Clone the repository or extract the project inside your htdocs directory.

cd C:\xampp\htdocs
git clone https://github.com/Saurabh2511s/Work-Log-System.git
OR extract zip and rename folder to worklog

Then enter the project folder:

cd worklog


Configure Environment File

Now update database details inside .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=worklog
DB_USERNAME=root
DB_PASSWORD=


Run Migrations & Seeders

This will create tables and insert default data:

php artisan migrate --seed


Start Development Server

php artisan serve

Application URL

http://127.0.0.1:8000

Note : if any error occurs 
Clear caches and retry:
php artisan optimize:clear 

Then 

Start Development Server

php artisan serve

Application URL

http://127.0.0.1:8000