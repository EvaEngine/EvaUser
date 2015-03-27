/*
Navicat MySQL Data Transfer

Source Server         : test-web
Source Server Version : 50541
Source Host           : localhost:3306
Source Database       : wscn

Target Server Type    : MYSQL
Target Server Version : 50541
File Encoding         : 65001

Date: 2015-03-27 21:04:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for eva_user_realname_auth_thread
-- ----------------------------
DROP TABLE IF EXISTS `eva_user_realname_auth_thread`;
CREATE TABLE `eva_user_realname_auth_thread` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `requestTime` int(10) unsigned DEFAULT NULL COMMENT '发起请求的时间',
  `quantity` int(10) unsigned DEFAULT NULL COMMENT '本次查询的身份证数量',
  `status` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
