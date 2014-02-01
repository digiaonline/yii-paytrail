<?php

class m140131_144917_create_paytrail_address_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_address` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `streetAddress` VARCHAR(64) NOT NULL,
                `postalCode` VARCHAR(16) NOT NULL,
                `postOffice` VARCHAR(64) NOT NULL,
                `countryCode` VARCHAR(2) NOT NULL,
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_address');
    }
}