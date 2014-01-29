-- Страница
 CREATE TABLE `page` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'Родительская страница',
	`title` TEXT NOT NULL COMMENT 'Заголовок',
	`text` TEXT NOT NULL COMMENT 'Текст',
	`type` TEXT NOT NULL COMMENT 'Тип страницы',
	`pos` INT NOT NULL DEFAULT '100500' COMMENT 'Позиция при сортировке',
	`hide` INT(1) NOT NULL DEFAULT '0' COMMENT 'Скрывать ли страницу'
);


-- Доп. поля страницы
 CREATE TABLE `prop` (
	`id` INT NOT NULL COMMENT 'К какой странице относится',
	`field` TEXT NOT NULL COMMENT 'Поле',
	`value` TEXT NOT NULL COMMENT 'Значение',
	UNIQUE KEY `field` ( `id`, `field`(20) )
);


-- Файлы
 CREATE TABLE `file` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'К какой странице относится',
	`gallery` TEXT NOT NULL COMMENT 'Название галереи',
	`filename` TEXT NOT NULL COMMENT 'Имя исходного файла',
	`type` TEXT NOT NULL COMMENT 'Тип файла',
	`pos` INT NOT NULL DEFAULT '0' COMMENT 'Позиция при сортировке'
);
