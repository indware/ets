<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "employee_on_visit".
 *
 * @property string $id
 * @property string $emp_master_id
 * @property string $from_date
 * @property string $to_date
 * @property string $address
 * @property string $is_active
 * @property string $created_date
 * @property string $created_by
 * @property string $modified_date
 * @property string $modified_by
 *
 * @property EmployeeMaster $empMaster
 */
class EmployeeOnVisit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_on_visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emp_master_id', 'created_by'], 'required'],
            [['emp_master_id', 'created_by', 'modified_by'], 'integer'],
            [['from_date', 'to_date', 'created_date', 'modified_date'], 'safe'],
            [['address', 'is_active'], 'string'],
            [['emp_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeMaster::className(), 'targetAttribute' => ['emp_master_id' => 'emp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emp_master_id' => 'Emp Master ID',
            'from_date' => 'From Date',
            'to_date' => 'To Date',
            'address' => 'Address',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmpMaster()
    {
        return $this->hasOne(EmployeeMaster::className(), ['emp_id' => 'emp_master_id']);
    }
}
