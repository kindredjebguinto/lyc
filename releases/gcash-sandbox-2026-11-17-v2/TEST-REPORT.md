# GCash sandbox implementation v2 — local verification

17 November 2026. All results below are for local source and isolated QA fixtures, not the live website or an account-backed PayMongo checkout.

| Check | Result | Evidence |
|---|---|---|
| Backend assertions | 41 passed | `verification/backend-results.json` |
| Local PHP HTTP checks | 16 passed | `verification/http-results.json` |
| Flutter tests | 20 passed | `verification/flutter-tests.log` |
| Changed PHP syntax | 14 files passed | `verification/php-lint-results.json` |
| Flutter analysis | No errors/warnings; 13 pre-existing informational notices | `verification/flutter-analyze.log` |
| PowerShell installer | Source reviewed; not executed on Windows | It defaults to hash-only preflight and refuses conflicting source versions. |
| Native Android/iOS build and deep-link handoff | Not run for this change | Requires device/native build verification. |
| Real PayMongo sandbox checkout/webhook delivery | Not run | No merchant account, test credentials, or deployed test endpoint was available. |
| Real money / live deployment | Not performed | Sandbox keys only; source rejects live keys. |

## v2 compatibility verification

22 checks passed: preflight simulation, installed-file hashes, rejection of unrelated local edits, new helper/dialog creation, current lock preservation, retained Customer HTML, no newer Admin helper dependency, and syntax validation of 14 PHP files. See verification/v2-results.json.

The Flutter test project combined the uploaded current API/payment files with the v2 patch and the previously available full project scaffold. flutter pub get succeeded using the uploaded pubspec.lock as the starting point. All 20 Flutter tests passed; analysis had no errors or warnings and 13 informational notices in existing screens.

The disposable PHP test tree combined the supplied current pages, the v2 source overlay and the prior full test scaffold. All 41 backend assertions and 16 HTTP checks passed, including active-checkout locks, successful manual Maya submission after resolution, proof-required Admin approval and cash/fee synchronization. Account-backed PayMongo and native-device limitations remain unchanged.

The PowerShell script itself was not executed on Windows here. Its manifest/preflight/apply decisions were simulated locally; actual PC preflight is still required. The supplied diagnostic ZIP was partial. Unuploaded files retain the baseline checks that previously passed in the owner's screenshot.

## Required scenarios

1. **Successful payment:** synthetic provider snapshot verifies amount/currency/source/mode/mapping; payment, fee status, reference, receipt eligibility and notification updated. Flutter displays verified success without proof upload.
2. **Pending:** provider pending remains unpaid. The Flutter pending result has a status-check action and no receipt/success claim.
3. **Failed:** a failed individual payment remains an active checkout; Flutter displays failure. A second payable session is not created while the first can still accept payment.
4. **Cancelled:** Flutter result/actions tested with a mock verified-cancellation response. Backend expiry logic releases only a provider-confirmed expired session with a nonprocessing, recognized intent state. Real provider expiry endpoint call awaits sandbox credentials.
5. **Expired:** backend snapshot restores the previous payable state. Processing or unknown intent data prevents release even on an expired page.
6. **Duplicate notification:** applying the same captured transaction twice produces one payment transition and one notification. This tests DB idempotency, not actual externally delivered webhook retries.
7. **Invalid callback:** valid test HMAC accepted; tampered, absent, stale, future and live-only signatures rejected. An unsigned local HTTP callback cannot report success. The return page cannot mark payment paid.
8. **Wrong amount:** rejected; payment stays Pending and the attempt enters blocked review. Currency, source, session, metadata, mode and multiple-capture mismatches also rejected.
9. **App closed:** widget disposal/recreation retains the pending identifier in mocked secure storage; reopened screen checks and clears it on verified success. Actual OS process kill/GCash handoff remains a device test.
10. **App return:** strict return-link parsing ignores success claims; lifecycle resume refreshes pending to paid using server response. Native Android/iOS URL delivery remains unverified.
11. **Synchronization:** fee payment Paid synchronizes reservation fee status. A late paid fee on a cancelled booking requests refund without reviving the booking. Active checkout blocks manual API, Admin cash, customer proof submission and quotation calculation.
12. **No proof after success:** GCash method returns checkout selection without reference/image. Verified confirmation shows a reference and receipt action. Manual methods still require reference/image; final-balance cash still needs neither.

## Additional checks

- Server decimal amount to centavos conversion and rejection of zero/invalid precision.
- Checkout URL limited to HTTPS checkout.paymongo.com.
- Concurrent manual writer waits on the row lock and is rejected after an active attempt is visible.
- Customer ownership enforcement; missing bearer authentication rejected by new customer APIs.
- Disabled/unconfigured checkout fails closed and does not fabricate a URL.
- Admin UI hides cash confirmation while an attempt is unresolved and explains the restriction.
- Manual screenshot picker retained at 320/390/768 px, large text/keyboard, oversized images and picker disposal.

The backend tests use an explicitly isolated local database named gcash_gateway_test. HTTP tests use a copied source tree with test-only database configuration. Neither test runner targeted luckyyuna.com. QA scripts under verification are the exact workspace scripts and contain local fixture paths; do not run them against a production database.
