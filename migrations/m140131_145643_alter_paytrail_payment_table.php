<?php

class m140131_145643_alter_paytrail_payment_table extends CDbMigration
{
	public function up()
	{
        $this->addForeignKey('paytrail_payment_contactId', 'paytrail_payment', 'contactId', 'paytrail_contact', 'id');
        $this->addForeignKey('paytrail_payment_urlsetId', 'paytrail_payment', 'urlsetId', 'paytrail_urlset', 'id');
    }

	public function down()
	{
        $this->dropForeignKey('paytrail_payment_urlsetId', 'paytrail_payment');
        $this->dropForeignKey('paytrail_payment_contactId', 'paytrail_payment');
    }
}