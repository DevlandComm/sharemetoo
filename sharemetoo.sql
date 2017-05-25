-- MySQL dump 10.13  Distrib 5.6.24, for osx10.8 (x86_64)
--
-- Host: localhost    Database: sharemetoo
-- ------------------------------------------------------
-- Server version	5.6.24

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
-- Table structure for table `application`
--

DROP TABLE IF EXISTS `application`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `application` (
  `appid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `appname` varchar(50) CHARACTER SET utf8 NOT NULL,
  `version` varchar(10) CHARACTER SET utf8 NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `description` varchar(500) CHARACTER SET utf8 DEFAULT NULL,
  `imagepath` varchar(255) CHARACTER SET utf8 DEFAULT 'images/icon-swg.png',
  PRIMARY KEY (`appid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `application`
--

LOCK TABLES `application` WRITE;
/*!40000 ALTER TABLE `application` DISABLE KEYS */;
INSERT INTO `application` VALUES (1,'FORM252','1.0.0',1,'South West Gas Form 252 intake form. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tempus et leo ac fringilla. Aliquam sed ornare tellus. Maecenas sed metus dignissim, bibendum felis id, tincidunt lectus. Integer egestas at dolor non suscipit.','images/icon-swg.png'),(2,'INVENTORY505','1.0.0',1,'James Dean LTD, inventory system.','images/icon-swg.png'),(3,'SYSTEMS4','1.0.0',1,'Systems iV','images/icon-swg.png');
/*!40000 ALTER TABLE `application` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_item`
--

DROP TABLE IF EXISTS `shared_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_item` (
  `idno` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `uname` varchar(150) DEFAULT NULL,
  `pw` varchar(45) DEFAULT NULL,
  `sharedurl` varchar(255) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `id_userid` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`idno`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_item`
--

LOCK TABLES `shared_item` WRITE;
/*!40000 ALTER TABLE `shared_item` DISABLE KEYS */;
INSERT INTO `shared_item` VALUES (1,'XFINITY Service','alogarta@xfinity.com','password','http://www.xfinity.com','XFINITY Service Access',1),(2,'Bubble Network','alogart@xfinity.com','password','http://www.bubble.com','My Bubble Network subscription details..',1),(3,'ZOOM INFINITY','alogarta','zoompassword','https://www.zoominfinity.com','ZOOM Infinity is a special portal that will allow you to bend space and time. You\'ll be able to open a portal to another Universe.',1),(4,'ZOOM','mdotson','ps','https://www.zoominfinity.com','ZOOM Infinity is a special portal that will allow you to bend space and time. You\'ll be able to open a portal to another Universe.',4);
/*!40000 ALTER TABLE `shared_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shared_with`
--

DROP TABLE IF EXISTS `shared_with`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shared_with` (
  `idno` int(11) NOT NULL AUTO_INCREMENT,
  `id_shared_item` int(11) DEFAULT NULL,
  `id_shared_with` mediumint(9) DEFAULT NULL,
  PRIMARY KEY (`idno`),
  KEY `fk_id_shared_item_idx` (`id_shared_item`),
  KEY `fk_id_user_idx` (`id_shared_with`),
  CONSTRAINT `fk_id_shared_item` FOREIGN KEY (`id_shared_item`) REFERENCES `shared_item` (`idno`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_id_user` FOREIGN KEY (`id_shared_with`) REFERENCES `user` (`userid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shared_with`
--

LOCK TABLES `shared_with` WRITE;
/*!40000 ALTER TABLE `shared_with` DISABLE KEYS */;
INSERT INTO `shared_with` VALUES (1,1,3),(2,1,2),(3,2,3),(13,2,2),(14,2,4),(15,4,1);
/*!40000 ALTER TABLE `shared_with` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `userid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `passw` varchar(155) CHARACTER SET utf8 NOT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'Pending' COMMENT 'Pending, Active, Barred',
  `imgurl` varchar(255) DEFAULT 'images/profile-icon.png',
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'aclogarta@yahoo.com','UYyxtZwcZ8lAh+7o3iL04dzrTq/W1ophJO7nB91Jm+E=','Active','uploads/IMG_2147x100.JPG'),(2,'jerome.longakit@g2-is.com','UYyxtZwcZ8lAh+7o3iL04dzrTq/W1ophJO7nB91Jm+E=','Active','uploads/jerome.jpeg'),(3,'alogarta@hotmail.com','UYyxtZwcZ8lAh+7o3iL04dzrTq/W1ophJO7nB91Jm+E=','Active','uploads/carmela.jpeg'),(4,'mdotson@yahoo.com','UYyxtZwcZ8lAh+7o3iL04dzrTq/W1ophJO7nB91Jm+E=','Active','images/profile-icon.png'),(5,'maclogarta@yahoo.com','UYyxtZwcZ8lAh+7o3iL04dzrTq/W1ophJO7nB91Jm+E=','Pending','uploads/miguel.jpeg');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userassignment`
--

DROP TABLE IF EXISTS `userassignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userassignment` (
  `idno` mediumint(9) NOT NULL AUTO_INCREMENT,
  `appid` mediumint(9) NOT NULL,
  `userid` mediumint(9) NOT NULL,
  `iduserroles` int(11) DEFAULT NULL,
  PRIMARY KEY (`idno`),
  KEY `userid` (`userid`),
  KEY `appid` (`appid`),
  CONSTRAINT `userassignment_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`),
  CONSTRAINT `userassignment_ibfk_2` FOREIGN KEY (`appid`) REFERENCES `application` (`appid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userassignment`
--

LOCK TABLES `userassignment` WRITE;
/*!40000 ALTER TABLE `userassignment` DISABLE KEYS */;
INSERT INTO `userassignment` VALUES (1,1,1,1),(2,1,2,2),(3,1,3,2);
/*!40000 ALTER TABLE `userassignment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userconnection`
--

DROP TABLE IF EXISTS `userconnection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userconnection` (
  `idno` mediumint(9) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `useridconnection` int(11) DEFAULT NULL,
  PRIMARY KEY (`idno`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userconnection`
--

LOCK TABLES `userconnection` WRITE;
/*!40000 ALTER TABLE `userconnection` DISABLE KEYS */;
INSERT INTO `userconnection` VALUES (1,1,2),(2,1,3),(3,2,1),(4,3,1),(5,3,2),(6,1,4),(7,1,5);
/*!40000 ALTER TABLE `userconnection` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userinfo`
--

DROP TABLE IF EXISTS `userinfo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userinfo` (
  `idno` mediumint(9) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `company` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mobile` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `userid` mediumint(9) NOT NULL,
  PRIMARY KEY (`idno`),
  KEY `userid` (`userid`),
  CONSTRAINT `userinfo_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userinfo`
--

LOCK TABLES `userinfo` WRITE;
/*!40000 ALTER TABLE `userinfo` DISABLE KEYS */;
INSERT INTO `userinfo` VALUES (1,'Antonio','Logarta','Xoloosh','aclogarta@yahoo.com','7073178504','',1),(2,'Jerome','Longakit','Genentec','jerome.longakit@g2-is.com','7072079818','',2),(3,'Carmela','Logarta','Xoloosh','alogarta@hotmail.com','7072079818',NULL,3),(4,'Miles','Dotson',NULL,'mdotson@yahoo.com',NULL,NULL,4),(5,'Miguel','Logarta',NULL,'maclogarta@yahoo.com',NULL,NULL,5);
/*!40000 ALTER TABLE `userinfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userroles`
--

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userroles` (
  `iduserroles` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(50) NOT NULL,
  `assignedtoorg` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`iduserroles`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userroles`
--

LOCK TABLES `userroles` WRITE;
/*!40000 ALTER TABLE `userroles` DISABLE KEYS */;
INSERT INTO `userroles` VALUES (1,'Administrator',NULL),(2,'User',NULL);
/*!40000 ALTER TABLE `userroles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-17  8:41:25
