# Maya receipt reference pictures — 2026-11-18

Adds a Maya receipt guide button before image selection. The guide contains exactly one accepted-format illustration and one not-accepted illustration, with tap-to-enlarge/zoom. Both are prominently marked SAMPLE / NOT A PAYMENT and contain no real recipient or sender details. The compact Maya receipt is the accepted illustration; the text explains that the approved success popup and expanded receipt are also supported. The rejected example shows a pending transfer with missing details. Customers must upload their own receipt with the actual configured shop recipient and correct amount/reference.

This package includes the previous strict v3 update cumulatively. No customer manual-review bypass. Maya requires one of the three approved text formats plus matching amount and recipient. GCash retains its amount/recipient checks without the manual-review bypass. Accepted submissions remain Under Review pending staff verification of actual funds. Format screening is on-device; the API blocks legacy explicit manual-review requests but does not perform independent server OCR. Illustrations do not change validation rules and do not prove payment authenticity.

## Install: Windows PowerShell 5.1
Extract the ZIP, open PowerShell in the extracted folder and stop the previous flutter run with Ctrl+C.

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Receipt-Guide.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
.\Apply-Receipt-Guide.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

Keep Apache/MySQL running and the USB phone authorized. The installer backs up files to LuckyYunaBackups under your user profile and stops on unknown edits. No SQL import or new package dependency. The pubspec change registers the two image assets, so pub get and a full rerun are required. The installer only updates local files; remote hosting is not deployed.

## Find the pictures
Open reservation payment, choose Maya, then tap `Accepted / not accepted receipt examples`. There is one picture per category. Tap a picture to enlarge it. The pictures are bundled offline; no server connection is needed to fetch them, although existing payment details still load from the backend.

## Checks
96 Flutter unit/widget tests passed, including the guide at 390x844 and 1.4 text scale, scrolling, two decoded assets and image zoom/close. Analysis: no errors/warnings and 13 existing info diagnostics. Five baseline compatibility hash checks passed. The included strict v3 API file is unchanged from the previously tested 30-check release. Native phone OCR, Windows PowerShell execution and real transfers were not run here. Original private receipt images and personal account details are not included.
