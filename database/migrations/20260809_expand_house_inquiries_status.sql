-- EXPAND HOUSE INQUIRIES STATUS ENUM
ALTER TABLE `house_inquiries` 
  MODIFY COLUMN `status` ENUM('pending', 'contacted', 'accepted', 'rejected', 'closed') NOT NULL DEFAULT 'pending';
