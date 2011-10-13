CREATE TABLE if not exists `default_database_versions` (
  `id` varchar(255)  NOT NULL,
  `db_version` int  NOT NULL DEFAULT 0,
  `updated_on` datetime  NOT NULL,
  PRIMARY KEY (`id`)
);
