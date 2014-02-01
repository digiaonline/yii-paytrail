<?php

class m140131_145155_create_paytrail_contact_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_contact` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `addressId` INT UNSIGNED NOT NULL,
                `firstName` VARCHAR(64) NOT NULL,
                `lastName` VARCHAR(64) NOT NULL,
                `email` VARCHAR(255) NOT NULL,
                `phoneNumber` VARCHAR(64) NULL DEFAULT NULL,
                `mobileNumber` VARCHAR(64) NULL DEFAULT NULL,
                `companyName` VARCHAR(128) NULL DEFAULT NULL,
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_contact');
    }
}