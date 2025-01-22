-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1:3306
-- Üretim Zamanı: 22 Oca 2025, 00:55:01
-- Sunucu sürümü: 9.1.0
-- PHP Sürümü: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `oyun`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `envanter`
--

DROP TABLE IF EXISTS `envanter`;
CREATE TABLE IF NOT EXISTS `envanter` (
  `envanter_id` int NOT NULL AUTO_INCREMENT,
  `karakter_id` int NOT NULL,
  `slot_numarasi` int NOT NULL,
  `esya_id` int DEFAULT NULL,
  PRIMARY KEY (`envanter_id`),
  UNIQUE KEY `unique_slot` (`karakter_id`,`slot_numarasi`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `envanter`
--

INSERT INTO `envanter` (`envanter_id`, `karakter_id`, `slot_numarasi`, `esya_id`) VALUES
(14, 2, 5, NULL),
(13, 2, 4, NULL),
(20, 3, 2, 10),
(10, 2, 1, 10),
(19, 3, 1, 5),
(7, 1, 7, NULL),
(8, 1, 8, NULL),
(9, 1, 9, NULL),
(15, 2, 6, NULL),
(16, 2, 7, NULL),
(17, 2, 8, NULL),
(18, 2, 9, NULL),
(25, 3, 7, NULL),
(26, 3, 8, NULL),
(27, 3, 9, NULL),
(28, 4, 1, 5),
(29, 4, 2, 10),
(34, 4, 7, NULL),
(35, 4, 8, NULL),
(36, 4, 9, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `esyalar`
--

DROP TABLE IF EXISTS `esyalar`;
CREATE TABLE IF NOT EXISTS `esyalar` (
  `esya_id` int NOT NULL AUTO_INCREMENT,
  `esya_adi` varchar(100) NOT NULL,
  `esya_turu` enum('SILAH','KASK','ZIRH','BOT','TUKETILEBILIR') DEFAULT NULL,
  `nadirlik` enum('YAYGIN','AZ_YAYGIN','NADIR','EFSANEVI','ESATIRI') NOT NULL,
  `seviye_gereksinimi` int DEFAULT '1',
  `aciklama` text,
  `olusturulma_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `meslek_id` int DEFAULT NULL,
  `can_bonusu` int DEFAULT '0',
  `mana_bonusu` int DEFAULT '0',
  `guc_bonusu` int DEFAULT '0',
  `ceviklik_bonusu` int DEFAULT '0',
  `dayaniklilik_bonusu` int DEFAULT '0',
  `zeka_bonusu` int DEFAULT '0',
  `fiyat` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`esya_id`),
  KEY `meslek_id` (`meslek_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `esyalar`
--

INSERT INTO `esyalar` (`esya_id`, `esya_adi`, `esya_turu`, `nadirlik`, `seviye_gereksinimi`, `aciklama`, `olusturulma_tarihi`, `meslek_id`, `can_bonusu`, `mana_bonusu`, `guc_bonusu`, `ceviklik_bonusu`, `dayaniklilik_bonusu`, `zeka_bonusu`, `fiyat`) VALUES
(1, 'Çırak Kılıcı', 'SILAH', 'YAYGIN', 1, 'Yeni başlayan savaşçılar için temel kılıç', '2025-01-16 17:52:42', 1, 0, 0, 2, 1, 1, 1, 75),
(2, 'Çırak Miğferi', 'KASK', 'YAYGIN', 1, 'Yeni başlayan savaşçılar için temel miğfer', '2025-01-16 17:52:42', 1, 0, 0, 1, 1, 1, 2, 50),
(3, 'Çırak Zırhı', 'ZIRH', 'YAYGIN', 1, 'Yeni başlayan savaşçılar için temel zırh', '2025-01-16 17:52:42', 1, 0, 0, 1, 1, 2, 1, 75),
(4, 'Çırak Çizmeleri', 'BOT', 'YAYGIN', 1, 'Yeni başlayan savaşçılar için temel çizme', '2025-01-16 17:52:42', 1, 0, 0, 1, 2, 1, 1, 25),
(5, 'Küçük Can İksiri', 'TUKETILEBILIR', 'YAYGIN', 1, 'Küçük miktarda can yeniler', '2025-01-16 17:52:42', 0, 10, 0, 0, 0, 0, 0, 20),
(6, 'Çırak Asası', 'SILAH', 'YAYGIN', 1, 'Yeni başlayan büyücüler için temel asa', '2025-01-16 17:52:42', 2, 0, 0, 2, 1, 1, 1, 75),
(7, 'Çırak Başlığı', 'KASK', 'YAYGIN', 1, 'Yeni başlayan büyücüler için temel başlık', '2025-01-16 17:52:42', 2, 0, 0, 1, 1, 1, 2, 50),
(8, 'Çırak Cübbesi', 'ZIRH', 'YAYGIN', 1, 'Yeni başlayan büyücüler için temel cübbe', '2025-01-16 17:52:42', 2, 0, 0, 1, 1, 2, 1, 75),
(9, 'Çırak Ayakkabıları', 'BOT', 'YAYGIN', 1, 'Yeni başlayan büyücüler için temel ayakkabı', '2025-01-16 17:52:42', 2, 0, 0, 1, 2, 1, 1, 25),
(10, 'Küçük Mana İksiri', 'TUKETILEBILIR', 'YAYGIN', 1, 'Küçük miktarda mana yeniler', '2025-01-16 17:52:42', 0, 0, 10, 0, 0, 0, 0, 20);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `karakterler`
--

DROP TABLE IF EXISTS `karakterler`;
CREATE TABLE IF NOT EXISTS `karakterler` (
  `karakter_id` int NOT NULL AUTO_INCREMENT,
  `kullanici_id` int NOT NULL,
  `karakter_adi` varchar(50) NOT NULL,
  `meslek_id` int DEFAULT NULL,
  `seviye` int DEFAULT '1',
  `deneyim` int DEFAULT '0',
  `dsp` int DEFAULT '5',
  `can` int DEFAULT '100',
  `max_can` int DEFAULT '100',
  `mana` int DEFAULT '50',
  `max_mana` int DEFAULT '50',
  `guc` int DEFAULT '1',
  `dayaniklilik` int DEFAULT '1',
  `ceviklik` int DEFAULT '1',
  `zeka` int DEFAULT '1',
  `para` int NOT NULL DEFAULT '100',
  PRIMARY KEY (`karakter_id`),
  UNIQUE KEY `karakter_adi` (`karakter_adi`),
  KEY `kullanici_id` (`kullanici_id`),
  KEY `meslek_id` (`meslek_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `karakterler`
--

INSERT INTO `karakterler` (`karakter_id`, `kullanici_id`, `karakter_adi`, `meslek_id`, `seviye`, `deneyim`, `dsp`, `can`, `max_can`, `mana`, `max_mana`, `guc`, `dayaniklilik`, `ceviklik`, `zeka`, `para`) VALUES
(1, 1, 'a', 1, 1, 0, 5, 110, 120, 20, 30, 10, 10, 8, 7, 100),
(2, 2, 'b', 2, 1, 0, 5, 80, 80, 100, 100, 5, 5, 5, 10, 100),
(3, 3, 'as', 1, 1, 0, 5, 121, 121, 30, 30, 10, 10, 8, 7, 100),
(4, 4, 'Emo', 1, 1, 0, 0, 120, 120, 30, 30, 14, 10, 9, 7, 100);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

DROP TABLE IF EXISTS `kullanicilar`;
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `kullanici_id` int NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre_hash` varchar(255) NOT NULL,
  `kayit_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kullanici_id`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `kullanici_adi`, `sifre_hash`, `kayit_tarihi`) VALUES
(1, 'a', '$2y$10$9nV.KGC2UOoo8NikLo1FieGUK8DfZUBNM7TYyicu43eIUh3o7qGgS', '2025-01-21 18:23:19'),
(2, 'b', '$2y$10$EVhkR.AyqSgdnlvmGt7I7etggm0cAKvTmaWvhAGgriAZZAO7Knwv6', '2025-01-21 21:00:20'),
(3, 'as', '$2y$10$50s.dx3L8klTR6uWIe9/keipywSFeD41f4KZAN4c78V4smy3Z1UmO', '2025-01-21 21:41:08'),
(4, 'em', '$2y$10$D2iNzFKCKTBLbOINRDjTQOLvr/Ff0DOa/FGW0oYRxrBQSHvyMClFu', '2025-01-21 22:51:36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kusanilan_ekipmanlar`
--

DROP TABLE IF EXISTS `kusanilan_ekipmanlar`;
CREATE TABLE IF NOT EXISTS `kusanilan_ekipmanlar` (
  `kusanim_id` int NOT NULL AUTO_INCREMENT,
  `karakter_id` int NOT NULL,
  `silah_id` int DEFAULT NULL,
  `kask_id` int DEFAULT NULL,
  `zirh_id` int DEFAULT NULL,
  `bot_id` int DEFAULT NULL,
  `olusturulma_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`kusanim_id`),
  UNIQUE KEY `unique_karakter` (`karakter_id`),
  KEY `silah_id` (`silah_id`),
  KEY `kask_id` (`kask_id`),
  KEY `zirh_id` (`zirh_id`),
  KEY `bot_id` (`bot_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `kusanilan_ekipmanlar`
--

INSERT INTO `kusanilan_ekipmanlar` (`kusanim_id`, `karakter_id`, `silah_id`, `kask_id`, `zirh_id`, `bot_id`, `olusturulma_tarihi`) VALUES
(1, 1, 1, 2, 3, 4, '2025-01-21 18:23:34'),
(2, 2, 6, 7, NULL, NULL, '2025-01-21 21:00:42'),
(3, 3, 1, 2, 3, 4, '2025-01-21 21:41:50'),
(4, 4, 1, 2, 3, 4, '2025-01-21 22:53:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `market`
--

DROP TABLE IF EXISTS `market`;
CREATE TABLE IF NOT EXISTS `market` (
  `mesya_id` int NOT NULL AUTO_INCREMENT,
  `esya_id` int DEFAULT NULL,
  `fiyat` int DEFAULT NULL,
  `stok` int DEFAULT NULL,
  `musteri_id` int DEFAULT NULL,
  PRIMARY KEY (`mesya_id`),
  KEY `esya_id` (`esya_id`),
  KEY `musteri_id` (`musteri_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `meslekler`
--

DROP TABLE IF EXISTS `meslekler`;
CREATE TABLE IF NOT EXISTS `meslekler` (
  `meslek_id` int NOT NULL AUTO_INCREMENT,
  `meslek_adi` varchar(50) NOT NULL,
  `aciklama` text,
  PRIMARY KEY (`meslek_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `meslekler`
--

INSERT INTO `meslekler` (`meslek_id`, `meslek_adi`, `aciklama`) VALUES
(1, 'Savaşçı', 'fiziksel vurur'),
(2, 'Büyücü', 'büyüsel vurur');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `postalar`
--

DROP TABLE IF EXISTS `postalar`;
CREATE TABLE IF NOT EXISTS `postalar` (
  `posta_id` int NOT NULL AUTO_INCREMENT,
  `gonderen_karakter_id` int NOT NULL,
  `alici_karakter_id` int NOT NULL,
  `esya_id` int DEFAULT NULL,
  `mesaj` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `gonderilme_tarihi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `okundu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`posta_id`),
  KEY `gonderen_karakter_id` (`gonderen_karakter_id`),
  KEY `alici_karakter_id` (`alici_karakter_id`),
  KEY `esya_id` (`esya_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Tablo döküm verisi `postalar`
--

INSERT INTO `postalar` (`posta_id`, `gonderen_karakter_id`, `alici_karakter_id`, `esya_id`, `mesaj`, `gonderilme_tarihi`, `okundu`) VALUES
(1, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Küçük Can İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(2, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Küçük Mana İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(3, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Çırak Kılıcı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(4, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Çırak Miğferi senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(5, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Çırak Zırhı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(6, 999, 1, NULL, 'Hoş geldin şampiyon! Bu Çırak Çizmeleri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 18:23:19', 1),
(7, 999, 2, 5, 'Hoş geldin şampiyon! Bu Küçük Can İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 0),
(8, 999, 2, NULL, 'Hoş geldin şampiyon! Bu Küçük Mana İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 1),
(9, 999, 2, NULL, 'Hoş geldin şampiyon! Bu Çırak Asası senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 1),
(10, 999, 2, NULL, 'Hoş geldin şampiyon! Bu Çırak Başlığı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 1),
(11, 999, 2, 8, 'Hoş geldin şampiyon! Bu Çırak Cübbesi senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 0),
(12, 999, 2, 9, 'Hoş geldin şampiyon! Bu Çırak Ayakkabıları senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:00:21', 0),
(13, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Küçük Can İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(14, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Küçük Mana İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(15, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Çırak Kılıcı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(16, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Çırak Miğferi senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(17, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Çırak Zırhı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(18, 999, 3, NULL, 'Hoş geldin şampiyon! Bu Çırak Çizmeleri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 21:41:08', 1),
(19, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Küçük Can İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1),
(20, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Küçük Mana İksiri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1),
(21, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Çırak Kılıcı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1),
(22, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Çırak Miğferi senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1),
(23, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Çırak Zırhı senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1),
(24, 999, 4, NULL, 'Hoş geldin şampiyon! Bu Çırak Çizmeleri senin başlangıç eşyan olarak verildi. İyi eğlenceler!', '2025-01-21 22:51:36', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
