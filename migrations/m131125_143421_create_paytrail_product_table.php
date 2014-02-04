<?php

class m131125_143421_create_paytrail_product_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_product` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `paymentId` INT UNSIGNED NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `code` VARCHAR(16) NULL DEFAULT NULL,
                `quantity` INT UNSIGNED NOT NULL,
                `price` DECIMAL(10, 2) NOT NULL,
                `vat` DECIMAL(4, 2) NOT NULL,
                `discount` DECIMAL(4, 2) NOT NULL,
                `type` TINYINT(4) UNSIGNED NOT NULL DEFAULT '1',
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_product');
    }
}