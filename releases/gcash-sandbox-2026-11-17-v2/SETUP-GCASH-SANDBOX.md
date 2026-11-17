# Lucky Yuna — GCash sandbox integration v2

Local source implementation, 17 November 2026. NOT deployed to luckyyuna.com. No PayMongo account, real provider sandbox checkout, real GCash app handoff, native Android/iOS build, or live payment was tested for this change.

## v2 compatibility update

This version matches the current files supplied after the first installer reported conflicts. See COMPATIBILITY-v2.md. Download/extract this v2 package into a new folder; do not mix it with v1. Start again with the check-only PowerShell command. pubspec.lock is not overwritten. The two previously missing helper/dialog files are intentionally NEW files.

## What is included

- Flutter GCash selection now opens backend-created PayMongo hosted checkout, without asking for a reference or image. Maya, bank transfer, card, and final-balance cash retain their existing manual flow.
- Server-owned amount in PHP centavos; payment/booking ownership and quotation eligibility; backend-only test secret key.
- Sandbox webhook signature verification (raw request body, HMAC-SHA256, test `te` signature, timestamp limits), followed by a provider GET. A browser return cannot mark a payment Paid.
- One additive payment_gateway_attempts table. Existing payments/reservations columns, status enums, manual APIs, receipt API, and reservation-fee credit calculation are retained.
- Committed checkout reservation before network requests; payment/reservation locks serialize manual actions with gateway creation and settlement. Existing active attempts are reused. Ambiguous POST failures are blocked for reconciliation, not retried automatically.
- Admin cash/approve/reject, customer proof submission, manual mobile API, and final-balance calculation cannot bypass an unresolved gateway attempt.
- An expired checkout with a payment still processing does NOT release the payment for another charge. Unknown intent data also stays blocked.
- Cancel requests expire the remote checkout and retrieve its verified state before allowing a new attempt. Failed individual payments keep the same checkout active until verified expiry.
- Persistent pending payment ID, cold-start recovery for an existing logged-in session, warm deep-link return, foreground refresh, bounded polling (12 checks at 10-second intervals), receipt and status screens.
- A late-paid reservation fee on a cancelled booking is recorded as paid and refund requested. The booking remains cancelled. Refund payout remains manual; this code does not send refunds.
- Live keys are intentionally refused. All gateway confirmations identify themselves as sandbox payments with no real funds received.

## Install safely on your PC first

1. Extract the ZIP outside htdocs. Back up your current source and database.
2. Run the included installer in PowerShell, using your actual project root:

   ```powershell
   .\Apply-GCash-Sandbox.ps1 -ProjectRoot 'C:\xampp\htdocs\Vehicle-Service-System'
   ```

   This first run only checks file hashes. It does not copy files or run SQL. If your files differ from the supplied source versions, it stops and lists the conflicts. Merge/review those differences first; do not overwrite newer work.

3. If preflight passes, apply the source patch:

   ```powershell
   .\Apply-GCash-Sandbox.ps1 -ProjectRoot 'C:\xampp\htdocs\Vehicle-Service-System' -Apply
   ```

   It backs up existing affected files under your user profile's LuckyYunaBackups folder. It copies only listed patch files; it does not replace your Gradle or Android plugin versions, database connections, or hosting base URLs.

4. Create a separate TEST database from your project schema. Point all test API/customer/admin database configurations to this same test database. Never enable sandbox payments against a live business database: test success intentionally changes payment and booking records in that database.
5. In phpMyAdmin, select the TEST database, then import ONLY:

   `vehicle-service-management/database/migrations/20261117_payment_gateway_attempts.sql`

   The migration creates one table. It has been applied only to an isolated QA database here. Do not re-import the full application database over existing business records.
6. Run `flutter pub get`, then `flutter analyze --no-fatal-infos` and `flutter test` after copying the included Flutter verification tests if desired. Build/run using the Flutter SDK you already have working. The existing Android Gradle fixes on your PC are preserved by this patch.
7. Until configuration exists, Pay with GCash returns a setup-unavailable message. Other manual methods remain available. That is expected.

## PayMongo and luckyyuna.com setup

Read-only checks found the public HTTPS homepage and `/admin/login.php` reachable. This does not establish hosting-control access, current API deployment paths, source parity, webhook routing, or database access.

1. Create your own PayMongo merchant account and confirm GCash availability, verification requirements, and test-mode access in its dashboard.
2. Prefer a staging subdomain such as `sandbox.luckyyuna.com` or a separately configured staging path, backed by a separate test database. Domain/DNS/hosting setup has not been changed here.
3. Deploy the reviewed PHP source to that test installation. Enable PHP mysqli, cURL with valid CA certificates, fileinfo, and HTTPS. API, customer website and admin website must point to the same TEST database.
4. Preserve your hosting's existing folder/base-URL mapping. Supplied source has sibling directories `vehicle-service-management` and `vehicle-service-management-admin`; your public `/admin` deployment may map differently. In particular, the new Admin guard include must resolve to the public application's shared `includes/payment_gateway_guard.php`. Do not replace hosting-specific config files with local XAMPP configurations.
5. Configure these values in the server environment or your host's private secret configuration (not Flutter, not chat, not a public text file):

   ```text
   PAYMONGO_ENABLED=1
   PAYMONGO_SECRET_KEY=<your test-mode secret, sk_test_...>
   PAYMONGO_WEBHOOK_SECRET=<your test webhook signing secret>
   PAYMONGO_RETURN_URL=https://<your-test-host>/<actual-api-path>/payments/gcash/return.php
   ```

   The return URL must be HTTPS with no query or fragment. The code appends the payment ID. Apache service environment may differ from a PowerShell terminal's environment; use hosting environment settings or Apache virtual-host configuration and restart as needed. The supplied PHP configuration reads getenv(); it does not parse .env files.

6. Register the test webhook URL ending `/payments/gcash/webhook.php` with PayMongo for `checkout_session.payment.paid`. It uses provider-signature authentication, not customer bearer authentication. Keep the endpoint publicly reachable over HTTPS. A webhook arriving before local session-ID persistence returns an error so it can be retried.
7. Configure Flutter's `API_BASE_URL` with the HTTPS TEST API URL using `--dart-define=API_BASE_URL=...`. No provider secret belongs in the app.
8. Confirm current PayMongo dashboard/API behavior against the official references and run actual sandbox checkout tests before enabling any public customer access. Direct developer-reference pages were blocked (HTTP 403) from this workspace; accessible code in PayMongo's official GitHub organization corroborated checkout creation, expiry, event type, test signatures, and processing-after-expiry handling. This is still not an account-backed integration test.

## Return links and recovery

The public HTTPS return page has a button to `luckyyuna://payments/return?payment_id=...`. Android/iOS scheme registrations and app_links handle that URI. Only the payment ID is used as a navigation hint; auth/ownership/provider checks run again on the backend. No tokens or success claims travel in the link.

A custom scheme is not a verified App Link and can be registered by another installed app. The link contains no secrets and cannot authorize payment. Verified Android App Links / iOS Universal Links would require your domain association files and release signing identifiers; these have not been configured.

On a cold start with a valid stored login, the app restores the remembered payment and checks its status. If the session expired or app data was cleared, log in and open Payments manually. Native GCash/browser handoff and OS process-kill behavior still require your phone test.

## API contracts (additive)

Paths below are relative to the existing API base. Customer routes require existing bearer authentication.

| Route | Request | Result |
|---|---|---|
| POST `payments/gcash/create.php` | JSON `{ "payment_id": 123 }` | Creates/reuses an owned, eligible checkout. Amount/currency/status supplied by a client are not used. |
| GET `payments/gcash/status.php?payment_id=123` | Owned payment ID | Existing payment plus verified gateway attempt state. Provider refresh is cached for eight seconds after an attempt update. |
| POST `payments/gcash/cancel.php` | JSON `{ "payment_id": 123 }` | Requests provider expiry, then verifies. A processing/paid transaction is not treated as cancelled. |
| POST `payments/gcash/webhook.php` | Original signed provider event body | Authenticates signature, maps checkout, retrieves provider state, applies a transaction idempotently. |
| GET `payments/gcash/return.php?payment_id=123` | Navigation hint | Static return page only; cannot update payment status. |

Success responses use:

```json
{
  "success": true,
  "payment_id": 123,
  "payment_status": "Pending",
  "amount": "200.00",
  "currency": "PHP",
  "gateway_status": "pending",
  "reference": null,
  "checkout_url": "https://checkout.paymongo.com/...",
  "message": "Waiting for verified payment..."
}
```

The example URL is illustrative, not a working checkout. The actual URL must be returned by the provider. Gateway states: `not_started`, `creating`, `pending`, `failed` (individual payment failed; session still active), `unknown`, `review`, `paid`, `expired`, `cancelled` (verified expiry following an explicit close request).

The attempt table's state remains pending when an individual payment fails; last_outcome stores the display outcome. Only verified captured payments update the existing payment to Paid. Return or cancel query parameters cannot do that.

Validation/eligibility/configuration errors return `{ "success": false, "message": "..." }` with 409; provider/internal availability failures use 503; invalid webhook signatures use 401. Existing authentication/method checks keep their current status codes. A missing gateway account does not fabricate a checkout URL.

## Reconciliation and rollback

From a trusted backend terminal, reconcile a known attempt:

```text
php vehicle-service-management/tools/reconcile_gcash.php <attempt_id>
```

If the create request timed out before a provider session ID was saved, find its checkout in the PayMongo dashboard using the attempt_token metadata, then:

```text
php vehicle-service-management/tools/reconcile_gcash.php <attempt_id> <cs_session_id>
```

The tool retrieves the session and validates its mapping. Do not guess an ID, delete an attempt, reset Pending to Awaiting Proof, or accept cash merely because the browser was closed. If no remote session can be found, resolve that uncertainty with the provider before releasing the payment manually. The tool is CLI-only; HTTP access returns 404.

To disable new checkout creation, remove PAYMONGO_ENABLED or set it to 0. Existing unresolved attempts still require reconciliation; disabling the provider does not prove no funds are pending. Keep credentials available until all active attempts have been resolved. Do not roll back the guards or drop the new table while any checkout may still accept or settle payment.

## Verification and remaining work

See TEST-REPORT.md and verification/ for actual results. Local tests use synthetic provider snapshots and HTTP mocks. Real PayMongo sandbox, native browser/GCash handoff, real device cold-start return, external webhook delivery, hosting-specific includes/base URLs, and TLS configuration remain unverified until your account and test hosting are ready. No real-money transactions, Git commits, pushes, or live deployments were performed.
