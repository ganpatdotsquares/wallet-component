# Wallet Component

A reusable wallet and payment component built on Laravel 11. It provides a REST API for user authentication, profile management, wallet balance, deposits, withdrawals, peer-to-peer transfers and transaction history, with Stripe Connect used for payments and payouts. A small admin panel and CMS pages are included.

Repository: https://github.com/ganpatdotsquares/wallet-component

## Features

- **Authentication:** register, login, OTP verification and resend, forgot/reset password, logout, account deletion (Laravel Sanctum tokens)
- **User profile:** view and update profile, change password
- **Wallet:** balance, deposit (initiate and confirm), withdraw request and cancel, send money, transaction history
- **Stripe Connect:** onboarding link for users to receive payouts
- **CMS pages:** fetched by slug
- **Admin panel:** under `routes/admin.php`

## Requirements

| Tool | Version |
|------|---------|
| PHP | 8.2 or higher |
| Composer | 2.x |
| MySQL / MariaDB | 5.7+ / 10.3+ (SQLite also works) |
| Node.js and npm | 18+ (only needed for Vite assets) |
| Stripe account | Test keys are enough for development |

Required PHP extensions: `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`.

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/ganpatdotsquares/wallet-component.git
cd wallet-component

# 2. Install PHP dependencies
composer install

# 3. Create the environment file and application key
cp .env.example .env
php artisan key:generate

# 4. Configure .env (see "Environment configuration" below), then run migrations
php artisan migrate

# 5. Expose uploaded files
php artisan storage:link

# 6. (Optional) Install and build front-end assets
npm install
npm run build

# 7. Start the app
php artisan serve
```

The API is then available at `http://127.0.0.1:8000/api/...`. On XAMPP you can instead point Apache at the `public/` directory.

## Environment configuration

Edit `.env` after copying it from `.env.example`.

### Application and database

```env
APP_NAME="Wallet Component"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=wallet_component
DB_USERNAME=root
DB_PASSWORD=
```

Create the database first, for example `CREATE DATABASE wallet_component;`.

### Stripe

These values are read in [config/services.php](config/services.php) and are not in `.env.example`, so add them yourself:

```env
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_CURRENCY=usd
STRIPE_CURRENCY_SIGN=$
STRIPE_WEBHOOK_SECRET=whsec_xxx
```

### Mail (used for OTP emails)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

With `MAIL_MAILER=log`, OTP emails are written to `storage/logs/laravel.log` instead of being sent. If mail delivery fails, the auth endpoints log the error and still return a normal response.

> Never commit `.env` or real Stripe keys.

## API overview

All routes are in [routes/api.php](routes/api.php) and are served under `/api`.

Every request passes through the `api.header` middleware, which converts camelCase request keys to snake_case. Routes marked "auth" also need an `Authorization` token returned by login (`api.authtoken` middleware).

| Area | Method and path | Auth |
|------|-----------------|------|
| Auth | `POST auth/register`, `auth/login`, `auth/delete` | No |
| Auth | `POST auth/otp-verification`, `auth/otp-verification-resend` | No |
| Auth | `POST auth/forgot-password`, `auth/forgot-password-otp`, `auth/reset-password` | No |
| User | `GET my-profile`, `GET logout`, `GET delete-account` | Yes |
| User | `POST update-profile`, `POST change-password` | Yes |
| CMS | `GET page/{slug}` | Yes |
| Stripe | `GET connect-stripe` | Yes |
| Wallet | `GET wallet/balance`, `GET wallet/transactions` | Yes |
| Wallet | `POST wallet/deposit/initiate`, `wallet/deposit/confirm` | Yes |
| Wallet | `POST wallet/withdraw-request`, `wallet/withdraw-request-cancel` | Yes |
| Wallet | `POST wallet/send` | Yes |

A ready-made Postman collection covering every endpoint is included: [WalletPayment.postman_collection.json](WalletPayment.postman_collection.json). Import it into Postman and set the base URL variable to your server.

## Using the component in another Laravel project

To add the wallet to an existing Laravel application, copy these parts across:

1. **Code:** `app/Http/Controllers/Api/V1`, `app/Http/Middleware` (`CheckHeader`, `CheckAuthorization`), `app/Models` (`Wallet`, `WalletTransaction`, `WithdrawRequest`, `User`), `app/Traits`, and `app/helpers.php` (autoloaded via `composer.json`).
2. **Routes:** the contents of `routes/api.php`.
3. **Middleware aliases:** register them in `bootstrap/app.php`:
   ```php
   $middleware->alias([
       'api.header'    => \App\Http\Middleware\CheckHeader::class,
       'api.authtoken' => \App\Http\Middleware\CheckAuthorization::class,
   ]);
   ```
4. **Database:** the wallet, wallet transaction, withdraw request and user-column migrations from `database/migrations`.
5. **Config:** the `stripe` block in `config/services.php` and the Stripe `.env` values above.
6. **Packages:** `composer require stripe/stripe-php laravel/sanctum`.

The project has been manually verified on Laravel 11, 12 and 13.

## Running tests

The test suite uses an in-memory SQLite database and a fake Stripe HTTP client, so no real Stripe or mail credentials are needed.

```bash
php artisan test
```

## Project structure

```
app/Http/Controllers/Api/V1   API controllers (Auth, User, Wallet, Stripe, Base)
app/Http/Middleware           CheckHeader, CheckAuthorization
app/Models                    User, Wallet, WalletTransaction, WithdrawRequest, ...
database/migrations           Schema, including wallet tables
routes/api.php                API routes
tests/Feature/Api             Feature tests for every endpoint
tests/Support                 Fake Stripe client used by the tests
```

## License

MIT
