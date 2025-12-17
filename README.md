# effective-todo

This is a REST API service for managing To-Do tasks.

## Tech stack

- PHP 8.5
- Laravel 12.0
- MySQL
- Nginx

## Setup

### Cloning

```bash
git clone https://github.com/ryadovoyy/effective-todo.git
cd effective-todo
```

### Environment variables

Copy the env example file:

```bash
cp .env.example .env
```

Change `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD` variables if you want.

## Run

```bash
docker compose up -d
```

Install dependencies and generate the app key:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
```

Then rebuild and update the config cache:

```bash
docker compose up -d --build
docker compose exec app php artisan config:cache
```

Also don't forget to run migrations:

```bash
docker compose exec app php artisan migrate
```

## Test

Now you are ready to see and test all endpoints. Import `postman-collection.json` in Postman and run the collection. You can also access `phpMyAdmin` on `localhost:8080`.
