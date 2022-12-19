-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Дек 19 2022 г., 18:03
-- Версия сервера: 10.6.11-MariaDB-1:10.6.11+maria~ubu2004
-- Версия PHP: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Yegor_courier`
--

-- --------------------------------------------------------

--
-- Структура таблицы `order`
--

CREATE TABLE `order` (
  `order_id` int(11) NOT NULL,
  `storeid` int(11) NOT NULL,
  `courier_id` int(11) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `order_class` char(20) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `order_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Дамп данных таблицы `order`
--

INSERT INTO `order` (`order_id`, `storeid`, `courier_id`, `client_id`, `order_class`, `order_date`, `order_status`) VALUES
(3, 3, 7, 10, 'medium', '2022-12-18 17:24:44', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `store`
--

CREATE TABLE `store` (
  `store_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `store_name` char(50) NOT NULL,
  `store_address` char(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `store`
--

INSERT INTO `store` (`store_id`, `owner_id`, `store_name`, `store_address`) VALUES
(3, 5, 'Ботинки', 'Улица 1, д. 2');

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `first_name` char(30) NOT NULL,
  `last_name` char(30) NOT NULL,
  `phone` char(12) NOT NULL,
  `email` char(50) NOT NULL,
  `password` char(100) NOT NULL,
  `profile_photo` text DEFAULT NULL,
  `address` char(100) DEFAULT NULL,
  `token` char(100) DEFAULT NULL,
  `whois` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `first_name`, `last_name`, `phone`, `email`, `password`, `profile_photo`, `address`, `token`, `whois`) VALUES
(4, 'Егор', 'Орешников', '89213578342', 'egorores@mail.ru', '$2y$13$MflF0F32CNsz/XbziCvf8.eixc.zErIxJpNMB7.HHSbqS2e9SW4LC', NULL, NULL, 'W4iq_q3Kl3Tz9I3iu6Trc8nleBo1bfzx', 1),
(5, 'Иван', 'Иванов', '88005553535', 'ivanivan@gmail.com', '$2y$13$2rjz7u5VmDXTOXpiI239SuiQBZnG2RY6eNgsI/7hDDXuGscuwaZB2', NULL, NULL, 'Ft-nK9oOjAUxApOIEzWu5-t8FxlFEo1q', 0),
(7, 'Вячеслав', 'Библая', '86709324265', 'biblaya@yandex.ru', '$2y$13$EuAXphXyna35ATMhJH7hre0Itu0fJNYKqmrqzFnw.ia.UK/1b5wXK', 'ee2e1307ab1b7fe6a381bf9c7fdff2710b960aef65a00e27d6b47ae37fd173ba.jpg', NULL, 'k31WnRCmnGUF7o4L0zqoEppUMyGR-nMg', 2),
(10, 'Сергей', 'Рогачев', '83421234365', 'enigma@gmail.com', '$2y$13$LKW/yCq/B2XNfAgIQBbya.ooxmi5658eqf6kGf8FUxSijUtPix5Dq', NULL, 'Улица Селина, Дом Каруселина, кв. 4', 'fyZV9Vr6ZvEN7UsF9vjn-kTlTivDHGFH', 3),
(11, 'Енисей', 'Енисеев', '89001234567', 'user@user.ru', '$2y$13$Cm37/W02Sukj3ktP4EjDv.wp/etMsFY8ve2BpiDLdwnpQuh4Wgjxq', NULL, NULL, 'WeNFKlzoQ_xJqnvjUZmSHDshlBlpuIOg', 0),
(12, 'Курьер', 'Курьеров', '83259429354', 'courier@cour.com', '$2y$13$NA6mXrVBpWZbCIF8dpC7Ru5VzgdGh5D49PfV55FcgbmS7W5vHI58u', NULL, NULL, NULL, 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `orders_idfk_1` (`storeid`),
  ADD KEY `orders_idfk_2` (`courier_id`),
  ADD KEY `orders_idfk_3` (`client_id`);

--
-- Индексы таблицы `store`
--
ALTER TABLE `store`
  ADD PRIMARY KEY (`store_id`),
  ADD KEY `stores_idfk_1` (`owner_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `order`
--
ALTER TABLE `order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `store`
--
ALTER TABLE `store`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `orders_idfk_1` FOREIGN KEY (`storeid`) REFERENCES `store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_idfk_2` FOREIGN KEY (`courier_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_idfk_3` FOREIGN KEY (`client_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `store`
--
ALTER TABLE `store`
  ADD CONSTRAINT `stores_idfk_1` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
