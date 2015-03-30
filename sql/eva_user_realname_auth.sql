/*
Navicat MySQL Data Transfer

Source Server         : test-web
Source Server Version : 50541
Source Host           : localhost:3306
Source Database       : wscn

Target Server Type    : MYSQL
Target Server Version : 50541
File Encoding         : 65001

Date: 2015-03-27 21:04:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for eva_user_realname_auth
-- ----------------------------
DROP TABLE IF EXISTS `eva_user_realname_auth`;
CREATE TABLE `eva_user_realname_auth` (
  `userId` int(10) unsigned NOT NULL,
  `realName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cardNum` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(10) DEFAULT '0' COMMENT '0未验证；3验证通过；2不一致；1号码不存在',
  `createTime` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
