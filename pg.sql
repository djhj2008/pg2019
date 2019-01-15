-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2019-01-15 21:40:55
-- 服务器版本： 5.5.27
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
-- 表的结构 `access`
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
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `bdevice`
--

CREATE TABLE `bdevice` (
  `autoid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `tsn` varchar(16) NOT NULL COMMENT '统一编号',
  `psn` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `uptime` varchar(4) NOT NULL,
  `count` int(4) NOT NULL DEFAULT '1' COMMENT '采集次数',
  `shed` int(11) NOT NULL COMMENT '棚',
  `state` int(11) NOT NULL DEFAULT '0',
  `rate_id` int(11) NOT NULL DEFAULT '0',
  `rate` int(11) NOT NULL DEFAULT '432000000',
  `assert_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'read assert',
  `url_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'url更换标志',
  `url` varchar(64) NOT NULL COMMENT '域名',
  `info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `brssi`
--

CREATE TABLE `brssi` (
  `id` int(11) NOT NULL,
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
-- 表的结构 `device`
--

CREATE TABLE `device` (
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
  `dev_assert` int(11) NOT NULL DEFAULT '0' COMMENT 'assert info'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='设备';

-- --------------------------------------------------------

--
-- 表的结构 `factory`
--

CREATE TABLE `factory` (
  `id` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `state` int(11) NOT NULL COMMENT '1初始2成功3失败',
  `fsn` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `field`
--

CREATE TABLE `field` (
  `id` int(11) NOT NULL,
  `sheds` int(11) NOT NULL,
  `folds` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `field`
--

INSERT INTO `field` (`id`, `sheds`, `folds`, `userid`) VALUES
(1, 4, 20, 22);

-- --------------------------------------------------------

--
-- 表的结构 `homepage`
--

CREATE TABLE `homepage` (
  `id` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT '0' COMMENT '1,更改域名',
  `hostname` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `product`
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
-- 表的结构 `psn`
--

CREATE TABLE `psn` (
  `id` int(11) NOT NULL,
  `tsn` int(11) NOT NULL COMMENT '国标编码',
  `sn` int(11) NOT NULL COMMENT 'SN',
  `userid` int(11) NOT NULL,
  `info` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `psn`
--

INSERT INTO `psn` (`id`, `tsn`, `sn`, `userid`, `info`) VALUES
(1, 1086756300, 1, 22, '一区'),
(2, 2086756300, 2, 23, '二区'),
(3, 1086100000, 3, 18, '北京测试');

-- --------------------------------------------------------

--
-- 表的结构 `rate`
--

CREATE TABLE `rate` (
  `id` int(11) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `rate`
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
-- 表的结构 `recovery`
--

CREATE TABLE `recovery` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psn` int(11) NOT NULL,
  `temp1` float NOT NULL,
  `msg` varchar(250) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `sickness`
--

CREATE TABLE `sickness` (
  `id` int(11) NOT NULL,
  `devid` int(11) NOT NULL,
  `psnid` int(11) NOT NULL,
  `shed` int(11) NOT NULL COMMENT '棚',
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
-- 表的结构 `sickrecord`
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
-- 表的结构 `taccess`
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
  `cur_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `temperror`
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
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(125) NOT NULL,
  `pwd` varchar(125) NOT NULL,
  `info` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `name`, `pwd`, `info`) VALUES
(18, 'testhome', '0fbf97af50536d28480e64d810a5c98f', ''),
(22, 'test1', '0fbf97af50536d28480e64d810a5c98f', '兴鸿旺牧业'),
(23, 'test2', '0fbf97af50536d28480e64d810a5c98f', '方圆养殖');

-- --------------------------------------------------------

--
-- 表的结构 `useropenid`
--

CREATE TABLE `useropenid` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `openid` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `useropenid`
--

INSERT INTO `useropenid` (`id`, `userid`, `openid`) VALUES
(2, 22, 'owIK80WnkJnwkC_6HHM2GRX3BM3Q'),
(4, 22, 'owIK80STNaS4fpFh8FH7LK0mL2Ro'),
(7, 23, 'owIK80fVYQv3JUUaXVQhD454l7Vk');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access`
--
ALTER TABLE `access`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bdevice`
--
ALTER TABLE `bdevice`
  ADD PRIMARY KEY (`autoid`);

--
-- Indexes for table `brssi`
--
ALTER TABLE `brssi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shed` (`shed`,`fold`),
  ADD KEY `devid` (`devid`);

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
-- Indexes for table `psn`
--
ALTER TABLE `psn`
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
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `useropenid`
--
ALTER TABLE `useropenid`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `access`
--
ALTER TABLE `access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167483;

--
-- 使用表AUTO_INCREMENT `bdevice`
--
ALTER TABLE `bdevice`
  MODIFY `autoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `brssi`
--
ALTER TABLE `brssi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=327;

--
-- 使用表AUTO_INCREMENT `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=705;

--
-- 使用表AUTO_INCREMENT `factory`
--
ALTER TABLE `factory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=524;

--
-- 使用表AUTO_INCREMENT `field`
--
ALTER TABLE `field`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `homepage`
--
ALTER TABLE `homepage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `psn`
--
ALTER TABLE `psn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `rate`
--
ALTER TABLE `rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `recovery`
--
ALTER TABLE `recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `sickness`
--
ALTER TABLE `sickness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用表AUTO_INCREMENT `sickrecord`
--
ALTER TABLE `sickrecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `taccess`
--
ALTER TABLE `taccess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45401;

--
-- 使用表AUTO_INCREMENT `temperror`
--
ALTER TABLE `temperror`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- 使用表AUTO_INCREMENT `useropenid`
--
ALTER TABLE `useropenid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
