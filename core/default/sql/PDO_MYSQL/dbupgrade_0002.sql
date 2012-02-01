
ALTER TABLE `default_users`
DROP `full_name`,
DROP `gender`,
DROP `birthday`,
DROP `country_code`,
DROP `aboutme`,
DROP `metadata`,
DROP `tags`
;

DROP TABLE IF EXISTS `default_vw_users_index`;
