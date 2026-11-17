# Compatibility revision v2

Prepared against LYC-GCash-Current-Files.zip supplied by the owner.

- The existing Flutter payment dialog is embedded in booking_payment_screen.dart. The patch moves selection to a dedicated dialog and adds that file as NEW.
- payment_balance.php is absent from the supplied includes directory. It is delivered as a NEW shared helper, rather than requiring a nonexistent original file.
- The Admin page is merged from the supplied version. It keeps its existing session-based audit identity and proof links; no dependency on newer adminActor/adminH helper functions is introduced.
- The Customer payment page retains its existing HTML and has transaction/ownership/active-checkout guards added to its submission handler. No new JS/CSS files are required.
- pubspec.lock is excluded so this patch does not overwrite dependency resolutions made by the owner's working Flutter SDK. Run flutter pub get after applying.
- Existing hash checks remain enabled. A real mismatch still stops the installer before any source copy.
- The screenshot showed that all other v1 preflight checks passed. For files outside this diagnostic ZIP, the previously accepted preflight baselines are retained; their current contents have not been independently re-uploaded or inspected.

No SQL migration was run on the owner's PC. No PayMongo account-backed or live transaction was performed. v2 local verification results are recorded in verification/v2-results.json.
