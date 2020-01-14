-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 14, 2020 at 04:41 PM
-- Server version: 5.5.27
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pg`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

CREATE TABLE `access` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301`
--

CREATE TABLE `access1301` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_1`
--

CREATE TABLE `access1301_1` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_2`
--

CREATE TABLE `access1301_2` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_3`
--

CREATE TABLE `access1301_3` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_4`
--

CREATE TABLE `access1301_4` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_5`
--

CREATE TABLE `access1301_5` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_7`
--

CREATE TABLE `access1301_7` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_20`
--

CREATE TABLE `access1301_20` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_21`
--

CREATE TABLE `access1301_21` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_30`
--

CREATE TABLE `access1301_30` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_31`
--

CREATE TABLE `access1301_31` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_32`
--

CREATE TABLE `access1301_32` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_33`
--

CREATE TABLE `access1301_33` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_34`
--

CREATE TABLE `access1301_34` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_35`
--

CREATE TABLE `access1301_35` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_36`
--

CREATE TABLE `access1301_36` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_37`
--

CREATE TABLE `access1301_37` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_38`
--

CREATE TABLE `access1301_38` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access1301_39`
--

CREATE TABLE `access1301_39` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_1`
--

CREATE TABLE `access_1` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_2`
--

CREATE TABLE `access_2` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_3`
--

CREATE TABLE `access_3` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_4`
--

CREATE TABLE `access_4` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_5`
--

CREATE TABLE `access_5` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_7`
--

CREATE TABLE `access_7` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_20`
--

CREATE TABLE `access_20` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_21`
--

CREATE TABLE `access_21` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_30`
--

CREATE TABLE `access_30` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_31`
--

CREATE TABLE `access_31` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_32`
--

CREATE TABLE `access_32` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_33`
--

CREATE TABLE `access_33` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_34`
--

CREATE TABLE `access_34` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_35`
--

CREATE TABLE `access_35` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_36`
--

CREATE TABLE `access_36` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_37`
--

CREATE TABLE `access_37` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_38`
--

CREATE TABLE `access_38` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `access_39`
--

CREATE TABLE `access_39` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `env_temp2` float NOT NULL DEFAULT '0',
  `delay` int(11) DEFAULT '0',
  `sign` int(11) DEFAULT '0',
  `cindex` int(11) DEFAULT '0',
  `lcount` int(11) NOT NULL DEFAULT '0' COMMENT 'lost count',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '1' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `state` int(11) NOT NULL DEFAULT '0',
  `real_temp` float NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `animal`
--

CREATE TABLE `animal` (
  `id` int(11) NOT NULL,
  `sn` varchar(10) NOT NULL COMMENT '编号',
  `devid` int(11) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `psnid` int(11) NOT NULL COMMENT 'psn',
  `sex` int(11) NOT NULL COMMENT '1公,2母',
  `kind` int(11) NOT NULL COMMENT '品种',
  `type` int(11) NOT NULL COMMENT '类型',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `birthweight` float DEFAULT NULL COMMENT '出生重量',
  `childnum` int(11) DEFAULT NULL COMMENT '胎次',
  `shedid` int(11) NOT NULL COMMENT '舍号',
  `area` int(11) NOT NULL COMMENT '区域A-(1-n)',
  `fold` int(11) NOT NULL DEFAULT '0' COMMENT '栏',
  `fathersn` varchar(10) DEFAULT NULL COMMENT '父亲编号',
  `mothersn` varchar(10) DEFAULT NULL COMMENT '母亲编号',
  `entertype` int(11) NOT NULL COMMENT '入场类型',
  `enterdate` date DEFAULT NULL COMMENT '入场日期',
  `entersource` varchar(80) DEFAULT NULL COMMENT '入场来源',
  `leaveweight` float NOT NULL DEFAULT '0' COMMENT '外购体重',
  `state` int(11) NOT NULL DEFAULT '0' COMMENT '0正常,1离场',
  `leavedate` date DEFAULT NULL,
  `photo` varchar(40) DEFAULT NULL COMMENT '头像',
  `info` varchar(80) DEFAULT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `anlkind`
--

CREATE TABLE `anlkind` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1牛,2猪',
  `subtype` int(11) NOT NULL COMMENT '1奶牛2肉牛',
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `anltype`
--

CREATE TABLE `anltype` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `sub_type` int(11) NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bdevice`
--

CREATE TABLE `bdevice` (
  `autoid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `tsn` varchar(16) NOT NULL COMMENT '统一编号',
  `psn` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `number` varchar(32) DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `uptime` varchar(4) NOT NULL,
  `count` int(4) NOT NULL DEFAULT '1' COMMENT '采集次数',
  `shed` int(11) NOT NULL DEFAULT '1' COMMENT '棚',
  `state` int(11) NOT NULL DEFAULT '0',
  `rate_id` int(11) NOT NULL DEFAULT '0',
  `rate` int(11) NOT NULL DEFAULT '432000000',
  `slave_stop` int(11) NOT NULL DEFAULT '0' COMMENT '1301停止开关',
  `assert_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'read assert',
  `url_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'url更换标志',
  `url` varchar(64) NOT NULL COMMENT '域名',
  `info` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bdevice`
--

INSERT INTO `bdevice` (`autoid`, `psnid`, `tsn`, `psn`, `id`, `number`, `version`, `uptime`, `count`, `shed`, `state`, `rate_id`, `rate`, `slave_stop`, `assert_flag`, `url_flag`, `url`, `info`) VALUES
(14, 1, '1086756300', 1, 1, NULL, 1, '0400', 2, 1, 0, 2, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(15, 1, '1086756300', 1, 2, NULL, 1, '0400', 2, 1, 0, 2, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(16, 2, '2086756300', 2, 1, NULL, 1, '0400', 2, 1, 0, 7, 434000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(17, 2, '2086756300', 2, 2, NULL, 1, '0400', 2, 1, 0, 7, 434000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(18, 1, '1086756300', 1, 3, NULL, 1, '0400', 2, 1, 0, 2, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(19, 2, '2086756300', 2, 3, NULL, 1, '0400', 2, 1, 0, 7, 434000000, 0, 0, 0, 'iot.xunrun.com.cn', ''),
(25, 11, '1086756300', 5, 1, NULL, 3, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(27, 11, '1086756300', 5, 2, NULL, 3, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(28, 12, '2086756300', 7, 1, NULL, 2, '0100', 1, 1, 0, 0, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(29, 12, '2086756300', 7, 2, NULL, 2, '0100', 1, 1, 0, 0, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(30, 12, '2086756300', 7, 3, NULL, 3, '0100', 1, 1, 0, 0, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(31, 20, '1086756300', 20, 1, NULL, 3, '0400', 4, 1, 0, 1, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(32, 20, '1086756300', 20, 2, NULL, 3, '0400', 4, 1, 0, 1, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(33, 4, '1086100000', 4, 1, NULL, 3, '0010', 1, 1, 0, 5, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(34, 4, '1086100000', 4, 2, NULL, 3, '0010', 1, 1, 0, 5, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(35, 30, '1086756300', 30, 1, NULL, 0, '0400', 4, 1, 0, 0, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(36, 31, '1086756300', 31, 1, NULL, 0, '0400', 4, 1, 0, 1, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(37, 32, '1086756300', 32, 1, NULL, 0, '0400', 4, 1, 0, 2, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(38, 33, '1086756300', 33, 1, NULL, 0, '0400', 4, 1, 0, 3, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(39, 34, '1086756300', 34, 1, NULL, 3, '0400', 4, 1, 0, 4, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(40, 35, '1086756300', 35, 1, '1440480508415', 3, '0100', 1, 1, 0, 5, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(41, 36, '1086756300', 36, 1, NULL, 0, '0400', 4, 1, 0, 6, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(42, 37, '1086756300', 37, 1, NULL, 0, '0400', 4, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(43, 38, '1086756300', 38, 1, NULL, 0, '0400', 4, 1, 0, 8, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(44, 39, '1086756300', 39, 1, NULL, 0, '0400', 4, 1, 0, 9, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(45, 11, '1086756300', 5, 5, NULL, 3, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(48, 11, '1086756300', 5, 15, NULL, 3, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(49, 11, '1086756300', 5, 3, NULL, 3, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(50, 11, '1086756300', 5, 4, NULL, 0, '0100', 1, 1, 0, 7, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(51, 35, '1086756300', 35, 2, '1440480508414', 3, '0100', 1, 1, 0, 5, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL),
(52, 3, '1086100000', 3, 1, '1501015076612', 0, '0400', 4, 1, 0, 1, 432000000, 0, 0, 0, 'iot.xunrun.com.cn', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `breed`
--

CREATE TABLE `breed` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `time` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brssi`
--

CREATE TABLE `brssi` (
  `id` int(11) NOT NULL,
  `station` int(11) NOT NULL DEFAULT '1278',
  `psnid` int(11) NOT NULL,
  `bsn` int(11) NOT NULL,
  `rssi` int(11) NOT NULL,
  `sn1` int(11) NOT NULL DEFAULT '0',
  `rssi1` int(11) NOT NULL DEFAULT '0',
  `sn2` int(11) NOT NULL DEFAULT '0',
  `rssi2` int(11) NOT NULL DEFAULT '0',
  `sn3` int(11) NOT NULL DEFAULT '0',
  `rssi3` int(11) NOT NULL DEFAULT '0',
  `sn4` int(11) NOT NULL DEFAULT '0',
  `rssi4` int(11) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `changeidlog`
--

CREATE TABLE `changeidlog` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `rfid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `old_psn` int(11) NOT NULL,
  `old_devid` int(11) NOT NULL,
  `new_devid` int(11) NOT NULL DEFAULT '0',
  `flag` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `dailywork`
--

CREATE TABLE `dailywork` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `msg` varchar(128) NOT NULL,
  `picurl` varchar(512) DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `id` int(11) NOT NULL,
  `psn` int(11) NOT NULL DEFAULT '0' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT 'psnid',
  `shed` int(11) NOT NULL COMMENT '棚',
  `fold` int(11) NOT NULL COMMENT '栏',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  `flag` int(1) NOT NULL COMMENT '状态',
  `state` int(1) NOT NULL COMMENT '情况',
  `s_count` int(11) NOT NULL COMMENT '生产次数',
  `rid` varchar(20) NOT NULL COMMENT 'RFID',
  `age` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `sn` varchar(40) DEFAULT NULL COMMENT '牧场SN',
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `battery` int(1) NOT NULL DEFAULT '0',
  `dev_state` int(11) NOT NULL DEFAULT '0',
  `dev_type` int(11) NOT NULL DEFAULT '0',
  `dev_assert` int(11) NOT NULL DEFAULT '0' COMMENT 'assert info',
  `avg_temp` float NOT NULL DEFAULT '0' COMMENT '平均温度',
  `re_flag` int(11) NOT NULL DEFAULT '0' COMMENT '回收标志1回收,2成功'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备';

-- --------------------------------------------------------

--
-- Table structure for table `entertype`
--

CREATE TABLE `entertype` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `entertype`
--

INSERT INTO `entertype` (`id`, `name`) VALUES
(1, '外购'),
(2, '自繁');

-- --------------------------------------------------------

--
-- Table structure for table `exitmanager`
--

CREATE TABLE `exitmanager` (
  `id` int(11) NOT NULL,
  `sn` varchar(10) NOT NULL,
  `psnid` int(11) NOT NULL,
  `shedid` int(11) NOT NULL,
  `date` date NOT NULL,
  `weight` float NOT NULL,
  `type` int(11) NOT NULL,
  `rcause` varchar(40) NOT NULL,
  `direction` varchar(40) NOT NULL,
  `workerid` int(11) NOT NULL,
  `info` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `exittype`
--

CREATE TABLE `exittype` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `exittype`
--

INSERT INTO `exittype` (`id`, `name`) VALUES
(1, '淘汰'),
(2, '死亡'),
(3, '出栏');

-- --------------------------------------------------------

--
-- Table structure for table `factory`
--

CREATE TABLE `factory` (
  `id` int(11) NOT NULL,
  `productno` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `state` int(11) NOT NULL COMMENT '1初始2成功3失败',
  `fsn` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `field`
--

CREATE TABLE `field` (
  `id` int(11) NOT NULL,
  `shedid` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `areas` int(11) NOT NULL COMMENT '区域数量',
  `folds` int(11) NOT NULL COMMENT '栏位数量',
  `workerid1` int(11) NOT NULL DEFAULT '0' COMMENT '饲养员',
  `workerid2` int(11) NOT NULL DEFAULT '0' COMMENT '兽医',
  `workerid3` int(11) NOT NULL DEFAULT '0',
  `psnid` int(11) NOT NULL COMMENT 'psn'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `homepage`
--

CREATE TABLE `homepage` (
  `id` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0' COMMENT '1,更改域名',
  `hostname` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lostacc`
--

CREATE TABLE `lostacc` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `sn` varchar(40) DEFAULT NULL,
  `shed` int(11) NOT NULL COMMENT '棚',
  `area` int(11) NOT NULL DEFAULT '0',
  `fold` int(11) NOT NULL COMMENT '栏',
  `state` int(11) NOT NULL,
  `flag` int(11) NOT NULL DEFAULT '0' COMMENT '治疗状态',
  `help` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '标记颜色',
  `days` int(11) NOT NULL DEFAULT '0',
  `temp1` float NOT NULL,
  `time` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='不良设备';

-- --------------------------------------------------------

--
-- Table structure for table `newdevice`
--

CREATE TABLE `newdevice` (
  `id` int(11) NOT NULL,
  `psn` int(11) NOT NULL DEFAULT '0' COMMENT 'psnid',
  `shed` int(11) NOT NULL COMMENT '棚',
  `fold` int(11) NOT NULL COMMENT '栏',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '时间',
  `flag` int(1) NOT NULL COMMENT '状态',
  `state` int(1) NOT NULL COMMENT '情况',
  `s_count` int(11) NOT NULL COMMENT '生产次数',
  `rid` varchar(20) NOT NULL COMMENT 'RFID',
  `age` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `sn` varchar(40) DEFAULT NULL COMMENT '牧场SN',
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `battery` int(1) NOT NULL DEFAULT '0',
  `dev_state` int(11) NOT NULL DEFAULT '0',
  `dev_type` int(11) NOT NULL DEFAULT '0',
  `dev_assert` int(11) NOT NULL DEFAULT '0' COMMENT 'assert info',
  `avg_temp` float NOT NULL DEFAULT '0' COMMENT '平均温度',
  `re_flag` int(11) NOT NULL DEFAULT '0' COMMENT '回收标志1回收,2成功'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备';

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `sn` int(11) NOT NULL,
  `freq` int(11) NOT NULL,
  `rtc` int(11) NOT NULL,
  `adc` int(11) NOT NULL,
  `rt` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `adc1` int(11) NOT NULL,
  `adc2` int(11) NOT NULL,
  `adc3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2019050801`
--

CREATE TABLE `product2019050801` (
  `sn` int(9) DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2019051301`
--

CREATE TABLE `product2019051301` (
  `sn` int(9) DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2019081901`
--

CREATE TABLE `product2019081901` (
  `sn` int(9) DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2019111301`
--

CREATE TABLE `product2019111301` (
  `sn` int(9) DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2019111401`
--

CREATE TABLE `product2019111401` (
  `sn` int(9) DEFAULT NULL,
  `time` varchar(19) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `product2020010801`
--

CREATE TABLE `product2020010801` (
  `sn` int(11) DEFAULT NULL,
  `time` varchar(14) DEFAULT NULL,
  `state` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `productlog`
--

CREATE TABLE `productlog` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `number` varchar(10) NOT NULL,
  `time` datetime NOT NULL,
  `factoryinfo` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `psn`
--

CREATE TABLE `psn` (
  `id` int(11) NOT NULL,
  `tsn` varchar(10) NOT NULL COMMENT '国标编码',
  `sn` int(11) NOT NULL COMMENT 'SN',
  `check_value` float NOT NULL COMMENT '补偿系数',
  `base_temp` float NOT NULL COMMENT '基础温度',
  `htemplev1` float NOT NULL,
  `htemplev2` float NOT NULL,
  `ltemplev1` float NOT NULL,
  `ltemplev2` float NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '18',
  `info` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `psn`
--

INSERT INTO `psn` (`id`, `tsn`, `sn`, `check_value`, `base_temp`, `htemplev1`, `htemplev2`, `ltemplev1`, `ltemplev2`, `userid`, `info`) VALUES
(1, '1086756300', 1, 0.25, 39, 39.5, 40, 37.5, 30, 2, '兴鸿旺牧业'),
(2, '2086756300', 2, 0.25, 39, 39.5, 40, 37.5, 30, 3, '方圆养殖'),
(3, '1086100000', 3, 0.25, 39, 39.5, 40, 37.5, 30, 1, '北京测试'),
(4, '1086100000', 4, 0.25, 39, 39.5, 40, 37.5, 30, 1, '测试2'),
(11, '1086756300', 5, 0.25, 39, 39.5, 40, 37.5, 30, 6, '实验'),
(12, '2086756300', 7, 0.25, 39, 39.5, 40, 37.5, 30, 5, '方圆养殖2'),
(20, '1086756300', 20, 0.25, 39, 39.5, 40, 37.5, 30, 18, '隆德县测试'),
(21, '1086756300', 21, 0.25, 39, 39.5, 40, 37.5, 30, 18, '隆德县测试'),
(30, '1086756300', 30, 0.25, 39, 39.5, 40, 37.5, 30, 18, '穆沟一组'),
(31, '1086756300', 31, 0.25, 39, 39.5, 40, 37.5, 30, 18, '穆沟二组'),
(32, '1086756300', 32, 0.25, 39, 39.5, 40, 37.5, 30, 18, '李士一组二组'),
(33, '1086756300', 33, 0.25, 39, 39.5, 40, 37.5, 30, 18, '李士三组四组'),
(34, '1086756300', 34, 0.25, 39, 39.5, 40, 37.5, 30, 18, '李士五组六组'),
(35, '1086756300', 35, 0.25, 39, 39.5, 40, 37.5, 30, 18, '串河村五组杨河牧业串河村集体'),
(36, '1086756300', 36, 0.25, 39, 39.5, 40, 37.5, 30, 18, '串河村三组四组'),
(37, '1086756300', 37, 0.25, 39, 39.5, 40, 37.5, 30, 18, '串河村一组二组'),
(38, '1086756300', 38, 0.25, 39, 39.5, 40, 37.5, 30, 18, '张树村一组二组'),
(39, '1086756300', 39, 0.25, 39, 39.5, 40, 37.5, 30, 18, '张树村三组四组腾龙牧业合作社');

-- --------------------------------------------------------

--
-- Table structure for table `purptype`
--

CREATE TABLE `purptype` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  `anitype` int(11) NOT NULL COMMENT '1牛2猪'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `purptype`
--

INSERT INTO `purptype` (`id`, `type`, `name`, `anitype`) VALUES
(1, 0, '犊牛舍', 1),
(2, 1, '育成牛舍', 1),
(3, 2, '基础母牛舍', 1),
(4, 3, '隔离牛舍', 1),
(5, 0, '幼猪舍', 2),
(6, 1, '成猪舍', 2),
(7, 2, '母猪舍', 2),
(8, 3, '隔离猪舍', 2);

-- --------------------------------------------------------

--
-- Table structure for table `rate`
--

CREATE TABLE `rate` (
  `id` int(11) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rate`
--

INSERT INTO `rate` (`id`, `rate_id`, `rate`) VALUES
(1, 0, 431687500),
(2, 1, 431937500),
(3, 2, 432000000),
(4, 3, 432062500),
(5, 4, 432312500),
(6, 5, 433993750),
(7, 6, 433937500),
(8, 7, 434000000),
(9, 8, 434062500),
(10, 9, 434312500);

-- --------------------------------------------------------

--
-- Table structure for table `recovery`
--

CREATE TABLE `recovery` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `msg` varchar(250) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sickness`
--

CREATE TABLE `sickness` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `sn` varchar(40) DEFAULT NULL,
  `shed` int(11) NOT NULL COMMENT '棚',
  `area` int(11) NOT NULL DEFAULT '0',
  `fold` int(11) NOT NULL COMMENT '栏',
  `state` int(11) NOT NULL,
  `flag` int(11) NOT NULL DEFAULT '0' COMMENT '治疗状态',
  `help` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0' COMMENT '标记颜色',
  `days` int(11) NOT NULL DEFAULT '0',
  `temp1` float NOT NULL,
  `time` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='不良设备';

-- --------------------------------------------------------

--
-- Table structure for table `sickrecord`
--

CREATE TABLE `sickrecord` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `msg` varchar(250) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sicktype`
--

CREATE TABLE `sicktype` (
  `id` int(11) NOT NULL,
  `kind` int(11) NOT NULL COMMENT '1,高温,2低温',
  `type` int(11) NOT NULL,
  `name` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sicktype`
--

INSERT INTO `sicktype` (`id`, `kind`, `type`, `name`) VALUES
(1, 1, 11, '生病'),
(2, 1, 12, '发情'),
(3, 1, 13, '床灯'),
(4, 1, 14, '配种'),
(5, 1, 15, '运动'),
(6, 1, 16, '晒太阳'),
(7, 1, 17, '发热源'),
(8, 1, 18, '其他'),
(9, 2, 31, '生病'),
(10, 2, 32, '进水'),
(11, 2, 33, '死亡'),
(12, 2, 34, '低温源'),
(13, 2, 35, '脱落'),
(14, 2, 36, '其他'),
(15, 0, 1, '断奶'),
(16, 0, 2, '孕期'),
(17, 0, 3, '产床'),
(18, 0, 4, '哺乳期'),
(20, 0, 0, '未标记'),
(21, 3, 51, '淘汰');

-- --------------------------------------------------------

--
-- Table structure for table `taccess`
--

CREATE TABLE `taccess` (
  `id` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `temp2` float DEFAULT NULL,
  `env_temp` float NOT NULL,
  `delay` int(11) DEFAULT '0',
  `time` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `psn` int(11) NOT NULL DEFAULT '0' COMMENT 'psnid',
  `psnid` int(11) NOT NULL DEFAULT '0' COMMENT '上报厂号',
  `sid` int(11) NOT NULL DEFAULT '0',
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `temperror`
--

CREATE TABLE `temperror` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL COMMENT '设备ID',
  `temp1` float NOT NULL,
  `time` int(11) NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `autoid` int(11) NOT NULL,
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(125) NOT NULL,
  `pwd` varchar(125) NOT NULL,
  `info` varchar(80) DEFAULT NULL,
  `tmp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`autoid`, `id`, `name`, `pwd`, `info`, `tmp`) VALUES
(1, 18, 'testhome', '0fbf97af50536d28480e64d810a5c98f', '', 'test234'),
(2, 25, 'xinghongwang1', '8f9efa027aac945fc64d9d31926343b5', '兴鸿旺牧业', 'xhwang125'),
(3, 23, 'fangyuan1', '2a1ce5ef70bd75f59dfe3e6d1e23f431', '方圆养殖', 'fyuan410'),
(4, 24, '13801394601', 'fd9f83a9a902dd974bd47f2004dd8f48', NULL, 'test234'),
(5, 25, 'fangyuan2', '2a1ce5ef70bd75f59dfe3e6d1e23f431', '方圆养殖', 'fyuan410'),
(6, 26, 'djhj2008', '0fbf97af50536d28480e64d810a5c98f', '测试', 'test234'),
(7, 100, 'admin', '4b78e581bdaffa037a6b11d58bdc934a', '管理员', '');

-- --------------------------------------------------------

--
-- Table structure for table `usermsginfo`
--

CREATE TABLE `usermsginfo` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `info` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `useropenid`
--

CREATE TABLE `useropenid` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `openid` varchar(80) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `worker`
--

CREATE TABLE `worker` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `type` int(11) NOT NULL,
  `phone` varchar(11) DEFAULT NULL,
  `info` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `workertype`
--

CREATE TABLE `workertype` (
  `id` int(11) NOT NULL,
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301`
--
ALTER TABLE `access1301`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_1`
--
ALTER TABLE `access1301_1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_2`
--
ALTER TABLE `access1301_2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_3`
--
ALTER TABLE `access1301_3`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_4`
--
ALTER TABLE `access1301_4`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_5`
--
ALTER TABLE `access1301_5`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_7`
--
ALTER TABLE `access1301_7`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_20`
--
ALTER TABLE `access1301_20`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_21`
--
ALTER TABLE `access1301_21`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_30`
--
ALTER TABLE `access1301_30`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_31`
--
ALTER TABLE `access1301_31`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_32`
--
ALTER TABLE `access1301_32`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_33`
--
ALTER TABLE `access1301_33`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_34`
--
ALTER TABLE `access1301_34`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_35`
--
ALTER TABLE `access1301_35`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_36`
--
ALTER TABLE `access1301_36`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_37`
--
ALTER TABLE `access1301_37`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_38`
--
ALTER TABLE `access1301_38`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access1301_39`
--
ALTER TABLE `access1301_39`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_1`
--
ALTER TABLE `access_1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_2`
--
ALTER TABLE `access_2`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_3`
--
ALTER TABLE `access_3`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_4`
--
ALTER TABLE `access_4`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_5`
--
ALTER TABLE `access_5`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_7`
--
ALTER TABLE `access_7`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_20`
--
ALTER TABLE `access_20`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_21`
--
ALTER TABLE `access_21`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_30`
--
ALTER TABLE `access_30`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_31`
--
ALTER TABLE `access_31`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_32`
--
ALTER TABLE `access_32`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_33`
--
ALTER TABLE `access_33`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_34`
--
ALTER TABLE `access_34`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_35`
--
ALTER TABLE `access_35`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_36`
--
ALTER TABLE `access_36`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_37`
--
ALTER TABLE `access_37`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_38`
--
ALTER TABLE `access_38`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_39`
--
ALTER TABLE `access_39`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `animal`
--
ALTER TABLE `animal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `anlkind`
--
ALTER TABLE `anlkind`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `anltype`
--
ALTER TABLE `anltype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bdevice`
--
ALTER TABLE `bdevice`
  ADD PRIMARY KEY (`autoid`);

--
-- Indexes for table `breed`
--
ALTER TABLE `breed`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brssi`
--
ALTER TABLE `brssi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `changeidlog`
--
ALTER TABLE `changeidlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dailywork`
--
ALTER TABLE `dailywork`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shed` (`shed`,`fold`),
  ADD KEY `devid` (`devid`);

--
-- Indexes for table `exitmanager`
--
ALTER TABLE `exitmanager`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `factory`
--
ALTER TABLE `factory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homepage`
--
ALTER TABLE `homepage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lostacc`
--
ALTER TABLE `lostacc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newdevice`
--
ALTER TABLE `newdevice`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shed` (`shed`,`fold`),
  ADD KEY `devid` (`devid`);

--
-- Indexes for table `productlog`
--
ALTER TABLE `productlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `psn`
--
ALTER TABLE `psn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purptype`
--
ALTER TABLE `purptype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rate`
--
ALTER TABLE `rate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recovery`
--
ALTER TABLE `recovery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sickness`
--
ALTER TABLE `sickness`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sickrecord`
--
ALTER TABLE `sickrecord`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sicktype`
--
ALTER TABLE `sicktype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taccess`
--
ALTER TABLE `taccess`
  ADD PRIMARY KEY (`id`),
  ADD KEY `devid` (`devid`);

--
-- Indexes for table `temperror`
--
ALTER TABLE `temperror`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`autoid`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `usermsginfo`
--
ALTER TABLE `usermsginfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `useropenid`
--
ALTER TABLE `useropenid`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `worker`
--
ALTER TABLE `worker`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workertype`
--
ALTER TABLE `workertype`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access`
--
ALTER TABLE `access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12210416;

--
-- AUTO_INCREMENT for table `access1301`
--
ALTER TABLE `access1301`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353759;

--
-- AUTO_INCREMENT for table `access1301_1`
--
ALTER TABLE `access1301_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_2`
--
ALTER TABLE `access1301_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_3`
--
ALTER TABLE `access1301_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_4`
--
ALTER TABLE `access1301_4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=345963;

--
-- AUTO_INCREMENT for table `access1301_5`
--
ALTER TABLE `access1301_5`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1140;

--
-- AUTO_INCREMENT for table `access1301_7`
--
ALTER TABLE `access1301_7`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `access1301_20`
--
ALTER TABLE `access1301_20`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_21`
--
ALTER TABLE `access1301_21`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_30`
--
ALTER TABLE `access1301_30`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_31`
--
ALTER TABLE `access1301_31`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_32`
--
ALTER TABLE `access1301_32`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_33`
--
ALTER TABLE `access1301_33`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_34`
--
ALTER TABLE `access1301_34`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `access1301_35`
--
ALTER TABLE `access1301_35`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3039;

--
-- AUTO_INCREMENT for table `access1301_36`
--
ALTER TABLE `access1301_36`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_37`
--
ALTER TABLE `access1301_37`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_38`
--
ALTER TABLE `access1301_38`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access1301_39`
--
ALTER TABLE `access1301_39`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_1`
--
ALTER TABLE `access_1`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_2`
--
ALTER TABLE `access_2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_3`
--
ALTER TABLE `access_3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_4`
--
ALTER TABLE `access_4`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4710727;

--
-- AUTO_INCREMENT for table `access_5`
--
ALTER TABLE `access_5`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3299;

--
-- AUTO_INCREMENT for table `access_7`
--
ALTER TABLE `access_7`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=509;

--
-- AUTO_INCREMENT for table `access_20`
--
ALTER TABLE `access_20`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_21`
--
ALTER TABLE `access_21`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_30`
--
ALTER TABLE `access_30`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_31`
--
ALTER TABLE `access_31`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_32`
--
ALTER TABLE `access_32`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_33`
--
ALTER TABLE `access_33`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_34`
--
ALTER TABLE `access_34`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=540;

--
-- AUTO_INCREMENT for table `access_35`
--
ALTER TABLE `access_35`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25926;

--
-- AUTO_INCREMENT for table `access_36`
--
ALTER TABLE `access_36`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_37`
--
ALTER TABLE `access_37`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_38`
--
ALTER TABLE `access_38`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `access_39`
--
ALTER TABLE `access_39`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `animal`
--
ALTER TABLE `animal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=658;

--
-- AUTO_INCREMENT for table `anlkind`
--
ALTER TABLE `anlkind`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `anltype`
--
ALTER TABLE `anltype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bdevice`
--
ALTER TABLE `bdevice`
  MODIFY `autoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `breed`
--
ALTER TABLE `breed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brssi`
--
ALTER TABLE `brssi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99669;

--
-- AUTO_INCREMENT for table `changeidlog`
--
ALTER TABLE `changeidlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `dailywork`
--
ALTER TABLE `dailywork`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5207;

--
-- AUTO_INCREMENT for table `exitmanager`
--
ALTER TABLE `exitmanager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `factory`
--
ALTER TABLE `factory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1883;

--
-- AUTO_INCREMENT for table `field`
--
ALTER TABLE `field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `homepage`
--
ALTER TABLE `homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lostacc`
--
ALTER TABLE `lostacc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `newdevice`
--
ALTER TABLE `newdevice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productlog`
--
ALTER TABLE `productlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `psn`
--
ALTER TABLE `psn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `purptype`
--
ALTER TABLE `purptype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rate`
--
ALTER TABLE `rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recovery`
--
ALTER TABLE `recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `sickness`
--
ALTER TABLE `sickness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4970;

--
-- AUTO_INCREMENT for table `sickrecord`
--
ALTER TABLE `sickrecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `sicktype`
--
ALTER TABLE `sicktype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `taccess`
--
ALTER TABLE `taccess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91342;

--
-- AUTO_INCREMENT for table `temperror`
--
ALTER TABLE `temperror`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `autoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `usermsginfo`
--
ALTER TABLE `usermsginfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useropenid`
--
ALTER TABLE `useropenid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `worker`
--
ALTER TABLE `worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
