# Download installer

[Download Lucky-Yuna-Manual-GCash-Maya-OCR.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/manual-gcash-maya-2026-11-17/Lucky-Yuna-Manual-GCash-Maya-OCR.zip)

Package size: 61,078 bytes. SHA-256: `8ede3f349547f1c13ab444f08d2ac548a7d5fbd9668425abcf491455973225cd`.

This is the source patch for your currently installed Optimized Baseline + GCash redirect patch. Apply it to the current project with the included preflight; select the two original QR image files using Set-Wallet-QRs.ps1. No new database migration is required. Existing DB connection settings and protected Website source are preserved.

# Lucky Yuna — manual GCash/Maya QR + on-device receipt screening

Source changes only. This replaces the installed redirect flow with manual GCash/Maya payments. No external gateway, external OCR service, Tesseract, executable access, server AI key, or new database migration is needed.

The app performs ML Kit Latin text recognition on the phone, then separate text classification and validation rules. This is screening, not proof of a genuine receipt. The backend independently checks authentication, payment ownership/eligibility, image MIME/dimensions/size, recorded QR amount where a shop version is supplied, stale shop details, and duplicate wallet references. It does NOT independently read the receipt's amount or recipient. Forged app-side OCR claims cannot make a payment Paid. Staff must verify actual funds.

## Approved payment details

Both GCash and Maya use TEDDY GIL LICO / 09068624144, explicitly supplied by the owner. Both supplied QR images show PHP 200.00. The configured QR is therefore available only for a payment record of PHP 200; a different amount requires a different approved QR/configuration. The app does not generate, retouch, reconstruct, or change recipient data in a QR.

The actual QR images were visible in chat but were not available as downloadable file bytes in this workspace. They are NOT recreated or substituted in this package. The included local setup script lets the owner choose the two original image files already on their PC; it copies those bytes exactly and verifies the copies with SHA-256. Until real files/configuration are installed, the app shows QR unavailable and does not display a fake code.

## Installation on the current PC source

1. Back up source and the existing database. Keep the current `Vehicle-Service-System` folder in place; this is a source PATCH for Optimized Baseline + the already-applied GCash-For-Optimized-Baseline patch.
2. Extract this package outside the project and open Windows PowerShell 5.1 from that extracted folder.
3. Run check-only preflight:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-Manual-Wallets.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
```

4. Only when preflight passes:

```powershell
.\Apply-Manual-Wallets.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
```

5. Save the two ORIGINAL QR images as PNG/JPG/WEBP on your PC. From the same extracted folder run:

```powershell
.\Set-Wallet-QRs.ps1
```

Select the GCash image first, then the Maya image. The approved name and number are prefilled. The script copies the originals into `C:\xampp\lyc-private\` (outside htdocs); it does not transfer money or change a database. Alternatively pass full file paths using `-GCashQr` and `-MayaQr` parameters. The backend validates actual MIME/dimensions when loading them.

6. Update Flutter dependencies:

```powershell
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
```

Then run on your phone using your already-working API_BASE_URL / adb reverse configuration. Rebuild/reinstall after adding native plugins; hot reload alone does not install ML Kit or gallery plugins. Internet is required to obtain build dependencies; the configured Latin recognition SDK is bundled for on-device use.

7. Select GCash and Maya, check both original QR images/name/number, test Copy/Save QR, and screen sample receipts. Confirm the actual QR recipient in your own wallet BEFORE accepting a real transfer. No live-payment test has been performed here.

## Hostinger setup

On Premium Web Hosting, use the existing PHP/MySQL API with no `exec` or `proc_open`. Copy `manual_wallets.php` and both original QR files into a private `lyc-private` directory immediately ABOVE the site's `public_html` document root. Use the same relative filenames from the generated config. The PHP loader defaults to `dirname(DOCUMENT_ROOT)/lyc-private/manual_wallets.php`. If your document root differs, configure a trusted absolute path with `LYC_MANUAL_WALLETS_FILE`; do not use a client-supplied path.

All API/Customer/Admin database connections must remain consistent with your chosen local/test database. This installer preserves the three existing database-connection config files, including `vehicle_service_system_sandbox` if that is what you configured. It does not deploy to luckyyuna.com.

## Workflow

- Flutter payment choices: GCash, Maya, Card; Cash only for non-reservation-fee payments. Bank Transfer is removed from NEW Flutter choices. Existing Bank Transfer records and the legacy backend method remain compatible. Protected Customer/Admin Website choices are unchanged.
- GCash/Maya submit starts disabled. After receipt selection: Checking receipt -> Ready for submission, Needs replacement, or Needs manual review.
- Clear amount/recipient mismatch keeps submit disabled. Incomplete/unreadable/failed OCR allows explicit manual review after the customer enters a transaction reference. The backend still independently enforces upload and payment rules.
- Reference extracted from OCR is editable. A different entered reference requires manual review acknowledgement rather than silently retaining a pass.
- Submit creates `Under Review`, never `Paid`. Existing authorized staff approval/rejection and receipts remain in place.
- No raw OCR text, OCR confidence score, extracted personal fields, new image hashes or new schema fields are stored. Only the existing receipt file/reference and a short manual-verification note are used.

## Duplicate and historical-checkout limits

Wallet references are normalized (case, spaces, hyphens) and checked across GCash/Maya payments already Under Review/Paid. A database advisory lock serializes matching references through THIS API. The protected websites write separately and do not take that lock; this is not a global database uniqueness guarantee. No ability to distinguish two edited screenshots with different forged references is claimed.

Old checkout creation now returns 410 and cannot contact PayMongo. Flutter startup/deep-link/redirect calls are removed or retired. Old provider history/table and server reconciliation files are retained; no table/history is deleted. Existing unresolved gateway attempts still block manual replacement. If any exist, reconcile them rather than blindly resetting payment status or paying again.

## Platform and verification limits

- Android: ML Kit and gallery plugins added. Runtime recognition and real gallery permission handling require the owner's Android phone test; Linux widget tests mock the native OCR boundary.
- iOS: deployment target raised to 15.5 as required by the chosen ML Kit package and photo-save usage text added. A macOS/Xcode/CocoaPods native build is still required; not verified here.
- Windows/web builds do not provide ML Kit native recognition. Their failure goes to the explicit manual review path; they are not claimed as supported OCR targets.
- No external OCR/gateway subscription or per-OCR API fee is introduced. Normal hosting/storage, build downloads, and any wallet transfer fees remain separate.
- Original QR decoding, actual receipt pixel OCR accuracy, edited-image authenticity, real transfers and native builds are not claimed as verified. See TEST-REPORT.md for actual local checks.

No Admin Website or Customer Website source files are modified by this patch. Implementation/testing was local. Package publication to GitHub follows the owner's separate explicit approval. No live deployment was performed.
