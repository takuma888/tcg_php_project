-- phpMyAdmin SQL Dump
-- version 4.7.6
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: 2018-06-23 05:30:58
-- 服务器版本： 5.7.20
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `users`
--
CREATE DATABASE IF NOT EXISTS `users` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `users`;

-- --------------------------------------------------------

--
-- 表的结构 `auth_session`
--

DROP TABLE IF EXISTS `auth_session`;
CREATE TABLE IF NOT EXISTS `auth_session` (
  `session_id` varbinary(128) NOT NULL,
  `session_data` blob NOT NULL,
  `session_lifetime` mediumint(9) NOT NULL,
  `session_time` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- 表的结构 `role_group`
--

DROP TABLE IF EXISTS `role_group`;
CREATE TABLE IF NOT EXISTS `role_group` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '主键,角色组的英文名',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `description` varchar(256) NOT NULL DEFAULT '' COMMENT '描述',
  `permissions` text COMMENT '权限,2的n次幂的2进制表示',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `role_permission`
--

DROP TABLE IF EXISTS `role_permission`;
CREATE TABLE IF NOT EXISTS `role_permission` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '主键,角色组的英文名',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `description` varchar(256) NOT NULL DEFAULT '' COMMENT '描述',
  `priority` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优先级',
  `permissions` text COMMENT '权限,2的n次幂的2进制表示',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
CREATE TABLE IF NOT EXISTS `user_auth` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL COMMENT '用户名',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(16) DEFAULT NULL COMMENT '手机',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `session_id` varbinary(128) NOT NULL DEFAULT '' COMMENT 'SESSION ID',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  `register_at` timestamp NULL DEFAULT NULL COMMENT '注册时间戳',
  `login_at` timestamp NULL DEFAULT NULL COMMENT '登录时间戳',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
CREATE TABLE IF NOT EXISTS `user_profile` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '主键',
  `nickname` varchar(128) DEFAULT '' COMMENT '昵称',
  `qq` varchar(16) DEFAULT '' COMMENT 'QQ号码',
  `wei_xin` varchar(32) DEFAULT '' COMMENT '微信号',
  `first_name` varchar(64) NOT NULL DEFAULT '' COMMENT '名字',
  `middle_name` varchar(64) NOT NULL DEFAULT '' COMMENT '中间名',
  `last_name` varchar(64) NOT NULL DEFAULT '' COMMENT '姓',
  `birthday` timestamp NULL DEFAULT NULL COMMENT '生日',
  `language` varchar(8) NOT NULL DEFAULT '' COMMENT '语言',
  `country` varchar(3) NOT NULL DEFAULT '' COMMENT '国家,ISO编码',
  `province` varchar(64) NOT NULL DEFAULT '' COMMENT '省份/州',
  `city` varchar(64) NOT NULL DEFAULT '' COMMENT '城市',
  `address` varchar(256) NOT NULL DEFAULT '' COMMENT '地址',
  `avatar` varchar(256) NOT NULL DEFAULT '' COMMENT '头像',
  `update_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_role`
--

DROP TABLE IF EXISTS `user_role`;
CREATE TABLE IF NOT EXISTS `user_role` (
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `rid` varchar(32) NOT NULL DEFAULT '' COMMENT '角色组ID',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间戳',
  PRIMARY KEY (`uid`,`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;
