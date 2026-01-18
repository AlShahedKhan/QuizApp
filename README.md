QuizTap (Pure PHP)

Setup
1) Create a MySQL database (default: quizapp).
2) Import schema and seed:
   - sql/schema.sql
   - sql/seed.sql
3) Update config/config.php with DB credentials (or use env vars DB_HOST, DB_NAME, DB_USER, DB_PASS).
4) Point your web server document root to this project folder.

Admin Login (seed)
- Username: admin
- Password: admin123

Monthly Reset (cron/task scheduler)
Run once at the start of each month:
php scripts/monthly-reset.php

Notes
- PHP 8+ recommended.
- Ensure MySQL uses utf8mb4.
