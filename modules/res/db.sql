-- Страница
CREATE TABLE IF NOT EXISTS `page` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'Родительская страница',
	`title` TEXT NOT NULL COMMENT 'Заголовок',
	`text` TEXT NOT NULL COMMENT 'Текст',
	`type` TEXT NOT NULL COMMENT 'Тип страницы',
	`pos` INT NOT NULL DEFAULT '100500' COMMENT 'Позиция при сортировке',
	`hide` INT(1) NOT NULL DEFAULT '0' COMMENT 'Скрывать ли страницу'
) ENGINE=InnoDB CHARACTER SET=utf8;


-- Доп. поля страницы
CREATE TABLE IF NOT EXISTS `prop` (
	`id` INT NOT NULL COMMENT 'К какой странице относится',
	`field` TEXT NOT NULL COMMENT 'Поле',
	`value` TEXT NOT NULL COMMENT 'Значение',
	UNIQUE KEY `field` ( `id`, `field`(20) )
) ENGINE=InnoDB CHARACTER SET=utf8;


-- Файлы
CREATE TABLE IF NOT EXISTS `file` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'К какой странице относится',
	`gallery` TEXT NOT NULL COMMENT 'Название галереи',
	`filename` TEXT NOT NULL COMMENT 'Имя исходного файла',
	`type` TEXT NOT NULL COMMENT 'Тип файла',
	`pos` INT NOT NULL DEFAULT '0' COMMENT 'Позиция при сортировке'
) ENGINE=InnoDB CHARACTER SET=utf8;


-- Logs
CREATE TABLE IF NOT EXISTS `log` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`count` int(11) NOT NULL DEFAULT '1',
	`type` varchar(30) NOT NULL,
	`hash` varchar(32) NOT NULL,
	`data` text NOT NULL,
	KEY `hash` (`type`(10),`hash`(10))
) ENGINE=InnoDB CHARACTER SET=utf8;
