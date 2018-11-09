/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `onecc` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `onecc`;

CREATE TABLE IF NOT EXISTS `callcard` (
  `callcard_id` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `callcard_caller_name` varchar(50) DEFAULT NULL,
  `callcard_caller_phone` varchar(50) DEFAULT NULL,
  `callcard_patienttype_code` varchar(50) DEFAULT NULL,
  `callcard_notes` varchar(500) DEFAULT NULL,
  `callcard_patient_name` varchar(100) DEFAULT NULL,
  `callcard_eventcode_code` varchar(50) DEFAULT NULL,
  `callcard_eventtype_code` varchar(50) DEFAULT NULL,
  `callcard_locationtype_code` varchar(50) DEFAULT NULL,
  `callcard_incident_address` varchar(500) DEFAULT NULL,
  `callcard_incident_coordinate` varchar(50) DEFAULT NULL,
  `callcard_filingtype_code` varchar(50) DEFAULT NULL,
  `callcard_time_create` datetime DEFAULT NULL,
  `callcard_time_submit` datetime DEFAULT NULL,
  PRIMARY KEY (`callcard_id`),
  KEY `FK1_callcard_callertype_code` (`callcard_patienttype_code`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eventcode` (
  `eventcode_code` varchar(50) DEFAULT NULL,
  `eventcode_desc` varchar(200) DEFAULT NULL,
  `eventcode_order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `eventtype` (
  `eventtype_code` varchar(50) NOT NULL,
  `eventtype_desc` varchar(50) NOT NULL,
  `eventtype_order` int(10) NOT NULL,
  PRIMARY KEY (`eventtype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `filingtype` (
  `filingtype_code` varchar(50) NOT NULL,
  `filingtype_desc` varchar(50) NOT NULL,
  `filingtype_order` int(10) NOT NULL,
  PRIMARY KEY (`filingtype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS `locationtype` (
  `locationtype_code` varchar(50) NOT NULL,
  `locationtype_desc` varchar(50) NOT NULL,
  `locationtype_order` int(10) NOT NULL,
  PRIMARY KEY (`locationtype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

CREATE TABLE IF NOT EXISTS `mobile` (
  `mobile_resource_nric_or_reg` varchar(50) NOT NULL DEFAULT 'xxx',
  `mobile_phone` varchar(50) DEFAULT NULL,
  `mobile_token` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`mobile_resource_nric_or_reg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `patienttype` (
  `patienttype_code` varchar(50) NOT NULL,
  `patienttype_desc` varchar(50) DEFAULT NULL,
  `patienttype_order` int(10) DEFAULT NULL,
  PRIMARY KEY (`patienttype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `photo` (
  `photo_id` int(10) NOT NULL AUTO_INCREMENT,
  `photo_responder_id` int(4) unsigned NOT NULL,
  `photo_url` varchar(200) NOT NULL,
  PRIMARY KEY (`photo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `resource` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_nric_or_reg` varchar(50) NOT NULL,
  `resource_resourcetype_code` varchar(10) DEFAULT NULL,
  `resource_name` varchar(50) DEFAULT NULL,
  `resource_phone` varchar(50) DEFAULT NULL,
  `resource_standby_location` varchar(50) DEFAULT NULL,
  `resource_venue_id` int(10) DEFAULT NULL,
  `resource_availability_from` datetime DEFAULT NULL,
  `resource_availability_to` datetime DEFAULT NULL,
  PRIMARY KEY (`resource_id`),
  UNIQUE KEY `UNIQUE` (`resource_nric_or_reg`,`resource_availability_from`,`resource_availability_to`)
) ENGINE=InnoDB AUTO_INCREMENT=909 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `resourcetype` (
  `resourcetype_code` varchar(10) NOT NULL,
  `resourcetype_desc` varchar(50) NOT NULL,
  `resourcetype_order` int(10) NOT NULL,
  PRIMARY KEY (`resourcetype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `responder` (
  `responder_id` int(10) NOT NULL AUTO_INCREMENT,
  `responder_callcard_id` int(4) unsigned zerofill DEFAULT NULL,
  `responder_resource_nric_or_reg` varchar(50) DEFAULT NULL,
  `responder_statustype_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`responder_id`),
  KEY `FK1_responder_callcard_id` (`responder_callcard_id`),
  KEY `FK2_responder_resource_nric_or_reg` (`responder_resource_nric_or_reg`),
  KEY `FK3_responder_statustype_code` (`responder_statustype_code`),
  CONSTRAINT `FK1_responder_callcard_id` FOREIGN KEY (`responder_callcard_id`) REFERENCES `callcard` (`callcard_id`) ON DELETE CASCADE,
  CONSTRAINT `FK2_responder_resource_nric_or_reg` FOREIGN KEY (`responder_resource_nric_or_reg`) REFERENCES `resource` (`resource_nric_or_reg`) ON DELETE CASCADE,
  CONSTRAINT `FK3_responder_statustype_code` FOREIGN KEY (`responder_statustype_code`) REFERENCES `statustype` (`statustype_code`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statuslog` (
  `statuslog_id` int(11) NOT NULL AUTO_INCREMENT,
  `statuslog_callcard_id` int(4) unsigned zerofill NOT NULL,
  `statuslog_time` datetime NOT NULL,
  `statuslog_desc` varchar(200) NOT NULL,
  PRIMARY KEY (`statuslog_id`),
  KEY `FK1_statuslog_callcard_id` (`statuslog_callcard_id`),
  CONSTRAINT `FK1_statuslog_callcard_id` FOREIGN KEY (`statuslog_callcard_id`) REFERENCES `callcard` (`callcard_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=353 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `statustype` (
  `statustype_code` varchar(50) NOT NULL,
  `statustype_desc` varchar(50) DEFAULT NULL,
  `statustype_order` int(10) DEFAULT NULL,
  `statustype_icon` varchar(100) DEFAULT NULL,
  `statustype_color` varchar(50) DEFAULT NULL,
  `statustype_log_template` varchar(200) DEFAULT NULL,
  `statustype_on_mobile` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`statustype_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `tracking` (
  `tracking_resource_nric_or_reg` varchar(50) DEFAULT NULL,
  `tracking_coordinate` varchar(50) DEFAULT NULL,
  `tracking_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user` (
  `user_username` varchar(50) NOT NULL,
  `user_password` varchar(50) DEFAULT NULL,
  `user_roles` varchar(50) DEFAULT NULL,
  `user_fullname` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`user_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `venue` (
  `venue_id` int(10) NOT NULL AUTO_INCREMENT,
  `venue_desc` varchar(100) DEFAULT NULL,
  `venue_address` varchar(100) DEFAULT NULL,
  `venue_coordinate` varchar(50) DEFAULT NULL,
  `venue_icon` varchar(50) DEFAULT NULL,
  `venuetype_code` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`venue_id`),
  UNIQUE KEY `venue_desc` (`venue_desc`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
