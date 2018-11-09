/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `onecc` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `onecc`;

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

/*!40000 ALTER TABLE `venue` DISABLE KEYS */;
INSERT INTO `venue` (`venue_id`, `venue_desc`, `venue_address`, `venue_coordinate`, `venue_icon`, `venuetype_code`) VALUES
	(1, 'Dataran Merdeka', 'Jalan Raja, City Centre, 50050 Kuala Lumpur', '3.147920,101.693270', 'venue_icon_1.png', 'venue'),
	(3, 'National Swimming Centre ', 'Level 1, Bukit Jalil National Sports Complex, 57700 Kuala Lumpur', '3.052590,101.691520', 'venue_icon_2.png', 'venue'),
	(5, 'National Stadium', 'Jalan Barat, Bukit Jalil, 57000 Kuala Lumpur', '3.054631,101.690930', 'venue_icon_3.png', 'venue'),
	(6, 'Putra Indoor Stadium', 'Lebuhraya Bukit Jalil, Bukit Jalil, Kuala Lumpur, 57000', '3.053971,101.693386', 'venue_icon_4.png', 'venue'),
	(7, 'MABA Stadium WPKL', '12th floor, Wisma MABA 6, Jalan Hang Jebat, 50150 Kuala Lumpur', '3.143178,101.700380', 'venue_icon_5.png', 'venue'),
	(8, 'Kuala Lumpur Convention Centre (KLCC)', 'Kuala Lumpur Convention Centre, Stall 7, Jalan Pinang, Kuala Lumpur City Centre, 50088 Kuala Lumpur', '3.154560,101.712905', 'venue_icon_6.png', 'venue'),
	(9, 'Malaysian International Trade & Exhibition Centre (MiTEC) ', 'Level 3, East Wing, Menara MATRADE, Jalan Sultan Haji Ahmad Shah, 50480 Kuala Lumpur, Malaysia', '3.177782,101.666695', 'venue_icon_7.png', 'venue'),
	(10, 'Kinrara Oval, Puchong', 'Malaysian Cricket Association, Bandar Kinrara 5, Puchong, Selangor', '3.048917,101.644481', 'venue_icon_8.png', 'venue'),
	(12, 'Shah Alam Stadium', 'Bahagian Pengurusan Stadium, Majlis Bandaraya Shah Alam, Level 1, Kuadron AB Stadium Shah Alam Seksy', '3.083258,101.545455', 'venue_icon_9.png', 'venue'),
	(13, 'Stadium Perbandaran Selayang ', 'Jalan 2/1, Baru Selayang, Batu Caves, 68100 Selangor, Malaysia', '3.255209,101.656992', 'venue_icon_9.png', 'venue'),
	(14, 'The Mines Resort City Golf Club ', 'Mines Wellness City, Seri Kembangan, 43300 Selangor, Malaysia', '3.032073,101.717618', 'venue_icon_10.png', 'venue'),
	(17, 'Empire City Ice Arena', 'Empire City Ice Arena, Empire City, Damansara Perdana', '3.166411,101.615333', 'venue_icon_12.png', 'venue'),
	(18, 'National Lawn Bowls Centre, Bukit Kiara', 'Merdeka Stadium Corporation, Level 1, National Stadium Bukit Jalil, Sri Petaling, Kuala Lumpur, Mala', '3.139108,101.648370', 'venue_icon_13.png', 'venue'),
	(19, 'Taman Rekreasi Pudu Ulu (Arena Petanque)', 'Jalan 1/91 (Pudu Ulu, Cheras) 56100 Kuala Lumpur, Kuala Lumpur Malaysia', '3.125088,101.734129', 'venue_icon_14.png', 'venue'),
	(20, 'MPPJ Stadium', 'Jalan Stadium 7/15, SS 7,Petaling Jaya, 47301 Selangor, Malaysia', '3.098850,101.594279', 'venue_icon_15.png', 'venue'),
	(21, 'Stadium Titiwangsa, Kuala Lumpur', 'Jalan Tembeling, Titiwangsa, 53200 Kuala Lumpur, Malaysia', '3.175724,101.706912', 'venue_icon_16.png', 'venue'),
	(22, 'Raintree Club', 'Lot 1002, Jalan Wickham, Off Jalan Ampang Hilir, 55000 Kuala Lumpur Wilayah Persekutuan, Malaysia\r\n\r', '3.155789,101.737768', 'venue_icon_17.png', 'venue'),
	(23, 'National Tennis Centre, Jalan Duta', 'Jalan Tuanku Abdul Halim, Bukit Tunku, 50480 Kuala Lumpur, Malaysia', '3.171925,101.675600', 'venue_icon_18.png', 'venue'),
	(24, 'Megalanes, Sunway Pyramid', 'Lot F1, 22 Level 1, Sunway Pyramid Shopping Centre, 3, Jalan PJS 11/15, Bandar Sunway, 46150 Selango', '3.073035,101.608858', 'venue_icon_19.png', 'venue'),
	(25, 'Putrajaya Lake', 'Putrajaya Lake, Precinct 1, 62000 Putrajaya, Malaysia', '2.942231,101.689056', 'venue_icon_20.png', 'venue'),
	(26, 'Badminton Stadium Cheras', 'alan Cheras, Taman Pertama, 56000 Kuala Lumpur, Malaysia', '3.119389,101.727801', 'venue_icon_21.png', 'venue');
/*!40000 ALTER TABLE `venue` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
