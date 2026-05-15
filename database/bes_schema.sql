-- Create database (optional - run only if not already created)
CREATE DATABASE IF NOT EXISTS bescms
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE bescms;

-- Table: residents
CREATE TABLE `residents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `full_name` VARCHAR(255) NOT NULL,
    `birthdate` DATE DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `contact_number` VARCHAR(20) DEFAULT NULL,
    `registered_voter` ENUM('yes','no') NOT NULL DEFAULT 'no',
    `added_by` VARCHAR(100) DEFAULT NULL,
    `last_updated_by` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_full_name` (`full_name`),
    KEY `idx_registered_voter` (`registered_voter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
CREATE TABLE `users` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `resident_id` INT(11) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin','resident','bhw') NOT NULL DEFAULT 'resident',
    `verification_status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `verification_doc` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    UNIQUE KEY `uk_resident_id` (`resident_id`),
    KEY `idx_verification_status` (`verification_status`),
    KEY `idx_role` (`role`),
    CONSTRAINT `fk_users_resident` FOREIGN KEY (`resident_id`) REFERENCES `residents` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: certificate_requests
CREATE TABLE `certificate_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `certificate_type` VARCHAR(100) NOT NULL,
    `purpose` TEXT NOT NULL,
    `quantity` INT(11) NOT NULL DEFAULT 1,
    `status` ENUM('pending','approved','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
    `admin_notes` TEXT DEFAULT NULL,
    `requested_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `approved_at` TIMESTAMP NULL DEFAULT NULL,
    `completed_at` TIMESTAMP NULL DEFAULT NULL,
    `cancelled_at` TIMESTAMP NULL DEFAULT NULL,
    `rejected_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_certificate_type` (`certificate_type`),
    KEY `idx_completed_at` (`completed_at`),
    CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default admin user (password: admin123)
-- The password is bcrypt-hashed for "admin123" (cost 10)
-- Also insert a dummy resident record for the admin so foreign key constraint is satisfied.
INSERT INTO `residents` (`full_name`, `added_by`, `registered_voter`) 
VALUES ('Barangay Administrator', 'system', 'no');

-- Get the last inserted resident_id (should be 1)
SET @admin_resident_id = LAST_INSERT_ID();

INSERT INTO `users` (`resident_id`, `email`, `password`, `role`, `verification_status`)
VALUES (
    @admin_resident_id,
    'admin@gmail.com',
    '$2y$10$bMQ8ClztcklWjDGQZ0qIBu.pk6OE1/lh3GKHLHIgdB7fUcjusPPJq',  -- password = "admin123"
    'admin',
    'approved'
);

-- Optionally insert a few sample residents for testing
INSERT INTO `residents` (`full_name`, `birthdate`, `address`, `contact_number`, `registered_voter`, `added_by`) VALUES
('Juan Dela Cruz', '1990-03-15', '123 Mabini St., Barangay Poblacion', '09171234567', 'yes', 'admin'),
('Maria Santos', '1985-07-22', '456 Rizal Ave.', '09221234568', 'yes', 'admin'),
('Pedro Reyes', '2000-11-01', '789 Bonifacio St.', '09331234569', 'no', 'admin');