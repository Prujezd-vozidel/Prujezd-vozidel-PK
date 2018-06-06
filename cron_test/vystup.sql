-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vygenerováno: Čtv 07. čen 2018, 01:11
-- Verze MySQL: 5.5.27-log
-- Verze PHP: 5.4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `prujezd_vozidel`
--
CREATE DATABASE `prujezd_vozidel` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `prujezd_vozidel`;

-- --------------------------------------------------------

--
-- Struktura tabulky `datum`
--

CREATE TABLE IF NOT EXISTS `datum` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `od` datetime NOT NULL,
  `do` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=98 ;

--
-- Vypisuji data pro tabulku `datum`
--

INSERT INTO `datum` (`id`, `od`, `do`) VALUES
(1, '2018-06-05 00:00:00', '2018-06-05 00:15:00'),
(2, '2018-06-05 00:15:00', '2018-06-05 00:30:00'),
(3, '2018-06-05 00:30:00', '2018-06-05 00:45:00'),
(4, '2018-06-05 00:45:00', '2018-06-05 01:00:00'),
(5, '2018-06-05 01:00:00', '2018-06-05 01:15:00'),
(6, '2018-06-05 01:15:00', '2018-06-05 01:30:00'),
(7, '2018-06-05 01:30:00', '2018-06-05 01:45:00'),
(8, '2018-06-05 01:45:00', '2018-06-05 02:00:00'),
(9, '2018-06-05 02:00:00', '2018-06-05 02:15:00'),
(10, '2018-06-05 02:15:00', '2018-06-05 02:30:00'),
(11, '2018-06-05 02:30:00', '2018-06-05 02:45:00'),
(12, '2018-06-05 02:45:00', '2018-06-05 03:00:00'),
(13, '2018-06-05 03:00:00', '2018-06-05 03:15:00'),
(14, '2018-06-05 03:15:00', '2018-06-05 03:30:00'),
(15, '2018-06-05 03:30:00', '2018-06-05 03:45:00'),
(16, '2018-06-05 03:45:00', '2018-06-05 04:00:00'),
(17, '2018-06-05 04:00:00', '2018-06-05 04:15:00'),
(18, '2018-06-05 04:15:00', '2018-06-05 04:30:00'),
(19, '2018-06-05 04:30:00', '2018-06-05 04:45:00'),
(20, '2018-06-05 04:45:00', '2018-06-05 05:00:00'),
(21, '2018-06-05 05:00:00', '2018-06-05 05:15:00'),
(22, '2018-06-05 05:15:00', '2018-06-05 05:30:00'),
(23, '2018-06-05 05:30:00', '2018-06-05 05:45:00'),
(24, '2018-06-05 05:45:00', '2018-06-05 06:00:00'),
(25, '2018-06-05 06:00:00', '2018-06-05 06:15:00'),
(26, '2018-06-05 06:15:00', '2018-06-05 06:30:00'),
(27, '2018-06-05 06:30:00', '2018-06-05 06:45:00'),
(28, '2018-06-05 06:45:00', '2018-06-05 07:00:00'),
(29, '2018-06-05 07:00:00', '2018-06-05 07:15:00'),
(30, '2018-06-05 07:15:00', '2018-06-05 07:30:00'),
(31, '2018-06-05 07:30:00', '2018-06-05 07:45:00'),
(32, '2018-06-05 07:45:00', '2018-06-05 08:00:00'),
(33, '2018-06-05 08:00:00', '2018-06-05 08:15:00'),
(34, '2018-06-05 08:15:00', '2018-06-05 08:30:00'),
(35, '2018-06-05 08:30:00', '2018-06-05 08:45:00'),
(36, '2018-06-05 08:45:00', '2018-06-05 09:00:00'),
(37, '2018-06-05 09:00:00', '2018-06-05 09:15:00'),
(38, '2018-06-05 09:15:00', '2018-06-05 09:30:00'),
(39, '2018-06-05 09:30:00', '2018-06-05 09:45:00'),
(40, '2018-06-05 09:45:00', '2018-06-05 10:00:00'),
(41, '2018-06-05 10:00:00', '2018-06-05 10:15:00'),
(42, '2018-06-05 10:15:00', '2018-06-05 10:30:00'),
(43, '2018-06-05 10:30:00', '2018-06-05 10:45:00'),
(44, '2018-06-05 10:45:00', '2018-06-05 11:00:00'),
(45, '2018-06-05 11:00:00', '2018-06-05 11:15:00'),
(46, '2018-06-05 11:15:00', '2018-06-05 11:30:00'),
(47, '2018-06-05 11:30:00', '2018-06-05 11:45:00'),
(48, '2018-06-05 11:45:00', '2018-06-05 12:00:00'),
(49, '2018-06-05 12:00:00', '2018-06-05 12:15:00'),
(50, '2018-06-05 12:15:00', '2018-06-05 12:30:00'),
(51, '2018-06-05 12:30:00', '2018-06-05 12:45:00'),
(52, '2018-06-05 12:45:00', '2018-06-05 13:00:00'),
(53, '2018-06-05 13:00:00', '2018-06-05 13:15:00'),
(54, '2018-06-05 13:15:00', '2018-06-05 13:30:00'),
(55, '2018-06-05 13:30:00', '2018-06-05 13:45:00'),
(56, '2018-06-05 13:45:00', '2018-06-05 14:00:00'),
(57, '2018-06-05 14:00:00', '2018-06-05 14:15:00'),
(58, '2018-06-05 14:15:00', '2018-06-05 14:30:00'),
(59, '2018-06-05 14:30:00', '2018-06-05 14:45:00'),
(60, '2018-06-05 14:45:00', '2018-06-05 15:00:00'),
(61, '2018-06-05 15:00:00', '2018-06-05 15:15:00'),
(62, '2018-06-05 15:15:00', '2018-06-05 15:30:00'),
(63, '2018-06-05 15:30:00', '2018-06-05 15:45:00'),
(64, '2018-06-05 15:45:00', '2018-06-05 16:00:00'),
(65, '2018-06-05 16:00:00', '2018-06-05 16:15:00'),
(66, '2018-06-05 16:15:00', '2018-06-05 16:30:00'),
(67, '2018-06-05 16:30:00', '2018-06-05 16:45:00'),
(68, '2018-06-05 16:45:00', '2018-06-05 17:00:00'),
(69, '2018-06-05 17:00:00', '2018-06-05 17:15:00'),
(70, '2018-06-05 17:15:00', '2018-06-05 17:30:00'),
(71, '2018-06-05 17:30:00', '2018-06-05 17:45:00'),
(72, '2018-06-05 17:45:00', '2018-06-05 18:00:00'),
(73, '2018-06-05 18:00:00', '2018-06-05 18:15:00'),
(74, '2018-06-05 18:15:00', '2018-06-05 18:30:00'),
(75, '2018-06-05 18:30:00', '2018-06-05 18:45:00'),
(76, '2018-06-05 18:45:00', '2018-06-05 19:00:00'),
(77, '2018-06-05 19:00:00', '2018-06-05 19:15:00'),
(78, '2018-06-05 19:15:00', '2018-06-05 19:30:00'),
(79, '2018-06-05 19:30:00', '2018-06-05 19:45:00'),
(80, '2018-06-05 19:45:00', '2018-06-05 20:00:00'),
(81, '2018-06-05 20:00:00', '2018-06-05 20:15:00'),
(82, '2018-06-05 20:15:00', '2018-06-05 20:30:00'),
(83, '2018-06-05 20:30:00', '2018-06-05 20:45:00'),
(84, '2018-06-05 20:45:00', '2018-06-05 21:00:00'),
(85, '2018-06-05 21:00:00', '2018-06-05 21:15:00'),
(86, '2018-06-05 21:15:00', '2018-06-05 21:30:00'),
(87, '2018-06-05 21:30:00', '2018-06-05 21:45:00'),
(88, '2018-06-05 21:45:00', '2018-06-05 22:00:00'),
(89, '2018-06-05 22:00:00', '2018-06-05 22:15:00'),
(90, '2018-06-05 22:15:00', '2018-06-05 22:30:00'),
(91, '2018-06-05 22:30:00', '2018-06-05 22:45:00'),
(92, '2018-06-05 22:45:00', '2018-06-05 23:00:00'),
(93, '2018-06-05 23:00:00', '2018-06-05 23:15:00'),
(94, '2018-06-05 23:15:00', '2018-06-05 23:30:00'),
(95, '2018-06-05 23:30:00', '2018-06-05 23:45:00'),
(96, '2018-06-05 23:45:00', '2018-06-06 00:00:00'),
(97, '2018-06-05 00:00:00', '2018-06-06 00:00:00');

-- --------------------------------------------------------

--
-- Struktura tabulky `mesto`
--

CREATE TABLE IF NOT EXISTS `mesto` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `mesto`
--

INSERT INTO `mesto` (`id`, `nazev`) VALUES
(1, 'Česká Kubice');

-- --------------------------------------------------------

--
-- Struktura tabulky `ulice`
--

CREATE TABLE IF NOT EXISTS `ulice` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(255) NOT NULL,
  `mesto_id` bigint(20) NOT NULL,
  `zem_sirka` double NOT NULL DEFAULT '-1' COMMENT 'Zemepisna sirka podle GOOGLE.',
  `zem_delka` double NOT NULL DEFAULT '-1' COMMENT 'Zemepisna delka podle GOOGLE.',
  PRIMARY KEY (`id`),
  KEY `fk_ulice_mesto_idx` (`mesto_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `ulice`
--

INSERT INTO `ulice` (`id`, `nazev`, `mesto_id`, `zem_sirka`, `zem_delka`) VALUES
(1, 'Česká Kubice', 1, 49.369388, 12.8588044);

-- --------------------------------------------------------

--
-- Struktura tabulky `vozidlo`
--

CREATE TABLE IF NOT EXISTS `vozidlo` (
  `id` bigint(20) NOT NULL COMMENT 'Odpovídá číslu skupiny vozidla (TypVozidla10 v csv souboru s daty). Hodnoty 0-10.',
  `nazev` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `vozidlo`
--

INSERT INTO `vozidlo` (`id`, `nazev`) VALUES
(0, 'Neznámé vozidlo'),
(1, 'Motocykl'),
(2, 'Auto'),
(3, 'Auto s přívěsem'),
(4, 'Dodávka'),
(5, 'Dodávka s přívěsem'),
(6, 'Lehký nákladní automobil'),
(7, 'Lehký nákladní automobil s přívěsem'),
(8, 'Nákladní automobil'),
(9, 'Nákladní automobil s přívěsem'),
(10, 'Autobus');

-- --------------------------------------------------------

--
-- Struktura tabulky `zarizeni`
--

CREATE TABLE IF NOT EXISTS `zarizeni` (
  `id` varchar(20) NOT NULL COMMENT 'Odpovídá idDevice v location.csv.',
  `smer_popis` varchar(255) NOT NULL COMMENT 'Odpovídá Name v locations.csv.',
  `stav` int(11) NOT NULL,
  `ulice_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_zarizeni_ulice1_idx` (`ulice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `zarizeni`
--

INSERT INTO `zarizeni` (`id`, `smer_popis`, `stav`, `ulice_id`) VALUES
('055', 'Česká Kubice, směr od Německa', 0, 1),
('056', 'Česká Kubice, směr od Babylonu', 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `zaznam`
--

CREATE TABLE IF NOT EXISTS `zaznam` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vozidla_pocet` int(11) NOT NULL,
  `rychlost_prumer` double NOT NULL,
  `vozidla_id` bigint(20) NOT NULL,
  `zaznam_cas_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_zaznam_vozidla1_idx` (`vozidla_id`),
  KEY `fk_zaznam_zaznam_cas1_idx` (`zaznam_cas_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Vypisuji data pro tabulku `zaznam`
--

INSERT INTO `zaznam` (`id`, `vozidla_pocet`, `rychlost_prumer`, `vozidla_id`, `zaznam_cas_id`) VALUES
(1, 1, 12, 0, 1),
(2, 1, 16, 1, 1),
(3, 4, 110.49233333333, 2, 1),
(4, 1, -1, 3, 1),
(5, 1, 19.05, 4, 1),
(6, 2, 25.5, 8, 1),
(7, 1, 65.4, 2, 2),
(8, 1, 65.789, 6, 2),
(9, 1, 25.15, 2, 3),
(10, 1, 12, 0, 4),
(11, 1, 16, 1, 4),
(12, 2, 25.5, 2, 4),
(13, 1, -1, 3, 4),
(14, 1, 19.05, 4, 4),
(15, 4, 110.49233333333, 8, 4),
(16, 1, 25.15, 8, 5);

-- --------------------------------------------------------

--
-- Struktura tabulky `zaznam_cas`
--

CREATE TABLE IF NOT EXISTS `zaznam_cas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `smer` int(11) NOT NULL COMMENT '1 nebo 2 viz struktura idDetektor v csv souboru s daty.',
  `zarizeni_id` varchar(20) NOT NULL,
  `datum_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_zaznam_cas_zarizeni1_idx` (`zarizeni_id`),
  KEY `datum_id` (`datum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `zaznam_cas`
--

INSERT INTO `zaznam_cas` (`id`, `smer`, `zarizeni_id`, `datum_id`) VALUES
(1, 2, '056', 1),
(2, 1, '056', 2),
(3, 2, '056', 2),
(4, 1, '055', 1),
(5, 1, '055', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `zaznam_prum_den`
--

CREATE TABLE IF NOT EXISTS `zaznam_prum_den` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vozidla_pocet` int(11) NOT NULL,
  `rychlost_prumer` double NOT NULL,
  `smer` int(11) NOT NULL,
  `zarizeni_id` varchar(20) NOT NULL,
  `vozidla_id` bigint(20) NOT NULL,
  `datum_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zarizeni_id` (`zarizeni_id`),
  KEY `vozidla_id` (`vozidla_id`),
  KEY `datum_id` (`datum_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Vypisuji data pro tabulku `zaznam_prum_den`
--

INSERT INTO `zaznam_prum_den` (`id`, `vozidla_pocet`, `rychlost_prumer`, `smer`, `zarizeni_id`, `vozidla_id`, `datum_id`) VALUES
(1, 1, 65.4, 1, '056', 2, 97),
(2, 1, 65.789, 1, '056', 6, 97),
(3, 1, 12, 2, '056', 0, 97),
(4, 1, 16, 2, '056', 1, 97),
(5, 5, 89.15675, 2, '056', 2, 97),
(6, 1, -1, 2, '056', 3, 97),
(7, 1, 19.05, 2, '056', 4, 97),
(8, 2, 25.5, 2, '056', 8, 97),
(9, 1, 12, 1, '055', 0, 97),
(10, 1, 16, 1, '055', 1, 97),
(11, 2, 25.5, 1, '055', 2, 97),
(12, 1, -1, 1, '055', 3, 97),
(13, 1, 19.05, 1, '055', 4, 97),
(14, 5, 89.15675, 1, '055', 8, 97);

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `ulice`
--
ALTER TABLE `ulice`
  ADD CONSTRAINT `fk_ulice_mesto` FOREIGN KEY (`mesto_id`) REFERENCES `mesto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `zarizeni`
--
ALTER TABLE `zarizeni`
  ADD CONSTRAINT `fk_zarizeni_ulice1` FOREIGN KEY (`ulice_id`) REFERENCES `ulice` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `zaznam`
--
ALTER TABLE `zaznam`
  ADD CONSTRAINT `fk_zaznam_vozidla1` FOREIGN KEY (`vozidla_id`) REFERENCES `vozidlo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_zaznam_zaznam_cas1` FOREIGN KEY (`zaznam_cas_id`) REFERENCES `zaznam_cas` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `zaznam_cas`
--
ALTER TABLE `zaznam_cas`
  ADD CONSTRAINT `zaznam_cas_ibfk_1` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_zaznam_cas_zarizeni1` FOREIGN KEY (`zarizeni_id`) REFERENCES `zarizeni` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Omezení pro tabulku `zaznam_prum_den`
--
ALTER TABLE `zaznam_prum_den`
  ADD CONSTRAINT `zaznam_prum_den_ibfk_3` FOREIGN KEY (`datum_id`) REFERENCES `datum` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `zaznam_prum_den_ibfk_1` FOREIGN KEY (`zarizeni_id`) REFERENCES `zarizeni` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `zaznam_prum_den_ibfk_2` FOREIGN KEY (`vozidla_id`) REFERENCES `vozidlo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
