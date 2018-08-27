<?php

use yii\db\Migration;

/**
 * Class m180710_061721_add_column_auth_token_to_ets_indware
 */
class m180710_061721_add_column_auth_token_to_ets_indware extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('employee_master', 'auth_token', $this->string(255)->unique()->after('password_reset_token'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('employee_master', 'auth_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180710_061721_add_column_auth_token_to_ets_indware cannot be reverted.\n";

        return false;
    }
    */
}
