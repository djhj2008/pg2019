-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 18, 2019 at 06:17 PM
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
  `sid` int(11) NOT NULL DEFAULT '0',
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
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本',
  `uptime` varchar(4) NOT NULL,
  `count` int(4) NOT NULL DEFAULT '1' COMMENT '采集次数',
  `shed` int(11) NOT NULL DEFAULT '1' COMMENT '棚',
  `state` int(11) NOT NULL DEFAULT '0',
  `rate_id` int(11) NOT NULL DEFAULT '0',
  `rate` int(11) NOT NULL DEFAULT '432000000',
  `assert_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'read assert',
  `url_flag` int(11) NOT NULL DEFAULT '0' COMMENT 'url更换标志',
  `url` varchar(64) NOT NULL COMMENT '域名',
  `info` text
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
  `sid` int(11) NOT NULL,
  `old_psn` int(11) NOT NULL,
  `old_devid` int(11) NOT NULL,
  `new_devid` int(11) NOT NULL DEFAULT '0',
  `flag` int(11) NOT NULL DEFAULT '0',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `device`
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

-- --------------------------------------------------------

--
-- Table structure for table `rate`
--

CREATE TABLE `rate` (
  `id` int(11) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `id` int(11) NOT NULL,
  `name` varchar(125) NOT NULL,
  `pwd` varchar(125) NOT NULL,
  `info` varchar(80) DEFAULT NULL,
  `tmp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1209632;

--
-- AUTO_INCREMENT for table `access1301`
--
ALTER TABLE `access1301`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74699;

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
  MODIFY `autoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `brssi`
--
ALTER TABLE `brssi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56230;

--
-- AUTO_INCREMENT for table `changeidlog`
--
ALTER TABLE `changeidlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `device`
--
ALTER TABLE `device`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1260;

--
-- AUTO_INCREMENT for table `exitmanager`
--
ALTER TABLE `exitmanager`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `factory`
--
ALTER TABLE `factory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=818;

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
-- AUTO_INCREMENT for table `newdevice`
--
ALTER TABLE `newdevice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `productlog`
--
ALTER TABLE `productlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `psn`
--
ALTER TABLE `psn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `sickness`
--
ALTER TABLE `sickness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=427;

--
-- AUTO_INCREMENT for table `sickrecord`
--
ALTER TABLE `sickrecord`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `taccess`
--
ALTER TABLE `taccess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53328;

--
-- AUTO_INCREMENT for table `temperror`
--
ALTER TABLE `temperror`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `usermsginfo`
--
ALTER TABLE `usermsginfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useropenid`
--
ALTER TABLE `useropenid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `worker`
--
ALTER TABLE `worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
