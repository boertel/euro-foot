CREATE TABLE `game` (
	`id` int NOT NULL AUTO_INCREMENT,
	`team_a` int NOT NULL,
	`team_b` int NOT NULL,
	`score` int NOT NULL,
	`start_date` datetime NOT NULL,
	`end_date` datetime NOT NULL,
	`location` varchar(255) NOT NULL,
	`stadium` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
    UNIQUE (team_a, team_b),
    FOREIGN KEY (`team_a`) REFERENCES team(id),
    FOREIGN KEY (`team_b`) REFERENCES team(id)
) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
