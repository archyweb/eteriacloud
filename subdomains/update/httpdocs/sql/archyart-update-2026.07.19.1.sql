CREATE TABLE IF NOT EXISTS `archy_google_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `google_account_id` VARCHAR(255) DEFAULT NULL,
  `google_email` VARCHAR(255) DEFAULT NULL,
  `access_token` TEXT DEFAULT NULL,
  `refresh_token` TEXT DEFAULT NULL,
  `token_type` VARCHAR(40) NOT NULL DEFAULT 'Bearer',
  `expires_at` DATETIME DEFAULT NULL,
  `scope` TEXT DEFAULT NULL,
  `selected_calendar_id` VARCHAR(255) DEFAULT NULL,
  `selected_calendar_name` VARCHAR(255) DEFAULT NULL,
  `sync_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `last_sync_token` TEXT DEFAULT NULL,
  `last_synced_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_archy_google_accounts_user` (`user_id`),
  KEY `idx_archy_google_accounts_google_account` (`google_account_id`),
  KEY `idx_archy_google_accounts_sync_enabled` (`sync_enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET @archy_calendar_table_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
);

SET @archy_calendar_source_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'calendar_source'
);

SET @archy_calendar_source_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_source_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `calendar_source` VARCHAR(30) NOT NULL DEFAULT ''local'' AFTER `calendar_class`',
  'SELECT 1'
);

PREPARE archy_calendar_source_stmt FROM @archy_calendar_source_sql;
EXECUTE archy_calendar_source_stmt;
DEALLOCATE PREPARE archy_calendar_source_stmt;

SET @archy_calendar_google_account_id_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_account_id'
);

SET @archy_calendar_google_account_id_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_account_id_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_account_id` INT UNSIGNED DEFAULT NULL AFTER `calendar_source`',
  'SELECT 1'
);

PREPARE archy_calendar_google_account_id_stmt FROM @archy_calendar_google_account_id_sql;
EXECUTE archy_calendar_google_account_id_stmt;
DEALLOCATE PREPARE archy_calendar_google_account_id_stmt;

SET @archy_calendar_google_calendar_id_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_calendar_id'
);

SET @archy_calendar_google_calendar_id_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_calendar_id_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_calendar_id` VARCHAR(255) DEFAULT NULL AFTER `google_account_id`',
  'SELECT 1'
);

PREPARE archy_calendar_google_calendar_id_stmt FROM @archy_calendar_google_calendar_id_sql;
EXECUTE archy_calendar_google_calendar_id_stmt;
DEALLOCATE PREPARE archy_calendar_google_calendar_id_stmt;

SET @archy_calendar_google_event_id_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_event_id'
);

SET @archy_calendar_google_event_id_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_event_id_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_event_id` VARCHAR(255) DEFAULT NULL AFTER `google_calendar_id`',
  'SELECT 1'
);

PREPARE archy_calendar_google_event_id_stmt FROM @archy_calendar_google_event_id_sql;
EXECUTE archy_calendar_google_event_id_stmt;
DEALLOCATE PREPARE archy_calendar_google_event_id_stmt;

SET @archy_calendar_google_etag_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_etag'
);

SET @archy_calendar_google_etag_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_etag_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_etag` VARCHAR(255) DEFAULT NULL AFTER `google_event_id`',
  'SELECT 1'
);

PREPARE archy_calendar_google_etag_stmt FROM @archy_calendar_google_etag_sql;
EXECUTE archy_calendar_google_etag_stmt;
DEALLOCATE PREPARE archy_calendar_google_etag_stmt;

SET @archy_calendar_google_html_link_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_html_link'
);

SET @archy_calendar_google_html_link_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_html_link_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_html_link` VARCHAR(500) DEFAULT NULL AFTER `google_etag`',
  'SELECT 1'
);

PREPARE archy_calendar_google_html_link_stmt FROM @archy_calendar_google_html_link_sql;
EXECUTE archy_calendar_google_html_link_stmt;
DEALLOCATE PREPARE archy_calendar_google_html_link_stmt;

SET @archy_calendar_google_sync_status_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_sync_status'
);

SET @archy_calendar_google_sync_status_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_sync_status_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_sync_status` VARCHAR(30) NOT NULL DEFAULT ''pending'' AFTER `google_html_link`',
  'SELECT 1'
);

PREPARE archy_calendar_google_sync_status_stmt FROM @archy_calendar_google_sync_status_sql;
EXECUTE archy_calendar_google_sync_status_stmt;
DEALLOCATE PREPARE archy_calendar_google_sync_status_stmt;

SET @archy_calendar_google_synced_at_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_synced_at'
);

SET @archy_calendar_google_synced_at_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_synced_at_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_synced_at` DATETIME DEFAULT NULL AFTER `google_sync_status`',
  'SELECT 1'
);

PREPARE archy_calendar_google_synced_at_stmt FROM @archy_calendar_google_synced_at_sql;
EXECUTE archy_calendar_google_synced_at_stmt;
DEALLOCATE PREPARE archy_calendar_google_synced_at_stmt;

SET @archy_calendar_google_last_error_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND COLUMN_NAME = 'google_last_error'
);

SET @archy_calendar_google_last_error_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_last_error_exists = 0,
  'ALTER TABLE `archy_calendar` ADD `google_last_error` TEXT DEFAULT NULL AFTER `google_synced_at`',
  'SELECT 1'
);

PREPARE archy_calendar_google_last_error_stmt FROM @archy_calendar_google_last_error_sql;
EXECUTE archy_calendar_google_last_error_stmt;
DEALLOCATE PREPARE archy_calendar_google_last_error_stmt;

SET @archy_calendar_google_event_idx_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND INDEX_NAME = 'idx_archy_calendar_google_event'
);

SET @archy_calendar_google_event_idx_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_event_idx_exists = 0,
  'ALTER TABLE `archy_calendar` ADD KEY `idx_archy_calendar_google_event` (`google_account_id`, `google_calendar_id`, `google_event_id`)',
  'SELECT 1'
);

PREPARE archy_calendar_google_event_idx_stmt FROM @archy_calendar_google_event_idx_sql;
EXECUTE archy_calendar_google_event_idx_stmt;
DEALLOCATE PREPARE archy_calendar_google_event_idx_stmt;

SET @archy_calendar_google_sync_status_idx_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'archy_calendar'
    AND INDEX_NAME = 'idx_archy_calendar_google_sync_status'
);

SET @archy_calendar_google_sync_status_idx_sql := IF(
  @archy_calendar_table_exists = 1 AND @archy_calendar_google_sync_status_idx_exists = 0,
  'ALTER TABLE `archy_calendar` ADD KEY `idx_archy_calendar_google_sync_status` (`google_sync_status`, `google_synced_at`)',
  'SELECT 1'
);

PREPARE archy_calendar_google_sync_status_idx_stmt FROM @archy_calendar_google_sync_status_idx_sql;
EXECUTE archy_calendar_google_sync_status_idx_stmt;
DEALLOCATE PREPARE archy_calendar_google_sync_status_idx_stmt;
