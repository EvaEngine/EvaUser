/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50623
 Source Host           : localhost
 Source Database       : wscn

 Target Server Type    : MySQL
 Target Server Version : 50623
 File Encoding         : utf-8

 Date: 06/03/2015 11:11:23 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `eva_user_realname_auth_log`
-- ----------------------------
DROP TABLE IF EXISTS `eva_user_realname_auth_log`;
CREATE TABLE `eva_user_realname_auth_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned DEFAULT NULL,
  `username` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `realName` char(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cardNum` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requestAt` int(10) unsigned DEFAULT NULL COMMENT '发起请求的时间',
  `quantity` int(10) unsigned DEFAULT '0' COMMENT '本次查询的身份证数量',
  `status` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compStatus` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `compMessage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
