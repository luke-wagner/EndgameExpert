CREATE DATABASE `chessapp` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

use `chessapp`;

CREATE TABLE `fens` (
  `game_link` varchar(60) NOT NULL,
  `move_number` int NOT NULL,
  `piece_count` int DEFAULT NULL,
  `fen` varchar(100) DEFAULT NULL,
  `descriptor` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`game_link`,`move_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `game_data` (
  `game_link` varchar(60) NOT NULL,
  `player_color` varchar(1) DEFAULT NULL,
  `outcome` tinyint(1) DEFAULT NULL,
  `result` varchar(25) DEFAULT NULL,
  `month` char(2) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  PRIMARY KEY (`game_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
