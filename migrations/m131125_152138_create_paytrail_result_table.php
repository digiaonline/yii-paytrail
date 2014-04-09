<?php

class m131125_152138_create_paytrail_result_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_result` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `paymentId` INT UNSIGNED NOT NULL,
                `orderNumber` VARCHAR(64) NOT NULL,
                `token` VARCHAR(64) NOT NULL,
                `url` TEXT NOT NULL,
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_result');
    }
}