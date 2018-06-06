-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Počítač: 127.0.0.1
-- Vygenerováno: Čtv 07. čen 2018, 00:14
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `mesto`
--

CREATE TABLE IF NOT EXISTS `mesto` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `vozidlo`
--

CREATE TABLE IF NOT EXISTS `vozidlo` (
  `id` bigint(20) NOT NULL COMMENT 'Odpovídá číslu skupiny vozidla (TypVozidla10 v csv souboru s daty). Hodnoty 0-10.',
  `nazev` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
