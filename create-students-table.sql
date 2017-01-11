DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(25) NOT NULL,
  `surname` VARCHAR(50) NOT NULL,
  `email` VARCHAR(60) NOT NULL,
  `gender` enum('f','m') NOT NULL,
  `group` VARCHAR(5) NOT NULL,
  `rating` INT NOT NULL,
  `token` VARCHAR(255) NOT NULL COMMENT 'Registration token',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
