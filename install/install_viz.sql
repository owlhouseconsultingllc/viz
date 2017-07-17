-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2017 at 08:17 PM
-- Server version: 5.7.9
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `viz`
--

-- --------------------------------------------------------

--
-- Table structure for table `attempts`
--

DROP TABLE IF EXISTS `attempts`;
CREATE TABLE IF NOT EXISTS `attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(39) NOT NULL,
  `expiredate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `setting` varchar(100) NOT NULL,
  `value` varchar(100) DEFAULT NULL,
  UNIQUE KEY `setting` (`setting`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`setting`, `value`) VALUES
('attack_mitigation_time', '+15 minutes'),
('attempts_before_ban', '30'),
('attempts_before_verify', '5'),
('bcrypt_cost', '10'),
('cookie_domain', NULL),
('cookie_forget', '+30 minutes'),
('cookie_http', '0'),
('cookie_name', 'authID'),
('cookie_path', '/'),
('cookie_remember', '+1 month'),
('cookie_secure', '0'),
('emailmessage_suppress_activation', '0'),
('emailmessage_suppress_reset', '0'),
('mail_charset', 'UTF-8'),
('password_min_score', '0'),
('request_key_expiration', '+10 minutes'),
('site_activation_page', 'activate'),
('site_email', 'david.john.oconnell@gmail.com'),
('site_key', 'fghuior.)/!/jdUkd8s2!7HVHG7777ghh'),
('site_name', 'viz'),
('site_password_reset_page', 'reset'),
('site_timezone', 'America/New_York'),
('site_url', 'http://localhost/viz'),
('smtp', '0'),
('smtp_auth', '1'),
('smtp_host', 'localhost'),
('smtp_password', 'password'),
('smtp_port', '25'),
('smtp_security', NULL),
('smtp_username', 'root'),
('table_attempts', 'attempts'),
('table_requests', 'requests'),
('table_sessions', 'sessions'),
('table_users', 'users'),
('verify_email_max_length', '100'),
('verify_email_min_length', '4'),
('verify_email_use_banlist', '1'),
('verify_password_min_length', '3');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `rkey` varchar(20) NOT NULL,
  `expire` datetime NOT NULL,
  `type` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `hash` varchar(40) NOT NULL,
  `expiredate` datetime NOT NULL,
  `ip` varchar(39) NOT NULL,
  `agent` varchar(200) NOT NULL,
  `cookie_crc` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tabs`
--

DROP TABLE IF EXISTS `tabs`;
CREATE TABLE IF NOT EXISTS `tabs` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `position` int(3) NOT NULL,
  `notes` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `isactive` tinyint(1) NOT NULL DEFAULT '0',
  `isedit` tinyint(1) DEFAULT NULL,
  `isadmin` tinyint(1) DEFAULT NULL,
  `dt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `isactive`, `isedit`, `isadmin`, `dt`) VALUES
(9, 'edit', '$2y$10$Hix3mdy0mRYbwQvfg9gNo.40YQNw7asvXCN569YfTP3w.Wos/AWia', 1, 1, 0, '2017-05-10 18:08:06'),
(13, 'admin', '$2y$10$nVmqoTpFMECh9pLNKM1SUeotVmQwx8CaU1QGxz.O7rb6Xf8QirKum', 1, 1, 1, '2017-05-25 05:08:37'),
(17, 'demo', '$2y$10$cmpBYCdNKrpEJ5YrH/70Fe.0NS6HzCEVkGeor2KEi.RzVXaGp9Sxq', 1, NULL, NULL, '2017-06-01 23:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `visualizations`
--

DROP TABLE IF EXISTS `visualizations`;
CREATE TABLE IF NOT EXISTS `visualizations` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `title` varchar(128) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `position` int(4) NOT NULL,
  `size` varchar(32) NOT NULL,
  `class` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `dbtype` varchar(128) DEFAULT NULL,
  `dbhost` varchar(64) DEFAULT NULL,
  `dbuser` varchar(64) DEFAULT NULL,
  `dbpass` varchar(64) DEFAULT NULL,
  `dbname` varchar(128) DEFAULT NULL,
  `query` varchar(2048) NOT NULL,
  `height` varchar(4) NOT NULL,
  `dateformat` varchar(32) NOT NULL,
  `notes` blob NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
