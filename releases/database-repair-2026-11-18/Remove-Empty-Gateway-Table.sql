-- Run only AFTER the complete sandbox export and successful repair.
-- Exact target verified in the local diagnostic; no DROP DATABASE.
-- Close the app and stop using the site during maintenance.
USE `vehicle_service_system_sandbox`;
-- WRITE lock keeps a concurrent gateway writer from racing the emptiness check.
-- If the table is already absent, stop here: nothing needs removing.
LOCK TABLES `payment_gateway_attempts` WRITE;
SET @lyc_cleanup_sql = IF(
 (SELECT COUNT(*) FROM `payment_gateway_attempts`)=0,
 'DROP TABLE `vehicle_service_system_sandbox`.`payment_gateway_attempts`',
 'SELECT ''KEPT: payment_gateway_attempts contains rows. No deletion performed.'' AS result'
);
PREPARE lyc_cleanup FROM @lyc_cleanup_sql;
EXECUTE lyc_cleanup;
DEALLOCATE PREPARE lyc_cleanup;
UNLOCK TABLES;
SELECT COUNT(*) AS gateway_table_remaining
 FROM information_schema.TABLES
 WHERE TABLE_SCHEMA='vehicle_service_system_sandbox' AND TABLE_NAME='payment_gateway_attempts';
