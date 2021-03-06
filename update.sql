CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `timezone` varchar(255) NOT NULL,
  `lang` ENUM('ru','en') NOT NULL,
  `email` varchar(255) NULL,
  `verify` int(1) unsigned NULL,
  `city_id` int(11) unsigned NOT NULL,

  `datecreate` DATETIME NULL DEFAULT NULL COMMENT 'Дата создания',
  `datesignup` DATETIME NULL DEFAULT NULL COMMENT 'Дата регистрации',
  `dateverify` DATETIME NULL DEFAULT NULL COMMENT 'Дата верификации',
  `dateactive` DATETIME NULL DEFAULT NULL COMMENT 'Дата последнего входа',
  `datetoken` DATETIME NULL DEFAULT NULL COMMENT 'Дата создания токена',
  `datemail` DATETIME NULL DEFAULT NULL COMMENT 'Последняя дата отправленного письма: confirm, remind',
  

  PRIMARY KEY (`user_id`),
  UNIQUE INDEX (`email`)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;