<?php

class m131125_151345_create_paytrail_urlset_table extends CDbMigration
{
    public function up()
    {
        $this->execute(
            "CREATE TABLE `paytrail_urlset` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `successUrl` TEXT NOT NULL,
                `failureUrl` TEXT NOT NULL,
                `notificationUrl` TEXT NOT NULL,
                `pendingUrl` TEXT NULL DEFAULT NULL,
                `status` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
    }

    public function down()
    {
        $this->dropTable('paytrail_urlset');
    }
}