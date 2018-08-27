<?php

use yii\db\Migration;

/**
 * Class m180706_153523_create_table_test
 */
class m180706_153523_create_table_test extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('test', [
            'id' => $this->primaryKey()->unsigned(),
            'name' => $this->string(20)->notNull()->defaultValue('suman')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('test');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180706_153523_create_table_test cannot be reverted.\n";

        return false;
    }
    */
}
