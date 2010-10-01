-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Ven 01 Octobre 2010 à 17:39
-- Version du serveur: 5.1.36
-- Version de PHP: 5.2.5

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

CREATE TABLE IF NOT EXISTS `gen_dicos` (
  `id_dico` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin NOT NULL,
  `maj` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url_source` varchar(255) COLLATE utf8_bin NOT NULL,
  `path_source` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_dico`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_terminaisons`
--

CREATE TABLE IF NOT EXISTS `gen_terminaisons` (
  `id_trm` int(11) NOT NULL AUTO_INCREMENT,
  `id_verbe` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_trm`),
  KEY `id_verbe` (`id_verbe`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=541 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_verbes`
--

CREATE TABLE IF NOT EXISTS `gen_verbes` (
  `id_verbe` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `modele` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_verbe`),
  KEY `num` (`num`),
  KEY `id_dico` (`id_dico`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=16 ;
