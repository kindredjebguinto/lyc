# GCash + Maya strict receipt guides / no Bank Transfer — 2026-11-18

GCash now has the same no-bypass customer behavior as Maya: the receipt must match its completed-transfer text format, amount and full configured recipient. GCash requires its wallet label, successful-transfer status, recipient label and reference label. Wrong, missing, unreadable, pending/failed/refunded or unsupported receipts cannot submit. A typed reference cannot override format/amount/number screening. Maya retains its three approved layouts. Every accepted upload still awaits staff verification of actual funds before Paid.

Both wallet selections have a reference guide with exactly one accepted illustration and one rejected illustration visible at a time. Each uses its own accepted picture and the shared generic rejected picture. All pictures are marked SAMPLE / NOT A PAYMENT and have no real recipient or sender data. The GCash example is an instructional successful-transfer format; no actual GCash proof image has yet been provided for native device validation.

Bank Transfer is removed from the app choices (already absent), customer proof form, appointment form and admin payment-add form, and rejected for new API submissions. Maya is available in those website forms. Historical bank records and database enums are retained, with no SQL import or record deletion. Card and applicable Cash options are preserved. Website forms do not gain native ML Kit OCR: receipt format screening described here is in the Flutter app. PHP rejects new bank methods and explicit legacy manual-review requests; it does not independently OCR images from modified clients or other upload channels.

## Install locally: Windows PowerShell 5.1
Extract the ZIP, open PowerShell in the extracted folder and stop the previous Flutter run with Ctrl+C. Keep Apache/MySQL running, USB connected and debugging authorized.

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Wallet-Update.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Wallet-Update.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

Cumulative source update accepts the approved manual-wallet baseline and subsequent receipt patches through the previous picture-guide release. Unknown edits stop preflight before any copying. Backups go to LuckyYunaBackups under the user profile. This updates local XAMPP files only; remote hosting is not deployed. Full instructions and verification reports are in this ZIP.

## Phone checks
For GCash and Maya: open the accepted/not-accepted guide, use your own original completed receipt, confirm matching amount and shop number, and enter its actual reference. Confirm valid submission remains Under Review. Wrong amount/number, unreadable/missing fields and pending or unknown format must stay disabled with no manual-review checkbox. If a real GCash receipt is blocked, inspect the local detected-details view; native OCR accuracy is not guaranteed by synthetic tests. Check the new website payment dropdowns no longer offer Bank Transfer; existing bank history must remain visible.

## Verification
109 Flutter tests passed, including GCash format/status/amount/recipient checks, actual dialog bypass prevention, wallet-specific pictures and enlarged-text guide scrolling/zoom. Thirty-one API checks passed, including new Bank Transfer rejection without changing the existing payment, auth/ownership, duplicate references, image checks and normal Under Review status. Four changed PHP files pass syntax checks and no longer contain Bank Transfer choices. Six baseline compatibility checks pass. Flutter analysis: no errors/warnings, 13 existing info diagnostics. Native phone OCR, real transfers, Windows PowerShell execution and authenticated website browser flows were not run here. No actual receipt images, sender details or private config files are published.
