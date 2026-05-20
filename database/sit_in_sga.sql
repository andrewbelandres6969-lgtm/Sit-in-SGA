CREATE DATABASE IF NOT EXISTS `sit_in_sga`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `sit_in_sga`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` VARCHAR(50) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `course_level` VARCHAR(20) DEFAULT NULL,
    `course` VARCHAR(150) DEFAULT NULL,
    `email` VARCHAR(150) DEFAULT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `sitin_remaining` INT NOT NULL DEFAULT 30,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `labs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_name` VARCHAR(100) NOT NULL,
    `total_computers` INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_labs_lab_name` (`lab_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `sitin_time_limit_minutes` INT NOT NULL DEFAULT 60,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `content` TEXT NOT NULL,
    `author_name` VARCHAR(100) NOT NULL DEFAULT 'CCS Admin',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sitin_records` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `lab_id` INT UNSIGNED NOT NULL,
    `purpose` VARCHAR(255) NOT NULL,
    `status` ENUM('Pending', 'Approved', 'Rejected', 'Completed', 'Expired') NOT NULL DEFAULT 'Pending',
    `computer_number` VARCHAR(50) DEFAULT NULL,
    `remarks` TEXT DEFAULT NULL,
    `time_in` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `approved_at` DATETIME DEFAULT NULL,
    `session_end` DATETIME DEFAULT NULL,
    `time_out` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sitin_user` (`user_id`),
    KEY `idx_sitin_lab` (`lab_id`),
    KEY `idx_sitin_status` (`status`),
    CONSTRAINT `fk_sitin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sitin_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `feedback` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `category` VARCHAR(80) NOT NULL DEFAULT 'General',
    `message` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_feedback_user` (`user_id`),
    CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reservations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `lab_id` INT UNSIGNED NOT NULL,
    `reservation_date` DATE NOT NULL,
    `time_slot` VARCHAR(50) NOT NULL,
    `purpose` VARCHAR(255) NOT NULL,
    `computer_number` VARCHAR(50) DEFAULT NULL,
    `status` ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') NOT NULL DEFAULT 'Pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_reservation_user` (`user_id`),
    CONSTRAINT `fk_reservation_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reservation_lab` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `settings` (`id`, `sitin_time_limit_minutes`) VALUES (1, 60);

INSERT IGNORE INTO `labs` (`id`, `lab_name`, `total_computers`) VALUES
(1, 'Laboratory 524', 50),
(2, 'Laboratory 526', 50),
(3, 'Laboratory 528', 50),
(4, 'Laboratory 530', 50),
(5, 'Programming Lab 1', 40),
(6, 'Programming Lab 2', 40);
