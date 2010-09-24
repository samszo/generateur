-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 24 Septembre 2010 à 19:32
-- Version du serveur: 5.1.30
-- Version de PHP: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `generateur`
--

-- --------------------------------------------------------

--
-- Structure de la table `gen_dicos`
--

DROP TABLE IF EXISTS `gen_dicos`;
CREATE TABLE IF NOT EXISTS `gen_dicos` (
  `id_dico` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `maj` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_dico`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC AUTO_INCREMENT=3 ;

--
-- Contenu de la table `gen_dicos`
--

INSERT INTO `gen_dicos` (`id_dico`, `url`, `type`, `maj`) VALUES
(1, 'http://localhost/generateur/data/upload/DS_determinants.rtf', 'dÃ©terminants', '2010-09-24 18:47:23'),
(2, 'http://localhost/generateur/data/upload/DS_determinants.rtf', 'dÃ©terminants', '2010-09-24 18:48:45');
