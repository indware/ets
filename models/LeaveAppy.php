<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leave_appy".
 *
 * @property string $id
 * @property string $user_emp_id
 * @property string $user_type
 * @property string $financial_year
 * @property string $applied_by
 * @property string $leave_reason
 * @property string $leave_status 0='denied',1='approved',2='pending'
 * @property string $approved_by
 * @property string $created_date
 */
class LeaveAppy extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_appy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['user_emp_id', 'applied_by', 'approved_by'], 'required'],
            [['user_emp_id', 'applied_by', 'approved_by'], 'integer'],
            [['user_type', 'leave_reason', 'leave_status'], 'string'],
            [['financial_year', 'created_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_emp_id' => 'User Emp ID',
            'user_type' => 'User Type',
            'financial_year' => 'Financial Year',
            'applied_by' => 'Applied By',
            'leave_reason' => 'Leave Reason',
            'leave_status' => 'Leave Status',
            'approved_by' => 'Approved By',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmp()
    {
        return $this->hasOne(EmployeeMaster::className(), ['emp_id' => 'user_emp_id']);
    }

    /**
     * @inheritdoc
     * @return LeaveAppyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeaveAppyQuery(get_called_class());
    }
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [
            'user_emp_id',
            'user_type',
            'financial_year',
            'applied_by',
            'leave_reason'
        ];
        
        return $scenarios;
    }
    
}
