-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 05, 2019 at 04:16 PM
-- Server version: 5.7.23
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skola`
--
CREATE DATABASE IF NOT EXISTS `skola` DEFAULT CHARACTER SET utf8 COLLATE utf8_slovenian_ci;
USE `skola`;

-- --------------------------------------------------------

--
-- Table structure for table `generacije`
--

DROP TABLE IF EXISTS `generacije`;
CREATE TABLE IF NOT EXISTS `generacije` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `generacija` varchar(15) COLLATE utf8_slovenian_ci NOT NULL,
  `slika` varchar(255) COLLATE utf8_slovenian_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `predmeti`
--

DROP TABLE IF EXISTS `predmeti`;
CREATE TABLE IF NOT EXISTS `predmeti` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `naziv` varchar(45) COLLATE utf8_slovenian_ci NOT NULL,
  `ocena` tinyint(4) NOT NULL,
  `ucenici_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_predmeti_ucenici1_idx` (`ucenici_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `pretraga_ucenika`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `pretraga_ucenika`;
CREATE TABLE IF NOT EXISTS `pretraga_ucenika` (
`ucenikID` int(10) unsigned
,`prezimeImeUcenika` varchar(91)
,`generacijaID` int(10) unsigned
,`generacija` varchar(15)
,`prosekUcenika` decimal(14,6)
,`matematikaUcenika` decimal(7,4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `prosecne_vrednosti`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `prosecne_vrednosti`;
CREATE TABLE IF NOT EXISTS `prosecne_vrednosti` (
`generacijaID` int(10) unsigned
,`generacija` varchar(15)
,`razredProsek` decimal(14,6)
,`razredMatematika` decimal(7,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `razredi`
--

DROP TABLE IF EXISTS `razredi`;
CREATE TABLE IF NOT EXISTS `razredi` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `odeljenje` varchar(10) COLLATE utf8_slovenian_ci NOT NULL,
  `slika` varchar(255) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `generacije_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_razredi_generacije1_idx` (`generacije_id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `razredi_has_ucenici`
--

DROP TABLE IF EXISTS `razredi_has_ucenici`;
CREATE TABLE IF NOT EXISTS `razredi_has_ucenici` (
  `razredi_id` int(10) UNSIGNED NOT NULL,
  `ucenici_id` int(10) UNSIGNED NOT NULL,
  `slika` varchar(255) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `matematika` tinyint(4) DEFAULT NULL,
  `prosek` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`razredi_id`,`ucenici_id`),
  KEY `fk_razredi_has_ucenici_ucenici1_idx` (`ucenici_id`),
  KEY `fk_razredi_has_ucenici_razredi1_idx` (`razredi_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `rezultati_pretrage`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `rezultati_pretrage`;
CREATE TABLE IF NOT EXISTS `rezultati_pretrage` (
`ucenikID` int(10) unsigned
,`prezimeImeUcenika` varchar(91)
,`generacijaID` int(10) unsigned
,`generacija` varchar(15)
,`prosekUcenika` decimal(14,6)
,`matematikaUcenika` decimal(7,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `ucenici`
--

DROP TABLE IF EXISTS `ucenici`;
CREATE TABLE IF NOT EXISTS `ucenici` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ime` varchar(45) COLLATE utf8_slovenian_ci NOT NULL,
  `prezime` varchar(45) COLLATE utf8_slovenian_ci NOT NULL,
  `ime_oca_majke` varchar(50) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `datum_rodjenja` date NOT NULL,
  `mesto_rodjenja` varchar(50) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `slika` varchar(255) COLLATE utf8_slovenian_ci DEFAULT NULL,
  `komentar` text COLLATE utf8_slovenian_ci,
  `generacije_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ucenici_generacije1_idx` (`generacije_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_slovenian_ci;

-- --------------------------------------------------------

--
-- Structure for view `pretraga_ucenika`
--
DROP TABLE IF EXISTS `pretraga_ucenika`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `pretraga_ucenika`  AS  select `ucenici`.`id` AS `ucenikID`,concat(`ucenici`.`prezime`,' ',`ucenici`.`ime`) AS `prezimeImeUcenika`,`generacije`.`id` AS `generacijaID`,`generacije`.`generacija` AS `generacija`,avg(`razredi_has_ucenici`.`prosek`) AS `prosekUcenika`,avg(`razredi_has_ucenici`.`matematika`) AS `matematikaUcenika` from ((`ucenici` left join `razredi_has_ucenici` on((`ucenici`.`id` = `razredi_has_ucenici`.`ucenici_id`))) join `generacije` on((`generacije`.`id` = `ucenici`.`generacije_id`))) group by `ucenikID` order by `prezimeImeUcenika`,`generacije`.`generacija` ;

-- --------------------------------------------------------

--
-- Structure for view `prosecne_vrednosti`
--
DROP TABLE IF EXISTS `prosecne_vrednosti`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `prosecne_vrednosti`  AS  select `mojatabela`.`generacijaID` AS `generacijaID`,`mojatabela`.`generacija` AS `generacija`,avg(`mojatabela`.`prosek`) AS `razredProsek`,avg(`mojatabela`.`matematika`) AS `razredMatematika` from (select `razredi`.`id` AS `razredID`,`razredi`.`odeljenje` AS `razred`,`generacije`.`id` AS `generacijaID`,`generacije`.`generacija` AS `generacija`,`razredi_has_ucenici`.`prosek` AS `prosek`,`razredi_has_ucenici`.`matematika` AS `matematika` from ((`razredi` join `generacije`) join `razredi_has_ucenici`) where ((`razredi`.`id` = `razredi_has_ucenici`.`razredi_id`) and (`razredi`.`generacije_id` = `generacije`.`id`) and (`razredi_has_ucenici`.`prosek` <> 'NULL'))) `mojatabela` group by `mojatabela`.`razredID` order by `mojatabela`.`generacija`,`mojatabela`.`razred` ;

-- --------------------------------------------------------

--
-- Structure for view `rezultati_pretrage`
--
DROP TABLE IF EXISTS `rezultati_pretrage`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `rezultati_pretrage`  AS  select `pretraga_ucenika`.`ucenikID` AS `ucenikID`,`pretraga_ucenika`.`prezimeImeUcenika` AS `prezimeImeUcenika`,`pretraga_ucenika`.`generacijaID` AS `generacijaID`,`pretraga_ucenika`.`generacija` AS `generacija`,`pretraga_ucenika`.`prosekUcenika` AS `prosekUcenika`,`pretraga_ucenika`.`matematikaUcenika` AS `matematikaUcenika` from `pretraga_ucenika` where (`pretraga_ucenika`.`prezimeImeUcenika` like '%niko%') ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `predmeti`
--
ALTER TABLE `predmeti`
  ADD CONSTRAINT `fk_predmeti_ucenici1` FOREIGN KEY (`ucenici_id`) REFERENCES `ucenici` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `razredi`
--
ALTER TABLE `razredi`
  ADD CONSTRAINT `fk_razredi_generacije1` FOREIGN KEY (`generacije_id`) REFERENCES `generacije` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `razredi_has_ucenici`
--
ALTER TABLE `razredi_has_ucenici`
  ADD CONSTRAINT `fk_razredi_has_ucenici_razredi1` FOREIGN KEY (`razredi_id`) REFERENCES `razredi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_razredi_has_ucenici_ucenici1` FOREIGN KEY (`ucenici_id`) REFERENCES `ucenici` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `ucenici`
--
ALTER TABLE `ucenici`
  ADD CONSTRAINT `fk_ucenici_generacije1` FOREIGN KEY (`generacije_id`) REFERENCES `generacije` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
