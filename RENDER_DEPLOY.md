# Render Deployment Guide

## 1. Push code
- Push this project (including `render.yaml`) to your GitHub repository.

## 2. Create from Blueprint
- In Render dashboard, choose `New` → `Blueprint`.
- Connect your repository.
- Render will detect `render.yaml` and create:
  - web service: `task-management-app`
  - PostgreSQL: `task-management-db`
- Web service uses Docker build from `Dockerfile` (stable for Laravel on Render).

## 3. Set required env vars
After first sync, open your web service env vars and set:
- `APP_URL` = your Render URL (for example `https://task-management-app.onrender.com`)
- `APP_KEY` = result from:

```bash
php artisan key:generate --show
```

## 4. First deploy commands (one-time)
Open web service `Shell` and run:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
```

## 5. Login
- Open your web service URL.
- Use existing admin account:
  - email: `admin@example.com`
  - password: `password`

## Notes
- Do not run `db:seed --force` on every deploy unless you intentionally want to refresh sample seeded data.
- If you see `500` with key error, check `APP_KEY` is set.
- If you see asset/manifest errors, trigger a redeploy (build runs `npm run build`).
- If Docker build fails during `composer install`, use latest commit (Dockerfile includes required PHP extensions for Laravel).
- Current Docker build uses multi-stage:
  - Composer stage for `vendor`
  - Node 20 stage for Vite assets
  - Final Apache+PHP runtime stage
