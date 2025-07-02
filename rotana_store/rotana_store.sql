-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 02 يوليو 2025 الساعة 03:05
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rotana_store`
--

-- --------------------------------------------------------

--
-- بنية الجدول `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `activities`
--

INSERT INTO `activities` (`id`, `user_id`, `action`, `description`, `image_url`, `ip_address`, `created_at`) VALUES
(27, 1, 'delete_product', 'تم حذف منتج: لابتوب MacBook Air', NULL, '::1', '2025-07-01 20:15:16'),
(28, 1, 'delete_product', 'تم حذف منتج: لابتوب HP Pavilion', NULL, '::1', '2025-07-01 20:15:20'),
(29, 1, 'delete_product', 'تم حذف منتج: لابتوب Dell Inspiron', NULL, '::1', '2025-07-01 20:15:22'),
(30, 1, 'delete_product', 'تم حذف منتج: لابتوب HP Pavilion', NULL, '::1', '2025-07-01 20:15:25'),
(31, 1, 'delete_product', 'تم حذف منتج: لابتوب Dell Inspiron', NULL, '::1', '2025-07-01 20:15:27'),
(32, 1, 'delete_product', 'تم حذف منتج: لابتوب HP Pavilion', NULL, '::1', '2025-07-01 20:15:29'),
(33, 1, 'delete_product', 'تم حذف منتج: لابتوب Lenovo ThinkPad', NULL, '::1', '2025-07-01 20:15:31'),
(34, 1, 'add_product', 'تم إضافة منتج جديد: 7777777777777', 'uploads/products/images_686444962931a.jpg', '::1', '2025-07-01 20:27:02'),
(35, 1, 'add_product', 'تم إضافة منتج جديد: 88888888888', 'uploads/products/MacBook_Pro_M2_13_480x480_686444d94540e.webp', '::1', '2025-07-01 20:28:09'),
(36, 1, 'add_product', 'تم إضافة منتج جديد: نلبببسسس', 'uploads/products/hous1_6864461db6d98.jpg', '::1', '2025-07-01 20:33:33'),
(37, 1, 'login', 'تم تسجيل الدخول بنجاح', NULL, '::1', '2025-07-01 20:40:44'),
(38, 1, 'add_product', 'تم إضافة منتج جديد: 99999999999999', 'uploads/products/images (1)_686447ec11926.jpg', '::1', '2025-07-01 20:41:16'),
(39, 1, 'add_product', 'تم إضافة منتج جديد: 99999999999999', 'admin/uploads/products/images (1)_6864491864ae7.jpg', '::1', '2025-07-01 20:46:16'),
(40, 1, 'add_product', 'تم إضافة منتج جديد: لابوبو', 'admin/uploads/products/محمد مزاحم_6864494a98461.jpg', '::1', '2025-07-01 20:47:06'),
(41, 1, 'add_product', 'تم إضافة منتج جديد: عععععععععععععع', 'admin/uploads/products/Screenshot 2025-05-25 004346_68644a6f39d42.png', '::1', '2025-07-01 20:51:59'),
(42, 1, 'delete_product', 'تم حذف منتج: لابوبو', NULL, '::1', '2025-07-01 20:52:30'),
(43, 1, 'delete_product', 'تم حذف منتج: 99999999999999', NULL, '::1', '2025-07-01 20:52:34'),
(44, 1, 'delete_product', 'تم حذف منتج: 7777777777777', NULL, '::1', '2025-07-01 20:52:39'),
(45, 1, 'delete_product', 'تم حذف منتج: عععععععععععععع', NULL, '::1', '2025-07-01 20:58:17'),
(46, 1, 'delete_product', 'تم حذف منتج: 99999999999999', NULL, '::1', '2025-07-01 20:58:19'),
(47, 1, 'delete_product', 'تم حذف منتج: نلبببسسس', NULL, '::1', '2025-07-01 20:58:21'),
(48, 1, 'add_product', 'تم إضافة منتج جديد: محمد', 'uploads/products/محمد مزاحم_68644c06a5417.jpg', '::1', '2025-07-01 20:58:46'),
(49, 1, 'add_product', 'تم إضافة منتج جديد: 999999999999999999999999999999', 'uploads/products/اجهت الشركة_68644d7edd7d5.jpg', '::1', '2025-07-01 21:05:02'),
(50, 1, 'delete_product', 'تم حذف منتج: 999999999999999999999999999999', NULL, '::1', '2025-07-01 21:22:49'),
(51, 1, 'delete_product', 'تم حذف منتج: محمد', NULL, '::1', '2025-07-01 21:22:52'),
(52, 1, 'delete_product', 'تم حذف منتج: 88888888888', NULL, '::1', '2025-07-01 21:22:55'),
(53, 1, 'delete_product', 'تم حذف منتج: 88888888888', NULL, '::1', '2025-07-01 21:22:58'),
(54, 1, 'add_product', 'تم إضافة منتج جديد: محمد', 'uploads/products/محمد مزاحم_686451c839a06.jpg', '::1', '2025-07-01 21:23:20'),
(55, 1, 'add_product', 'تم إضافة منتج جديد: الزنلز', 'uploads/products/MacBook_Pro_M2_13_480x480_6864553c484d2.webp', '::1', '2025-07-01 21:38:04'),
(56, 1, 'add_product', 'تم إضافة منتج جديد: كنتكت', 'uploads/products/Pink-scaled_6864559468a93.webp', '::1', '2025-07-01 21:39:32'),
(57, 1, 'add_product', 'تم إضافة منتج جديد: بعفبعب', 'uploads/products/Pink-scaled_686455c7b669c.webp', '::1', '2025-07-01 21:40:23'),
(58, 1, 'add_product', 'تم إضافة منتج جديد: تمىكنةكنة', 'uploads/products/og__eui2mpgzwyaa_specs_686456337e25d.png', '::1', '2025-07-01 21:42:11'),
(59, 1, 'add_product', 'تم إضافة منتج جديد: جنجن', 'uploads/products/OeR6ks7DLiZlYylkcyrjJ4cs6G45FtF20GPqz1eM_6864567e04cbf.webp', '::1', '2025-07-01 21:43:26'),
(60, 1, 'add_product', 'تم إضافة منتج جديد: ايربود', 'uploads/products/OeR6ks7DLiZlYylkcyrjJ4cs6G45FtF20GPqz1eM (1)_68645746d8e09.webp', '::1', '2025-07-01 21:46:46'),
(61, 1, 'add_product', 'تم إضافة منتج جديد: فيب', 'uploads/products/VOOPOO-VINCI-3-POD-MOD-KIT_68645847cbf63.webp', '::1', '2025-07-01 21:51:03'),
(62, 1, 'add_product', 'تم إضافة منتج جديد: فافون', 'uploads/products/lj3rfvtlfm1-w960_686458b411512.jpg', '::1', '2025-07-01 21:52:52'),
(63, 1, 'add_product', 'تم إضافة منتج جديد: فافةن', 'uploads/products/979879_686459f7a1f7d.jpg', '::1', '2025-07-01 21:58:15'),
(64, 1, 'delete_product', 'تم حذف منتج: كنتكت', NULL, '::1', '2025-07-01 21:58:56'),
(65, 1, 'add_product', 'تم إضافة منتج جديد: ككةكة', 'uploads/products/images_68645a803f38d.jpeg', '::1', '2025-07-01 22:00:32'),
(66, 1, 'add_product', 'تم إضافة منتج جديد: نجنحخنحخنحخن', 'uploads/products/1dde2886-e4f1-4a94-87f3-dad4015b61c5_68645adb74e02.png', '::1', '2025-07-01 22:02:03'),
(67, 1, 'login', 'تم تسجيل الدخول بنجاح', NULL, '::1', '2025-07-01 22:05:01'),
(68, 1, 'delete_product', 'تم حذف منتج: الزنلز', NULL, '::1', '2025-07-01 22:05:59'),
(69, 1, 'delete_product', 'تم حذف منتج: الزنلز', NULL, '::1', '2025-07-01 22:06:03'),
(70, 1, 'update_settings', 'تم تحديث إعدادات المتجر', NULL, '::1', '2025-07-02 00:40:34');

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'اللابتوبات', 'laptops', 'fas fa-laptop', 'أحدث أجهزة الكمبيوتر المحمولة', 1, '2025-07-01 20:15:16', '2025-07-01 20:15:16'),
(8, 'الهواتف', 'phones', 'fas fa-mobile-alt', 'أفضل الهواتف الحديثة', 1, '2025-07-01 20:15:16', '2025-07-01 20:15:16'),
(9, 'الاكسسوارات', 'accessories', 'fas fa-headphones', 'ملحقات الأجهزة الإلكترونية', 1, '2025-07-01 20:15:16', '2025-07-01 20:15:16');

-- --------------------------------------------------------

--
-- بنية الجدول `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `products`
--

INSERT INTO `products` (`id`, `category_id`, `title`, `description`, `price`, `old_price`, `icon`, `image_url`, `is_active`, `is_featured`, `created_at`, `updated_at`) VALUES
(201, 8, 'بعفبعب', 'منمىمنى', 1000.00, NULL, 'fas fa-mobile-alt', 'uploads/products/Pink-scaled_686455c7b669c.webp', 1, 0, '2025-07-01 21:40:23', '2025-07-01 21:40:23'),
(202, 9, 'تمىكنةكنة', 'كةكةكنة', 100.00, NULL, 'fas fa-laptop', 'uploads/products/og__eui2mpgzwyaa_specs_686456337e25d.png', 1, 0, '2025-07-01 21:42:11', '2025-07-01 21:42:11'),
(203, 9, 'جنجن', 'كنةكةك', 1880.00, NULL, 'fas fa-headphones', 'uploads/products/OeR6ks7DLiZlYylkcyrjJ4cs6G45FtF20GPqz1eM_6864567e04cbf.webp', 1, 0, '2025-07-01 21:43:26', '2025-07-01 21:43:26'),
(204, 9, 'ايربود', 'مىمى', 769000.00, NULL, 'fas fa-headphones', 'uploads/products/OeR6ks7DLiZlYylkcyrjJ4cs6G45FtF20GPqz1eM (1)_68645746d8e09.webp', 1, 0, '2025-07-01 21:46:46', '2025-07-01 21:46:46'),
(205, 9, 'فيب', 'مااجمنكم', 100.00, NULL, 'fas fa-headphones', 'uploads/products/VOOPOO-VINCI-3-POD-MOD-KIT_68645847cbf63.webp', 1, 0, '2025-07-01 21:51:03', '2025-07-01 21:51:03'),
(206, 8, 'فافون', 'نتلانلا', 10660.00, NULL, 'fab fa-apple', 'uploads/products/lj3rfvtlfm1-w960_686458b411512.jpg', 1, 0, '2025-07-01 21:52:52', '2025-07-01 21:52:52'),
(207, 8, 'فافةن', 'وتمتىم', 11.00, NULL, 'fab fa-apple', 'uploads/products/979879_686459f7a1f7d.jpg', 1, 0, '2025-07-01 21:58:15', '2025-07-01 21:58:15'),
(208, 7, 'ككةكة', 'منةكنةك', 100.00, NULL, 'fas fa-laptop', 'uploads/products/images_68645a803f38d.jpeg', 1, 0, '2025-07-01 22:00:32', '2025-07-01 22:00:32'),
(209, 7, 'نجنحخنحخنحخن', 'كمةكمة', 969.00, NULL, 'fas fa-laptop', 'uploads/products/1dde2886-e4f1-4a94-87f3-dad4015b61c5_68645adb74e02.png', 1, 0, '2025-07-01 22:02:03', '2025-07-01 22:02:03');

-- --------------------------------------------------------

--
-- بنية الجدول `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'store_name', 'روتانا', '2025-07-01 13:06:54', '2025-07-01 13:06:54'),
(2, 'store_description', 'متجر الأجهزة الإلكترونية الأول في كربلاء', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(3, 'whatsapp_number', '+9647813681814', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(4, 'phone_number', '++9647813681814', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(5, 'address', 'كربلاء حي النقيب - كربلاء حي العامل السوق الاخير', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(6, 'facebook_url', 'https://www.facebook.com/mhmd.mzahm.bas.althmazy', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(7, 'instagram_url', 'https://www.instagram.com/00put/', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(8, 'telegram_url', 'https://www.google.iq', '2025-07-01 13:06:54', '2025-07-02 00:40:34'),
(9, 'currency', 'د.ع', '2025-07-01 13:06:54', '2025-07-01 13:06:54');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$791egAhJe52FF5/6au36BuqKwm47J8VattAEoTfd2XDoFboDV74di', 'admin@rotana.com', '2025-07-01 13:06:54', '2025-07-01 13:06:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
