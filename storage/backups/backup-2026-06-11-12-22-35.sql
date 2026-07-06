-- Soma POS Database Backup
-- Generated: 2026-06-11 12:22:35
-- Database: soma_pos

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `cash_drawer_sessions`;
CREATE TABLE `cash_drawer_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `opening_balance` decimal(10,2) NOT NULL,
  `closing_balance` decimal(10,2) DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_drawer_sessions_user_id_foreign` (`user_id`),
  CONSTRAINT `cash_drawer_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('1', 'Electronics', 'electronics', NULL, '2026-06-10 00:16:34', '2026-06-10 00:16:34');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('2', 'Clothing', 'clothing', NULL, '2026-06-10 00:16:34', '2026-06-10 00:16:34');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('3', 'Footwear', 'footwear', NULL, '2026-06-10 00:16:35', '2026-06-10 00:16:35');
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES ('4', 'Accessories', 'accessories', NULL, '2026-06-10 00:16:35', '2026-06-10 00:16:35');

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `loyalty_card` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_spent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_email_unique` (`email`),
  UNIQUE KEY `customers_loyalty_card_unique` (`loyalty_card`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('1', '0001_01_01_000000_create_users_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('2', '0001_01_01_000001_create_cache_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('3', '0001_01_01_000002_create_jobs_table', '1');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('4', '2026_06_09_190935_create_products_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('5', '2026_06_09_190937_create_customers_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('6', '2026_06_09_190938_create_sales_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('7', '2026_06_09_190939_create_sale_items_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('8', '2026_06_09_190941_create_cash_drawer_sessions_table', '2');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('9', '2026_06_10_000910_create_categories_table', '3');
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES ('10', '2026_06_11_095617_add_role_to_users_table', '4');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `low_stock_threshold` int NOT NULL DEFAULT '5',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_barcode_index` (`barcode`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('1', 'Wireless Headphones', 'WH-001', NULL, '79.99', '45.00', '23', '5', 'Electronics', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-10 19:22:32');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('2', 'Running Shoes', 'RS-002', NULL, '89.99', '52.00', '12', '5', 'Footwear', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-10 19:22:32');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('3', 'Smart Watch', 'SW-003', NULL, '199.99', '120.00', '9', '5', 'Electronics', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-11 08:38:19');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('4', 'T-Shirt (Black)', 'TS-004', NULL, '24.99', '12.00', '49', '5', 'Clothing', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-10 09:29:46');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('5', 'Jeans (Blue)', 'JN-005', NULL, '59.99', '30.00', '23', '5', 'Clothing', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-10 19:22:32');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('6', 'Wireless Mouse', 'WM-006', NULL, '29.99', '15.00', '0', '5', 'Electronics', NULL, NULL, '1', '2026-06-09 19:13:34', '2026-06-09 20:14:06');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('7', 'Bluetooth Speaker', 'BS-007', NULL, '49.99', '28.00', '0', '5', 'Electronics', NULL, NULL, '1', '2026-06-09 19:13:35', '2026-06-09 23:32:03');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('8', 'Phone Charger', 'PC-008', NULL, '19.99', '8.00', '0', '5', 'Accessories', NULL, NULL, '1', '2026-06-09 19:13:35', '2026-06-10 19:22:32');
INSERT INTO `products` (`id`, `name`, `sku`, `barcode`, `price`, `cost`, `stock_quantity`, `low_stock_threshold`, `category`, `category_id`, `image`, `is_active`, `created_at`, `updated_at`) VALUES ('9', 'Backpack', 'BP-009', NULL, '45.99', '22.00', '0', '5', 'Bags', NULL, NULL, '1', '2026-06-09 19:13:35', '2026-06-09 20:13:47');

DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('1', '1', '7', '1', '49.99', '0.00', '49.99', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('2', '1', '9', '2', '45.99', '0.00', '91.98', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('3', '1', '5', '3', '59.99', '0.00', '179.97', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('4', '1', '2', '1', '89.99', '0.00', '89.99', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('5', '1', '1', '1', '79.99', '0.00', '79.99', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('6', '2', '5', '1', '59.99', '0.00', '59.99', '2026-06-09 20:09:49', '2026-06-09 20:09:49');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('7', '3', '5', '1', '59.99', '0.00', '59.99', '2026-06-09 20:10:28', '2026-06-09 20:10:28');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('8', '4', '9', '6', '45.99', '0.00', '275.94', '2026-06-09 20:13:47', '2026-06-09 20:13:47');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('9', '5', '6', '5', '29.99', '0.00', '149.95', '2026-06-09 20:14:06', '2026-06-09 20:14:06');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('10', '6', '7', '2', '49.99', '0.00', '99.98', '2026-06-09 23:32:03', '2026-06-09 23:32:03');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('11', '7', '2', '1', '89.99', '0.00', '89.99', '2026-06-09 23:34:45', '2026-06-09 23:34:45');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('12', '8', '8', '1', '19.99', '0.00', '19.99', '2026-06-10 09:29:46', '2026-06-10 09:29:46');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('13', '8', '4', '1', '24.99', '0.00', '24.99', '2026-06-10 09:29:46', '2026-06-10 09:29:46');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('14', '9', '8', '1', '19.99', '0.00', '19.99', '2026-06-10 19:22:32', '2026-06-10 19:22:32');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('15', '9', '5', '2', '59.99', '0.00', '119.98', '2026-06-10 19:22:32', '2026-06-10 19:22:32');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('16', '9', '2', '1', '89.99', '0.00', '89.99', '2026-06-10 19:22:32', '2026-06-10 19:22:32');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('17', '9', '1', '1', '79.99', '0.00', '79.99', '2026-06-10 19:22:32', '2026-06-10 19:22:32');
INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `total`, `created_at`, `updated_at`) VALUES ('18', '10', '3', '1', '199.99', '0.00', '199.99', '2026-06-11 08:38:19', '2026-06-11 08:38:19');

DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `terminal_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TERM-01',
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) NOT NULL,
  `change_due` decimal(10,2) NOT NULL DEFAULT '0.00',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `sale_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
  KEY `sales_user_id_foreign` (`user_id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('1', 'SOMA-20260609-0001', '1', NULL, 'TERM-01', '491.92', '0.00', NULL, '78.71', '570.63', '580.00', '9.37', 'cash', 'completed', NULL, '2026-06-09 20:09:10', '2026-06-09 20:09:10', '2026-06-09 20:09:10');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('2', 'SOMA-20260609-0002', '1', NULL, 'TERM-01', '59.99', '0.00', NULL, '9.60', '69.59', '69.59', '0.00', 'card', 'completed', NULL, '2026-06-09 20:09:49', '2026-06-09 20:09:49', '2026-06-09 20:09:49');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('3', 'SOMA-20260609-0003', '1', NULL, 'TERM-01', '59.99', '0.00', NULL, '9.60', '69.59', '69.59', '0.00', 'mobile_money', 'completed', NULL, '2026-06-09 20:10:28', '2026-06-09 20:10:28', '2026-06-09 20:10:28');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('4', 'SOMA-20260609-0004', '1', NULL, 'TERM-01', '275.94', '0.00', NULL, '44.15', '320.09', '320.09', '0.00', 'mobile_money', 'completed', NULL, '2026-06-09 20:13:47', '2026-06-09 20:13:47', '2026-06-09 20:13:47');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('5', 'SOMA-20260609-0005', '1', NULL, 'TERM-01', '149.95', '0.00', NULL, '23.99', '173.94', '173.94', '0.00', 'card', 'completed', NULL, '2026-06-09 20:14:06', '2026-06-09 20:14:06', '2026-06-09 20:14:06');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('6', 'SOMA-20260609-0006', '1', NULL, 'TERM-01', '99.98', '0.00', NULL, '16.00', '115.98', '115.98', '0.00', 'card', 'completed', NULL, '2026-06-09 23:32:03', '2026-06-09 23:32:03', '2026-06-09 23:32:03');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('7', 'SOMA-20260609-0007', '1', NULL, 'TERM-01', '89.99', '0.00', NULL, '14.40', '104.39', '104.39', '0.00', 'mobile_money', 'completed', NULL, '2026-06-09 23:34:45', '2026-06-09 23:34:45', '2026-06-09 23:34:45');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('8', 'SOMA-20260610-0008', '1', NULL, 'TERM-01', '44.98', '0.00', NULL, '7.20', '52.18', '52.18', '0.00', 'card', 'completed', NULL, '2026-06-10 09:29:46', '2026-06-10 09:29:46', '2026-06-10 09:29:46');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('9', 'SOMA-20260610-0009', '1', NULL, 'TERM-01', '309.95', '0.00', NULL, '49.59', '359.54', '360.00', '0.46', 'cash', 'completed', NULL, '2026-06-10 19:22:32', '2026-06-10 19:22:32', '2026-06-10 19:22:32');
INSERT INTO `sales` (`id`, `invoice_no`, `user_id`, `customer_id`, `terminal_id`, `subtotal`, `discount`, `discount_type`, `tax`, `total`, `paid`, `change_due`, `payment_method`, `status`, `notes`, `sale_date`, `created_at`, `updated_at`) VALUES ('10', 'SOMA-20260611-0010', '1', NULL, 'TERM-01', '199.99', '0.00', NULL, '32.00', '231.99', '231.99', '0.00', 'card', 'completed', NULL, '2026-06-11 08:38:19', '2026-06-11 08:38:19', '2026-06-11 08:38:19');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('aitts35O4iv3DiGIFl6VwYqImlbR1LlyduXmIl3x', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT2JEN1l1TUp0aDgyekRmNGZic2wzN3BVSXVBSVF5dWFsbndqb0dLcyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9zb21hLXBvcy50ZXN0L2FkbWluL2RhdGFiYXNlLWJhY2t1cCI7czo1OiJyb3V0ZSI7czoyMToiYWRtaW4uZGF0YWJhc2UtYmFja3VwIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', '1781169753');
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES ('qzSuJ18KaSZIknf8oKk54vyAuzr3BqNvQkzhvSHF', '2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:127.0) Gecko/20100101 Firefox/127.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZlFEd1VmQ0FCOWozMTJpQnlrTmRmNVRiQWE1S055QktrNWF0VXNSWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDk6Imh0dHA6Ly9zb21hLXBvcy50ZXN0L3JlcG9ydHMvZW1wbG95ZWUtcGVyZm9ybWFuY2UiO3M6NToicm91dGUiO3M6Mjg6InJlcG9ydHMuZW1wbG95ZWUtcGVyZm9ybWFuY2UiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', '1781163137');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','employee') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'employee',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES ('1', 'Ian Kamau', 'iankamnganga@gmail.com', NULL, '$2y$12$qJmJrKaJm6Z1wBUNHGT4PuGtjJTqHu0KORTa28VqQH9rebXAuyMSm', 'employee', NULL, '2026-06-09 19:19:33', '2026-06-10 00:43:30');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES ('2', 'System Admin', 'admin@somapos.com', NULL, '$2y$12$Cp5h7yF3dgPLpMsULWHES.y2gi0TDGcNoZ.lruV.Wqw02tTMvS1Oa', 'admin', NULL, '2026-06-11 10:08:23', '2026-06-11 10:08:23');

SET FOREIGN_KEY_CHECKS=1;
