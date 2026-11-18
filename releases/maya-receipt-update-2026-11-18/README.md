# Maya receipt layout update — 2026-11-18

Cumulative update for the approved manual GCash/Maya OCR package. Accepts the original manual-wallet patch or the previous amount/phone update. Unknown file edits stop preflight before copying. Backs up changed files in LuckyYunaBackups. No SQL is run.

## Install in Windows PowerShell 5.1
Extract this ZIP, open PowerShell in its extracted folder, then:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Receipt-Rules.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Receipt-Rules.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

Uses the existing dependencies, QR configuration and backend. No new pubspec changes. Keep Apache/MySQL running and phone USB debugging enabled. The local API override above is for the existing USB/XAMPP setup.

## Receipt screening
Both wallets require the expected amount and full recipient mobile from the existing backend configuration (local or +63 format). Names, wallet branding and extracted reference are not screening gates. Reference is still required on submission for existing backend duplicate checks and can be entered manually.

Supports synthetic OCR text matching the supplied Maya success-overlay, compact and expanded layouts. Destination is preferred over Source. A completed outgoing `Sent money` debit such as `- ₱200.00` is treated as 200 pesos sent; pending/refund debit text is not normalized this way. Masked/missing/ambiguous fields need staff review. Passing screening only permits submission: every upload remains Under Review until staff verifies it. OCR does not establish payment authenticity.

## Database inventory — run from the extracted ZIP folder
```powershell
& "C:\xampp\php\php.exe" ".\Check-Local-Database.php" "C:\xampp\htdocs\Vehicle-Service-System"
```
Paste the output for a targeted repair/cleanup. This CLI-only tool reads literal config database settings without executing config PHP, lists visible local databases, missing website-required fields and legacy gateway table counts. It prints no credentials or individual customer records and changes nothing. Dynamic config settings may require local inspection.

The old PayMongo redirect is retired. A database named `vehicle_service_system_sandbox` can still be the active booking/manual-payment database. Do not delete it based on its name. `payment_gateway_attempts` is only the old gateway table. Keep historical/unresolved rows pending review and backup. Unreferenced databases may be used by other projects. The website schema warning is not repaired by this Flutter-only update; output is required to select the correct database and additive repair. Do not import the full SQL blindly: its USE directive can target another database.

## Verification
58 Flutter unit/widget tests passed, including 12 new synthetic Maya layout cases. Analysis: no errors/warnings; 13 existing info diagnostics. Ten local read-only database diagnostic assertions passed. Four package compatibility hash checks passed. Windows PowerShell execution, native ML Kit OCR on these screenshots and actual phone transfers were not tested here. No real screenshot, sender number or payment reference is included. No website files, schema or records are modified by this package.
