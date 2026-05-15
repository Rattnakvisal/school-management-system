# School Management System

A Laravel 12 school management platform for running the daily operations of a school from one role-based web application. The system includes public website content, authentication, admin management tools, teacher workflows, and student self-service screens.

## Features

- Role-based dashboards for `admin`, `staff`, `teacher`, and `student`
- Email and password login, login OTP flow, and Google OAuth support
- Admin management for students, teachers, admin/staff accounts, classes, subjects, schedules, attendance, finance, reports, contact messages, and homepage content
- Teacher tools for classes, schedules, attendance, law requests, assignments, grades, notices, and settings
- Student tools for subjects, law requests, grades, assignments, notices, and settings
- PDF and Excel exports for reports, users, payments, students, and teachers
- Telegram bot/webhook support for OTP and notification workflows
- Responsive Blade UI powered by Vite and Tailwind CSS

## Tech Stack

- PHP `^8.2`
- Laravel `^12.0`
- MySQL 8
- Laravel Breeze, Socialite, DomPDF, Laravel Excel, Telegram Bot SDK
- Vite 7 and Tailwind CSS 4
- Docker, Nginx, Node 20

## Requirements

For Docker setup:

- Docker Desktop
- A MySQL database, either local MySQL or the optional Docker `db` service

For local setup:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL

## Quick Start With Docker

1. Copy the environment file:

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

2. Update database values in `.env` if needed. The Docker app service uses these variables:

```env
DOCKER_DB_HOST=host.docker.internal
DOCKER_DB_PORT=3308
DOCKER_DB_DATABASE=school_management_system
DOCKER_DB_USERNAME=root
DOCKER_DB_PASSWORD=
```

3. Start the containers:

```bash
docker compose up -d --build
```

4. Generate the application key:

```bash
docker compose exec app php artisan key:generate
```

5. Run migrations and seed demo users:

```bash
docker compose exec app php artisan migrate --seed
```

6. Open the app:

```text
http://localhost:8000
```

## Optional Docker Database

By default, Docker connects to MySQL on your host machine through `host.docker.internal`. To use the bundled MySQL container instead:

```bash
docker compose --profile docker-db up -d db
```

Then set:

```env
DOCKER_DB_HOST=db
DOCKER_DB_PORT=3306
DOCKER_DB_DATABASE=school_management_system
DOCKER_DB_USERNAME=laravel
DOCKER_DB_PASSWORD=laravel
DOCKER_DB_ROOT_PASSWORD=root
```

Restart the app container after changing database values:

```bash
docker compose up -d --force-recreate app
```

## Local Development

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Copy and configure the environment:

```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations and seeders:

```bash
php artisan migrate --seed
```

5. Start the app and Vite dev server:

```bash
composer run dev
```

If you prefer separate terminals:

```bash
php artisan serve
npm run dev
php artisan queue:listen --tries=1
```

## Demo Accounts

`database/seeders/DatabaseSeeder.php` creates these active users:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `visal.admin@example.com` | `Wq_76wZtR2aPRmq` |
| Staff | `sokha.staff@example.com` | `Wq_76wZtR2aPRmq` |
| Teacher | `dara.teacher@example.com` | `Wq_76wZtR2aPRmq` |

Change these credentials before using the project outside local development.

## Main Routes

| Area | URL |
| --- | --- |
| Public website | `/` |
| Login | `/login` |
| Admin dashboard | `/admin/dashboard` |
| Staff dashboard | `/staff/dashboard` |
| Teacher dashboard | `/teacher/dashboard` |
| Student dashboard | `/student/dashboard` |

## Useful Commands

Run tests:

```bash
php artisan test
```

Format PHP code:

```bash
./vendor/bin/pint
```

Build frontend assets:

```bash
npm run build
```

Clear Laravel caches:

```bash
php artisan optimize:clear
```

Cache and validate Blade views:

```bash
php artisan view:cache
php artisan view:clear
```

Create the public storage link:

```bash
php artisan storage:link
```

## Docker Commands

Start services:

```bash
docker compose up -d
```

Rebuild services and assets:

```bash
docker compose up -d --build
```

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Run seeders:

```bash
docker compose exec app php artisan db:seed
```

View logs:

```bash
docker compose logs -f app
docker compose logs -f web
docker compose logs -f assets
```

Start Vite in Docker:

```bash
docker compose --profile frontend up vite
```

Stop services:

```bash
docker compose down
```

## Environment Notes

- `APP_TIMEZONE` defaults to `Asia/Phnom_Penh`.
- `SESSION_DRIVER`, `CACHE_STORE`, and `QUEUE_CONNECTION` use database-backed drivers by default, so migrations must be run before login flows work reliably.
- Google OAuth requires `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, and `GOOGLE_REDIRECT_URI`.
- Email OTP requires valid `MAIL_*` SMTP settings.
- Telegram integration requires `TELEGRAM_BOT_TOKEN`, `TELEGRAM_BOT_USERNAME`, and optional webhook settings.

## Troubleshooting

If login fails after a fresh setup, confirm migrations and seeders ran:

```bash
php artisan migrate:status
php artisan db:seed
```

If Docker shows `502 Bad Gateway`, restart Nginx:

```bash
docker compose restart web
```

If frontend styles look stale, rebuild assets:

```bash
npm run build
```

Or with Docker:

```bash
docker compose run --rm assets
```

If uploaded images do not load, create the storage symlink:

```bash
php artisan storage:link
```
