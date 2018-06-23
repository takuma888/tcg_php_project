-- phpMyAdmin SQL Dump
-- version 4.7.6
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: 2018-06-23 08:48:21
-- 服务器版本： 5.7.20
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `offers`
--
CREATE DATABASE IF NOT EXISTS `offers` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `offers`;

-- --------------------------------------------------------

--
-- 表的结构 `offer_base`
--

DROP TABLE IF EXISTS `offer_base`;
CREATE TABLE `offer_base` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '唯一ID',
  `source` varchar(32) NOT NULL DEFAULT '' COMMENT '来源',
  `offer_id` varchar(256) NOT NULL DEFAULT '' COMMENT '来源的offer的ID',
  `offer_name` varchar(256) NOT NULL DEFAULT '' COMMENT 'offer的产品的名称',
  `package_name` varchar(256) NOT NULL DEFAULT '' COMMENT 'offer的包名',
  `country` varchar(16) NOT NULL DEFAULT '' COMMENT '国家',
  `platform` varchar(32) NOT NULL DEFAULT '' COMMENT '平台，操作系统如ios或者android',
  `payout_type` varchar(8) NOT NULL DEFAULT '' COMMENT '支付的单位',
  `payout` decimal(14,6) NOT NULL DEFAULT '0.000000' COMMENT '支付的值',
  `category` varchar(256) NOT NULL DEFAULT '' COMMENT 'offer的分类',
  `status` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'offer的状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
  PARTITION BY KEY (country);

-- --------------------------------------------------------

--
-- 表的结构 `offer_ext`
--

DROP TABLE IF EXISTS `offer_ext`;
CREATE TABLE `offer_ext` (
  `id` varchar(32) NOT NULL DEFAULT '' COMMENT '唯一ID',
  `info` mediumtext NOT NULL COMMENT 'offer的具体内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `offer_base`
--
ALTER TABLE `offer_base`
  ADD PRIMARY KEY (`id`,`country`),
  ADD KEY `offer` (`source`,`offer_id`),
  ADD KEY `country` (`country`),
  ADD KEY `platform` (`platform`),
  ADD KEY `category` (`category`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `offer_ext`
--
ALTER TABLE `offer_ext`
  ADD PRIMARY KEY (`id`);
COMMIT;
