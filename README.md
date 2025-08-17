# Calendar Access Token Generator

A minimal Laravel 9 application that emulates the Google Calendar API "quickstart" flow.
Use it to obtain OAuth credentials (`client_secret_generated.json`) compatible with the [`spatie/laravel-google-calendar`](https://github.com/spatie/laravel-google-calendar) package.

---

## Purpose

Many Laravel projects rely on **offline access tokens** to interact with Google Calendar through Spatie’s package.
This repository walks you through that OAuth process in isolation, generating the token files you can later drop into your main application.

It grew out of our attempts at [Mytherapist.ng](https://mytherapist.ng) to hook up Google Calendar.
After wrestling with the setup, we open-sourced the code so others facing similar hurdles can reuse it.

---

## How It Works

1. **Calendar service wrapper** (`app/Calendar.php`) configures a `Google_Client` with the desired scopes (`calendar` and `calendar.events`) and reads `client_secret.json` from `storage/keys/`.
2. **Route `/connect`**: initiates the OAuth consent screen by redirecting the user to Google’s authorization URL.
3. **Route `/store`**: receives the `code` from Google, exchanges it for an access & refresh token, and writes the resulting JSON to `storage/keys/client_secret_generated.json`.
4. The generated token file is ready to be used by `spatie/laravel-google-calendar` in any Laravel application.

---

## Getting Started

### Prerequisites
- PHP ≥ 8.0.2
- Composer
- A Google Cloud project with Calendar API enabled

### Installation
```
git clone https://github.com/your-user/calendar-access-token.git
cd calendar-access-token
composer install
cp .env.example .env   # adjust APP_NAME, etc. as needed
```

### Set Up Google OAuth Credentials
1. In the Google Cloud Console, create an **OAuth client ID** (type: Web application).
2. Add `http://127.0.0.1:8006/store` to the authorized redirect URIs.
3. Download the JSON credentials and save them as  `storage/keys/client_secret.json` in this project.

### Run the Authorization Flow
```
php artisan serve --port=8006
```

1. Visit `http://127.0.0.1:8006/connect`.
2. Sign in with your Google account and grant calendar access.
3. Google redirects back to `/store`, which writes `storage/keys/client_secret_generated.json` (contains `access_token`, `refresh_token`, etc.).

---

## Using the Generated Credentials

In your main Laravel project that uses `spatie/laravel-google-calendar`:

1. Copy both `client_secret.json` and `client_secret_generated.json` to an accessible location (often `storage/` or `config/`).
2. Reference the path in your Spatie config (`config/google-calendar.php`):

```php
'credentials_json' => storage_path('keys/client_secret.json'),
'token_json'       => storage_path('keys/client_secret_generated.json'),
```

Now the package can authenticate without prompting again.

---

## Security Notes
- **Never commit** `client_secret.json` or the generated token file to version control.
- Store them securely and rotate them if compromised.

---

## Troubleshooting

- **Invalid redirect_uri**: Ensure the URI in Google Cloud matches the one used locally (`/store`).
- **Expired token**: rerun the flow (`/connect`) to refresh the credentials.

---

## Contributors

- [@Mane-Olawale](https://github.com/Mane-Olawale) – code
- [@theafolayan](https://github.com/theafolayan) – docs

---

## License

This project is open-sourced under the [MIT license](LICENSE).

