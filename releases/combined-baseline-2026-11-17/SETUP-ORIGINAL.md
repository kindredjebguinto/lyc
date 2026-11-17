# Original combined baseline — Lucky Yuna

This archive combines the owner's original vsm_customer.zip and LYC-Websites-XAMPP (1).zip. All 321 original file contents are retained byte-for-byte. Only this guide and ORIGINAL-SOURCE-MANIFEST.json are added. No GCash v1/v2 patch, optimization, local PC diagnostic files, compiled APK, or database export is added.

## Folder layout

C:\xampp\htdocs\Vehicle-Service-System\
    vsm_customer\
    vehicle-service-management\
    vehicle-service-management-admin\

## Replace the old source without mixing versions

1. Stop Apache while changing the source directories. Rename your current Vehicle-Service-System folder to Vehicle-Service-System-backup-<date>. Keep it; it may contain credentials, uploaded proofs, Android build settings, and other local changes.
2. Extract this archive to C:\xampp\htdocs\. The result must have ONE Vehicle-Service-System folder, not Vehicle-Service-System\Vehicle-Service-System.
3. Keep your existing database. This source replacement does not reset or replace it. Export a database backup before considering any SQL import. The supplied original SQL is vehicle-service-management/database/lyc_database.sql; a source restore alone does not prove a database migration is required.
4. If your XAMPP credentials differ from local root/blank-password defaults, restore only your local database connection settings in the three original config files. If retaining existing booking/payment records, copy their uploads/payment_proofs directory from the backup too.
5. Start Apache/MySQL. Open the Customer website at http://localhost/Vehicle-Service-System/vehicle-service-management/ and Admin at http://localhost/Vehicle-Service-System/vehicle-service-management-admin/.
6. Run flutter pub get in vsm_customer with your working Flutter SDK.

## Existing Flutter 3.50 build fix

Original Android files intentionally retain AGP 9.1.0 and Gradle 9.3.1. Earlier, your PC needed AGP 9.1.1 and Gradle 9.5.0 with Flutter 3.50. If using that same SDK, reapply those two version changes after restoring the source (or copy ONLY your known-working corresponding Gradle settings from the backup). Do not replace new source with the entire old app folder.

- android/settings.gradle.kts: com.android.application version 9.1.0 -> 9.1.1
- android/gradle/wrapper/gradle-wrapper.properties: gradle-9.3.1-all.zip -> gradle-9.5.0-all.zip

No native build or end-to-end test was rerun for this packaging task. Verification here checks archive integrity, required paths, and byte-for-byte parity with both uploaded original ZIPs. A fresh database has no demo accounts or user records; services catalog seed data is supplied by the original SQL.

Use this baseline if you want to restart integration from the original supplied project. Do not assume either previously generated GCash installer will match it without a new preflight.
