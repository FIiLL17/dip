-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 18 2025 г., 15:04
-- Версия сервера: 10.7.5-MariaDB
-- Версия PHP: 8.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `klouddisc`
--

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vinyl_id` int(11) NOT NULL,
  `added_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `vinyl_id`, `added_at`) VALUES
(2, 1, 3, '2025-06-05 23:40:56');

-- --------------------------------------------------------

--
-- Структура таблицы `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_holder` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_expiry` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_cvv` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_four` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `user_id`, `card_number`, `card_holder`, `card_expiry`, `card_cvv`, `card_type`, `last_four`, `created_at`) VALUES
(1, 1, '$2y$10$KJdKRoJWFeu384d7MVVrDOZau96tQ1f.JQad7AjRgyoN5eI64EbqG', 'IVIN IVANOV', '1212', '$2y$10$dn.7pujsqLeNj12VFk2qIuP92Hajvt7Hg3Ehz7yxamJkrNVhydK6W', 'UNKNOWN', '1212', '2025-06-06 01:18:26'),
(2, 2, '$2y$10$DUQvBDbk9wOi2rNPrtns6uaHsre3THUWjYxounNWozant0e6MwRYW', 'wsws', 'wsw', '$2y$10$.q2ETIqStd1hXOnpv/w3f.kyeMA7wUdIh0jsvyo/pebup.5ureiGW', 'UNKNOWN', '', '2025-06-06 01:25:32'),
(3, 3, '$2y$10$WoIbmeJLHQDBQ5W8csAOCOJQjZorjcbXt8zZHayR1.ds/ZGpO3kjW', 'IRINA FRILOVA', '1212', '$2y$10$CGXrYmm1ufiS4aJu1kwFjuO7kwaEO6fJGT3kXXRt/44D8syDiXi/2', 'UNKNOWN', '1212', '2025-06-06 23:20:47');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'fill', 'fill@gmail.com', '$2y$10$iH1FVTzmho02ZafoZUMGWeJ5PrTATir5dCOweT/uCjoMji7SmkSYS', '2025-06-05 13:12:31'),
(2, 'Джон', 'dj@mail.ru', '$2y$10$P2bR7O99zESH.gpjTYxb1e97PXcxjZCsR7evTqDiT18ltle0oENDi', '2025-06-06 01:24:47'),
(3, 'fill1', 'fill1@gmail.com', '$2y$10$.NyPj5iJy7V3gxM5GiZTYuwV/QC5qF9KdtrSLFNsXRYVjIeBO9bwq', '2025-06-06 23:19:27');

-- --------------------------------------------------------

--
-- Структура таблицы `user_data`
--

CREATE TABLE `user_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `favorites` text NOT NULL DEFAULT '[]',
  `cart` text NOT NULL DEFAULT '[]',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reg_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `full_name`, `name`, `email`, `phone`, `address`, `reg_date`) VALUES
(2, 2, 'ws', NULL, NULL, 'ws', 'ws', '2025-06-06 04:25:57'),
(4, 1, 'Дверцов Афонасьев Алесандр', NULL, NULL, '+79012345678', '123 Main St, Apt 4B, Moscow, Russia', '2025-06-06 20:37:53'),
(5, 3, 'Иванов Алексей Дмитриевич', NULL, NULL, '+9090009900', '2121', '2025-06-07 02:20:02');

-- --------------------------------------------------------

--
-- Структура таблицы `vinyls`
--

CREATE TABLE `vinyls` (
  `id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `artist` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `vinyls`
--

INSERT INTO `vinyls` (`id`, `title`, `artist`, `price`, `image`) VALUES
(1, 'Angel Of Small Death', 'Hozier', '2499.00', 'img/vinyls/hozier.jpg'),
(2, 'Melody Maker', 'The Kooks', '2199.00', 'img/vinyls/kooks.jpg'),
(3, 'Mercy', 'Dotan', '2299.00', 'img/vinyls/dotan.jpg');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vinyl_id` (`vinyl_id`);

--
-- Индексы таблицы `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `user_data`
--
ALTER TABLE `user_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `vinyls`
--
ALTER TABLE `vinyls`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `user_data`
--
ALTER TABLE `user_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `vinyls`
--
ALTER TABLE `vinyls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`vinyl_id`) REFERENCES `vinyls` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_data`
--
ALTER TABLE `user_data`
  ADD CONSTRAINT `user_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
