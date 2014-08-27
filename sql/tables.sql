SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `scrapy` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `scrapy`;

DROP TABLE IF EXISTS `eva_user_profiles`;
CREATE TABLE IF NOT EXISTS `eva_user_profiles` (
  `userId` int(10) NOT NULL,
  `site` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoDir` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photoName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fullName` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `relationshipStatus` enum('single','inRelationship','engaged','married','widowed','separated','divorced','other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `height` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `weight` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `addressMore` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zipcode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `degree` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `industry` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `interest` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneBusiness` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneMobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phoneHome` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `signature` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `longitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `localIm` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `internalIm` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `otherIm` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updatedAt` int(10) DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `eva_user_tokens`;
CREATE TABLE IF NOT EXISTS `eva_user_tokens` (
  `sessionId` varchar(40) COLLATE utf8_bin NOT NULL,
  `token` varchar(32) COLLATE utf8_bin NOT NULL,
  `userHash` varchar(32) COLLATE utf8_bin NOT NULL,
  `userId` int(10) NOT NULL,
  `refreshAt` int(10) NOT NULL,
  `expiredAt` int(10) NOT NULL,
  PRIMARY KEY (`sessionId`,`token`,`userHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `eva_user_users`;
CREATE TABLE IF NOT EXISTS `eva_user_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('active','deleted','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'active',
  `accountType` enum('basic','premium','etc') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'basic',
  `screenName` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastName` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `oldPassword` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatarId` int(10) DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8_unicode_ci DEFAULT 'en',
  `emailStatus` enum('active','inactive') COLLATE utf8_unicode_ci DEFAULT 'inactive',
  `emailConfirmedAt` int(10) DEFAULT NULL,
  `createdAt` int(10) DEFAULT NULL,
  `loginAt` int(10) DEFAULT NULL,
  `failedLogins` tinyint(1) DEFAULT '0',
  `loginFailedAt` int(10) DEFAULT NULL,
  `activationHash` varchar(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `activedAt` int(10) DEFAULT NULL,
  `passwordResetHash` char(40) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `passwordResetAt` int(10) DEFAULT NULL,
  `providerType` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'DEFAULT',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;
