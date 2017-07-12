-- Adminer 4.2.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `timer`;
CREATE TABLE `timer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `flag` tinyint(3) unsigned NOT NULL,
  `parent` bigint(20) unsigned NOT NULL,
  `target` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `stamp` bigint(20) NOT NULL,
  `start` bigint(20) NOT NULL,
  `end` bigint(20) NOT NULL,
  `style` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `flag` (`flag`),
  KEY `parent` (`parent`),
  KEY `target` (`target`),
  KEY `source` (`source`),
  KEY `stamp` (`stamp`),
  KEY `start` (`start`),
  KEY `end` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `chanyeyuan`;
CREATE TABLE `chanyeyuan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '项目名称',
  `type` smallint(6) NOT NULL COMMENT '算法||hidden',
  `post` text NOT NULL COMMENT '数据||hidden',
  `jzmj` varchar(255) NOT NULL COMMENT '建筑面积||hidden',
  `zdmj` varchar(255) NOT NULL COMMENT '占地面积||hidden',
  `glfbz` varchar(255) NOT NULL COMMENT '管理费标准||hidden',
  `fwdj` varchar(255) NOT NULL COMMENT '预计服务等级||hidden',
  `content` text NOT NULL COMMENT '计算结果||editor',
  `authenip` varchar(255) NOT NULL,
  `authenoperator` varchar(255) NOT NULL,
  `authenstamp` bigint(20) NOT NULL,
  `authenaddress` varchar(255) NOT NULL,
  `authenlocation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='=$title';


-- 2015-12-18 09:18:54
