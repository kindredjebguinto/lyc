# Lucky Yuna mobile authentication upgrade

Implemented locally after approval. Delivery to kindredjebguinto/lyc was subsequently approved by the owner. No hosting deployment or changes to your actual PC/hosting database. Existing Admin/Customer Website pages and booking/payment/vehicle functionality are not part of this patch.

## Delivered behavior
- Welcome screen with Continue with Google first, plus manual Sign In / Create Account / Forgot Password.
- Official google_sign_in plugin and server-side signature/issuer/audience/expiry/subject/verified-email checks using Google's PHP auth library.
- Stable Google-subject identity mapping to the existing users.id/customers.id, password-confirmed same-email linking, first-time profile completion and no email-only adoption of guest profiles.
- Google-only users have no usable local password. Set Password requires fresh matching Google proof and cannot replace an existing password. Existing password changes require the current password.
- PH contact-number validation; no claim of SMS verification or new phone uniqueness policy.
- Hashed expiring single-use email reset tokens, HTTPS reset form, generic outward responses and API session revocation on credential changes.
- Existing secure token storage, backend startup session validation, offline retry, central protected-API expiry handling, and protection against delayed responses clearing a newer login.
- Android release cleartext disabled and credential backup disabled; debug USB/XAMPP HTTP remains possible with an explicit loopback-only backend allowance. Release signing requires private release credentials rather than silently using the debug key.

## Configuration status
Google OAuth and SMTP are NOT configured yet, per your answer. Google sign-in and email delivery are setup-required until you complete `setup/SETUP-GOOGLE-AND-EMAIL.md`. Manual sign-in can be used first after importing the migration, applying the patch, and setting the local private config appropriately. The ZIP includes locked PHP dependencies; Composer is not required on the user's PC merely to copy this package.

Start with the setup guide. Export a full backup, inspect the configured database and check for duplicate customer-user mappings, import only the additive migration into the confirmed active database, then use the hash-checking installer. It never executes SQL or copies private credentials. Keep SQL files outside the web root.

## Limits and compatibility
- Anonymous registration intentionally normalizes responses (202; success/message; user/customer null) to reduce email enumeration. The existing Flutter registration uses success/message. Other API consumers that require returned registration IDs must adapt.
- Matching emails offer password-confirmed linking. The profile screen also offers explicit existing-email/password proof for a different Google email. Independently created customers are not merged.
- Existing shorter valid passwords can still log in. Newly set passwords require 12–72 UTF-8 bytes because the compatible hashing implementation must not silently truncate input.
- Owned profile duplicates stop the uniqueness migration; no customer is deleted/merged. Missing owned profiles during login require reviewed assistance rather than silent creation/adoption.
- Password events revoke API sessions; existing PHP website sessions and website email-change routes require separate approved website hardening. This is not a complete website security audit.
- Outward recovery messages are generic, but synchronous SMTP response timing is not asserted identical for every account. Delivery failures invalidate the undelivered token and record a generic server error.
- Actual Google account selection/login, real email delivery, iOS native builds and real-phone end-to-end flows are NOT verified here. Android APK compilation was attempted but stopped during required NDK installation after workspace storage exhaustion; native compilation is unverified.

See TEST-REPORT.md for actual results and verification logs. No passwords, reset tokens, Google ID tokens, SMTP credentials, keystore or private config is included. QA uses synthetic identities/test JWKS and a fake mail transport. The production endpoints always use the real Google verifier/mailer; no runtime fake-auth switch is present.
