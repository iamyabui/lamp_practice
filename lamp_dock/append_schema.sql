SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- 自動的にcommit文が実行される機能を無効にする
SET AUTOCOMMIT = 0;
-- 自動的にcommitされるのを防ぎ、ユーザでcommit/rollback文を実行する
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `history` (
  `order_id` int(11) NOT NULL,  
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

