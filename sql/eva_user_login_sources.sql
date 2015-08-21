CREATE TABLE `eva_user_login_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `source` varchar(20) NOT NULL DEFAULT '',
  `loginAt` int(11) NOT NULL,
  `remoteIp` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;