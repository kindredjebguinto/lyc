# Lucky Yuna — Google Sign-In and email recovery setup

This guide is for Windows PowerShell 5.1, your existing XAMPP project and Android phone. The auth source is implemented, but real Google login/email delivery require your own configuration. No Google password, OAuth client secret, SMTP password or keystore belongs in chat, Flutter code or a public repository.

## 1. Back up and inspect the active database
Extract the ZIP outside htdocs, then open PowerShell in its folder. Keep Apache/MySQL running.

```powershell
& "C:\xampp\php\php.exe" ".\setup\Check-Auth-Readiness.php" "C:\xampp\htdocs\Vehicle-Service-System"
```

The checker parses config literals without executing the config PHP. Confirm the three database names match. Earlier they all used `vehicle_service_system_sandbox`; use the actual checker output. If it reports duplicate owned profile groups, STOP and review them without deleting/merging any records. Do not add the unique index by deleting customers.

In phpMyAdmin, select that active database and Export -> Quick -> SQL. Keep the full backup privately. PHP 8.1+, mysqli, mbstring, OpenSSL, JSON and tokenizer are required. The included Composer dependencies are resolved for PHP 8.1 compatibility, covering typical PHP 8.2 XAMPP and the reported PHP 8.3 hosting.

## 2. Create private authentication configuration
Copy `setup/auth.example.php` to `C:\xampp\lyc-private\auth.php` if that file does not already exist. Create the directory if necessary. Keep existing `manual_wallets.php` and QR files unchanged. Edit an existing auth.php carefully instead of overwriting it.

For local USB/XAMPP testing set:

```php
'allow_local_http' => true,
```

Keep Google ID arrays empty and SMTP fields blank initially. Manual sign-in remains available; Google/email recovery will report setup required. On hosting this file must remain outside public_html, and `allow_local_http` must be false. The loader also supports a server-only `LYC_AUTH_CONFIG` path.

## 3. Import the additive migration
In phpMyAdmin select the confirmed active database. Import `migration/google_auth_recovery.sql` from this ZIP. It has no USE/CREATE DATABASE/DROP, no account seed, and no existing-account updates. DDL commits automatically. It adds four auth tables, two token metadata columns and an owned-profile uniqueness index. A duplicate index failure can leave a partial migration; stop on errors and review the message. Never import the entire old application SQL for this update.

Run Check-Auth-Readiness.php again: expect AUTH SCHEMA READY for all three configs, and zero duplicate groups. The upgraded auth endpoints fail closed if the migration/index is incomplete.

## 4. Install source and test manual sign-in first
The installer validates hashes and backs up changed files before copying; it never executes SQL. It excludes database credentials, existing website pages and all payment/vehicle/booking code.

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Auth-Upgrade.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Auth-Upgrade.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

Stop the previous Flutter run with Ctrl+C before rerunning; a hot reload does not change compile-time API/client IDs. New passwords require 12–72 UTF-8 bytes; existing valid shorter passwords still work for login. New signup/profile completion requires a valid PH mobile number. The number is contact information, not SMS-verified. Existing non-unique-phone policy is retained; phone alone never links accounts.

Verify existing login, profile/customer ID, vehicles, bookings and payments first. Missing owned customer profiles are not automatically created/adopted during login; contact the service center to repair such a legacy record. Incomplete existing profiles go to Complete Your Profile. Anonymous signup returns a generic response with user/customer null to prevent email enumeration; current Flutter uses success/message. External API clients that rely on registration IDs must adapt to the normalized response. There is no default password for Google-only accounts.

## 5. Create the Google OAuth clients
1. Open Google Cloud Console / Google Auth Platform and create or select your project.
2. Set Branding/app name to Lucky Yuna and your support/developer contact details. Use your own verified domain where required by Google's setup.
3. For testing, choose the appropriate external audience and add the Google accounts you will actually test with as test users. Basic profile/email/OpenID sign-in is sufficient; do not add Drive/Gmail permissions.
4. Create a Web application OAuth client. Copy its PUBLIC client ID ending in `.apps.googleusercontent.com`. This is the serverClientId/audience; no client secret is used in this implementation.
5. Run the included script from the extracted ZIP folder:

```powershell
.\setup\Show-Android-Fingerprints.ps1
```

6. Create an Android OAuth client with package `com.vsm.vsm_customer` and the debug SHA-1 printed by the script. Keep SHA-256 available for signing/domain configuration as applicable. Production requires the release/Play app-signing certificate registered too; a debug fingerprint does not cover a release build.
7. Put the Web client ID in private auth.php:

```php
'google_web_client_ids' => ['YOUR_WEB_CLIENT_ID.apps.googleusercontent.com'],
'google_presenter_client_ids' => ['YOUR_ANDROID_CLIENT_ID.apps.googleusercontent.com'],
```

The presenter allowlist is used if the token contains azp. Include the registered iOS client ID too when enabling iOS. Token audiences remain restricted to the Web IDs; the Android client ID is not a replacement for the server audience.

8. Rebuild on the phone using the SAME Web client ID:

```powershell
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api --dart-define=GOOGLE_WEB_CLIENT_ID=YOUR_WEB_CLIENT_ID.apps.googleusercontent.com
```

This native flow uses Google's account picker and a server ID token, without a website OAuth callback route. Firebase Authentication is not required. The PHP server needs outbound HTTPS to Google's public signing-key endpoint. No Google passwords are requested or stored.

## 6. Test Google linking and profile completion
- New Google email: Continue with Google -> Complete Your Profile -> editable name + PH number -> same normal dashboard. Do not repeatedly create accounts after network errors: restart Google sign-in and the subject mapping resolves the existing record.
- Existing manual account with the SAME Google email: Continue with Google -> confirm its Lucky Yuna password -> link to the existing user/customer. No automatic email-only linking. If password is forgotten, complete recovery then restart Google sign-in.
- Returning linked identity: same records and no repeated profile form when required information exists.
- Google-only user: Account/Profile -> Sign-in & Password -> verify Google -> Set Password. It saves a local Lucky Yuna password, revokes previous API sessions and asks you to sign in again. Manual login then uses the same customer ID.
- Existing local password: Change Password requires its current password. Set Password cannot overwrite it. All password changes invalidate API tokens and outstanding reset/link challenges.

For different Google/manual emails, select Already a customer? Link your existing account before creating a profile, then prove ownership with the existing Lucky Yuna email/password. This adds an authentication method to the same customer; manual sign-in keeps using that existing Lucky Yuna email. Phone alone never links accounts. Two independently created customers are not merged automatically; request reviewed assistance rather than deleting either account.

## 7. Configure SMTP and a real HTTPS reset endpoint
Choose an email sender/mailbox you control, using your provider's SMTP settings. Your Hostinger website plan does not by itself confirm a mailbox or SMTP entitlement; any charge depends on your email provider/plan. Do not use website/FTP credentials as SMTP credentials.

Set private auth.php's smtp host, username, password, from_email, port and encryption. Use provider-documented port 587/TLS or 465/SSL. Keep from_email authorized for that mailbox and set SPF/DKIM as instructed by the provider. SSL certificate verification stays enabled.

Set reset_url to the exact deployed HTTPS endpoint ending in `/api/auth/reset_password.php`. The correct URL prefix depends on your deployed folder layout; verify it rather than assuming a path.

IMPORTANT: the reset endpoint must use the SAME account database as the app API. A reset link sent to the live domain will not reset your local sandbox password if those are different databases. For full email testing, use a backed-up HTTPS staging backend/test database consistently. A local-only localhost URL on someone else's phone is not your PC. Do not upload private configs or actual customer backups to a public test service.

Email links carry the reset token in a fragment (`#token=...`), so it is not part of the ordinary HTTP request URL. The reset page removes the fragment and submits the token/new passwords in POST. The hashed token is valid 30 minutes, single-use, and invalidated on password changes. GET/email scanners do not consume it. Google-only accounts use Google, then Set Password, rather than resetting their Google password.

The outward response is generic for missing/manual/Google-only accounts. If SMTP delivery fails, the undelivered token is invalidated and the server records a generic delivery failure without credentials/tokens. The operator should monitor mail delivery; a generic response is not proof an email arrived. SMTP is synchronous in this version; response timing is not claimed to be identical for every account.

## 8. Production and iOS
Use an HTTPS API_BASE_URL. Release builds reject cleartext traffic, and Android token backups are disabled. Debug builds allow the local HTTP/ADB workflow. Backend local HTTP allowance applies only to loopback requests, never an arbitrary forwarded-IP header.

Android release signing requires your private android/key.properties and keystore; release tasks no longer silently use the debug signing key. Keep those files private and ignored by version control. Use the release/Play signing fingerprints in Google Cloud.

For iOS, create the iOS OAuth client with bundle ID `com.vsm.vsmCustomer`, set its reversed-client scheme in `ios/Flutter/GoogleAuth.xcconfig`, and pass GOOGLE_IOS_CLIENT_ID plus the Web client ID when building. The placeholder scheme is deliberately unconfigured. Build/test iOS on macOS before shipping; no iOS native build was performed here.

Existing Admin/Customer Website pages are not edited. They share users/passwords, so a Google-only user can use existing website manual login after setting a Lucky Yuna password. API password changes revoke API tokens, but existing PHP website sessions are NOT guaranteed revoked by this patch. Website email-change/session hardening needs a separate approved website change. This is not a complete website penetration test or production security certification.
