-- Migration: Add size and amenities to houses table
-- Created At: 2026-08-09T06:58:00+01:00

ALTER TABLE `houses` 
  ADD COLUMN `size` INT UNSIGNED NULL AFTER `rent_period`,
  ADD COLUMN `amenities` TEXT NULL AFTER `size`;
