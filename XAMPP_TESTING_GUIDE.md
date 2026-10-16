# Test Luck Yuna on XAMPP

The website ZIP contains both the public/customer website and the staff admin portal. The Flutter ZIP is the customer app source, not an APK. Both use the same PHP API and database.

## 1. Extract the websites

Use XAMPP with PHP 8.2 or newer, mysqli and MariaDB enabled. Extract `LYC-Websites-XAMPP.zip` into `C:\xampp\htdocs\` so the result is:

```
C:\xampp\htdocs\Vehicle-Service-System\
    vehicle-service-management\
    vehicle-service-management-admin\
    START_HERE.md
```

Keep the folder name `Vehicle-Service-System`; the app's base paths use it. Start Apache and MySQL in the XAMPP control panel. No npm or Composer installation is needed. Bootstrap and its icons are bundled locally, with their licenses, so the website's layout does not require a CDN connection.

## 2. Import the database

For BOTH a fresh installation and an existing installation, import this ONE file in phpMyAdmin:

`Vehicle-Service-System/vehicle-service-management/database/lyc_database.sql`

It creates `vehicle_service_system` if needed and applies all required schema updates and service catalogs. No other SQL imports are needed. A fresh installation contains no demo accounts, tokens, vehicles, bookings or payments.

For an existing installation, export a database backup and back up your website folders first. The same SQL preserves existing records, passwords and catalog prices and can be imported again. Do not delete the database. Its target database is `vehicle_service_system`; if you use another name, change BOTH the CREATE DATABASE and USE lines before importing, plus your connection settings below.

Ownership is restored through an unambiguous customer account email and existing customer links. Unmatched records remain unlinked for staff review. The earliest legacy staff account without a staff level becomes Manager; review staff access after upgrading.

Replace both website folders, preserving your local database credentials and uploaded payment proofs. Sign out and sign in again, then hard-refresh. Signed-in customers open their dashboard from the website root; visitors see the public homepage.

The default local database connection is `localhost`, database `vehicle_service_system`, user `root`, empty password. If your local XAMPP database uses different credentials, update all three connection files:

- `vehicle-service-management/includes/config.php`
- `vehicle-service-management/api/config/database.php`
- `vehicle-service-management-admin/includes/config.php`

These are local-development defaults; do not use an empty database password on a public server.

## 3. Create your manager and team

On the XAMPP computer, open this address in your browser:

`http://localhost/Vehicle-Service-System/vehicle-service-management-admin/setup.php`

Create your own manager email and password (at least 12 characters). Setup is local-only and closes after the first manager exists. There is no default manager password. For an existing database with a Manager account, sign in with that account instead.

Then:

1. In **Team**, add the shop's mechanics and mark them Available. A fresh database deliberately has no mechanics; customer slots remain unavailable until you add active mechanics.
2. In **Staff access**, create adviser, cashier, mechanic or read-only staff accounts.
3. Link each Mechanic account to its corresponding Team record. One mechanic record can be linked to one staff account. Unlinked mechanic accounts cannot access jobs.
4. Review the supplied service catalog and prices before taking real appointments.

## 4. Open the websites

Public/customer website:

`http://localhost/Vehicle-Service-System/vehicle-service-management/`

Staff sign-in:

`http://localhost/Vehicle-Service-System/vehicle-service-management-admin/login.php`

If Apache uses port 8080, add `:8080` after localhost in these addresses and in Flutter's API override.

## 5. Test a complete job

1. Register a separate customer account and add a vehicle. Book a service in the Flutter app or signed-in customer website.
2. Select a slot. Today is selectable in Manila time. Each slot shows the number of mechanics free for the complete selected service duration. Multiple appointments can overlap while capacity remains; past starts, closed days and services ending after 5 PM are unavailable. New jobs use the sum of service estimates (minimum 30 minutes per service), without an automatic double-time allowance. Existing jobs keep their previously reserved duration.
3. Upload a test reservation-fee proof. As Cashier or Manager, open **Payments → Proofs to verify**, compare the amount/reference with the actual transfer, and approve or reject it. An image alone does not prove a real payment.
4. As Adviser or Manager, confirm the appointment. Assign a free mechanic from the job workspace.
5. On the appointment date, check in the vehicle. A mechanic can start inspection only after check-in and the reserved start time.
6. As Mechanic, open the assigned job and save inspection notes. As Adviser, prepare and send the quotation.
7. The customer accepts in the app. For an in-person/phone response, the adviser may record Accepted or Rejected with a customer-decision reference. Accepted/rejected quotations are read-only afterward so the agreed price stays consistent with billing.
8. The final balance uses only verified, unrefunded reservation-fee credit. A fully covered bill does not ask for another transfer. Unverified/refunded fees are not automatically credited.
9. The mechanic starts work, saves work performed and marks the vehicle ready. Cashier verifies the remaining payment. Adviser/Manager releases and completes the job once outstanding payments are resolved.
10. Check the receipt, customer service history and staff activity log.

Guest website requests remain supported. Guest bookings do not have an app login; the adviser records customer quotation decisions with a consent reference and the cashier handles their payment in the job workspace. A legacy guest payment estimate is aligned with the accepted quotation only while it is unsubmitted/unverified.

## Walk-in customers

Managers and Service Advisers can open **Walk-in** in the staff portal (also available from Today and Schedule). Select an existing customer's vehicle, or enter a new guest's contact and vehicle details; an email/app account is optional. Select services, press **Check / refresh today's slots**, then choose **Start now** during open hours or another remaining slot today.

Registering a walk-in records arrival and reserves shared mechanic capacity immediately. It does not create or require an advance reservation-fee payment. Assign the mechanic from the job workspace; inspection, customer quotation consent, final payment and release still follow the normal workflow. Existing app customers retain ownership when staff select their existing vehicle. Guests do not have to download the app. For a quotation on a booking without an app account, use **Print / Save PDF**, choose **Presented to customer**, and record Accepted or Rejected with the customer decision reference. This saves the quotation without creating an app notification for a nonexistent account. Walk-ins linked to an app account still receive in-app quotation notifications.

Import the updated single `lyc_database.sql` before using this feature; it adds walk-in tracking without deleting existing data. Rebuild and reinstall the updated Flutter source to see the Today shortcut and new capacity cards. Replacing PHP files alone cannot update an already installed APK.

## When a job takes longer than estimated

Import the updated single `vehicle-service-management/database/lyc_database.sql` before replacing the running site. It adds the schedule-change history and original-estimate/start fields while preserving existing data.

In the job workspace, open **Expected finish & schedule → Extend job**. Managers, Service Advisers and the assigned Mechanic can extend a checked-in job already under inspection or service. Choose +15 to +240 minutes and enter a reason. The extension starts from the later of the existing expected finish or now, so an overdue job gets a future expected finish. The original estimate and every change remain in history.

App, website and walk-in booking availability use the extended window immediately. Existing customers' appointment times remain unchanged. Capacity for a displaced booking is retained before offering new bookings. Conflicts appear on Today, Schedule and affected job workspaces, including unassigned bookings whose capacity is exhausted.

A Manager or Service Adviser can open the affected booking and either:

- **Reassign mechanic** while the job is still Approved. Only a mechanic free for the full service window can be used; enter a reason. The appointment time stays the same.
- **Record an agreed new start** for a Pending/Approved booking after contacting the customer. They may agree to wait at the shop or return at another time. Record the agreed date/time, reason and customer-agreement reference (who, when and how). Capacity, opening hours and the chosen mechanic are checked again. The original appointment is retained in history and shown alongside the agreed start in customer bookings.

Recorded extensions and agreed time changes create in-app notifications for linked customers. The updated Flutter source also shows expected finish and original booking details; rebuild/reinstall it to see these labels. Existing installed apps still use the updated backend capacity and notifications. Staff must phone or speak to guests themselves; no SMS, email or phone-call provider is included. Marking work **Ready for Pickup** releases mechanic capacity; payment verification and vehicle release remain separate steps. No quotation price or payment is changed by a schedule update.

## 6. Run the Flutter source

Extract `LYC-Flutter-App-Source.zip` separately and open `vsm_customer` in Android Studio or VS Code. Install a Flutter SDK satisfying the existing `pubspec.yaml` requirement: Dart >=3.13.4 and <4.0.0. Check `flutter --version` first. Existing locked package versions and Android toolchain pins are preserved. Missing lockfile entries for already-declared dependencies have been completed.

From the `vsm_customer` folder:

```sh
flutter doctor
flutter pub get --enforce-lockfile
flutter run --dart-define=API_BASE_URL=http://10.0.2.2/Vehicle-Service-System/vehicle-service-management/api
```

`10.0.2.2` reaches the XAMPP computer from an Android Studio emulator. For a physical Android phone, connect it and the PC to the same Wi-Fi, allow Apache through the PC firewall for your local network, and use the PC's LAN address:

```sh
flutter run --dart-define=API_BASE_URL=http://YOUR_PC_LAN_IP/Vehicle-Service-System/vehicle-service-management/api
```

Do not use localhost in the phone's API address: that points to the phone itself. If Apache uses a different port, include it after the IP. Machine-specific `android/local.properties` and build caches are omitted; Flutter regenerates local configuration on your PC. Firebase push notifications remain optional and require your own Firebase project.

The Flutter source is included with the earlier slot-label improvements. No APK was built. The payment-proof dialog passed eight focused Flutter widget tests on Flutter 3.47.7 / Dart 3.13.5, and static analysis of its changed files passed. Android build/device testing and the native gallery-to-server upload still need verification on your machine. Native Android build compatibility still needs verification on your machine; dependency resolution may need attention if your installed SDK is older than the package requires.

## Payment proofs and local upload limits

Keep `uploads/payment_proofs/.htaccess` in place. Apache denies direct access to uploaded proofs; authorized Managers/Cashiers view them through the staff proof endpoint. AllowOverride must permit the supplied access rule (normal XAMPP htdocs configuration supports .htaccess).

The proof form uses the actual PHP upload limit, capped at 5 MB. For 5 MB uploads, use `upload_max_filesize=5M` and `post_max_size=8M` in the XAMPP PHP configuration, then restart Apache. Make sure Apache can write to `vehicle-service-management/uploads/payment_proofs`.

## What was verified

PHP syntax, JavaScript syntax, public/customer layout and booking checks, staff role/access checks, scoped mechanic workflows, CSRF protection, check-in/work/payment/release workflow, quotation consent, verified-fee credit calculations, payment review/auditing and responsive admin pages passed locally. A separate fresh Apache/PHP/MariaDB installation also passed manager initialization and proof-access tests. This is evidence from Linux Apache/PHP 8.4/MariaDB 11.4, not a claim that Windows XAMPP or a Flutter device build was executed here.

No real payment-provider integration, merchant QR/account details or Firebase credentials are included. Use synthetic transactions for local testing. Additional work after an accepted quotation and excess-fee refunds need separate, explicit billing review; accepted quotations cannot be silently repriced.

## Payment image dialog fix

The payment dialog now has a finite, scrollable content width, so the image preview and full-width buttons do not trigger Flutter's infinite-width layout assertion. To patch an existing Flutter project, replace BOTH `lib/screens/payments/booking_payment_screen.dart` and `lib/screens/payments/payment_proof_dialog.dart` from the updated source. Run `flutter pub get`, stop the running app, then run it again. No database import or PHP update is required for this dialog fix.

The included regression test runs with `flutter test test/payment_proof_dialog_test.dart`. It covers image preview/submission at 320/390/768px, keyboard/large text, required fields, cancelling an open picker, oversize rejection and cash payments.

## Guest walk-in quotation save fix

To patch an existing installation where presenting a guest quotation fails, copy `vehicle-service-management-admin/job_quotation.php` and `vehicle-service-management-admin/includes/workspace.php` from this release. No SQL import is required for this specific fix. Guest quotations can be presented in person or as printed/PDF copies; no email or SMS provider is configured.
