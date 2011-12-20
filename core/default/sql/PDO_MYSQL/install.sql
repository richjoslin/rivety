
CREATE TABLE `default_cache_tags` (
	`id` bigint(20) NOT NULL auto_increment,
	`tag` varchar(50) NOT NULL,
	`created_on` datetime NOT NULL,
	PRIMARY KEY  (`id`, `tag`)
);

CREATE TABLE `default_caches` (
	`id` bigint(20) NOT NULL auto_increment,
	`name` varchar(255) NOT NULL,
	`data` text NOT NULL,
	`created_on` datetime NOT NULL,
	PRIMARY KEY  (`id`),
	UNIQUE KEY `name` (`name`)
);

CREATE TABLE `default_caches_tags` (
	`cache_id` bigint(20) NOT NULL,
	`tag_id` bigint(20) NOT NULL,
	`created_on` datetime NOT NULL,
	PRIMARY KEY (`cache_id`, `tag_id`)
);

CREATE TABLE `default_config` (
	`ckey` varchar(64) NOT NULL,
	`module` varchar(64) NOT NULL default 'default',
	`is_cached` tinyint(4) NOT NULL default '0',
	`value` text,
	PRIMARY KEY  (`ckey`,`module`)
);

CREATE TABLE `default_countries` (
	`country_code` varchar(5) NOT NULL,
	`continent` varchar(2) NOT NULL,
	`country` text NOT NULL,
	`sortorder` int(11) NOT NULL default '10',
	PRIMARY KEY  (`country_code`)
);

CREATE TABLE `default_database_versions` (
	`id` varchar(255) NOT NULL,
	`db_version` int(11) NOT NULL default '0',
	`updated_on` datetime NOT NULL,
	PRIMARY KEY  (`id`)
);

CREATE TABLE `default_images` (
	`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`username` VARCHAR( 50 ) NOT NULL ,
	`path` TEXT NOT NULL ,
	`filename` TEXT NOT NULL ,
	`metadata` TEXT NULL ,
	`uploaded` DATETIME NOT NULL ,
	`replaced` DATETIME NOT NULL
);

CREATE TABLE `default_locales` (
	`country_code` varchar(6) character set utf8 NOT NULL,
	`language_code` varchar(2) NOT NULL,
	`pseudo_country_code` varchar(6) default NULL,
	`pseudo_language_code` varchar(2) default NULL,
	`region_name` text NOT NULL,
	`country_name` text NOT NULL,
	`language_name` text NOT NULL,
	`created_on` datetime NOT NULL,
	`last_modified_on` datetime NOT NULL,
	PRIMARY KEY  (`country_code`,`language_code`)
);

CREATE TABLE `default_modules` (
	`id` varchar(255) NOT NULL,
	`is_enabled` tinyint(4) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);

CREATE TABLE `default_navigation` (
	`id` int(11) NOT NULL auto_increment,
	`parent_id` int(11) NOT NULL default '0',
	`role_id` int(11) NOT NULL,
	`module` text,
	`group` text,
	`short_name` text NOT NULL,
	`link_text` text,
	`url` text,
	`sort_order` int(11) NOT NULL default '0',
	PRIMARY KEY  (`id`)
);

CREATE TABLE `default_roles` (
	`id` int(11) NOT NULL auto_increment,
	`shortname` varchar(64) NOT NULL default 'empty',
	`description` text,
	`isadmin` tinyint(4) NOT NULL default '0',
	`isguest` tinyint(4) NOT NULL default '0',
	`isdefault` tinyint(4) NOT NULL default '0',
	PRIMARY KEY  (`id`),
	UNIQUE KEY `uc_shortname` (`shortname`)
);

CREATE TABLE `default_roles_resources` (
	`role_id` int(11) NOT NULL,
	`module` varchar(64) NOT NULL default '',
	`controller` varchar(64) NOT NULL default '',
	`action` varchar(64) NOT NULL default '',
	PRIMARY KEY  (`role_id`,`module`,`controller`,`action`)
);

CREATE TABLE `default_roles_resources_extra` (
	`role_id` int(11) NOT NULL,
	`module` varchar(64) NOT NULL,
	`resource` varchar(256) NOT NULL,
	PRIMARY KEY  (`role_id`,`module`,`resource`)
);

CREATE TABLE `default_roles_roles` (
	`role_id` int(11) NOT NULL,
	`inherits_role_id` int(11) NOT NULL,
	PRIMARY KEY  (`role_id`,`inherits_role_id`)
);

CREATE TABLE `default_sessions` (
	`id` varchar(32) NOT NULL,
	`expiration` int(11) NOT NULL,
	`value` text,
	PRIMARY KEY  (`id`)
);

CREATE TABLE `default_users` (
	`username` varchar(50) NOT NULL default '',
	`password` varchar(50) default NULL,
	`full_name` varchar(100) default NULL,
	`gender` enum('male','female','unspecified') NOT NULL default 'unspecified',
	`email` varchar(255) default NULL,
	`created_on` datetime NOT NULL,
	`last_login_on` datetime default NULL,
	`last_modified_on` datetime default NULL,
	`last_activity_on` datetime default NULL,
	`birthday` date default NULL,
	`ip` varchar(64) default NULL,
	`country_code` varchar(5) default NULL,
	`aboutme` text,
	`metadata` text,
	`tags` text,
	PRIMARY KEY  (`username`),
	UNIQUE KEY `email` (`email`)
);

CREATE TABLE `default_users_roles` (
	`username` varchar(64) NOT NULL,
	`role_id` int(11) NOT NULL,
	`last_modified_on` datetime default NULL,
	PRIMARY KEY  (`username`,`role_id`)
);

CREATE OR REPLACE VIEW `default_vw_users_index` AS
select
	`default_users`.`username` AS `username`,
	`default_users`.`password` AS `password`,
	`default_users`.`full_name` AS `full_name`,
	`default_users`.`gender` AS `gender`,
	`default_users`.`email` AS `email`,
	`default_users`.`created_on` AS `created_on`,
	`default_users`.`tags` AS `tags`,
	`default_users`.`last_login_on` AS `last_login_on`,
	`default_users`.`last_modified_on` AS `last_modified_on`,
	`default_users`.`birthday` AS `birthday`,
	`default_users`.`ip` AS `ip`,
	`default_users`.`country_code` AS `country_code`,
	(select `default_countries`.`continent` AS `continent` from `default_countries` where (`default_countries`.`country_code` = `default_users`.`country_code`)) AS `region`,
	`default_users`.`aboutme` AS `aboutme`,
	dayofyear(`default_users`.`birthday`) AS `birthday_day`,
	floor(((unix_timestamp(curdate()) - unix_timestamp(`default_users`.`birthday`)) / 31556926)) AS `age`
from `default_users`;

INSERT INTO `default_countries`
	(`country_code`, `continent`, `country`, `sortorder`)
VALUES
	('AO', 'AF', 'Angola, Republic of', 10),
	('BF', 'AF', 'Burkina Faso', 10),
	('BI', 'AF', 'Burundi, Republic of', 10),
	('BJ', 'AF', 'Benin, Republic of', 10),
	('BW', 'AF', 'Botswana, Republic of', 10),
	('CD', 'AF', 'Congo, Democratic Republic of the', 10),
	('CF', 'AF', 'Central African Republic', 10),
	('CG', 'AF', 'Congo, Republic of the', 10),
	('CI', 'AF', 'Cote d''Ivoire, Republic of', 10),
	('CM', 'AF', 'Cameroon, Republic of', 10),
	('CV', 'AF', 'Cape Verde, Republic of', 10),
	('DJ', 'AF', 'Djibouti, Republic of', 10),
	('DZ', 'AF', 'Algeria, People''s Democratic Republic of', 10),
	('EG', 'AF', 'Egypt, Arab Republic of', 10),
	('EH', 'AF', 'Western Sahara', 10),
	('ER', 'AF', 'Eritrea, State of', 10),
	('ET', 'AF', 'Ethiopia, Federal Democratic Republic of', 10),
	('GA', 'AF', 'Gabon, Gabonese Republic', 10),
	('GH', 'AF', 'Ghana, Republic of', 10),
	('GM', 'AF', 'Gambia, Republic of the', 10),
	('GN', 'AF', 'Guinea, Republic of', 10),
	('GQ', 'AF', 'Equatorial Guinea, Republic of', 10),
	('GW', 'AF', 'Guinea-Bissau, Republic of', 10),
	('KE', 'AF', 'Kenya, Republic of', 10),
	('KM', 'AF', 'Comoros, Union of the', 10),
	('LR', 'AF', 'Liberia, Republic of', 10),
	('LS', 'AF', 'Lesotho, Kingdom of', 10),
	('LY', 'AF', 'Libyan Arab Jamahiriya', 10),
	('MA', 'AF', 'Morocco, Kingdom of', 10),
	('MG', 'AF', 'Madagascar, Republic of', 10),
	('ML', 'AF', 'Mali, Republic of', 10),
	('MR', 'AF', 'Mauritania, Islamic Republic of', 10),
	('MU', 'AF', 'Mauritius, Republic of', 10),
	('MW', 'AF', 'Malawi, Republic of', 10),
	('MZ', 'AF', 'Mozambique, Republic of', 10),
	('NA', 'AF', 'Namibia, Republic of', 10),
	('NE', 'AF', 'Niger, Republic of', 10),
	('NG', 'AF', 'Nigeria, Federal Republic of', 10),
	('RE', 'AF', 'Reunion', 10),
	('RW', 'AF', 'Rwanda, Republic of', 10),
	('SC', 'AF', 'Seychelles, Republic of', 10),
	('SD', 'AF', 'Sudan, Republic of', 10),
	('SH', 'AF', 'St. Helena', 10),
	('SL', 'AF', 'Sierra Leone, Republic of', 10),
	('SN', 'AF', 'Senegal, Republic of', 10),
	('SO', 'AF', 'Somalia, Somali Republic', 10),
	('ST', 'AF', 'Sao Tome and Principe', 10),
	('SZ', 'AF', 'Swaziland, Kingdom of', 10),
	('TD', 'AF', 'Chad, Republic of', 10),
	('TG', 'AF', 'Togo, Togolese Republic', 10),
	('TN', 'AF', 'Tunisia, Tunisian Republic', 10),
	('TZ', 'AF', 'Tanzania, United Republic of', 10),
	('UG', 'AF', 'Uganda, Republic of', 10),
	('YT', 'AF', 'Mayotte', 10),
	('ZA', 'AF', 'South Africa, Republic of', 10),
	('ZM', 'AF', 'Zambia, Republic of', 10),
	('ZW', 'AF', 'Zimbabwe, Republic of', 10),
	('AQ', 'AN', 'Antarctica (the territory South of 60 deg S)', 10),
	('BV', 'AN', 'Bouvet Island (Bouvetoya)', 10),
	('GS', 'AN', 'S. Georgia and the S. Sandwich Islands', 10),
	('HM', 'AN', 'Heard Island and McDonald Islands', 10),
	('TF', 'AN', 'French Southern Territories', 10),
	('AE', 'AS', 'United Arab Emirates', 10),
	('AF', 'AS', 'Afghanistan, Islamic Republic of', 10),
	('AM', 'AS', 'Armenia, Republic of', 10),
	('AZ', 'AS', 'Azerbaijan, Republic of', 10),
	('BD', 'AS', 'Bangladesh, People''s Republic of', 10),
	('BH', 'AS', 'Bahrain, Kingdom of', 10),
	('BN', 'AS', 'Brunei Darussalam', 10),
	('BT', 'AS', 'Bhutan, Kingdom of', 10),
	('CC', 'AS', 'Cocos (Keeling) Islands', 10),
	('CN', 'AS', 'China, People''s Republic of', 10),
	('CX', 'AS', 'Christmas Island', 10),
	('CY', 'AS', 'Cyprus, Republic of', 10),
	('GE', 'AS', 'Georgia', 10),
	('HK', 'AS', 'Hong Kong', 10),
	('ID', 'AS', 'Indonesia, Republic of', 10),
	('IL', 'AS', 'Israel, State of', 10),
	('IN', 'AS', 'India, Republic of', 10),
	('IO', 'AS', 'British Indian Ocean Territory', 10),
	('IQ', 'AS', 'Iraq, Republic of', 10),
	('IR', 'AS', 'Iran, Islamic Republic of', 10),
	('JO', 'AS', 'Jordan, Hashemite Kingdom of', 10),
	('JP', 'AS', 'Japan', 10),
	('KG', 'AS', 'Kyrgyz Republic', 10),
	('KH', 'AS', 'Cambodia, Kingdom of', 10),
	('KP', 'AS', 'Korea, Democratic People''s Republic of', 10),
	('KR', 'AS', 'Korea, Republic of', 10),
	('KW', 'AS', 'Kuwait, State of', 10),
	('KZ', 'AS', 'Kazakhstan, Republic of', 10),
	('LA', 'AS', 'Lao People''s Democratic Republic', 10),
	('LB', 'AS', 'Lebanon, Lebanese Republic', 10),
	('LK', 'AS', 'Sri Lanka', 10),
	('MM', 'AS', 'Myanmar, Union of', 10),
	('MN', 'AS', 'Mongolia', 10),
	('MO', 'AS', 'Macao', 10),
	('MV', 'AS', 'Maldives, Republic of', 10),
	('MY', 'AS', 'Malaysia', 10),
	('NP', 'AS', 'Nepal, State of', 10),
	('OM', 'AS', 'Oman, Sultanate of', 10),
	('PH', 'AS', 'Philippines, Republic of the', 10),
	('PK', 'AS', 'Pakistan, Islamic Republic of', 10),
	('PS', 'AS', 'Palestinian Territory, Occupied', 10),
	('QA', 'AS', 'Qatar, State of', 10),
	('SA', 'AS', 'Saudi Arabia, Kingdom of', 10),
	('SG', 'AS', 'Singapore, Republic of', 10),
	('SY', 'AS', 'Syrian Arab Republic', 10),
	('TH', 'AS', 'Thailand, Kingdom of', 10),
	('TJ', 'AS', 'Tajikistan, Republic of', 10),
	('TL', 'AS', 'Timor-Leste, Democratic Republic of', 10),
	('TM', 'AS', 'Turkmenistan', 10),
	('TR', 'AS', 'Turkey, Republic of', 10),
	('TW', 'AS', 'Taiwan', 10),
	('UZ', 'AS', 'Uzbekistan, Republic of', 10),
	('VN', 'AS', 'Vietnam, Socialist Republic of', 10),
	('YE', 'AS', 'Yemen', 10),
	('AD', 'EU', 'Andorra, Principality of', 10),
	('AL', 'EU', 'Albania, Republic of', 10),
	('AT', 'EU', 'Austria, Republic of', 10),
	('BA', 'EU', 'Bosnia and Herzegovina', 10),
	('BE', 'EU', 'Belgium, Kingdom of', 10),
	('BG', 'EU', 'Bulgaria, Republic of', 10),
	('BY', 'EU', 'Belarus, Republic of', 10),
	('CH', 'EU', 'Switzerland, Swiss Confederation', 10),
	('CZ', 'EU', 'Czech Republic', 10),
	('DE', 'EU', 'Germany, Federal Republic of', 10),
	('DK', 'EU', 'Denmark, Kingdom of', 10),
	('EE', 'EU', 'Estonia, Republic of', 10),
	('ES', 'EU', 'Spain, Kingdom of', 10),
	('FI', 'EU', 'Finland, Republic of', 10),
	('FO', 'EU', 'Faroe Islands', 10),
	('FR', 'EU', 'France, French Republic', 10),
	('GB', 'EU', 'United Kingdom & N. Ireland', 10),
	('GG', 'EU', 'Guernsey, Bailiwick of', 10),
	('GI', 'EU', 'Gibraltar', 10),
	('GR', 'EU', 'Greece, Hellenic Republic', 10),
	('HR', 'EU', 'Croatia, Republic of', 10),
	('HU', 'EU', 'Hungary, Republic of', 10),
	('IE', 'EU', 'Ireland', 10),
	('IM', 'EU', 'Isle of Man', 10),
	('IS', 'EU', 'Iceland, Republic of', 10),
	('IT', 'EU', 'Italy, Italian Republic', 10),
	('JE', 'EU', 'Jersey, Bailiwick of', 10),
	('LI', 'EU', 'Liechtenstein, Principality of', 10),
	('LT', 'EU', 'Lithuania, Republic of', 10),
	('LU', 'EU', 'Luxembourg, Grand Duchy of', 10),
	('LV', 'EU', 'Latvia, Republic of', 10),
	('MC', 'EU', 'Monaco, Principality of', 10),
	('MD', 'EU', 'Moldova, Republic of', 10),
	('ME', 'EU', 'Montenegro, Republic of', 10),
	('MK', 'EU', 'Macedonia', 10),
	('MT', 'EU', 'Malta, Republic of', 10),
	('NL', 'EU', 'Netherlands, Kingdom of the', 10),
	('NO', 'EU', 'Norway, Kingdom of', 10),
	('PL', 'EU', 'Poland, Republic of', 10),
	('PT', 'EU', 'Portugal, Portuguese Republic', 10),
	('RO', 'EU', 'Romania', 10),
	('RS', 'EU', 'Serbia, Republic of', 10),
	('RU', 'EU', 'Russian Federation', 10),
	('SE', 'EU', 'Sweden, Kingdom of', 10),
	('SI', 'EU', 'Slovenia, Republic of', 10),
	('SJ', 'EU', 'Svalbard & Jan Mayen Islands', 10),
	('SK', 'EU', 'Slovakia (Slovak Republic)', 10),
	('SM', 'EU', 'San Marino, Republic of', 10),
	('UA', 'EU', 'Ukraine', 10),
	('VA', 'EU', 'Holy See (Vatican City State)', 10),
	('AG', 'NA', 'Antigua and Barbuda', 10),
	('AI', 'NA', 'Anguilla', 10),
	('AN', 'NA', 'Netherlands Antilles', 10),
	('AW', 'NA', 'Aruba', 10),
	('BB', 'NA', 'Barbados', 10),
	('BM', 'NA', 'Bermuda', 10),
	('BS', 'NA', 'Bahamas, Commonwealth of the', 10),
	('BZ', 'NA', 'Belize', 10),
	('CA', 'NA', 'Canada', 10),
	('CR', 'NA', 'Costa Rica, Republic of', 10),
	('CU', 'NA', 'Cuba, Republic of', 10),
	('DM', 'NA', 'Dominica, Commonwealth of', 10),
	('DO', 'NA', 'Dominican Republic', 10),
	('GD', 'NA', 'Grenada', 10),
	('GL', 'NA', 'Greenland', 10),
	('GP', 'NA', 'Guadeloupe', 10),
	('GT', 'NA', 'Guatemala, Republic of', 10),
	('HN', 'NA', 'Honduras, Republic of', 10),
	('HT', 'NA', 'Haiti, Republic of', 10),
	('JM', 'NA', 'Jamaica', 10),
	('KN', 'NA', 'St. Kitts and Nevis, Federation of', 10),
	('KY', 'NA', 'Cayman Islands', 10),
	('LC', 'NA', 'St. Lucia', 10),
	('MQ', 'NA', 'Martinique', 10),
	('MS', 'NA', 'Montserrat', 10),
	('MX', 'NA', 'Mexico, United Mexican States', 10),
	('NI', 'NA', 'Nicaragua, Republic of', 10),
	('PA', 'NA', 'Panama, Republic of', 10),
	('PM', 'NA', 'St. Pierre and Miquelon', 10),
	('PR', 'NA', 'Puerto Rico, Commonwealth of', 10),
	('SV', 'NA', 'El Salvador, Republic of', 10),
	('TC', 'NA', 'Turks and Caicos Islands', 10),
	('TT', 'NA', 'Trinidad and Tobago, Republic of', 10),
	('US', 'NA', 'USA', 10),
	('VC', 'NA', 'St. Vincent and the Grenadines', 10),
	('VG', 'NA', 'British Virgin Islands', 10),
	('VI', 'NA', 'US Virgin Islands', 10),
	('AS', 'OC', 'American Samoa', 10),
	('AU', 'OC', 'Australia, Commonwealth of', 10),
	('CK', 'OC', 'Cook Islands', 10),
	('FJ', 'OC', 'Fiji, Republic of the Fiji Islands', 10),
	('FM', 'OC', 'Micronesia, Federated States of', 10),
	('GU', 'OC', 'Guam', 10),
	('KI', 'OC', 'Kiribati, Republic of', 10),
	('MH', 'OC', 'Marshall Islands, Republic of the', 10),
	('MP', 'OC', 'Northern Mariana Islands', 10),
	('NC', 'OC', 'New Caledonia', 10),
	('NF', 'OC', 'Norfolk Island', 10),
	('NR', 'OC', 'Nauru, Republic of', 10),
	('NU', 'OC', 'Niue', 10),
	('NZ', 'OC', 'New Zealand', 10),
	('PF', 'OC', 'French Polynesia', 10),
	('PG', 'OC', 'Papua New Guinea, Independent State of', 10),
	('PN', 'OC', 'Pitcairn Islands', 10),
	('PW', 'OC', 'Palau, Republic of', 10),
	('SB', 'OC', 'Solomon Islands', 10),
	('TK', 'OC', 'Tokelau', 10),
	('TO', 'OC', 'Tonga, Kingdom of', 10),
	('TV', 'OC', 'Tuvalu', 10),
	('UM', 'OC', 'United States Minor Outlying Islands', 10),
	('VU', 'OC', 'Vanuatu, Republic of', 10),
	('WF', 'OC', 'Wallis and Futuna', 10),
	('WS', 'OC', 'Samoa, Independent State of', 10),
	('AR', 'SA', 'Argentina, Argentine Republic', 10),
	('BO', 'SA', 'Bolivia, Republic of', 10),
	('BR', 'SA', 'Brazil, Federative Republic of', 10),
	('CL', 'SA', 'Chile, Republic of', 10),
	('CO', 'SA', 'Colombia, Republic of', 10),
	('EC', 'SA', 'Ecuador, Republic of', 10),
	('FK', 'SA', 'Falkland Islands (Malvinas)', 10),
	('GF', 'SA', 'French Guiana', 10),
	('GY', 'SA', 'Guyana, Co-operative Republic of', 10),
	('PE', 'SA', 'Peru, Republic of', 10),
	('PY', 'SA', 'Paraguay, Republic of', 10),
	('SR', 'SA', 'Suriname, Republic of', 10),
	('UY', 'SA', 'Uruguay, Eastern Republic of', 10),
	('VE', 'SA', 'Venezuela, Bolivarian Republic of', 10);

INSERT INTO `default_locales`
	(`country_code`, `language_code`, `pseudo_country_code`, `pseudo_language_code`, `region_name`, `country_name`, `language_name`, `created_on`, `last_modified_on`)
VALUES
	('US', 'en', NULL , NULL , 'North America', 'United States of America', 'English', NOW(), NOW());

INSERT INTO `default_database_versions`
	(`id`, `db_version`, `updated_on`)
VALUES
	('default', 0, NOW());

INSERT INTO `default_roles`
	(`id`, `shortname`, `description`, `isadmin`, `isguest`, `isdefault`)
VALUES
	(1, 'guest', 'Minimal Access', 0, 1, 0),
	(2, 'member', 'Standard Access', 0, 0, 1),
	(3, 'admin', 'All Access', 1, 0, 0);

INSERT INTO `default_roles_roles`
	(`role_id`, `inherits_role_id`)
VALUES
	(2, 1),
	(3, 2);

INSERT INTO `default_roles_resources`
	(`role_id`, `module`, `controller`, `action`)
VALUES
	(1, 'default', 'Auth', 'denied'),
	(1, 'default', 'Auth', 'error'),
	(1, 'default', 'Auth', 'login'),
	(1, 'default', 'Auth', 'missing'),
	(1, 'default', 'Index', 'index'),
	(1, 'default', 'Install', 'secondstage'),
	(1, 'default', 'Install', 'finished'),
	(1, 'default', 'User', 'forgotpassword'),
	(1, 'default', 'User', 'index'),
	(1, 'default', 'User', 'profile'),
	(1, 'default', 'User', 'register'),
	(1, 'default', 'User', 'resetpassword'),
	(2, 'default', 'Auth', 'loginredirect'),
	(2, 'default', 'Auth', 'logout'),
	(2, 'default', 'User', 'cancel'),
	(2, 'default', 'User', 'edit'),
	(2, 'default', 'User', 'loginbounce'),
	(2, 'default', 'User', 'postregister'),
	(3, 'default', 'Admin', 'index'),
	(3, 'default', 'Config', 'index'),
	(3, 'default', 'Module', 'index'),
	(3, 'default', 'Module', 'plugin'),
	(3, 'default', 'Module', 'uninstall'),
	(3, 'default', 'Navigation', 'delete'),
	(3, 'default', 'Navigation', 'edit'),
	(3, 'default', 'Navigation', 'editrole'),
	(3, 'default', 'Navigation', 'movedown'),
	(3, 'default', 'Navigation', 'moveup'),
	(3, 'default', 'Resource', 'edit'),
	(3, 'default', 'Role', 'delete'),
	(3, 'default', 'Role', 'edit'),
	(3, 'default', 'Role', 'index'),
	(3, 'default', 'User', 'deleteavatar'),
	(3, 'default', 'Useradmin', 'delete'),
	(3, 'default', 'Useradmin', 'edit'),
	(3, 'default', 'Useradmin', 'index'),
	(3, 'default', 'Useradmin', 'testdata');

INSERT INTO `default_users`
	(`username`, `password`, `full_name`, `gender`, `email`, `created_on`, `last_login_on`, `last_modified_on`, `last_activity_on`, `birthday`, `ip`, `country_code`, `aboutme`, `metadata`, `tags`)
VALUES
	('@@@@ADMIN_USERNAME@@@@', NULL, 'Administrator', 'unspecified', '@@@@ADMIN_EMAIL@@@@', '@@@@CREATED_ON@@@@@', NULL, NULL, NULL, '1976-05-03', '127.0.0.1', NULL, NULL, NULL, NULL);

INSERT INTO `default_users_roles`
	(`username`, `role_id`, `last_modified_on`)
VALUES
	('@@@@ADMIN_USERNAME@@@@', 3, NOW());

INSERT INTO `default_navigation`
	(`id`, `parent_id`, `role_id`, `module`, `group`, `short_name`, `link_text`, `url`, `sort_order`)
VALUES
	(1, 0, 3, 'default', NULL, 'main', 'Main', NULL, 0),
	(2, 1, 3, 'default', NULL, 'main_home', 'Home', '/', 10),
	(6, 0, 3, 'default', NULL, 'admin_header', 'Admin', NULL, 0),
	(7, 6, 3, 'default', NULL, 'admin_home', 'Home', '/default/admin/index/', 10),
	(8, 6, 3, 'default', NULL, 'admin_useradmin', 'Users', '/default/useradmin/index/', 20),
	(9, 21, 3, 'default', NULL, 'admin_config', 'Config', '/default/config/index/', 10),
	(10, 0, 2, 'default', NULL, 'main', 'Main', NULL, 0),
	(11, 10, 2, 'default', NULL, 'main_home', 'Home', '/', 10),
	(15, 0, 1, 'default', NULL, 'main', 'Main', NULL, 0),
	(16, 15, 1, 'default', NULL, 'main_home', 'Home', '/', 10),
	(21, 6, 3, 'default', NULL, 'admin_settings', 'Settings', NULL, 40),
	(22, 21, 3, 'default', NULL, 'admin_modules', 'Modules', '/default/module/index/', 20),
	(24, 21, 3, 'default', NULL, 'admin_testdata', 'Load Test Data', '/default/useradmin/testdata/', 40),
	(23, 21, 3, 'default', NULL, 'admin_roles', 'Roles', '/default/role/index/', 30);
