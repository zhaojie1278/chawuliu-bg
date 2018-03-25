/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : chawuliu

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-03-21 20:55:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for c_admin
-- ----------------------------
DROP TABLE IF EXISTS `c_admin`;
CREATE TABLE `c_admin` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `ip` varchar(20) DEFAULT '',
  `last_login_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='后台管理账号';

-- ----------------------------
-- Records of c_admin
-- ----------------------------
INSERT INTO `c_admin` VALUES ('1', 'admin', '833e08b17dd64d9a7868f62c2a266994', '1', '127.0.0.1', '2018-03-20 11:12:43', '2018-03-03 02:42:43');
INSERT INTO `c_admin` VALUES ('4', 'zhaojie', 'f66dde290e0e5efc3a4da41ba0b14920', '1', '', null, '2018-03-11 15:39:02');
