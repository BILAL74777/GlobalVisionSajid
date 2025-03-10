-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2025 at 09:10 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goritmi_ttc_global`
--

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text,
  `commission` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `phone`, `email`, `address`, `commission`, `created_at`, `updated_at`) VALUES
(1, 'Wasim Ahmad', '03215112', 'abc@test.example', 'Adddress...', 12, '2025-03-10 04:02:42', '2025-03-10 05:24:17'),
(2, 'Bilal Hussain', '0312458544', NULL, NULL, 10, '2025-03-10 05:27:23', '2025-03-10 05:27:23'),
(3, 'Wajid Ali', '0324584555', NULL, NULL, 7, '2025-03-10 05:27:35', '2025-03-10 05:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `employee_accounts`
--

CREATE TABLE `employee_accounts` (
  `id` int NOT NULL,
  `visa_id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `cash_in` int NOT NULL,
  `cash_out` int NOT NULL,
  `employee_commission` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employee_accounts`
--

INSERT INTO `employee_accounts` (`id`, `visa_id`, `employee_id`, `cash_in`, `cash_out`, `employee_commission`, `amount`, `created_at`, `updated_at`) VALUES
(17, 37, 1, 150000, 300000, '12%', -18000.00, '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(18, 37, 2, 150000, 300000, '10%', -15000.00, '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(19, 37, 3, 150000, 300000, '7%', -10500.00, '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(20, 38, 1, 50000, 100000, '12%', -6000.00, '2025-03-10 15:47:37', '2025-03-10 15:47:37'),
(21, 38, 2, 50000, 100000, '10%', -5000.00, '2025-03-10 15:47:37', '2025-03-10 15:47:37'),
(22, 38, 3, 50000, 100000, '7%', -3500.00, '2025-03-10 15:47:37', '2025-03-10 15:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_visas`
--

CREATE TABLE `family_visas` (
  `id` bigint UNSIGNED NOT NULL,
  `visa_id` bigint UNSIGNED DEFAULT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visa_fee` int NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pak_visa_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_visas`
--

INSERT INTO `family_visas` (`id`, `visa_id`, `full_name`, `phone_number`, `status`, `visa_fee`, `amount`, `tracking_id`, `gmail`, `pak_visa_password`, `gmail_password`, `gender`, `date`, `created_at`, `updated_at`) VALUES
(12, 37, 'Gul Bano', '0312522222', 'Initial', 100000, 50000.00, '12314123412', 'gul@test.exmaple', '12314123412', '12314123412', 'Female', '2025-03-11', '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(13, 37, 'Ahmad Raza', '0312522222', 'Initial', 100000, 50000.00, '12314123412', 'gul@test.exmaple', '12314123412', '1231412341212314123412', 'Male', '2025-03-11', '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(14, 47, 'Muhammad Jamal', '232551144', 'Initial', 1856200, 253600.00, '2113123', 's@teste.exe', '455554646', '14554545', 'Male', '2025-03-11', '2025-03-10 16:04:12', '2025-03-10 16:04:12');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(7, '2014_10_12_000000_create_users_table', 1),
(8, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(9, '2019_08_19_000000_create_failed_jobs_table', 1),
(10, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(11, '2025_02_24_194641_create_visas_table', 1),
(12, '2025_02_24_194643_create_family_visas_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(4, 'Wasim Khan', 'test@wasim.exe', '0312458555', 'Abc, street # 123', '2025-03-10 15:54:01', '2025-03-10 15:54:19');

-- --------------------------------------------------------

--
-- Table structure for table `referral_accounts`
--

CREATE TABLE `referral_accounts` (
  `id` int NOT NULL,
  `visa_id` int NOT NULL,
  `cash_in` int NOT NULL,
  `cash_out` int NOT NULL,
  `refferal_commission` int DEFAULT NULL,
  `referral_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `referral_accounts`
--

INSERT INTO `referral_accounts` (`id`, `visa_id`, `cash_in`, `cash_out`, `refferal_commission`, `referral_id`, `created_at`, `updated_at`) VALUES
(6, 42, 100000, 50000, 5000, 4, '2025-03-10 15:59:29', '2025-03-10 15:59:29'),
(7, 47, 253600, 1856200, 55600, 4, '2025-03-10 16:04:12', '2025-03-10 16:04:12');

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int NOT NULL,
  `file_original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size` int DEFAULT NULL,
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visas`
--

CREATE TABLE `visas` (
  `id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visa_fee` int NOT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pak_visa_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gmail_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `entry_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `family_members` int DEFAULT NULL,
  `referral` int DEFAULT NULL,
  `referral_commission` int DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_commission` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visas`
--

INSERT INTO `visas` (`id`, `full_name`, `phone_number`, `status`, `visa_fee`, `amount`, `tracking_id`, `gmail`, `pak_visa_password`, `gmail_password`, `gender`, `date`, `entry_type`, `family_name`, `family_members`, `referral`, `referral_commission`, `employee_id`, `employee_commission`, `created_at`, `updated_at`) VALUES
(37, 'Gul Ahmad', '03124586555', 'Initial', 100000, 50000.00, '123456', 'gul@test.exmaple', '123456', '123456', 'Other', '2025-03-11', 'Family', 'Family One', 3, NULL, NULL, NULL, NULL, '2025-03-10 15:45:05', '2025-03-10 15:45:05'),
(38, 'Taimoor Khan', '012455854', 'Initial', 100000, 50000.00, '123555', 'test@exe.co', '12452', '4221115', 'Male', '2025-03-11', 'Individual', NULL, 1, NULL, NULL, NULL, NULL, '2025-03-10 15:47:37', '2025-03-10 15:47:37'),
(42, 'Markram Angel', '0125455221', 'Initial', 50000, 100000.00, '1223455', 'markram@test.exe', '1234555', '123445', 'Male', '2025-03-11', 'Individual', NULL, 1, NULL, NULL, NULL, NULL, '2025-03-10 15:59:29', '2025-03-10 15:59:29'),
(43, 'Saqib Ali', '032545555', 'Initial', 1856200, 253600.00, '5465665', 's@teste.exe', '121346456', '5464645', 'Male', '2025-03-11', 'Family', 'Family Two', 2, NULL, NULL, NULL, NULL, '2025-03-10 16:03:07', '2025-03-10 16:03:07'),
(44, 'Saqib Ali', '032545555', 'Initial', 1856200, 253600.00, '5465665', 's@teste.exe', '121346456', '5464645', 'Male', '2025-03-11', 'Family', 'Family Two', 2, NULL, NULL, NULL, NULL, '2025-03-10 16:03:42', '2025-03-10 16:03:42'),
(45, 'Saqib Ali', '032545555', 'Initial', 1856200, 253600.00, '5465665', 's@teste.exe', '121346456', '5464645', 'Male', '2025-03-11', 'Family', 'Family Two', 2, NULL, NULL, NULL, NULL, '2025-03-10 16:03:44', '2025-03-10 16:03:44'),
(46, 'Saqib Ali', '032545555', 'Initial', 1856200, 253600.00, '5465665', 's@teste.exe', '121346456', '5464645', 'Male', '2025-03-11', 'Family', 'Family Two', 2, NULL, NULL, NULL, NULL, '2025-03-10 16:03:51', '2025-03-10 16:03:51'),
(47, 'Saqib Ali', '032545555', 'Initial', 1856200, 253600.00, '5465665', 's@teste.exe', '121346456', '5464645', 'Male', '2025-03-11', 'Family', 'Family Two', 2, NULL, NULL, NULL, NULL, '2025-03-10 16:04:12', '2025-03-10 16:04:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `family_visas`
--
ALTER TABLE `family_visas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_visas_visa_id_foreign` (`visa_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `referral_accounts`
--
ALTER TABLE `referral_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `visas`
--
ALTER TABLE `visas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_accounts`
--
ALTER TABLE `employee_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_visas`
--
ALTER TABLE `family_visas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `referral_accounts`
--
ALTER TABLE `referral_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visas`
--
ALTER TABLE `visas`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `family_visas`
--
ALTER TABLE `family_visas`
  ADD CONSTRAINT `family_visas_visa_id_foreign` FOREIGN KEY (`visa_id`) REFERENCES `visas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
