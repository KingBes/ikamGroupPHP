/*
Navicat MySQL Data Transfer

Source Server         : 本地sql
Source Server Version : 80012
Source Host           : localhost:3306
Source Database       : ceshi

Target Server Type    : MYSQL
Target Server Version : 80012
File Encoding         : 65001

Date: 2023-04-28 15:52:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for kllxs_api_config
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_api_config`;
CREATE TABLE `kllxs_api_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(255) NOT NULL COMMENT 'api类型',
  `key` varchar(255) NOT NULL COMMENT 'key',
  `val` text COMMENT 'val',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `key` (`key`) USING BTREE,
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='api配置';

-- ----------------------------
-- Records of kllxs_api_config
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_group
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_group`;
CREATE TABLE `kllxs_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `rob_wxid` varchar(255) NOT NULL COMMENT '机器人wxid',
  `isManager` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0非机器人管理1是机器人管理',
  `group_wxid` varchar(255) NOT NULL COMMENT '群wxid',
  `headimgurl` text COMMENT '头像',
  `nickname` varchar(255) NOT NULL COMMENT '群名',
  `member_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '群人数',
  `state` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0关群1开群',
  `out_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '到期时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rob_wxid` (`rob_wxid`),
  KEY `group_wxid` (`group_wxid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群';

-- ----------------------------
-- Records of kllxs_group
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_group_config
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_group_config`;
CREATE TABLE `kllxs_group_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_wxid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '群id',
  `name` varchar(255) NOT NULL COMMENT '名',
  `val` varchar(255) DEFAULT NULL COMMENT '值',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `group_wxid` (`group_wxid`),
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群配置';

-- ----------------------------
-- Records of kllxs_group_config
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_group_member
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_group_member`;
CREATE TABLE `kllxs_group_member` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_wxid` varchar(255) NOT NULL COMMENT '群id',
  `member_wxid` varchar(255) NOT NULL COMMENT '成员wxid',
  `headimgurl` text COMMENT '头像',
  `wx_num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信号',
  `group_nickname` varchar(255) NOT NULL COMMENT '群昵称',
  `sex` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0未知1男2女',
  `diamond` decimal(20,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '钻石',
  `charm` bigint(20) NOT NULL DEFAULT '0' COMMENT '魅力',
  `cash` bigint(20) unsigned NOT NULL DEFAULT '1000' COMMENT '现金',
  `is_black` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0正常1小黑屋',
  `is_owner` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0不是群主1是群主',
  `is_admin` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0普通1管理',
  `is_out_group` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0正常1退群',
  `wealth_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '求财神时间',
  `msg_day_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当天发表次数',
  `msg_num` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '总发表次数',
  `out_group_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '退群时间',
  `come_group_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '进群时间',
  PRIMARY KEY (`id`),
  KEY `group_wxid` (`group_wxid`),
  KEY `member_wxid` (`member_wxid`),
  KEY `diamond` (`diamond`),
  KEY `cash` (`cash`),
  KEY `is_black` (`is_black`),
  KEY `is_admin` (`is_admin`),
  KEY `is_out_group` (`is_out_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='群成员';

-- ----------------------------
-- Records of kllxs_group_member
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_group_punch
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_group_punch`;
CREATE TABLE `kllxs_group_punch` (
  `id` bigint(20) unsigned NOT NULL COMMENT '主键',
  `group_wxid` varchar(255) NOT NULL COMMENT '群id',
  `total` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '总打卡次数',
  `ranking` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '当天排名',
  `series` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '连续打卡次数',
  `diamond` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '获得钻石',
  `cash` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '获得现金',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '打卡时间',
  PRIMARY KEY (`id`),
  KEY `update_time` (`update_time` DESC) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 群打卡';

-- ----------------------------
-- Records of kllxs_group_punch
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_rob_friend
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_rob_friend`;
CREATE TABLE `kllxs_rob_friend` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `rob_wxid` varchar(255) NOT NULL COMMENT '机器人id',
  `headimgurl` text COMMENT '头像',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `sex` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0未知1男2女',
  `wx_num` varchar(255) DEFAULT NULL COMMENT '微信号',
  `note` varchar(255) DEFAULT NULL COMMENT '备注',
  `friend_wxid` varchar(255) NOT NULL COMMENT '好友id',
  `is_host` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0正常1主人',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `rob_wxid` (`rob_wxid`),
  KEY `friend_wxid` (`friend_wxid`),
  KEY `is_host` (`is_host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='机器人好友';

-- ----------------------------
-- Records of kllxs_rob_friend
-- ----------------------------

-- ----------------------------
-- Table structure for kllxs_token
-- ----------------------------
DROP TABLE IF EXISTS `kllxs_token`;
CREATE TABLE `kllxs_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `type` varchar(255) NOT NULL COMMENT '类型',
  `str_val` longtext COMMENT '字符串内容',
  `json_val` json DEFAULT NULL COMMENT 'json内容',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='token信息存储';

-- ----------------------------
-- Records of kllxs_token
-- ----------------------------
