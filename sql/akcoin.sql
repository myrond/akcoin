-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: bitcoin
-- ------------------------------------------------------
-- Server version	5.1.49-3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accountBalance`
--

DROP TABLE IF EXISTS `accountBalance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountBalance` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balance` varchar(40) DEFAULT NULL,
  `sendAddress` varchar(255) DEFAULT '',
  `paid` varchar(40) DEFAULT '0',
  `threshold` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  KEY `b_userId` (`userId`)
) ENGINE=MyISAM AUTO_INCREMENT=394 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accountHistory`
--

DROP TABLE IF EXISTS `accountHistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountHistory` (
  `id` int(255) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(255) NOT NULL,
  `balanceDelta` varchar(45) NOT NULL,
  `blockNumber` int(11) NOT NULL,
  `userShares` int(11) NOT NULL,
  `totalShares` int(11) NOT NULL,
  `sitePercent` int(11) NOT NULL,
  `donatePercent` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=145 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `locks`
--

DROP TABLE IF EXISTS `locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  KEY `name` (`name`)
) ENGINE=MEMORY AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `networkBlocks`
--

DROP TABLE IF EXISTS `networkBlocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `networkBlocks` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(255) NOT NULL,
  `timestamp` int(255) NOT NULL,
  `accountAddress` varchar(255) NOT NULL,
  `confirms` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `blockNumber_index` (`blockNumber`),
  KEY `confirms_index` (`confirms`)
) ENGINE=MyISAM AUTO_INCREMENT=3590 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `timestamp` int(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pool_worker`
--

DROP TABLE IF EXISTS `pool_worker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pool_worker` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `username` char(50) DEFAULT NULL,
  `password` char(255) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `hashrate` int(11) DEFAULT NULL,
  `stale_records_24_hours` int(11) DEFAULT NULL,
  `accepted_records_24_hours` int(11) DEFAULT NULL,
  `accepted_current_block` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `p_username` (`username`),
  KEY `username_userid` (`username`,`associatedUserId`),
  KEY `userid` (`associatedUserId`)
) ENGINE=MyISAM AUTO_INCREMENT=859 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serveracceptedsharehistory`
--

DROP TABLE IF EXISTS `serveracceptedsharehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serveracceptedsharehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shares` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  `rare` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serverhashhistory`
--

DROP TABLE IF EXISTS `serverhashhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serverhashhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hashrate` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serverstalesharehistory`
--

DROP TABLE IF EXISTS `serverstalesharehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serverstalesharehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `stales` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `serverworkerhistory`
--

DROP TABLE IF EXISTS `serverworkerhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serverworkerhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workerrate` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `setting` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shares`
--

DROP TABLE IF EXISTS `shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shares` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY AUTO_INCREMENT=285784 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shares-memory`
--

DROP TABLE IF EXISTS `shares-memory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shares-memory` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `rem_host` varchar(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shares_counted`
--

DROP TABLE IF EXISTS `shares_counted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shares_counted` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `count` int(11) NOT NULL,
  `invalid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shares_history`
--

DROP TABLE IF EXISTS `shares_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shares_history` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `counted` int(1) NOT NULL COMMENT 'BOOLEAN) Tells server if it used these shares for counting',
  `blockNumber` int(255) NOT NULL,
  `username` varchar(120) NOT NULL,
  `our_result` enum('Y','N') NOT NULL,
  `upstream_result` enum('Y','N') DEFAULT NULL,
  `reason` varchar(50) DEFAULT NULL,
  `solution` varchar(257) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` double(10,2) NOT NULL DEFAULT '0.00',
  `userId` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sh_blocknumber` (`blockNumber`),
  KEY `sh_counted` (`counted`),
  KEY `time` (`time`,`username`),
  KEY `upstream_result` (`upstream_result`),
  KEY `our_result_username` (`our_result`,`username`),
  KEY `counted_our_result_username_blocknumber` (`counted`,`our_result`,`username`,`blockNumber`),
  KEY `blockNumber_username` (`blockNumber`,`username`),
  KEY `fk_sh_webUsers` (`userId`),
  KEY `counted_userId_blockNumber` (`counted`,`userId`,`blockNumber`)
) ENGINE=MyISAM AUTO_INCREMENT=12897948 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `useracceptedsharehistory`
--

DROP TABLE IF EXISTS `useracceptedsharehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useracceptedsharehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `shares` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`,`associatedUserId`),
  KEY `associatedUserId` (`associatedUserId`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userhashhistory`
--

DROP TABLE IF EXISTS `userhashhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userhashhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `hashrate` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`,`associatedUserId`),
  KEY `associatedUserId` (`associatedUserId`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userstalesharehistory`
--

DROP TABLE IF EXISTS `userstalesharehistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userstalesharehistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `stales` int(11) DEFAULT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`,`associatedUserId`),
  KEY `associatedUserId` (`associatedUserId`),
  KEY `currentdate_2` (`currentdate`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `userworkerhistory`
--

DROP TABLE IF EXISTS `userworkerhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userworkerhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `associatedUserId` int(255) NOT NULL,
  `username` char(50) NOT NULL,
  `currentdate` varchar(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currentdate` (`currentdate`,`associatedUserId`,`username`)
) ENGINE=MyISAM AUTO_INCREMENT=1031 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webUsers`
--

DROP TABLE IF EXISTS `webUsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webUsers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `admin` int(1) NOT NULL,
  `username` varchar(40) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL COMMENT 'Associated email: used for validating users, and re-setting passwords',
  `emailAuthPin` varchar(10) NOT NULL COMMENT 'The pin required to authorize that email address',
  `secret` varchar(10) NOT NULL,
  `loggedIp` varchar(255) NOT NULL,
  `sessionTimeoutStamp` int(255) NOT NULL,
  `accountLocked` int(255) NOT NULL COMMENT 'This is the timestamp when the account will be unlocked(usually used to lock accounts that are trying to be bruteforced)',
  `accountFailedAttempts` int(2) NOT NULL COMMENT 'This counts the number of failed attempts for web login',
  `pin` varchar(255) NOT NULL COMMENT 'four digit pin to allow account changes',
  `share_count` int(11) DEFAULT NULL,
  `stale_share_count` int(11) DEFAULT NULL,
  `shares_this_round` int(11) DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
  `activeEmail` int(1) DEFAULT NULL,
  `hashrate` int(11) DEFAULT NULL,
  `donate_percent` varchar(11) DEFAULT '0',
  `round_estimate` varchar(40) DEFAULT '0',
  `anonymous` enum('Y','N') DEFAULT 'N',
  `score` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=361 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `winning_shares`
--

DROP TABLE IF EXISTS `winning_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `winning_shares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blockNumber` int(11) NOT NULL,
  `username` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-18 19:14:57
