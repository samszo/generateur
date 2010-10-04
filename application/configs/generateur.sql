-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 04 Octobre 2010 à 22:31
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
-- Structure de la table `gen_adjectifs`
--

CREATE TABLE IF NOT EXISTS `gen_adjectifs` (
  `id_adjectif` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `elision` int(11) NOT NULL,
  `prefix` varchar(255) COLLATE utf8_bin NOT NULL,
  `m_s` varchar(255) COLLATE utf8_bin NOT NULL,
  `f_s` varchar(255) COLLATE utf8_bin NOT NULL,
  `m_p` varchar(255) COLLATE utf8_bin NOT NULL,
  `f_p` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_adjectif`),
  KEY `id_dico` (`id_dico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_complements`
--

CREATE TABLE IF NOT EXISTS `gen_complements` (
  `id_cpm` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_cpm`),
  KEY `id_dico` (`id_dico`),
  KEY `num` (`num`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=534 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_concepts`
--

CREATE TABLE IF NOT EXISTS `gen_concepts` (
  `id_concept` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  `type` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_concept`),
  KEY `id_dico` (`id_dico`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_determinants`
--

CREATE TABLE IF NOT EXISTS `gen_determinants` (
  `id_dtm` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_dtm`),
  KEY `num` (`num`),
  KEY `id_dico` (`id_dico`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2529 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Structure de la table `gen_syntagmes`
--

CREATE TABLE IF NOT EXISTS `gen_syntagmes` (
  `id_syn` int(11) NOT NULL AUTO_INCREMENT,
  `id_dico` int(11) NOT NULL,
  `num` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `lib` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_syn`),
  KEY `num` (`num`),
  KEY `id_dico` (`id_dico`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=218 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1884 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=52 ;
