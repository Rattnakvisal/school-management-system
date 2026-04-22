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

## Docker Setup

The default `docker-compose.yml` now uses your local MySQL instance:

- `web` is exposed on `http://localhost:8000`
- `app` connects to `host.docker.internal:3308`
- `assets` builds Vite files during startup, so a fresh clone can boot without a manual local `npm run build`
- the bundled `db` service is available only when you explicitly enable the `docker-db` profile

If your local MySQL credentials are different, update the `DOCKER_DB_*` values in `.env`. The defaults are:

```bash
DOCKER_DB_HOST=host.docker.internal
DOCKER_DB_PORT=3308
DOCKER_DB_DATABASE=school_management_system
DOCKER_DB_USERNAME=root
DOCKER_DB_PASSWORD=your-password
```

## Quick Start

1. Copy env file if needed:

```bash
cp .env.example .env
```

2. Start Docker services:

```bash
docker compose up -d --build
```

3. Generate app key (only first setup):

```bash
docker compose exec app php artisan key:generate
```

4. Run migrations:

```bash
docker compose exec app php artisan migrate
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

Rebuild containers and assets:

```bash
docker compose up -d --build
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
docker compose logs -f assets
```

Use the optional Docker MySQL service:

```bash
docker compose --profile docker-db up -d db
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

### Frontend assets are missing or stale

Re-run the asset builder:

```bash
docker compose run --rm assets
```

### Need the bundled Docker MySQL container

Start it only when needed:

```bash
docker compose --profile docker-db up -d db
```
