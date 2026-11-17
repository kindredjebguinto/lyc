# Original project + completed optimizations (before GCash)

One complete Vehicle-Service-System folder for XAMPP, containing vsm_customer, vehicle-service-management, and vehicle-service-management-admin.

Included changes after the original uploads:
- Lazy Flutter dashboard tabs and fewer redundant notification/dashboard requests.
- Cached receipt request with manual refresh/retry.
- Aggregated dashboard SQL, lazy website images, and compressed hero image.
- Registration creates linked customer records; API login repairs missing customer profile only after password verification.
- Dedicated Flutter payment proof dialog with layout/image validation fixes.
- Previously working Flutter 3.50 Android build settings: AGP 9.1.1, Gradle 9.5.0.

No GCash checkout integration, webhook, gateway credentials or payment-attempt migration is included. Existing manual GCash proof flow remains. No uploads or live/test customer database exports are included.

## Put this in htdocs

1. Stop Apache. Rename the existing Vehicle-Service-System folder to a backup name. Keep the backup.
2. Extract this ZIP to C:\xampp\htdocs\ so that C:\xampp\htdocs\Vehicle-Service-System\vsm_customer exists. Do not copy over the old folder and mix versions.
3. Keep your existing database. Do not delete or reset it. Back it up before any SQL import. This package contains the original schema/service catalog SQL, not a copy of your current database. If it is already compatible, changing source alone requires no database reset.
4. If needed, restore local database credentials in the three database connection configs and your original uploads/payment_proofs folder from the backup.
5. Start Apache/MySQL; open http://localhost/Vehicle-Service-System/vehicle-service-management/ or http://localhost/Vehicle-Service-System/vehicle-service-management-admin/.
6. Open Windows PowerShell and run:

```powershell
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
```

Run the app using your previously working phone connection/API_BASE_URL settings. None of the source packaging actions deploys to luckyyuna.com.

Validation notes: Original optimization metrics (105 backend assertions, 14 Flutter tests) are historical results from that work, not newly executed full-system tests for this archive. Fresh package-specific checks are recorded separately in the GitHub release guide. Android/iOS native builds and real-device tests have not been repeated for this packaging task.
