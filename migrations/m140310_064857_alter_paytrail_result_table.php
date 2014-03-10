<?php

class m140310_064857_alter_paytrail_result_table extends CDbMigration
{
    public function up()
    {
        $this->alterColumn('paytrail_result', 'token', 'VARCHAR(64) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->alterColumn('paytrail_result', 'token', 'VARCHAR(64) NOT NULL');
    }
}