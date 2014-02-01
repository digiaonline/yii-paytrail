<?php

class m140201_205450_alter_paytrail_result_table extends CDbMigration
{
	public function up()
	{
        $this->addForeignKey('paytrail_result_paymentId', 'paytrail_result', 'paymentId', 'paytrail_payment', 'id');
    }

	public function down()
	{
        $this->dropForeignKey('paytrail_result_paymentId', 'paytrail_result');
    }
}