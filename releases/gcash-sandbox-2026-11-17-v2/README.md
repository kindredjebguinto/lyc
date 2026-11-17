# Download GCash Sandbox v2

[Download GCash-Sandbox-v2.zip](https://raw.githubusercontent.com/kindredjebguinto/lyc/main/releases/gcash-sandbox-2026-11-17-v2/GCash-Sandbox-v2.zip)

# Lucky Yuna GCash Sandbox v2

Compatibility revision for the source supplied in LYC-GCash-Current-Files.zip.

1. Extract this ZIP into a NEW folder, for example GCash-Sandbox-v2.
2. Open Windows PowerShell 5.1 from that folder's address bar (type powershell and press Enter).
3. Run the preflight only:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\Apply-GCash-Sandbox.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System"
```

4. Only after `Preflight passed`, apply:

```powershell
.\Apply-GCash-Sandbox.ps1 -ProjectRoot "C:\xampp\htdocs\Vehicle-Service-System" -Apply
```

5. Resolve dependencies with your working Flutter SDK:

```powershell
cd "C:\xampp\htdocs\Vehicle-Service-System\vsm_customer"
& "C:\development\flutter\bin\flutter.bat" pub get
```

The installer backs up affected existing files, refuses unknown source changes and never executes SQL. pubspec.lock is left to flutter pub get. The current Customer markup and Admin audit identity are retained. See SETUP-GCASH-SANDBOX.md before migrating a separate TEST database.

Local checks: 22 compatibility/syntax checks, 41 backend assertions, 16 HTTP checks and 20 Flutter tests passed. PowerShell was simulated, not executed on Windows. No live deployment or real PayMongo transaction has been tested.
