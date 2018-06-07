USE `default`;

DROP TABLE IF EXISTS `test_basic`;

CREATE TABLE `test_basic` (
  `incrementShouldCallAssertPropertyTypeContainsInt` INT(11) NOT NULL AUTO_INCREMENT,
  `varcharShouldCallAssertPropertyTypeContainsString` VARCHAR(255) NOT NULL,
  `timestampShouldCallAssertPropertyTypeContainsCarbon` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`incrementShouldCallAssertPropertyTypeContainsInt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
