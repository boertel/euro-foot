CREATE TABLE `bet` (
	`id` int NOT NULL AUTO_INCREMENT,
	`game_id` int NOT NULL,
	`user_id` int NOT NULL,
	`score_a` int NOT NULL,
	`score_b` int NOT NULL,
        `validated` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
    UNIQUE (`game_id`, `user_id`),
    FOREIGN KEY (`game_id`) REFERENCES game(id),
    FOREIGN KEY (`user_id`) REFERENCES user(id)
) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
