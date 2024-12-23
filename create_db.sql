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
  `client_name` varchar(45) DEFAULT NULL,
  `client_email` varchar(45) DEFAULT NULL,
  `player_name` varchar(45) NOT NULL,
  `game_link` varchar(60) NOT NULL,
  `player_color` varchar(1) DEFAULT NULL,
  `outcome` tinyint(1) DEFAULT NULL,
  `result` varchar(25) DEFAULT NULL,
  `month` char(2) DEFAULT NULL,
  `year` char(4) DEFAULT NULL,
  PRIMARY KEY (`player_name`,`game_link`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

