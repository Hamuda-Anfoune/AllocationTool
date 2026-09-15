# TA Allocation System

An API-only Laravel application that allocates teaching assistants (TAs) to modules based on submitted preferences (module priority, programming language, prior experience), run per academic year for a university.

## Stack

- Laravel 13, PHP 8.3+
- Laravel Sanctum (bearer-token auth, no cookie/session)
- All endpoints live under `routes/api.php`, versioned at `/api/v1/`

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Running tests

```bash
php artisan test
```
