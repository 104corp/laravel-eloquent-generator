USE `default`;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `birthday` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '1970-01-01 01:01:01',
  `updated_at` timestamp NOT NULL DEFAULT '1970-01-01 01:01:01',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
