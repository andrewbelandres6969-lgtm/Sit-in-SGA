USE `sit_in_sga`;

ALTER TABLE `reservations`
ADD COLUMN `computer_number` VARCHAR(50) DEFAULT NULL AFTER `purpose`;