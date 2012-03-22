
DROP TABLE IF EXISTS `default_screen_alerts`;

CREATE TABLE default_screen_alerts  ( 
	`id`      	int(11) AUTO_INCREMENT NOT NULL,
	`username`	varchar(50) NOT NULL,
	`type`    	tinyint(1) UNSIGNED NOT NULL,
	`message` 	text NOT NULL,
	`mca`     	text NULL,
	`created` 	datetime NOT NULL,
	`expires` 	datetime NULL,
	PRIMARY KEY(id)
);