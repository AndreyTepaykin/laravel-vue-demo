# Admin Starter

Laravel + Vue SPA admin starter based on [Argon Dashboard](https://www.creative-tim.com/product/vue-argon-dashboard), with Sanctum authentication, role-based access, and user management.

## Stack

- **Backend:** Laravel 13, PHP 8.3+, Laravel Sanctum, Spatie Laravel Permission
- **Frontend:** Vue 3, Vue Router, Vuex, Vite, Bootstrap 5 (Argon Dashboard)
- **Auth:** Cookie-based SPA auth (login, register, logout, forgot/reset password)

## Features

- SPA shell served by Laravel (`/{any?}` → Vue)
- Auth API with rate limiting
- Profile update (`GET`/`PUT /api/user`)
- User CRUD (`/api/users`) for admin workflows
- Seeded `admin` and `user` roles

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and npm
- SQLite (default) or MySQL/PostgreSQL

## Setup

```bash
composer setup
```

Or step by step:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
```

## Development

```bash
composer run dev
```

This starts the Laravel server, queue listener, logs, and Vite. Or run separately:

```bash
php artisan serve
npm run dev
```

App URL (local): [http://127.0.0.1:8080](http://127.0.0.1:8080)

## Demo accounts

After seeding:

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | `admin@jsonapi.com` | `secret` |
| User  | `test@example.com`  | `password` |

## Useful routes

| Area | Path |
|------|------|
| Login | `/login` |
| Register | `/register` |
| Dashboard | `/dashboard-default` |
| User profile | `/examples/user-profile` |
| User management | `/examples/user-management/list-users` |

## Testing

```bash
composer test
# or
php artisan test
```

## License

MIT
