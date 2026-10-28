SET @archy_user_totp_table_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
);

SET @archy_user_totp_create_sql := IF(
  @archy_user_totp_table_exists = 0,
  'CREATE TABLE `archy_user_totp` (
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
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
  'SELECT 1'
);

PREPARE archy_user_totp_create_stmt FROM @archy_user_totp_create_sql;
EXECUTE archy_user_totp_create_stmt;
DEALLOCATE PREPARE archy_user_totp_create_stmt;

SET @archy_user_totp_pending_secret_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND COLUMN_NAME = 'pending_secret'
);

SET @archy_user_totp_pending_secret_sql := IF(
  @archy_user_totp_pending_secret_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD `pending_secret` VARCHAR(128) DEFAULT NULL AFTER `secret`',
  'SELECT 1'
);

PREPARE archy_user_totp_pending_secret_stmt FROM @archy_user_totp_pending_secret_sql;
EXECUTE archy_user_totp_pending_secret_stmt;
DEALLOCATE PREPARE archy_user_totp_pending_secret_stmt;

SET @archy_user_totp_last_used_counter_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND COLUMN_NAME = 'last_used_counter'
);

SET @archy_user_totp_last_used_counter_sql := IF(
  @archy_user_totp_last_used_counter_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD `last_used_counter` BIGINT UNSIGNED DEFAULT NULL AFTER `confirmed_at`',
  'SELECT 1'
);

PREPARE archy_user_totp_last_used_counter_stmt FROM @archy_user_totp_last_used_counter_sql;
EXECUTE archy_user_totp_last_used_counter_stmt;
DEALLOCATE PREPARE archy_user_totp_last_used_counter_stmt;

SET @archy_user_totp_failed_attempts_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND COLUMN_NAME = 'failed_attempts'
);

SET @archy_user_totp_failed_attempts_sql := IF(
  @archy_user_totp_failed_attempts_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD `failed_attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `last_used_counter`',
  'SELECT 1'
);

PREPARE archy_user_totp_failed_attempts_stmt FROM @archy_user_totp_failed_attempts_sql;
EXECUTE archy_user_totp_failed_attempts_stmt;
DEALLOCATE PREPARE archy_user_totp_failed_attempts_stmt;

SET @archy_user_totp_locked_until_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND COLUMN_NAME = 'locked_until'
);

SET @archy_user_totp_locked_until_sql := IF(
  @archy_user_totp_locked_until_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD `locked_until` DATETIME DEFAULT NULL AFTER `failed_attempts`',
  'SELECT 1'
);

PREPARE archy_user_totp_locked_until_stmt FROM @archy_user_totp_locked_until_sql;
EXECUTE archy_user_totp_locked_until_stmt;
DEALLOCATE PREPARE archy_user_totp_locked_until_stmt;

SET @archy_user_totp_enabled_idx_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND INDEX_NAME = 'idx_archy_user_totp_enabled'
);

SET @archy_user_totp_enabled_idx_sql := IF(
  @archy_user_totp_enabled_idx_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD KEY `idx_archy_user_totp_enabled` (`enabled`)',
  'SELECT 1'
);

PREPARE archy_user_totp_enabled_idx_stmt FROM @archy_user_totp_enabled_idx_sql;
EXECUTE archy_user_totp_enabled_idx_stmt;
DEALLOCATE PREPARE archy_user_totp_enabled_idx_stmt;

SET @archy_user_totp_locked_until_idx_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_user_totp'
    AND INDEX_NAME = 'idx_archy_user_totp_locked_until'
);

SET @archy_user_totp_locked_until_idx_sql := IF(
  @archy_user_totp_locked_until_idx_exists = 0,
  'ALTER TABLE `archy_user_totp` ADD KEY `idx_archy_user_totp_locked_until` (`locked_until`)',
  'SELECT 1'
);

PREPARE archy_user_totp_locked_until_idx_stmt FROM @archy_user_totp_locked_until_idx_sql;
EXECUTE archy_user_totp_locked_until_idx_stmt;
DEALLOCATE PREPARE archy_user_totp_locked_until_idx_stmt;
