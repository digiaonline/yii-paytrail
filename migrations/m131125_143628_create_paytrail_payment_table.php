<?php

class m131125_143628_create_paytrail_payment_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_payment` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `contactId` INT UNSIGNED NOT NULL,
                `urlsetId` INT UNSIGNED NOT NULL,
                `orderNumber` VARCHAR(64) NOT NULL,
                `referenceNumber` VARCHAR(22) NULL DEFAULT NULL,
                `description` TEXT NULL DEFAULT NULL,
                `currency` VARCHAR(3) NOT NULL DEFAULT 'EUR',
                `locale` VARCHAR(5) NOT NULL DEFAULT 'fi_FI',
                `includeVat` TINYINT(1) NOT NULL DEFAULT '1',
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_payment');
    }
}