-- Babura House Connect
-- Database Schema v1.0
-- Engine: InnoDB
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci

CREATE DATABASE IF NOT EXISTS `babura_house_connect`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `babura_house_connect`;

-- 1. USERS TABLE
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `role` ENUM('tenant', 'landlord', 'admin') NOT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NULL UNIQUE,
  `phone` VARCHAR(30) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `profile_photo` VARCHAR(255) NULL,
  `status` ENUM('active', 'suspended', 'pending') NOT NULL DEFAULT 'active',
  `email_verified_at` TIMESTAMP NULL,
  `last_login_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_role` (`role`),
  INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TENANT PROFILES
CREATE TABLE IF NOT EXISTS `tenant_profiles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `occupation` VARCHAR(150) NULL,
  `preferred_area` VARCHAR(150) NULL,
  `bio` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_tenant_profiles_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. LANDLORD PROFILES
CREATE TABLE IF NOT EXISTS `landlord_profiles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL UNIQUE,
  `address` VARCHAR(255) NULL,
  `bio` TEXT NULL,
  `verification_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `rating_average` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_landlord_profiles_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. LANDLORD VERIFICATIONS
CREATE TABLE IF NOT EXISTS `landlord_verifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `landlord_id` BIGINT UNSIGNED NOT NULL,
  `nin_hash` VARCHAR(255) NOT NULL,
  `nin_last4` VARCHAR(4) NULL,
  `id_type` VARCHAR(50) NOT NULL,
  `id_document_path` VARCHAR(255) NOT NULL,
  `verification_photo` VARCHAR(255) NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` TEXT NULL,
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_landlord_verifications_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_landlord_verifications_admin` FOREIGN KEY (`reviewed_by`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. AREAS
CREATE TABLE IF NOT EXISTS `areas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. HOUSES
CREATE TABLE IF NOT EXISTS `houses` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `landlord_id` BIGINT UNSIGNED NOT NULL,
  `area_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `house_type` ENUM('single_room', 'room_and_parlor', 'two_bedroom', 'three_bedroom', 'four_bedroom', 'self_contain', 'flat', 'duplex', 'compound_house', 'shop', 'other') NOT NULL,
  `rent_amount` DECIMAL(12,2) NOT NULL,
  `rent_period` ENUM('month', 'year') NOT NULL DEFAULT 'year',
  `bedrooms` INT UNSIGNED NOT NULL DEFAULT 1,
  `bathrooms` INT UNSIGNED NOT NULL DEFAULT 1,
  `status` ENUM('draft', 'pending_approval', 'published', 'rejected', 'rented', 'archived') NOT NULL DEFAULT 'draft',
  `availability` ENUM('available', 'rented') NOT NULL DEFAULT 'available',
  `featured` BOOLEAN NOT NULL DEFAULT FALSE,
  `featured_until` TIMESTAMP NULL,
  `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_houses_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_houses_area` FOREIGN KEY (`area_id`)
    REFERENCES `areas` (`id`) ON DELETE RESTRICT,
  INDEX `idx_houses_landlord_id` (`landlord_id`),
  INDEX `idx_houses_area_id` (`area_id`),
  INDEX `idx_houses_status` (`status`),
  INDEX `idx_houses_availability` (`availability`),
  INDEX `idx_houses_featured` (`featured`),
  INDEX `idx_houses_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. HOUSE IMAGES
CREATE TABLE IF NOT EXISTS `house_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_primary` BOOLEAN NOT NULL DEFAULT FALSE,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_house_images_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. HOUSE VIDEOS
CREATE TABLE IF NOT EXISTS `house_videos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `thumbnail_path` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_house_videos_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. FAVORITES
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_favorites_tenant` FOREIGN KEY (`tenant_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_tenant_house` (`tenant_id`, `house_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. REVIEWS
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reviewer_id` BIGINT UNSIGNED NOT NULL,
  `landlord_id` BIGINT UNSIGNED NOT NULL,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `rating` INT NOT NULL,
  `comment` TEXT NOT NULL,
  `status` ENUM('published', 'hidden', 'reported') NOT NULL DEFAULT 'published',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_reviewer` FOREIGN KEY (`reviewer_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
  INDEX `idx_reviews_landlord_id` (`landlord_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. NOTIFICATIONS
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `message` TEXT NOT NULL,
  `read_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  INDEX `idx_notifications_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. SUBSCRIPTION PLANS
CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `duration_days` INT UNSIGNED NOT NULL,
  `listing_limit` INT NOT NULL DEFAULT -1,
  `featured_listings` INT NOT NULL DEFAULT 0,
  `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. SUBSCRIPTIONS
CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `landlord_id` BIGINT UNSIGNED NOT NULL,
  `plan_id` BIGINT UNSIGNED NOT NULL,
  `status` ENUM('active', 'expired', 'cancelled', 'pending') NOT NULL DEFAULT 'pending',
  `starts_at` TIMESTAMP NULL,
  `ends_at` TIMESTAMP NULL,
  `auto_renew` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_subscriptions_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_subscriptions_plan` FOREIGN KEY (`plan_id`)
    REFERENCES `subscription_plans` (`id`) ON DELETE RESTRICT,
  INDEX `idx_subscriptions_landlord_id` (`landlord_id`),
  INDEX `idx_subscriptions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. PAYMENTS
CREATE TABLE IF NOT EXISTS `payments` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `subscription_id` BIGINT UNSIGNED NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'NGN',
  `payment_reference` VARCHAR(100) NOT NULL UNIQUE,
  `provider` VARCHAR(50) NOT NULL,
  `status` ENUM('pending', 'successful', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `paid_at` TIMESTAMP NULL,
  `metadata` JSON NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_payments_subscription` FOREIGN KEY (`subscription_id`)
    REFERENCES `subscriptions` (`id`) ON DELETE SET NULL,
  INDEX `idx_payments_reference` (`payment_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. REPORTS
CREATE TABLE IF NOT EXISTS `reports` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `reporter_id` BIGINT UNSIGNED NULL,
  `house_id` BIGINT UNSIGNED NULL,
  `landlord_id` BIGINT UNSIGNED NULL,
  `reason` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `status` ENUM('pending', 'reviewed', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
  `reviewed_by` BIGINT UNSIGNED NULL,
  `reviewed_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reports_reporter` FOREIGN KEY (`reporter_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reports_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reports_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reports_admin` FOREIGN KEY (`reviewed_by`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. ACTIVITY LOGS
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NULL,
  `action` VARCHAR(100) NOT NULL,
  `description` TEXT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA FOR AREAS
INSERT INTO `areas` (`name`, `slug`, `is_active`) VALUES
  ('Kofar Yamma', 'kofar-yamma', TRUE),
  ('Kofar Gabas', 'kofar-gabas', TRUE);
