CREATE DATABASE test_mysql DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

USE `test_mysql`;

DROP TABLE IF EXISTS `test_basic`;

CREATE TABLE `test_basic` (
  `incrementShouldCallAssertPropertyTypeContainsInt` INT(11) NOT NULL AUTO_INCREMENT,
  `varcharShouldCallAssertPropertyTypeContainsString` VARCHAR(255) NOT NULL,
  `timestampShouldCallAssertPropertyTypeContainsCarbon` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`incrementShouldCallAssertPropertyTypeContainsInt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
