-- 17. HOUSE INQUIRIES TABLE
CREATE TABLE IF NOT EXISTS `house_inquiries` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `landlord_id` BIGINT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('pending', 'contacted', 'closed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_inquiries_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inquiries_tenant` FOREIGN KEY (`tenant_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inquiries_landlord` FOREIGN KEY (`landlord_id`)
    REFERENCES `landlord_profiles` (`id`) ON DELETE CASCADE,
  INDEX `idx_inquiries_house_id` (`house_id`),
  INDEX `idx_inquiries_tenant_id` (`tenant_id`),
  INDEX `idx_inquiries_landlord_id` (`landlord_id`),
  INDEX `idx_inquiries_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
