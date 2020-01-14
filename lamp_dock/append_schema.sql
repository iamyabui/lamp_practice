CREATE TABLE `sample`.`orders` ( 
     `order_id` INT NOT NULL AUTO_INCREMENT ,
     `user_id` INT NOT NULL , 
     `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
     `updated` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
     PRIMARY KEY (`order_id`)
     ) ENGINE = InnoDB;

CREATE TABLE `sample`.`orders` ( 
    `detail_id` INT NOT NULL AUTO_INCREMENT , 
    `order_id` INT NOT NULL , 
    `item_id` INT NOT NULL , 
    `amount` INT NOT NULL , 
    `price_bought` INT NOT NULL , 
    `created` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `updated` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    PRIMARY KEY (`detail_id`)
    ) ENGINE = InnoDB;

