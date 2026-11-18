# Receipt reader v2 — 2026-11-18

Cumulative Flutter-only update for the approved manual GCash/Maya wallet implementation. Accepts the manual wallet baseline, previous amount/phone patch, or Maya layout patch; unknown source edits stop preflight. No database import, new dependencies or backend configuration changes.

## Install (Windows PowerShell 5.1)
Extract this ZIP and open PowerShell in the extracted folder. Stop the old flutter run with Ctrl+C, then:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Receipt-Reader.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Receipt-Reader.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

Backups are stored under your user profile's LuckyYunaBackups directory. Keep Apache/MySQL running, the phone USB connected/authorized, and the working API override above. A hot reload alone does not apply a changed API define.

## What changed
ML Kit line bounding boxes now determine visual reading order instead of relying on the plugin's text-block order. Compatible horizontal text regions are joined into rows, so Source/Destination headings stay alongside their values. Amount parsing supports separated currency and amount lines, typographic minus signs/spaces and spaced decimal points. A decimal-only money line is a candidate only in a completed outgoing transfer context. Multiple distinct amounts remain ambiguous; wrong amounts/numbers fail screening. Digits are never completed from the configured recipient/expected amount. The original proof image is not changed or re-encoded.

## Verify on the phone
Choose Maya, select the original receipt image, then look for `View detected receipt details (reader v2)`. Its presence confirms this UI update is running. Open it to inspect the amount candidates, detected recipient and actual text used for screening. The view is local only: no automatic logging or extra upload of recognized text. Select text to copy when needed; redact sender and reference details before sharing publicly. If the number or amount is still missing, send the relevant detected text privately for diagnosis; do not assume that synthetic tests prove native OCR accuracy. Dark overlays may still prevent native OCR from reading a number; prefer the full saved receipt from Maya.

This keeps the approved amount-plus-full-recipient-number rule and existing reference/duplicate checks. Names and branding are not screening gates. Manual-review fallback remains available and every submission still requires staff verification. No payment is automatically marked Paid.

## Verification and limits
70 Flutter unit/widget tests passed, including synthetic bounding-box reading order, split money tokens, wrong/missing/conflicting fields, and the detected-details UI. Flutter analysis has no errors/warnings and 13 existing info diagnostics. Five source hash compatibility checks passed. Windows PowerShell execution, native Android OCR on the supplied images and actual payment transfers were not executed here. Real receipt screenshots, personal recipient details, sender numbers and original transaction references are not included in this public package.
