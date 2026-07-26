CREATE TABLE IF NOT EXISTS `archy_core_updates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(50) NOT NULL,
  `requires_version` VARCHAR(50) DEFAULT NULL,
  `status` ENUM('pending', 'running', 'success', 'failed', 'rolled_back', 'skipped') NOT NULL DEFAULT 'pending',
  `dry_run` TINYINT(1) NOT NULL DEFAULT 0,
  `package_file` VARCHAR(255) DEFAULT NULL,
  `package_sha256` CHAR(64) DEFAULT NULL,
  `sql_file` VARCHAR(255) DEFAULT NULL,
  `sql_sha256` CHAR(64) DEFAULT NULL,
  `backup_path` VARCHAR(500) DEFAULT NULL,
  `started_at` DATETIME DEFAULT NULL,
  `finished_at` DATETIME DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `details` LONGTEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_archy_core_updates_version` (`version`),
  KEY `idx_archy_core_updates_status` (`status`),
  KEY `idx_archy_core_updates_started_at` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @archy_notices_table_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_notices'
);

SET @archy_notices_category_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_notices'
    AND COLUMN_NAME = 'category'
);

SET @archy_notices_category_sql := IF(
  @archy_notices_table_exists = 1 AND @archy_notices_category_exists = 0,
  'ALTER TABLE `archy_notices` ADD `category` VARCHAR(50) NOT NULL DEFAULT ''general'' AFTER `notice`',
  'SELECT 1'
);

PREPARE archy_notices_category_stmt FROM @archy_notices_category_sql;
EXECUTE archy_notices_category_stmt;
DEALLOCATE PREPARE archy_notices_category_stmt;

SET @archy_notices_source_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_notices'
    AND COLUMN_NAME = 'source'
);

SET @archy_notices_source_sql := IF(
  @archy_notices_table_exists = 1 AND @archy_notices_source_exists = 0,
  'ALTER TABLE `archy_notices` ADD `source` VARCHAR(32) NOT NULL DEFAULT ''local'' AFTER `category`',
  'SELECT 1'
);

PREPARE archy_notices_source_stmt FROM @archy_notices_source_sql;
EXECUTE archy_notices_source_stmt;
DEALLOCATE PREPARE archy_notices_source_stmt;

SET @archy_notices_priority_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_notices'
    AND COLUMN_NAME = 'priority'
);

SET @archy_notices_priority_sql := IF(
  @archy_notices_table_exists = 1 AND @archy_notices_priority_exists = 0,
  'ALTER TABLE `archy_notices` ADD `priority` VARCHAR(16) NOT NULL DEFAULT ''normal'' AFTER `source`',
  'SELECT 1'
);

PREPARE archy_notices_priority_stmt FROM @archy_notices_priority_sql;
EXECUTE archy_notices_priority_stmt;
DEALLOCATE PREPARE archy_notices_priority_stmt;

SET @archy_users_table_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_users'
);

SET @archy_users_password_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_users'
    AND COLUMN_NAME = 'password'
);

SET @archy_users_password_cleanup_sql := IF(
  @archy_users_table_exists = 1 AND @archy_users_password_exists = 1,
  'UPDATE `archy_users` SET `password` = '''' WHERE `password` IS NOT NULL AND `password` <> ''''',
  'SELECT 1'
);

PREPARE archy_users_password_cleanup_stmt FROM @archy_users_password_cleanup_sql;
EXECUTE archy_users_password_cleanup_stmt;
DEALLOCATE PREPARE archy_users_password_cleanup_stmt;

SET @archy_users_login_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_users'
    AND COLUMN_NAME = 'login'
);

SET @archy_users_login_drop_sql := IF(
  @archy_users_table_exists = 1 AND @archy_users_login_exists = 1,
  'ALTER TABLE `archy_users` DROP COLUMN `login`',
  'SELECT 1'
);

PREPARE archy_users_login_drop_stmt FROM @archy_users_login_drop_sql;
EXECUTE archy_users_login_drop_stmt;
DEALLOCATE PREPARE archy_users_login_drop_stmt;

CREATE TABLE IF NOT EXISTS `archy_login_otps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT UNSIGNED NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `selector_hash` CHAR(64) NOT NULL,
  `otp_hash` VARCHAR(255) NOT NULL,
  `remember` TINYINT(1) NOT NULL DEFAULT 0,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at` DATETIME NOT NULL,
  `consumed_at` DATETIME DEFAULT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_archy_login_otps_selector_hash` (`selector_hash`),
  KEY `idx_archy_login_otps_uid_consumed` (`uid`, `consumed_at`),
  KEY `idx_archy_login_otps_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
