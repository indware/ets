<?php

use yii\db\Migration;

/**
 * Class m180706_135417_create_column_password_reset_token_employee_master
 */
class m180706_135417_create_column_password_reset_token_employee_master extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('employee_master', 'password_reset_token', $this->string(60)->unique()->after('password'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('employee_master', 'password_reset_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180706_135417_create_column_password_reset_token_employee_master cannot be reverted.\n";

        return false;
    }
    */
}
