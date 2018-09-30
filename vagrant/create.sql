
CREATE USER 'youtuben'@'%' IDENTIFIED BY 'fg2wkPCEhmzyHJYNdeUV2mnFrgk8XcZW';
CREATE DATABASE IF NOT EXISTS `youtuben`;
GRANT ALL PRIVILEGES ON `youtuben`.* TO 'youtuben'@'%';
GRANT ALL PRIVILEGES ON `youtuben\_%`.* TO 'youtuben'@'%';
flush privileges;
