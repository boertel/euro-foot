CREATE TABLE `user` (
	`id` int NOT NULL AUTO_INCREMENT,
    `facebookId` varchar(255) NOT NULL,
	`username` varchar(255) NOT NULL,
	`first_name` varchar(255) NOT NULL,
	`last_name` varchar(255) NOT NULL,
	`email` varchar(255) NOT NULL,
	`token` varchar(255) NOT NULL,
	`score` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	UNIQUE KEY `username` (`username`),
    UNIQUE KEY `facebookId_unique` (`facebookId`)
) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
