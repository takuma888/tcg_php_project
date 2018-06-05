DROP TABLE IF EXISTS `users`.`user_auth`;
CREATE TABLE IF NOT EXISTS `users`.`user_auth` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(128) DEFAULT '' COMMENT '用户名',
  `email` VARCHAR(128) DEFAULT '' COMMENT '邮箱',
  `mobile` VARCHAR(16) DEFAULT '' COMMENT '手机',
  `password` VARCHAR(32) NOT NULL DEFAULT '' COMMENT '密码',
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`),
  PRIMARY KEY (`id`)
) ENGINE InnoDB DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_general_ci;

