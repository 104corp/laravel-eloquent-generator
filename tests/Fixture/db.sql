CREATE DATABASE test_mysql DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

USE `test_mysql`;

DROP TABLE IF EXISTS `should_return_int`;

CREATE TABLE `should_return_int` (
  `increment_field` INT(11) NOT NULL AUTO_INCREMENT,
  `int_field` INT(11) NOT NULL,
  PRIMARY KEY (`increment_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
