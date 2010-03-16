-- Страница
 CREATE TABLE `page` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'Родительская страница',
	`title` TEXT NOT NULL COMMENT 'Заголовок',
	`text` TEXT NOT NULL COMMENT 'Текст'
);


-- Доп. поля страницы
 CREATE TABLE `prop` (
	`id` INT NOT NULL COMMENT 'К какой странице относится',
	`field` TEXT NOT NULL COMMENT 'Поле',
	`value` TEXT NOT NULL COMMENT 'Значение'
);


-- Файлы
 CREATE TABLE `file` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'К какой странице относится',
	`type` TEXT NOT NULL COMMENT 'Тип файла'
);
