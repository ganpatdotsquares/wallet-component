# Admin Wallet & Payment Manager

The Wallet & Payment system was developed to enable seamless money management between users and the platform. It allows users to manage funds, make withdrawal requests, and perform transactions, while enabling the admin to monitor activities and manage requests. This module improves financial transparency and streamlines payment operations for both users and administrators.

---

## Features

Admin Side:
- Withdraw request list/show/status
- Withdraw request status update
- Wallet transactions history list/show

API Side:
- Wallet deposit via Stripe (PaymentIntents)
- Wallet Withdraw Request (bank or Stripe)
- Wallet Balance check
- Send money between users' wallets
- User Connect Stripe Account (Express onboarding)
- My Wallet Transactions
- Withdraw request cancellation by user (if status = pending)

---

## Requirements

- PHP >= 8.1
- Laravel Framework >= 12.x
- `laravel/sanctum` installed and configured in the host app (ships by default with a fresh Laravel 12 project)

---

## Installation

### 1. Add this repository to `composer.json`

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/ganpatdotsquares/wallet-component.git"
    }
]
```

### 2. Require the package via Composer

```bash
composer require admin/wallets:@dev
```

`stripe/stripe-php` is declared as a dependency of this package and installs automatically with it.

### 3. Run migrations

```bash
php artisan migrate
```

The package registers its own migrations automatically (`wallets`, `wallet_transactions`, `withdraw_requests`, and a `users` table alter adding `stripe_customer_id`, `stripe_account_id`, `stripe_payouts_enabled`). No `--path` flag is needed.

### 4. Update your `User` model

```php
use admin\wallets\Models\Wallet;
use admin\wallets\Models\WalletTransaction;

protected $fillable = [
    // ...your existing fields
    'stripe_customer_id',
    'stripe_account_id',
    'stripe_payouts_enabled',
];

public function wallet()
{
    return $this->hasOne(Wallet::class);
}

public function transactions()
{
    return $this->hasMany(WalletTransaction::class);
}
```

### 5. Configure `.env`

```
STRIPE_KEY=pk_test_xxx
STRIPE_SECRET=sk_test_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx
STRIPE_CURRENCY=usd
STRIPE_CURRENCY_SIGN=$
```

### 6. Update `config/services.php`

```php
'stripe' => [
    'publicKey'           => env('STRIPE_KEY'),
    'secretKey'           => env('STRIPE_SECRET'),
    'stripeCurrency'      => env('STRIPE_CURRENCY', 'usd'),
    'stripeWebhookSecret' => env('STRIPE_WEBHOOK_SECRET'),
    'stripeCurrencySign'  => env('STRIPE_CURRENCY_SIGN', '$'),
],
```

### 7. Set up Sanctum API authentication

All wallet API routes are guarded by `auth:sanctum`. Make sure your `User` model uses `Laravel\Sanctum\HasApiTokens` and that you have a way to issue tokens (e.g. a login endpoint that calls `$user->createToken('api')->plainTextToken`).

### 8. (Optional) Publish for customization

```bash
php artisan vendor:publish --tag=wallet --force
```

Copies config, migrations, and views into your app so you can override them.

### 9. Connect a Stripe Account for withdrawals

Before a user can submit a withdrawal request, they must connect a Stripe Express account via `GET /api/v1/wallets/connect-stripe`, then complete the returned onboarding URL. Requires Stripe Connect to be available on your Stripe account.

---

## Admin UI requirement

The admin routes (withdraw list/show, transaction list/show) only register when your application has an `admins` table with a `website_slug` column, and a registered `admin.auth` middleware alias — this package was built to sit behind an existing custom admin panel. Without those, `registerAdminRoutes()` silently no-ops and only the API routes are available. The Stripe onboarding redirect route is registered independently and does not require this.

---

## API Routes

| Method | Endpoint                                   | Description                          |
|--------|---------------------------------------------|---------------------------------------|
| GET    | `/api/v1/wallets/balance`                  | Check wallet balance                  |
| POST   | `/api/v1/wallets/deposit/initiate`         | Create a Stripe PaymentIntent         |
| POST   | `/api/v1/wallets/deposit/confirm`          | Confirm a succeeded deposit           |
| POST   | `/api/v1/wallets/withdraw-request`         | Request withdrawal (bank or stripe)   |
| POST   | `/api/v1/wallets/withdraw-request-cancel`  | Cancel pending withdrawal             |
| POST   | `/api/v1/wallets/send`                     | Send money to another user            |
| GET    | `/api/v1/wallets/transactions`             | List wallet transactions              |
| GET    | `/api/v1/wallets/connect-stripe`           | Get/create Stripe Connect onboarding link |

All routes require a valid Sanctum bearer token.

---

## Postman Collection

A ready-to-import Postman collection is included: [`Wallet-Component.postman_collection.json`](./Wallet-Component.postman_collection.json).

It includes:
- Auth requests (`Register`, `Login`) that auto-capture the Sanctum token into a collection variable
- All wallet API requests, pre-wired to use that token
- A helper request to confirm a test Stripe PaymentIntent directly against Stripe's API (using the `pm_card_visa` test token), so the deposit flow can be fully tested without a client-side checkout UI

Set the `base_url` and `stripe_secret_key` collection variables to match your environment before running requests.

---

## Protecting Admin/API Routes

Routes are already wrapped with the following middleware by the service provider:

```php
Route::middleware(['web','admin.auth'])->group(function () {
    // admin routes
});

Route::middleware(['api','auth:sanctum'])->group(function () {
    // api routes
});
```

---

## Admin: Status Change Script Example

If building a custom admin UI against the withdraw/transaction status-change endpoints, here's an example using SweetAlert2 + Toastr:

```js
function openModelToChangeStatus(element, currentStatus) {
    var url = $(element).data('url');
    var id = $(element).data('id');

    Swal.fire({
        title: "Change Withdraw Status",
        input: "select",
        inputOptions: {
            approved: "Approve",
            declined: "Decline",
            pending: "Pending"
        },
        inputValue: currentStatus,
        inputPlaceholder: "Select status",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        preConfirm: (selectedStatus) => {
            if (!selectedStatus) {
                Swal.showValidationMessage("Please select a status.");
                return false;
            }
            return selectedStatus;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            changeStatusAjax(id, result.value, url);
        }
    });
}

function changeStatusAjax(id, selectedStatus, url) {
    $.ajax({
        url: url,
        method: 'POST',
        data: {
            status: selectedStatus,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            toastr.success(response.message || 'Status updated successfully');
            setTimeout(() => location.reload(), 1000);
        },
        error: function (xhr) {
            let message = 'Something went wrong.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            toastr.error(message);
        }
    });
}
```

---

## License

This package is open-sourced software licensed under the MIT license.
