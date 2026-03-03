-- ============================================
-- Migration: Add per-user Evolution API credentials
-- Run this on EXISTING databases (production)
-- ============================================

ALTER TABLE users
  ADD COLUMN evolution_base_url VARCHAR(500) DEFAULT NULL AFTER evolution_instance,
  ADD COLUMN evolution_instance_name VARCHAR(255) DEFAULT NULL AFTER evolution_base_url,
  ADD COLUMN evolution_token TEXT DEFAULT NULL AFTER evolution_instance_name,
  ADD COLUMN evolution_connection_status ENUM('active','inactive','unconfigured') DEFAULT 'unconfigured' AFTER evolution_token;
