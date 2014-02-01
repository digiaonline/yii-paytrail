<?php

class m140201_205603_alter_paytrail_contact_table extends CDbMigration
{
	public function up()
	{
        $this->addForeignKey('paytrail_contact_addressId', 'paytrail_contact', 'addressId', 'paytrail_address', 'id');
    }

	public function down()
	{
        $this->dropForeignKey('paytrail_contact_addressId', 'paytrail_contact');
    }
}