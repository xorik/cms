-- Страница
 CREATE TABLE `page` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`gid` INT NOT NULL COMMENT 'Родительская страница',
	`title` TEXT NOT NULL COMMENT 'Заголовок',
	`text` TEXT NOT NULL COMMENT 'Текст'
);
