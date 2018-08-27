<?php

use yii\db\Migration;

/**
 * Class m180706_070219_create_table_employee_master
 */
class m180706_070219_create_table_employee_master extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('employee_master', [
            'emp_id' => $this->primaryKey()->unsigned(),
            'emp_code' => $this->string(60)->unique()->notNull(),
            'supervisor_id' => $this->integer()->notNull(),
            'city_id' => $this->integer()->notNull(),
            'emp_name' => $this->string(100)->notNull(),
            'phone' => $this->string(10)->unique()->notNull(),
            'email_id' => $this->string(100)->unique()->notNull(),
            'dob' => $this->date(),
            'password' => $this->string(255)->notNull(),
            'time_from' => $this->time()->notNull(),
            'time_to' => $this->time()->notNull(),
            'week_of' => $this->string(60)->notNull(),
            'is_active' => $this->boolean()->defaultValue(1)->notNull(),
            'created_by' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180706_070219_create_table_employee_master cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180706_070219_create_table_employee_master cannot be reverted.\n";

        return false;
    }
    */
}
