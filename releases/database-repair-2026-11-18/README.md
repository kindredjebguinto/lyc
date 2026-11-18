# Local sandbox schema repair — 2026-11-18

Target: `vehicle_service_system_sandbox` on the user's XAMPP PC. The local diagnostic confirmed that all three application/website configs point to it, with nine missing required schema fields. The obsolete gateway table was empty. No live hosting/database changes were made here.

## Back up first
1. Keep Apache/MySQL running. Close the phone app and stop using the site during maintenance.
2. Open http://localhost/phpmyadmin and select `vehicle_service_system_sandbox` in the left sidebar.
3. Export -> Quick -> SQL -> Go/Export. Keep the completed full SQL backup on your PC. Do not upload it publicly.

## Repair
1. In that same database, Import -> select `Repair-Active-Sandbox.sql` -> Go/Import.
2. Stop if any red SQL error appears; send the error text. DDL commits automatically, so a failed import can be partially applied. The repair is repeatable.
3. Refresh the admin page. Re-run the read-only checker and expect all three config sections to show Schema check: READY.

The SQL explicitly targets the confirmed sandbox. It adds the five missing columns, creates four canonical tables (job_schedule_events, service_categories, service_catalog, reservation_services), and seeds the baseline's seven categories/sixteen default catalog services. It adds the canonical mechanic indexes and expands the reservation fee enum for walk-ins. Existing customer accounts, passwords, bookings, payments and existing service records are preserved. Existing reservations default to Online; new original-estimate/check-in fields remain NULL. This does not infer historical walk-ins or assignments. It does not import the entire application SQL, switch configs, merge databases or drop any database.

## Remove only the empty legacy gateway table
After successful repair and the full backup above, import `Remove-Empty-Gateway-Table.sql` in the same database. It takes a write lock, checks the row count and drops only `payment_gateway_attempts` when empty. If rows exist, it keeps the table and reports KEPT. `gateway_table_remaining = 0` means removal succeeded. If the table is already absent, the lock command reports a missing-table error; no second cleanup is needed. The current manual wallet flow and quotation helpers check for absence of this optional table. Old PayMongo reconciliation tools should no longer be run.

Keep `vehicle_service_system_sandbox`, `vehicle_service_system`, and the MySQL/phpMyAdmin system databases. The original database is not referenced by these configs, but it may be a useful backup or used elsewhere; it is outside this cleanup.

## Verification and limits
Nine assertions passed on an isolated MariaDB 11.8 fixture reconstructed with the reported missing columns/tables. Checks cover unchanged values in all pre-existing tables, actual website schema readiness guard, catalog seeds and repeatability, walk-in enum, removal of an empty gateway table, retention of nonempty gateway history, and the actual gateway guard when the table is absent. SQL uses MariaDB 10.4-compatible syntax for the user's XAMPP. It was not executed on the user's PC or hosting. Schema readiness does not certify every application flow; verify login, service selection, booking and manual proof submission after import.
