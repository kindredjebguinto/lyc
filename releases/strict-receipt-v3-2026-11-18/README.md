# Strict receipt reader v3 — 2026-11-18

Removes the customer manual-review bypass. Maya submission requires one of the three approved text layouts AND matching amount/full configured recipient number. The three formats are success popup (Confirm transaction / successfully sent / Source / My Wallet / Destination / View receipt / Done), expanded receipt (Sent money / Completed / Source / My Wallet / Destination / Transaction details / Maya), and compact receipt (Sent money to / Completed / Reference ID / Share / Maya). Pending/failed/refunded/reversed text is blocked. Missing/ambiguous/unreadable amount or number cannot submit, even after typing a reference. Missing required layout text also blocks submission. It matches layouts, not the original transaction reference or exact screenshot pixels.

GCash keeps its existing amount/recipient screening; the customer fallback is removed for both wallets. Card and final-balance cash behavior stay as before. References remain required for duplicate checking. Staff still verifies actual funds before Paid. All submitted proof remains Under Review.

The API rejects explicit legacy receipt_review_requested flags for both wallets; an older app offering that checkbox cannot use it successfully after applying the API file. Format recognition is on-device, not server OCR: modified clients/other upload channels are not independently image-classified by this PHP endpoint. This does not prove screenshot authenticity. No external OCR or new dependencies are introduced.

## Install: Windows PowerShell 5.1
Extract this ZIP; open PowerShell in the extracted folder. Stop the old Flutter run with Ctrl+C. Keep Apache/MySQL running and the phone USB debugging authorized.

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Strict-Receipt.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Strict-Receipt.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

The cumulative installer accepts the approved manual wallet baseline and subsequent receipt patches through reader v2. Unknown changes stop preflight before copying. Existing changed files are backed up to LuckyYunaBackups under your user profile. Thirteen files; no SQL, credential/config changes or admin/customer website page changes. One API endpoint is updated. For remote hosting, this local installer does not deploy it: update the corresponding API file on hosting separately if using that backend.

## Phone verification
The diagnostic button now says strict reader v3. Test all three Maya formats with original readable receipt images. Test wrong amount, wrong number, unsupported format and unreadable image: no checkbox and submit remains disabled. The detected-details view explains missing OCR data; choose a clearer full saved receipt when blocked. The old manual-review path no longer rescues blurry or dark images. Never publicly share sender/reference details from diagnostic text.

## Checks
95 Flutter unit/widget tests passed, including all three permitted Maya formats through the real dialog, rejection of unknown format despite matching numbers, and inability to submit after OCR failure even with typed reference. Thirty local API checks passed, including rejection of old manual-review flags, unchanged rejected payment, auth/ownership/upload/duplicate rules and continued Under Review on normal submission. PHP syntax passes. Analysis has no errors/warnings and 13 existing info diagnostics. Six package compatibility checks passed. Native ML Kit on the actual screenshots, Windows PowerShell execution and real transfers were not run here. Public tests use synthetic merchant details; no actual receipts or personal recipient/sender numbers are included.
