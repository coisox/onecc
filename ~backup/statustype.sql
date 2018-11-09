/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `onecc` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `onecc`;

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

/*!40000 ALTER TABLE `statustype` DISABLE KEYS */;
INSERT INTO `statustype` (`statustype_code`, `statustype_desc`, `statustype_order`, `statustype_icon`, `statustype_color`, `statustype_log_template`, `statustype_on_mobile`) VALUES
	('acknowledge', 'Acknowledge', 3, 'ic_hearing_white_24px.svg', 'orange', 'RESPONDER acknowledged', 'enabled'),
	('atpatient', 'At Patient', 6, 'ic_accessible_white_24px.svg', 'teal', 'RESPONDER at patient', 'enabled'),
	('atscene', 'At Scene', 5, 'ic_pin_drop_white_24px.svg', 'light-green', 'RESPONDER at scene', 'enabled'),
	('back', 'On Way Back', 8, 'ic_rotate_90_degrees_ccw_white_24px.svg', 'indigo', 'RESPONDER on the way back', 'enabled'),
	('completed', 'Completed', 10, 'ic_done_white_24px.svg', 'pink accent-4', 'RESPONDER marked as completed by USERNAME', 'disabled'),
	('dispatched', 'Dispatched', 2, 'ic_notifications_active_white_24px.svg', 'red', 'RESPONDER has been dispatched by USERNAME', 'disabled'),
	('enroute', 'En Route', 4, 'ic_near_me_white_24px.svg', 'yellow', 'RESPONDER en route', 'enabled'),
	('finished', 'Finished', 9, 'ic_done_all_white_24px.svg', 'purple', 'RESPONDER finished', 'enabled'),
	('new', 'Not Assigned', 1, 'ic_more_horiz_white_24px.svg', 'grey darken-1', 'RESPONDER has been pulled back by USERNAME', NULL),
	('transporting', 'Transporting', 7, 'ic_directions_bus_white_24px.svg', 'blue', 'RESPONDER transporting patient', 'enabled');
/*!40000 ALTER TABLE `statustype` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
