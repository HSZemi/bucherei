CREATE TABLE `buch` (
  `ID` INTEGER AUTO_INCREMENT PRIMARY KEY,
  `Nummer` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Autor` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Titel` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Sparte` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Erscheinungsjahr` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Verlag` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Beschreibung` text COLLATE utf8mb4_german2_ci,
  `Bereich` varchar(255) COLLATE utf8mb4_german2_ci DEFAULT NULL,
  `Entliehen` boolean DEFAULT FALSE
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_german2_ci;