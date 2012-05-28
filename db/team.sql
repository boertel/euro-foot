CREATE TABLE IF NOT EXISTS `team` (
	`id` int NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
    `flag` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

--
-- Contenu de la table `team`
--

INSERT INTO `team` (`id`, `name`, `flag`) VALUES
(1, 'ALLEMAGNE', 'de.png'),
(2, 'ANGLETERRE', 'england.png'),
(3, 'CROATIE', 'cr.png'),
(4, 'DANEMARK', 'dk.png'),
(5, 'ESPAGNE', 'es.png'),
(6, 'FRANCE', 'fr.png'),
(7, 'GRECE', 'gr.png'),
(8, 'IRLANDE', 'ie.png'),
(9, 'ITALIE', 'it.png'),
(10, 'PAYS-BAS', 'nl.png'),
(11, 'POLOGNE', 'pl.png'),
(12, 'PORTUGAL', 'pt.png'),
(13, 'REP. TCHEQUE', 'cz.png'),
(14, 'RUSSIE', 'ru.png'),
(15, 'SUEDE', 'se.png'),
(16, 'UKRAINE', 'ua.png');
