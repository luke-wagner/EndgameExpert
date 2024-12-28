CREATE DATABASE  IF NOT EXISTS `chessapp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `chessapp`;
-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: chessapp
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `fens`
--

DROP TABLE IF EXISTS `fens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fens` (
  `game_link` varchar(60) NOT NULL,
  `move_number` int NOT NULL,
  `piece_count` int DEFAULT NULL,
  `fen` varchar(100) DEFAULT NULL,
  `descriptor` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`game_link`,`move_number`),
  CONSTRAINT `fens_game_link` FOREIGN KEY (`game_link`) REFERENCES `game_data` (`game_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `game_data`
--

DROP TABLE IF EXISTS `game_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `game_data` (
  `session_id` int unsigned DEFAULT NULL,
  `player_name` varchar(45) NOT NULL,
  `game_link` varchar(60) NOT NULL,
  `player_color` varchar(1) DEFAULT NULL,
  `outcome` tinyint(1) DEFAULT NULL,
  `result` varchar(25) DEFAULT NULL,
  `month` char(2) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  PRIMARY KEY (`player_name`,`game_link`),
  KEY `session_id_idx` (`session_id`),
  KEY `game_link_indx` (`game_link`),
  CONSTRAINT `game_data_session_id` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_name` varchar(45) DEFAULT NULL,
  `client_email` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session_data`
--

DROP TABLE IF EXISTS `session_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session_data` (
  `session_id` int unsigned NOT NULL,
  `username` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `month_str` char(2) COLLATE utf8mb4_0900_as_ci NOT NULL,
  `year_str` char(4) COLLATE utf8mb4_0900_as_ci NOT NULL,
  PRIMARY KEY (`session_id`,`username`,`month_str`,`year_str`),
  CONSTRAINT `session_data_session_id` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_as_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'chessapp'
--
/*!50003 DROP FUNCTION IF EXISTS `f_not_in_range` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f_not_in_range`(
    p_session_id INT UNSIGNED,
    p_username VARCHAR(45),
    p_start_date DATE,
    p_end_date DATE
) RETURNS int
    DETERMINISTIC
BEGIN
    DECLARE total_months INT;
    DECLARE accounted_months INT;

    -- Normalize the start_date to the first day of the month
    SET p_start_date = DATE_FORMAT(p_start_date, '%Y-%m-01');

    -- Normalize the end_date to the last day of the month
    SET p_end_date = LAST_DAY(p_end_date);

    -- Calculate the total months between start and end dates
    SET total_months = PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM p_end_date), EXTRACT(YEAR_MONTH FROM p_start_date)) + 1;

    -- Count the months in session_data that match the session_id, username, and fall within the range
    SET accounted_months = (
        SELECT COUNT(DISTINCT CONCAT(year_str, '-', month_str))
        FROM session_data
        WHERE session_id = p_session_id
          AND username = p_username
          AND STR_TO_DATE(CONCAT(year_str, '-', month_str, '-01'), '%Y-%m-%d') BETWEEN p_start_date AND p_end_date
    );

    -- Return the difference
    RETURN total_months - accounted_months;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-27 18:35:35
