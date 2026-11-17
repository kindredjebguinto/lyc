# Lucky Yuna combined baselines

## Recommended for retaining our completed improvements

[Download Vehicle-Service-System-Optimized-Baseline.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/combined-baseline-2026-11-17/Vehicle-Service-System-Optimized-Baseline.zip)

One complete Vehicle-Service-System folder: Flutter app, public/customer website + API, and Admin website. Includes the completed performance optimizations, registration/profile fixes, payment-dialog layout fixes, and previously working AGP 9.1.1 / Gradle 9.5.0 versions. **No GCash v1/v2 integration.**

## Untouched original uploads, combined as a backup

[Download Vehicle-Service-System-Original-Combined.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/combined-baseline-2026-11-17/Vehicle-Service-System-Original-Combined.zip)

All original source contents retained byte-for-byte. Original Android versions remain in this backup.

## Install one baseline, without mixing old source files

1. Keep a backup of the existing Vehicle-Service-System directory and database.
2. Rename the old directory; extract ONE of the downloaded ZIPs into C:\\xampp\\htdocs\\.
3. Confirm C:\\xampp\\htdocs\\Vehicle-Service-System\\vsm_customer exists.
4. Preserve existing database records, local database credentials and uploaded proofs. Replacing source does not require deleting the database.
5. Run flutter pub get with your working SDK. See the included guide for website URLs and Android-version notes.

[Optimized setup guide](./SETUP-OPTIMIZED.md) · [Original backup guide](./SETUP-ORIGINAL.md) · [Fresh verification](./VERIFICATION.md)

Do not assume an earlier GCash installer will match either baseline without a new preflight.
