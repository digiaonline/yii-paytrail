<?php

class m131125_145711_create_paytrail_payment_product_table extends CDbMigration
{
	public function up()
	{
        $this->execute(
            "CREATE TABLE `paytrail_payment_product` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `paymentId` INT UNSIGNED NOT NULL,
                `productId` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`)
            ) COLLATE='utf8_general_ci' ENGINE=InnoDB;"
        );
        $this->addForeignKey('paytrail_payment_product_paymentId', 'paytrail_payment_product', 'paymentId', 'paytrail_payment', 'id');
        $this->addForeignKey('paytrail_payment_product_productId', 'paytrail_payment_product', 'productId', 'paytrail_product', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('paytrail_payment_product_productId', 'paytrail_payment_product');
        $this->dropForeignKey('paytrail_payment_product_paymentId', 'paytrail_payment_product');
        $this->dropTable('paytrail_payment_product');
    }
}