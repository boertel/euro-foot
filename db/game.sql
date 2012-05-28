CREATE TABLE `Game` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_group` int(11) NOT NULL,
    `team_a` int NOT NULL,
    `team_b` int NOT NULL,
    `score_a` int(11) DEFAULT NULL,
    `score_b` int(11) DEFAULT NULL,
    `start_date` datetime NOT NULL,
    `end_date` datetime DEFAULT NULL,
    `location` varchar(255) DEFAULT NULL,
    `stadium` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE (team_a, team_b),
    FOREIGN KEY (`id_group`) REFERENCES group(id),
    FOREIGN KEY (`team_a`) REFERENCES team(id),
    FOREIGN KEY (`team_b`) REFERENCES team(id)
) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

--
-- Contenu de la table `team`
--

INSERT INTO `game` (`id`, `id_group`, `team_a`, `team_b`, `score_a`, `score_b`, `start_date`, `end_date`, `location`, `stadium`) VALUES
(1, 1, 11, 7, NULL, NULL, '2012-06-08 17:00:00', NULL, NULL, NULL),
(2, 1, 14, 13, NULL, NULL, '2012-06-08 19:45:00', NULL, NULL, NULL),
(3, 2, 10, 4, NULL, NULL, '2012-06-09 17:00:00', NULL, NULL, NULL),
(4, 2, 1, 12, NULL, NULL, '2012-06-09 19:45:00', NULL, NULL, NULL),
(5, 3, 5, 9, NULL, NULL, '2012-06-10 17:00:00', NULL, NULL, NULL),
(6, 3, 8, 3, NULL, NULL, '2012-06-10 19:45:00', NULL, NULL, NULL),
(7, 4, 6, 2, NULL, NULL, '2012-06-11 17:00:00', NULL, NULL, NULL),
(8, 4, 16, 15, NULL, NULL, '2012-06-11 19:45:00', NULL, NULL, NULL),
(9, 1, 7, 13, NULL, NULL, '2012-06-12 17:00:00', NULL, NULL, NULL),
(10, 1, 11, 14, NULL, NULL, '2012-06-12 19:45:00', NULL, NULL, NULL),
(11, 2, 4, 12, NULL, NULL, '2012-06-13 17:00:00', NULL, NULL, NULL),
(12, 2, 10, 1, NULL, NULL, '2012-06-13 19:45:00', NULL, NULL, NULL),
(13, 3, 9, 3, NULL, NULL, '2012-05-14 17:00:00', NULL, NULL, NULL),
(14, 3, 5, 8, NULL, NULL, '2012-06-14 19:45:00', NULL, NULL, NULL),
(15, 4, 16, 6, NULL, NULL, '2012-05-15 17:00:00', NULL, NULL, NULL),
(16, 4, 15, 2, NULL, NULL, '2012-06-15 19:45:00', NULL, NULL, NULL),
(17, 1, 7, 14, NULL, NULL, '2012-06-16 19:45:00', NULL, NULL, NULL),
(18, 1, 13, 11, NULL, NULL, '2012-06-16 19:45:00', NULL, NULL, NULL),
(19, 2, 4, 1, NULL, NULL, '2012-06-17 19:45:00', NULL, NULL, NULL),
(20, 2, 12, 10, NULL, NULL, '2012-06-17 19:45:00', NULL, NULL, NULL),
(21, 3, 3, 5, NULL, NULL, '2012-06-18 19:45:00', NULL, NULL, NULL),
(22, 3, 9, 8, NULL, NULL, '2012-06-18 19:45:00', NULL, NULL, NULL),
(23, 4, 15, 6, NULL, NULL, '2012-06-19 19:45:00', NULL, NULL, NULL),
(24, 4, 2, 16, NULL, NULL, '2012-06-19 19:45:00', NULL, NULL, NULL);
