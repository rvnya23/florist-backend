# Sudut Flora Backend

Laravel 13 REST API for the Sudut Flora Vue application.

## Setup

```bash
cp .env.example .env
composer setup
```

The existing Sudut Flora database is reused through the database values in `.env`. Product images are stored on the `public` disk and exposed by `php artisan storage:link`.

For local SPA authentication, the default origins are `http://localhost:5173` and `http://127.0.0.1:5173`.

## Run

```bash
php artisan serve
```

The API is available at `http://127.0.0.1:8000/api/v1`.

## Format check

```bash
./vendor/bin/pint --test
```
