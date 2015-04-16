-- @Time: 2014-08-22 16:45:49
-- @Author: mr.5 <mr5.simple@gmail.com>
ALTER TABLE `eva_user_users` ADD `extension` char(8) DEFAULT '00000000'  NOT NULL AFTER `providerType`;

-- @Time: 2015-04-16 16:29:30
-- @Author: mr.5 <mr5.simple@gmail.com>
ALTER TABLE `eva_user_users` ADD `usernameCustomized` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '用户名是否已自定义' AFTER `username`;
