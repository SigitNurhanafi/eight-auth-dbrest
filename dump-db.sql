-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.1.2-MariaDB - MariaDB Server
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table laravel.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravel.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table laravel.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravel.migrations: ~6 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2026_02_17_064153_add_role_to_users_table', 1),
	(6, '2026_02_17_141055_add_soft_deletes_to_users_table', 1);

-- Dumping structure for table laravel.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravel.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table laravel.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravel.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table laravel.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table laravel.users: ~12 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Admin User', 'admin@example.com', 'admin', '2026-02-17 07:36:25', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'YiXRstKmsY', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(2, 'Regular User', 'user@example.com', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'BODTM8WkXT', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(3, 'Prof. Porter Erdman V', 'nicholaus49@example.com', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', '3iE1SPwAtk', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(4, 'Breanna Mertz', 'precious.kassulke@example.org', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'DWAjqMhTBP', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(5, 'Camden Collins MD', 'ernie.vandervort@example.net', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'Db4LONsIzr', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(6, 'Bertram Ziemann', 'clovis.dubuque@example.org', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'HQuVm78pz8', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(7, 'Brycen Lindgren', 'rollin.bogisich@example.net', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'jRWlVv1ox1', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(8, 'Mrs. Velva Swaniawski MD', 'koelpin.brooklyn@example.net', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'aLiEZ3yjOf', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(9, 'Braxton Welch', 'bsipes@example.org', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'nsm54c9qow', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(10, 'Jordane Little', 'pzboncak@example.net', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'un6WR5jIeS', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(11, 'Lewis Brekke', 'vvon@example.com', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'EGvzk9wK8W', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL),
	(12, 'Prof. Bradley Jacobs', 'amiya.bins@example.com', 'user', '2026-02-17 07:36:26', '$2y$12$RTOqitqZkGzSr5BGVPIxQ.gA5WOMz/wXXjmQICpnHTUzPjgQTj5dK', 'hwl4jKwieZ', '2026-02-17 07:36:26', '2026-02-17 07:36:26', NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
