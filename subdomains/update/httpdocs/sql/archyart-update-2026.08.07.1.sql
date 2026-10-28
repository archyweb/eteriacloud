CREATE TABLE IF NOT EXISTS `archy_user_totp` (
  `uid` INT UNSIGNED NOT NULL,
  `secret` VARCHAR(128) DEFAULT NULL,
  `pending_secret` VARCHAR(128) DEFAULT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `confirmed_at` DATETIME DEFAULT NULL,
  `last_used_counter` BIGINT UNSIGNED DEFAULT NULL,
  `failed_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `idx_archy_user_totp_enabled` (`enabled`),
  KEY `idx_archy_user_totp_locked_until` (`locked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
