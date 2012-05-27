CREATE TABLE IF NOT EXISTS `group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
)  ENGINE=innoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

--
-- Contenu de la table `group`
--

INSERT INTO `group` (`id`, `title`) VALUES
(1, 'Groupe A'),
(2, 'Groupe B'),
(3, 'Groupe C'),
(4, 'Groupe D');