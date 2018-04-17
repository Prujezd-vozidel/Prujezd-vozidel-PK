-- MySQL Script generated by MySQL Workbench
-- 04/07/18 10:59:14
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema prujezd_vozidel
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `prujezd_vozidel` ;

-- -----------------------------------------------------
-- Schema prujezd_vozidel
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `prujezd_vozidel` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `prujezd_vozidel` ;

-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`mesto`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`mesto` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`mesto` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`ulice`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`ulice` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`ulice` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `nazev` VARCHAR(255) NOT NULL,
  `mesto_id` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_ulice_mesto_idx` (`mesto_id` ASC),
  CONSTRAINT `fk_ulice_mesto`
    FOREIGN KEY (`mesto_id`)
    REFERENCES `prujezd_vozidel`.`mesto` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`zarizeni`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`zarizeni` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`zarizeni` (
  `id` VARCHAR(20) NOT NULL COMMENT 'Odpovídá idDevice v location.csv.',
  `smer_popis` VARCHAR(255) NOT NULL COMMENT 'Odpovídá Name v locations.csv.',
  `stav` INT NOT NULL,
  `ulice_id` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_zarizeni_ulice1_idx` (`ulice_id` ASC),
  CONSTRAINT `fk_zarizeni_ulice1`
    FOREIGN KEY (`ulice_id`)
    REFERENCES `prujezd_vozidel`.`ulice` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`zaznam_cas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`zaznam_cas` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`zaznam_cas` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `datetime_od` DATETIME NOT NULL,
  `datetime_do` DATETIME NOT NULL,
  `smer` INT NOT NULL COMMENT '1 nebo 2 viz struktura idDetektor v csv souboru s daty.',
  `zarizeni_id` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_zaznam_cas_zarizeni1_idx` (`zarizeni_id` ASC),
  CONSTRAINT `fk_zaznam_cas_zarizeni1`
    FOREIGN KEY (`zarizeni_id`)
    REFERENCES `prujezd_vozidel`.`zarizeni` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`vozidla`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`vozidla` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`vozidla` (
  `id` BIGINT(20) NOT NULL COMMENT 'Odpovídá číslu skupiny vozidla (TypVozidla10 v csv souboru s daty). Hodnoty 0-10.',
  `nazev` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `prujezd_vozidel`.`zaznam`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `prujezd_vozidel`.`zaznam` ;

CREATE TABLE IF NOT EXISTS `prujezd_vozidel`.`zaznam` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
  `vozidla_pocet` INT NOT NULL,
  `rychlost_prumer` DOUBLE NOT NULL,
  `vozidla_id` BIGINT(20) NOT NULL,
  `zaznam_cas_id` BIGINT(20) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_zaznam_vozidla1_idx` (`vozidla_id` ASC),
  INDEX `fk_zaznam_zaznam_cas1_idx` (`zaznam_cas_id` ASC),
  CONSTRAINT `fk_zaznam_vozidla1`
    FOREIGN KEY (`vozidla_id`)
    REFERENCES `prujezd_vozidel`.`vozidla` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_zaznam_zaznam_cas1`
    FOREIGN KEY (`zaznam_cas_id`)
    REFERENCES `prujezd_vozidel`.`zaznam_cas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;