# School Management System

Laravel 12 school management platform with role-based dashboards for:

- Admin
- Teacher
- Student

Main auth flows:

- Email/password login
- Login OTP flow
- Google OAuth login

## Tech Stack

- PHP 8.4 (Docker `app` service)
- Laravel 12
- MySQL 8
- Nginx
- Vite

## Current Docker Setup (Important)

The current `docker-compose.yml` is configured so:

- `web` is exposed on `http://localhost:8000`
- `app` (Laravel/PHP-FPM) connects to **host MySQL** at:
  - `DB_HOST=host.docker.internal`
  - `DB_PORT=3308`

This is intentional so the app can use your existing MySQL data/users on port `3308`.

The `db` service still exists, but it is **not exposed to host port** by default.

## Quick Start

1. Install dependencies (if needed):

```bash
composer install
npm install
```

2. Build frontend assets:

```bash
npm run build
```

3. Start Docker services:

```bash
docker compose up -d --build
```

4. Generate app key (only first setup):

```bash
docker compose exec app php artisan key:generate
```

5. Open:

```text
http://localhost:8000
```

## Database Commands

Run migrations:

```bash
docker compose exec app php artisan migrate
```

Run seeders:

```bash
docker compose exec app php artisan db:seed
```

Check migration status:

```bash
docker compose exec app php artisan migrate:status
```

## Default Admin Seeder

`DatabaseSeeder` creates/updates an admin user:

- Email: `visalchunrathanak@gmail.com`
- Password: `Wq_76wZtR2aPRmq`
- Role: `admin`

## Role Routes

- Admin: `/admin/*`
- Teacher: `/teacher/*`
- Student: `/student/*`

Public routes include home `/`, login `/login`, Google auth, contact, and Telegram webhook.

## Useful Docker Commands

Start services:

```bash
docker compose up -d
```

Restart only app:

```bash
docker compose up -d --force-recreate app
```

Restart nginx:

```bash
docker compose restart web
```

View logs:

```bash
docker compose logs -f app
docker compose logs -f web
docker compose logs -f db
```

Stop services:

```bash
docker compose down
```

## Troubleshooting

### Login fails even though users exist

Verify app DB connectivity:

```bash
docker compose exec app php artisan migrate:status
```

Check user count:

```bash
docker compose exec app php artisan tinker --execute="echo \App\Models\User::count();"
```

### `502 Bad Gateway` on `localhost:8000`

Restart `web` (nginx) after recreating `app`:

```bash
docker compose restart web
```

### `ERR_INVALID_HTTP_RESPONSE` on `localhost:3308`

This is expected in a browser. MySQL is not HTTP.
Use a SQL client (SQLyog, DBeaver, Workbench) for DB port connections.

