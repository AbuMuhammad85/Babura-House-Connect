-- Add 'investigating' to the ENUM column first
ALTER TABLE `reports` MODIFY COLUMN `status` ENUM('pending', 'reviewed', 'resolved', 'dismissed', 'investigating') NOT NULL DEFAULT 'pending';

-- Migrate any existing records with status = 'reviewed' to 'investigating'
UPDATE `reports` SET `status` = 'investigating' WHERE `status` = 'reviewed';

-- Remove the old 'reviewed' status value from the ENUM definition
ALTER TABLE `reports` MODIFY COLUMN `status` ENUM('pending', 'investigating', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending';
