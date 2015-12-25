/*
Navicat MySQL Data Transfer

Source Server         : lab
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : af

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-12-24 03:21:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for af_event
-- ----------------------------
DROP TABLE IF EXISTS `af_event`;
CREATE TABLE `af_event` (
  `eventId` bigint(20) NOT NULL AUTO_INCREMENT,
  `eventOwner` bigint(20) NOT NULL DEFAULT '0',
  `eventTitle` varchar(255) NOT NULL DEFAULT '',
  `contentPathA` varchar(255) NOT NULL DEFAULT '',
  `releaseTime` datetime DEFAULT NULL,
  `overTime` datetime DEFAULT NULL,
  `expired` tinyint(4) DEFAULT NULL,
  `eventStatus` varchar(10) DEFAULT NULL,
  `contentPathB` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`eventId`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of af_event
-- ----------------------------

-- ----------------------------
-- Table structure for af_user
-- ----------------------------
DROP TABLE IF EXISTS `af_user`;
CREATE TABLE `af_user` (
  `userId` bigint(20) NOT NULL AUTO_INCREMENT,
  `userAccount` varchar(255) NOT NULL DEFAULT '',
  `userPassword` varchar(255) NOT NULL DEFAULT '',
  `userName` varchar(255) NOT NULL DEFAULT '',
  `userType` varchar(4) NOT NULL DEFAULT '0',
  `userMail` varchar(255) DEFAULT '',
  `userDescription` varchar(255) DEFAULT '',
  `userCreateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of af_user
-- ----------------------------
INSERT INTO `af_user` VALUES ('1', '1', '', '123', 'A', '516001066@qq.com', 'Arp', '2015-12-19 18:48:05');
INSERT INTO `af_user` VALUES ('2', '2', '', '123', 'F', '413969976@qq.com', 'Tf', '2015-12-19 18:48:09');
