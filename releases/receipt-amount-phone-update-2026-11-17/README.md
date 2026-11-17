# Download update

[Download Lucky-Yuna-Receipt-Amount-Phone-Update.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/receipt-amount-phone-update-2026-11-17/Lucky-Yuna-Receipt-Amount-Phone-Update.zip)

# Receipt screening update + read-only local database diagnostic

This patch is for the already-installed Lucky-Yuna-Manual-GCash-Maya-OCR.zip version.

## Receipt screening requested by the owner

For BOTH GCash and Maya, a single matching transfer amount and matching full recipient mobile number are enough for VALID_FOR_SUBMISSION. The current approved number is 09068624144, supplied by the existing shop configuration. The account name, wallet branding, and OCR-extracted reference no longer gate the pass result.

Missing/masked/ambiguous number or amount -> NEEDS_REVIEW. A clear wrong amount or full recipient number -> INVALID. A clearly unrelated image without payment fields remains INVALID. Name-only matching is no longer sufficient. Multiple conflicting phone numbers do not cause the parser to cherry-pick a matching number. Explicit receiver labels are preferred; sender/reference lines are excluded from the fallback.

The existing API still requires a transaction reference to detect duplicates. If OCR does not read it, type it from the wallet. That manual entry does not require an extra OCR-review acknowledgement when amount + recipient phone already match. Staff still verifies actual funds before Paid. This does not authenticate screenshots or introduce automatic payment approval.

## Website screenshot: Database update required

The Admin schema guard found at least one missing table/column in the database selected by its own config. App login can work while Admin workflow tables are missing. Another possibility is that Admin and API point to different databases.

The existing full lyc_database.sql starts with CREATE DATABASE / USE vehicle_service_system. Selecting a sandbox in phpMyAdmin does not override that USE statement. Do not blindly import it into a selected sandbox expecting that database to be upgraded. No repair SQL or database writes are included here; first identify the actual connections and missing fields.

From Windows PowerShell 5.1, in this extracted package folder, run this READ-ONLY check:

```powershell
& "C:\xampp\php\php.exe" ".\Check-Local-Database.php" "C:\xampp\htdocs\Vehicle-Service-System"
```

It parses the three config files without executing them, connects only to local hosts and reads information_schema metadata. It prints database names and missing field names, never passwords/user account records. Uses the normal XAMPP PHP mysqli/tokenizer extensions. Share that output to determine the exact next repair. Do not send the actual config files or credentials. No Admin/Customer source is modified or schema guard bypassed.

## Apply receipt rule update

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Receipt-Rules.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
```

Only after Preflight passed:

```powershell
.\Apply-Receipt-Rules.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
```

Re-run on your connected Android phone with Apache/MySQL running and your WORKING API_BASE_URL, for example with the previous USB setup:

```powershell
& "$env:LOCALAPPDATA\Android\Sdk\platform-tools\adb.exe" reverse tcp:8080 tcp:80
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" run --dart-define=API_BASE_URL=http://127.0.0.1:8080/Vehicle-Service-System/vehicle-service-management/api
```

No new dependencies or database migration are needed for this receipt-rule update. Existing payment reference/duplicate checks and manual verification remain unchanged. It does not repair the Admin database automatically.

## Verification

46 Flutter unit/widget tests passed with mocked OCR, including the new two-field pass, optional name/branding/OCR reference, manual reference entry without extra review, sender/reference exclusion, ambiguous numbers, wrong amount/number, and existing manual flows. Flutter analysis: no errors/warnings; 13 existing infos.

6 read-only database diagnostic checks passed on isolated fixtures: two config-name variants, missing field reporting, no execution of config PHP, unchanged payment count, and no credentials printed. Native OCR accuracy, Windows execution and the user's actual database state remain unverified from this environment. No live-site or actual PC database writes were made.
