# Authentication verification report — 2026-11-19

All database mutations were limited to disposable workspace fixtures. No actual PC/hosting migration, live-domain authentication test, Google account login or email delivery was performed.

## Executed
- Flutter: 121 tests passed, including the 109 existing receipt/payment tests and 12 auth/session/input/widget tests. The Google SDK boundary is mocked for cancellation/single-flight tests. Startup-expiry and offline tests use mocked HTTP and secure storage.
- Backend service/security: 68 checks passed. Synthetic verified Google identities and a fake mail transport cover first/returning login, ownership-confirmed same/different-email linking, Google-only password setup, wrong/stale identity rejection, matching confirmation, PH numbers, generic recovery responses, hash-only token storage, expiration/reuse, transport failure invalidation, session revocation, login throttling, unchanged vehicle/booking/payment records, untouched guest profiles, missing-profile rejection, and simultaneous registration resolving to one account. User ID and customer ID deliberately differ in the fixture.
- Cryptographic verifier: 14 checks passed using the actual Google PHP verifier/JWT library with synthetic RSA signatures and trusted test JWKS. Wrong signature/issuer/audience, unsigned alg-none, expired/future tokens, unverified email, untrusted presenter, bad claims, malformed/oversized tokens and absent configuration are rejected.
- Actual local HTTP contracts/security: 13 checks passed for manual login, no-store responses, me/logout, token rejection, method restrictions, unconfigured Google/mail behavior, reset-form protections, and blocked unverified mobile email changes.
- Read-only setup checker: 4 checks passed (three config variants, no config execution, no credential output).
- Migration safety: repeated migration on clean fixture succeeds; 3 additional checks show duplicate owned profiles stop the unique-index step without merging/deleting existing rows.
- PHP syntax: 21 auth/setup files passed lint at the recorded check. Android manifest XML and iOS plist parse successfully.
- Dependency resolution is pinned for PHP 8.1 compatibility; Composer reported no vulnerability advisories at resolution time.

Final source-scope/installer/hash validation is recorded in verification/package-checks.json. Final Flutter analysis output is included; existing unrelated info diagnostics are preserved rather than refactoring booking/payment code.

## Not passed / not executed
- Android APK compile: attempted, but no APK was produced. Gradle extraction first exhausted workspace disk; after removing disposable caches, required NDK installation exhausted storage again and was stopped. This is not an Android compile pass.
- Native iOS build: not executed (no macOS/Xcode validation).
- Actual Google sign-in and real SMTP delivery: not executed; owner has no OAuth/SMTP configuration yet.
- Physical phone flows, public HTTPS staging reset URL, production signing/Play fingerprints and website browser sessions: require owner configuration/testing.

The production verifier contains no flag to accept the synthetic test identities. The tests inject a verifier object at the service boundary or supply test JWKS to the official verifier in QA code only.
