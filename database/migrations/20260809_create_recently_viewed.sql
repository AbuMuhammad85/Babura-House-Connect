-- 18. RECENTLY VIEWED TABLE
CREATE TABLE IF NOT EXISTS `recently_viewed` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `house_id` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_recently_viewed_tenant` FOREIGN KEY (`tenant_id`)
    REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_recently_viewed_house` FOREIGN KEY (`house_id`)
    REFERENCES `houses` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `uq_tenant_recently_viewed` (`tenant_id`, `house_id`),
  INDEX `idx_recent_tenant_id_updated` (`tenant_id`, `updated_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
