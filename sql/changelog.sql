-- @Time: 2014-08-22 16:45:49
-- @Author: mr.5 <mr5.simple@gmail.com>
ALTER TABLE `eva_user_users` ADD `extension` char(8) DEFAULT '00000000'  NOT NULL AFTER `providerType`;