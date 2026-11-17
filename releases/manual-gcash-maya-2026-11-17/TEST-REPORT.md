# Manual GCash/Maya + on-device receipt screening — test report

Local implementation/test results. No live-site changes, real transfer, provider verification, or external OCR upload was performed. GitHub package publication was separately approved after these local checks.

| Check | Result | Evidence |
|---|---|---|
| Flutter unit/widget tests | 37 passed | verification/flutter-tests.log |
| Backend HTTP/helper checks | 26 passed | verification/backend-tests.json |
| PHP syntax | 122 files passed | verification/php-lint.json |
| Source/installer compatibility | 55 assertions passed | verification/compatibility.json |
| Flutter analysis | No errors/warnings; 13 existing informational notices | verification/flutter-analyze.log |
| Native Android ML Kit runtime | NOT VERIFIED | Requires rebuild and the owner's phone |
| Native gallery saving/permissions | NOT VERIFIED | Widget tests verify bytes/callback wiring only |
| Native Android/iOS builds | NOT VERIFIED | No native build completed in this task; iOS requires macOS/Xcode and deployment target 15.5 |
| Windows PowerShell scripts | NOT EXECUTED HERE | Manifest/apply decisions simulated, scripts reviewed; actual PC preflight required |
| Original live QR decoding | NOT VERIFIED | User selects original image files with setup script; original QR files were not available as workspace bytes |

## Requested receipt scenarios

- Valid-looking GCash/Maya receipt: synthetic OCR-text extraction/classification/rules and widget submission passed. This is eligibility for manual review, not authenticity.
- Random gallery photo: empty OCR output -> NEEDS_REVIEW. No claim that native vision recognizes arbitrary photo categories.
- Non-payment screenshot: clearly unrelated readable text -> INVALID; tested.
- Blurred/cropped receipt: incomplete simulated OCR text -> NEEDS_REVIEW; tested. Actual pixel-level blur/crop/native OCR accuracy remains unverified.
- Wrong amount: clearly-labelled differing amount -> INVALID; tested. Fees/balance/total are not used as the transfer amount. Conflicting amount fields -> NEEDS_REVIEW.
- Wrong recipient: different full recipient number or readable mismatching recipient -> INVALID. A matching number with conflicting OCR name, masked details or cropped prefix -> NEEDS_REVIEW; tested.
- Missing reference: NEEDS_REVIEW; requires customer-entered reference and explicit manual review. GCash Ref. No. and Maya Reference ID label variants covered.
- Duplicate reference: API rejects normalized references already Under Review/Paid across GCash/Maya. Existing protected Website writers are not globally serialized with this API; no DB uniqueness guarantee is claimed.
- Edited/suspicious screenshot: internally coherent fabricated text CAN pass initial screening. Test explicitly demonstrates that no authenticity check is established. Staff must check actual funds. No forgery detection accuracy claim.
- OCR failure: mocked native error -> NEEDS_REVIEW, submit disabled until explicit review and reference; tested.
- Offline/network failure: shop-details fetch failure -> retry UI and disabled wallet submission; tested. Actual network-drop/device-resume testing remains a phone check.
- Valid submission: authenticated local multipart upload -> Under Review, amount unchanged, fee status synchronized; tested.
- Manual fallback: accepted as unverified proof for staff review; no automatic Paid transition. Forged client Paid/amount/OCR claims do not approve a payment; tested.
- Booking/payment compatibility: reservation-fee cash prohibition, legacy Card/Bank Transfer API behavior, ownership, state checks, and fixed-200 QR eligibility passed. Full new end-to-end booking/release tests were not repeated.

## UI and compatibility checks

- Bank Transfer absent from Flutter method choices; existing records and backend compatibility retained.
- GCash and Maya choices, initial submit disable, checking indicator, mismatch block and manual-review acknowledgement tested.
- Stale OCR result cannot apply after changing wallets.
- Save QR callback receives original bytes; Copy button uses approved phone number. Actual OS gallery permissions still require device testing.
- No fake QR is displayed when configuration is missing. Fixed PHP 200 QR unavailable for another amount.
- Manual Card proof UI checked at 320/390/768 px, keyboard/large text, picker disposal, oversized image and cash selection.
- Customer/Admin Website files and DB-connection settings byte-for-byte unchanged against the installed baseline. Source patch has no SQL or pubspec.lock override.
- Old gateway creation now returns HTTP 410. No new gateway call, external OCR key, exec/proc_open requirement or database migration introduced.

QA used an isolated local database named manual_wallet_test and a disposable PHP source copy, never luckyyuna.com or the user's PC database. Synthetic solid-color PNGs were test fixtures only, never live payment QR images. Test scripts use workspace-specific paths; do not run them against production.
