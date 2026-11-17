# Lucky Yuna GCash sandbox source patch

[Download GCash-Sandbox.zip](https://github.com/kindredjebguinto/lyc/raw/refs/heads/main/releases/gcash-sandbox-2026-11-17/GCash-Sandbox.zip)

If the direct link opens a preview, open [the ZIP file](./GCash-Sandbox.zip) and click **Download raw file**.

- Archive size: 108,610 bytes; contains 29 runtime patch files plus installer, migration, guides and verification evidence.
- SHA-256: `5423a3de29fa48b72d30489bc04537f5252fd4852e6848e947280ebac1ce54a4`
- [Setup guide](./SETUP-GCASH-SANDBOX.md)
- [Test report](./TEST-REPORT.md)

Extract the ZIP before running its installer. Start with the installer's check-only preflight. It refuses source conflicts and never runs SQL automatically.

This is a source patch, not an APK or a live deployment. GCash requires your own PayMongo test account, private backend credentials and a separate HTTPS test backend/database. Account-backed PayMongo sandbox, native app handoff and device testing remain outstanding.

Local checks: 41 backend assertions, 12 HTTP checks and 20 Flutter tests passed. Provider responses were synthetic/mocked. PHP syntax checks passed; Flutter analysis had no errors/warnings and 13 pre-existing informational notices.

Uploaded with the repository owner's explicit permission to publish the source package in this public repository. Existing application/release files are unchanged.
