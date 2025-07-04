-- Dropshipping Plugin Database Schema

-- WooCommerce Configurations Table
CREATE TABLE IF NOT EXISTS `dropshipping_woocommerce_configs` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` text NULL,
    `store_url` varchar(500) NOT NULL,
    `consumer_key` varchar(255) NOT NULL,
    `consumer_secret` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `last_sync_at` timestamp NULL DEFAULT NULL,
    `total_products` int(11) NOT NULL DEFAULT 0,
    `sync_status` enum('not_synced','syncing','completed','failed') NOT NULL DEFAULT 'not_synced',
    `created_by` bigint(20) UNSIGNED NOT NULL,
    `updated_by` bigint(20) UNSIGNED NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dropshipping_woocommerce_configs_is_active_index` (`is_active`),
    KEY `dropshipping_woocommerce_configs_sync_status_index` (`sync_status`),
    KEY `dropshipping_woocommerce_configs_created_by_foreign` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dropshipping Products Table
CREATE TABLE IF NOT EXISTS `dropshipping_products` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `woocommerce_config_id` bigint(20) UNSIGNED NOT NULL,
    `woocommerce_product_id` int(11) NOT NULL,
    `name` varchar(500) NOT NULL,
    `slug` varchar(500) NOT NULL,
    `description` longtext NULL,
    `short_description` text NULL,
    `price` decimal(10,2) NULL,
    `regular_price` decimal(10,2) NULL,
    `sale_price` decimal(10,2) NULL,
    `sku` varchar(100) NULL,
    `stock_quantity` int(11) NULL,
    `stock_status` enum('instock','outofstock','onbackorder') NOT NULL DEFAULT 'instock',
    `categories` longtext NULL,
    `tags` longtext NULL,
    `images` longtext NULL,
    `gallery_images` longtext NULL,
    `attributes` longtext NULL,
    `variations` longtext NULL,
    `weight` varchar(50) NULL,
    `dimensions` longtext NULL,
    `meta_data` longtext NULL,
    `status` enum('draft','pending','private','publish') NOT NULL DEFAULT 'publish',
    `featured` tinyint(1) NOT NULL DEFAULT 0,
    `catalog_visibility` enum('visible','catalog','search','hidden') NOT NULL DEFAULT 'visible',
    `date_created` timestamp NULL DEFAULT NULL,
    `date_modified` timestamp NULL DEFAULT NULL,
    `last_synced_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dropshipping_products_config_woo_id_unique` (`woocommerce_config_id`, `woocommerce_product_id`),
    KEY `dropshipping_products_woocommerce_config_id_foreign` (`woocommerce_config_id`),
    KEY `dropshipping_products_woocommerce_product_id_index` (`woocommerce_product_id`),
    KEY `dropshipping_products_status_index` (`status`),
    KEY `dropshipping_products_stock_status_index` (`stock_status`),
    KEY `dropshipping_products_featured_index` (`featured`),
    KEY `dropshipping_products_sku_index` (`sku`),
    CONSTRAINT `dropshipping_products_woocommerce_config_id_foreign` FOREIGN KEY (`woocommerce_config_id`) REFERENCES `dropshipping_woocommerce_configs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Import History Table
CREATE TABLE IF NOT EXISTS `dropshipping_product_import_history` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `tenant_id` varchar(255) NOT NULL,
    `woocommerce_store_id` bigint(20) UNSIGNED NULL,
    `woocommerce_config_id` bigint(20) UNSIGNED NOT NULL,
    `woocommerce_product_id` bigint(20) UNSIGNED NULL,
    `dropshipping_product_id` bigint(20) UNSIGNED NULL,
    `local_product_id` bigint(20) UNSIGNED NULL,
    `import_type` enum('single','bulk','auto_sync','manual') NOT NULL DEFAULT 'single',
    `import_status` enum('pending','completed','failed') NOT NULL DEFAULT 'pending',
    `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
    `imported_data` longtext NULL,
    `pricing_adjustments` longtext NULL,
    `error_message` text NULL,
    `import_settings` longtext NULL,
    `imported_at` timestamp NULL DEFAULT NULL,
    `imported_by` bigint(20) UNSIGNED NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dropshipping_import_history_tenant_id_index` (`tenant_id`),
    KEY `dropshipping_import_history_woocommerce_store_id_index` (`woocommerce_store_id`),
    KEY `dropshipping_import_history_woocommerce_config_id_foreign` (`woocommerce_config_id`),
    KEY `dropshipping_import_history_woocommerce_product_id_index` (`woocommerce_product_id`),
    KEY `dropshipping_import_history_dropshipping_product_id_foreign` (`dropshipping_product_id`),
    KEY `dropshipping_import_history_import_status_index` (`import_status`),
    KEY `dropshipping_import_history_status_index` (`status`),
    KEY `dropshipping_import_history_import_type_index` (`import_type`),
    KEY `dropshipping_import_history_imported_by_foreign` (`imported_by`),
    CONSTRAINT `dropshipping_import_history_woocommerce_config_id_foreign` FOREIGN KEY (`woocommerce_config_id`) REFERENCES `dropshipping_woocommerce_configs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plan Limits Table
CREATE TABLE IF NOT EXISTS `dropshipping_plan_limits` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `package_id` bigint(20) UNSIGNED NOT NULL,
    `monthly_import_limit` int(11) NOT NULL DEFAULT 100 COMMENT '-1 for unlimited',
    `total_import_limit` int(11) NOT NULL DEFAULT -1 COMMENT '-1 for unlimited',
    `bulk_import_limit` int(11) NOT NULL DEFAULT 20 COMMENT '-1 for unlimited',
    `auto_sync_enabled` tinyint(1) NOT NULL DEFAULT 0,
    `pricing_markup_min` decimal(5,2) NULL COMMENT 'Minimum markup percentage',
    `pricing_markup_max` decimal(5,2) NULL COMMENT 'Maximum markup percentage',
    `allowed_categories` longtext NULL COMMENT 'JSON array of allowed category IDs',
    `restricted_categories` longtext NULL COMMENT 'JSON array of restricted category IDs',
    `settings` longtext NULL COMMENT 'Additional settings JSON',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dropshipping_plan_limits_package_id_unique` (`package_id`),
    KEY `dropshipping_plan_limits_package_id_index` (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Plugin Settings Table
CREATE TABLE IF NOT EXISTS `dropshipping_settings` (
    `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `key` varchar(255) NOT NULL,
    `value` longtext NULL,
    `type` enum('string','integer','boolean','json','array') NOT NULL DEFAULT 'string',
    `description` text NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `dropshipping_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT IGNORE INTO `dropshipping_settings` (`key`, `value`, `type`, `description`) VALUES
('auto_sync_interval', '24', 'integer', 'Auto sync interval in hours'),
('default_markup_percentage', '20', 'integer', 'Default markup percentage for imported products'),
('enable_auto_price_update', '0', 'boolean', 'Enable automatic price updates from WooCommerce'),
('enable_auto_stock_update', '1', 'boolean', 'Enable automatic stock updates from WooCommerce'),
('import_product_reviews', '0', 'boolean', 'Import product reviews along with products'),
('max_sync_products_per_batch', '50', 'integer', 'Maximum products to sync per batch'),
('notification_email', '', 'string', 'Email for import notifications'),
('enable_import_notifications', '1', 'boolean', 'Send notifications for import activities');

-- Note: Plan limits will be set when the plugin is activated for specific packages
-- This avoids referencing main database tables during tenant database creation

-- Note: Plugin registration is handled by the insertThirdPartyPluginTables method
-- to ensure compatibility with tenant database structure 