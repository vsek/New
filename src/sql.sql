#---------------------------------- 1.0 -----------------------------------------
CREATE TABLE `new` ( `id` INT NOT NULL AUTO_INCREMENT ,  `name` VARCHAR(255) NOT NULL ,  `link` VARCHAR(255) NOT NULL ,  `perex` TEXT NULL ,  `text` TEXT NOT NULL ,  `created` DATETIME NOT NULL ,    PRIMARY KEY  (`id`)) ENGINE = InnoDB;
ALTER TABLE `new` ADD `image` VARCHAR(255) NULL AFTER `created`;

