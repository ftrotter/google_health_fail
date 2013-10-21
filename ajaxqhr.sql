-- MySQL dump 10.11
--
-- Host: localhost    Database: ajaxqhr
-- ------------------------------------------------------
-- Server version	5.0.51a

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
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `patient` (
  `id` int(11) NOT NULL auto_increment,
  `person_id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `patient`
--

LOCK TABLES `patient` WRITE;
/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
INSERT INTO `patient` VALUES (1,1,'123456789abcde');
/*!40000 ALTER TABLE `patient` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `person` (
  `id` int(10) NOT NULL auto_increment,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `cell_phone` varchar(50) NOT NULL,
  `home_phone` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address_line1` varchar(100) NOT NULL,
  `state` varchar(2) NOT NULL,
  `city` varchar(20) NOT NULL,
  `zip` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES (1,'Fred','Clayton','Trotter','(713) 965-4327','','fred.trotter@gmail.com','4414 Rockwood','TX','Houston',77004),(3,'Kimberly','','Dunn','(832) 752-1635','','kim.dunn@uth.tmc.edu','2504 ELMEN ST SUITE 222','TX','Houston',77019);
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan`
--

DROP TABLE IF EXISTS `plan`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plan` (
  `id` int(11) NOT NULL auto_increment,
  `patient_id` int(11) NOT NULL,
  `protocol_id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `active` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plan`
--

LOCK TABLES `plan` WRITE;
/*!40000 ALTER TABLE `plan` DISABLE KEYS */;
INSERT INTO `plan` VALUES (1,1,0,'Eye Survey (02/12/98',1),(2,1,0,'Diabetes',1),(3,1,0,'HDL 35 (04/02/09)',1),(4,1,0,'Depression',1),(5,1,0,'Hypertension',1),(6,1,0,'Lipids',1),(7,1,0,'Diabetes',1);
/*!40000 ALTER TABLE `plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_data`
--

DROP TABLE IF EXISTS `plan_data`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `plan_data` (
  `id` int(11) NOT NULL auto_increment,
  `plan_id` int(11) NOT NULL,
  `value` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `codeset` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `xml_id` int(11) NOT NULL,
  `ccr_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `plan_data`
--

LOCK TABLES `plan_data` WRITE;
/*!40000 ALTER TABLE `plan_data` DISABLE KEYS */;
INSERT INTO `plan_data` VALUES (1,1,'intervention','This is a test of the intervention engine? How well does this handle\n\nA rapidly expanding \n\nwindow areas??\n\n\n',0,0,0,0,0,'2009-04-03 12:46:20'),(2,1,'goal','This is the end goal\n',0,0,0,0,0,'2009-04-03 12:46:37'),(3,1,'rf_family_smoking','on',0,0,0,0,0,'2009-04-03 12:46:49'),(4,1,'rf_economic','on',0,0,0,0,0,'2009-04-03 12:46:49'),(5,1,'rf_refusal','on',0,0,0,0,0,'2009-04-03 12:46:49'),(6,1,'rf_family_history','on',0,0,0,0,0,'2009-04-03 12:46:49'),(7,1,'rf_alcohol_abuse','on',0,0,0,0,0,'2009-04-03 12:46:49'),(8,1,'risk','LDL 130 (04/02/09)',0,1231241,0,0,0,'2009-04-03 12:47:05'),(9,1,'risk','HDL 35 (04/02/09)',0,1231239,0,0,0,'2009-04-03 12:47:07'),(10,1,'risk','Knee Surgery (04/02/08)',0,1231235,0,0,0,'2009-04-03 12:47:14'),(11,1,'meds','Lipitor 40mg once daily',0,1231257,0,0,0,'2009-04-03 12:47:17'),(12,1,'risk','Lipitor 40mg once daily',0,1231257,0,0,0,'2009-04-03 12:47:19'),(13,1,'intervention','\n			This is a test of the intervention engine? How well does this handle\n\nA rapidly expanding \n\nwindow areas??\n\nHow about adding to this??\n\n\n',0,0,0,0,0,'2009-04-03 12:49:21'),(14,2,'risk','chloride 100 (04/02/09)',0,1231243,0,0,0,'2009-04-03 14:29:40'),(15,2,'meds','Viagra as \"needed\"',0,1231259,0,0,0,'2009-04-03 14:31:13'),(16,3,'risk','Hypertension',0,1231233,0,0,0,'2009-04-03 14:44:26'),(17,3,'risk','Cholesterol 180 (04/02/09)',0,1231238,0,0,0,'2009-04-03 14:44:31'),(18,3,'risk','Pulse 65 (04/02/09)',0,1231244,0,0,0,'2009-04-03 14:44:35'),(19,3,'risk','Respiration 15 (03/12/09)',0,1231247,0,0,0,'2009-04-03 14:44:37'),(20,3,'risk','Viagra as \"needed\"',0,1231259,0,0,0,'2009-04-03 14:44:41'),(21,3,'meds','Aspirin 40 mg once daily and as needed',0,1231258,0,0,0,'2009-04-03 14:45:18'),(22,3,'meds','Lipitor 40mg once daily',0,1231257,0,0,0,'2009-04-03 14:45:24'),(23,3,'rf_low_insurance','on',0,0,0,0,0,'2009-04-03 14:46:37'),(24,3,'rf_refusal','on',0,0,0,0,0,'2009-04-03 14:46:37'),(25,3,'rf_family_history','on',0,0,0,0,0,'2009-04-03 14:46:37'),(26,3,'rf_drug_abuse','on',0,0,0,0,0,'2009-04-03 14:46:37'),(27,0,'Procedures','Eye Surgery (02/12/98)',0,1231236,0,0,0,'2009-04-03 14:46:54'),(28,3,'intervention','This is how you save interventions. \n\nThis now \n\n\nThe easy way \n\n\n\n\n\n\nto deal with space\n\n',0,0,0,0,0,'2009-04-03 14:48:52'),(29,3,'risk','Knee Surgery (04/02/08)',0,1231235,0,0,0,'2009-04-03 14:50:36'),(30,3,'risk','LDL 130 (04/02/09)',0,1231241,0,0,0,'2009-04-03 14:50:39'),(31,1,'meds','Aspirin 40 mg once daily and as needed',0,1231258,0,0,0,'2009-04-03 14:50:43'),(32,1,'risk','Alcohol Abuse',0,1231232,0,0,0,'2009-04-03 14:50:49'),(33,3,'intervention','\n			This is how you save interventions. \n\nThis now \n\n\nThe easy way \n\n\n\nBob\n\n\nto deal with space\n\n',0,0,0,0,0,'2009-04-03 14:51:27'),(34,2,'risk','Eye Surgery (02/12/98)',0,1231236,0,0,0,'2009-04-03 15:06:05'),(35,4,'risk','Lipitor 40mg once daily',0,1231257,0,0,0,'2009-04-03 15:10:40'),(36,4,'risk','Aspirin 40 mg once daily and as needed',0,1231258,0,0,0,'2009-04-03 15:10:43'),(37,4,'risk','glucose 85 (04/02/09)',0,1231242,0,0,0,'2009-04-03 15:10:56'),(38,4,'meds','Viagra as \"needed\"',0,1231259,0,0,0,'2009-04-03 15:11:19'),(39,5,'rf_economic','on',0,0,0,0,0,'2009-04-03 15:14:38'),(40,5,'rf_refusal','on',0,0,0,0,0,'2009-04-03 15:14:38'),(41,5,'intervention','Medications, exercise, weight reduction\n',0,0,0,0,0,'2009-04-03 15:15:12'),(42,5,'goal','120/80\n',0,0,0,0,0,'2009-04-03 15:15:38'),(43,5,'owner_first','Kim',0,0,0,0,0,'2009-04-03 15:16:41'),(44,5,'owner_last','Dunn',0,0,0,0,0,'2009-04-03 15:16:41'),(45,5,'owner_phone','8327521635',0,0,0,0,0,'2009-04-03 15:16:41'),(46,5,'owner_city','',0,0,0,0,0,'2009-04-03 15:16:41'),(47,5,'owner_zip','',0,0,0,0,0,'2009-04-03 15:16:41'),(48,5,'owner_state','TX',0,0,0,0,0,'2009-04-03 15:16:41'),(49,6,'rf_family_smoking','on',0,0,0,0,0,'2009-04-03 15:37:30'),(50,6,'rf_refusal','on',0,0,0,0,0,'2009-04-03 15:37:30'),(51,6,'rf_family_history','on',0,0,0,0,0,'2009-04-03 15:37:30'),(52,6,'rf_obesity','on',0,0,0,0,0,'2009-04-03 15:37:30'),(53,6,'intervention','my intervention\n',0,0,0,0,0,'2009-04-03 15:37:58'),(54,6,'goal','my goals\n',0,0,0,0,0,'2009-04-03 15:38:23'),(55,6,'risk','Knee Surgery (04/02/08)',0,1231235,0,0,0,'2009-04-03 15:53:50');
/*!40000 ALTER TABLE `plan_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `provider` (
  `id` int(11) NOT NULL auto_increment,
  `person_id` int(11) NOT NULL,
  `npi` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `provider`
--

LOCK TABLES `provider` WRITE;
/*!40000 ALTER TABLE `provider` DISABLE KEYS */;
INSERT INTO `provider` VALUES (1,3,1952522963);
/*!40000 ALTER TABLE `provider` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-04-16 21:10:46
