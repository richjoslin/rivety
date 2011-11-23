
DROP TABLE IF EXISTS `default_screen_alerts`;

CREATE TABLE `default_screen_alerts`
(
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`username` VARCHAR( 50 ) NOT NULL ,
`type` TINYINT( 1 ) UNSIGNED NOT NULL ,
`message` TEXT NOT NULL ,
`mca` TEXT NULL ,
`created` DATETIME NOT NULL ,
`expires` DATETIME NULL
);
