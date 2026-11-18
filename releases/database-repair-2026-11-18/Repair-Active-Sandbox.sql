-- Targeted local repair for the confirmed active XAMPP database.
-- Export vehicle_service_system_sandbox in phpMyAdmin FIRST.
-- MariaDB 10.4+. No database creation, account changes or existing-row updates.
-- DDL commits automatically. Re-running is supported.
USE `vehicle_service_system_sandbox`;
ALTER TABLE `reservations`
 ADD COLUMN IF NOT EXISTS `checked_in_at` DATETIME DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS `booking_channel` ENUM('Online','Walk-in') NOT NULL DEFAULT 'Online',
 ADD COLUMN IF NOT EXISTS `original_duration_minutes` INT DEFAULT NULL,
 ADD COLUMN IF NOT EXISTS `original_appointment_at` DATETIME DEFAULT NULL;
-- Canonical workflow enum includes Not Required for walk-in reservations.
ALTER TABLE `reservations` MODIFY `reservation_fee_status`
 ENUM('Awaiting Payment','Under Review','Paid','Refund Requested','Refunded','Rejected','Forfeited','Not Required')
 NOT NULL DEFAULT 'Awaiting Payment';
ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `mechanic_id` INT DEFAULT NULL;
ALTER TABLE `users` ADD INDEX IF NOT EXISTS `staff_mechanic_idx` (`mechanic_id`);
ALTER TABLE `users` ADD UNIQUE INDEX IF NOT EXISTS `staff_mechanic_unique` (`mechanic_id`);

CREATE TABLE IF NOT EXISTS `service_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `service_catalog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estimated_duration` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_catalog_category` (`category_id`),
  CONSTRAINT `fk_catalog_category` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `reservation_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reservation_id` int(11) NOT NULL,
  `service_id` int(10) unsigned NOT NULL,
  `service_name_snapshot` varchar(150) NOT NULL,
  `price_snapshot` decimal(10,2) NOT NULL,
  `duration_snapshot` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_res_service` (`reservation_id`,`service_id`),
  KEY `fk_rs_service` (`service_id`),
  CONSTRAINT `fk_rs_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rs_service` FOREIGN KEY (`service_id`) REFERENCES `service_catalog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS job_schedule_events (
 id INT AUTO_INCREMENT PRIMARY KEY,
 reservation_id INT NOT NULL,
 actor_id INT NOT NULL,
 action VARCHAR(30) NOT NULL,
 old_start DATETIME NOT NULL,
 new_start DATETIME NOT NULL,
 old_duration INT NOT NULL,
 new_duration INT NOT NULL,
 reason VARCHAR(500) NOT NULL,
 consent_reference VARCHAR(400) DEFAULT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 KEY job_schedule_history (reservation_id,id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Restore the default catalog required by the service selector. Existing IDs are kept.
INSERT IGNORE INTO service_categories (id,category_name,slug,sort_order,status) VALUES
(1,'PMS Package','pms-package',1,'Active'),(2,'Periodic Services','periodic-services',2,'Active'),(3,'AC Services & Repair','ac-services-repair',3,'Active'),(4,'Brake Services','brake-services',4,'Active'),(5,'Tire Services','tire-services',5,'Active'),(6,'Engine Services','engine-services',6,'Active'),(7,'Battery Services','battery-services',7,'Active');

INSERT IGNORE INTO service_catalog (id,category_id,service_name,description,price,estimated_duration,sort_order,status) VALUES
(1,1,'PMS Basic','Basic preventive maintenance package including standard vehicle inspection.',1500,90,1,'Active'),
(2,1,'PMS Standard','Preventive maintenance package with additional fluid and safety checks.',2500,120,2,'Active'),
(3,1,'PMS Premium','Comprehensive preventive maintenance package for complete vehicle care.',4500,180,3,'Active'),
(4,2,'Regular Oil Change','Regular engine oil replacement with oil filter inspection.',1200,60,1,'Active'),
(5,2,'Semi-Synthetic Oil Change','Semi-synthetic oil service suitable for regular driving conditions.',1800,60,2,'Active'),
(6,2,'Fully Synthetic Oil Change','Premium fully synthetic engine oil replacement.',2600,60,3,'Active'),
(7,3,'Air Conditioning Check','Inspection of the air conditioning system and cooling performance.',800,45,1,'Active'),
(8,3,'AC Cleaning','Air conditioning cleaning service.',1500,90,2,'Active'),
(9,4,'Brake Inspection','Inspection of brake pads, rotors and braking performance.',500,45,1,'Active'),
(10,4,'Brake Cleaning and Adjustment','Brake cleaning and adjustment service.',1200,90,2,'Active'),
(11,5,'Tire Rotation','Rotate tires for more even tread wear.',700,45,1,'Active'),
(12,5,'Tire Inspection','Inspection of tire condition, pressure and tread.',350,30,2,'Active'),
(13,6,'Engine Diagnostic','Basic engine diagnostic inspection.',1500,90,1,'Active'),
(14,6,'Engine Tune-Up','General engine tune-up service.',2200,120,2,'Active'),
(15,7,'Battery Check','Battery health and charging system inspection.',400,30,1,'Active'),
(16,7,'Battery Replacement Service','Battery removal, replacement and charging-system check.',800,45,2,'Active');

SELECT DATABASE() AS repaired_database, 'Schema repair statements completed; run Check-Local-Database.php to verify.' AS result;
