<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "late_count".
 *
 * @property string $id
 * @property string $emp_id
 * @property string $date
 * @property string $check_in_time
 * @property string $time_in_lat
 * @property double $time_in_lng
 * @property string $late_reason
 * @property string $late_status 0='denied',1='approved',2='pending'
 * @property int $approved_by
 * @property string $created_date
 *
 * @property EmployeeMaster $emp
 */
class LateCount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'late_count';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emp_id', 'date', 'late_reason'], 'required'],
            [['emp_id', 'approved_by'], 'integer'],
            [['date', 'created_date'], 'safe'],
            [['late_reason', 'late_status'], 'string'],
            [['emp_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeMaster::className(), 'targetAttribute' => ['emp_id' => 'emp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emp_id' => 'Emp ID',
            'date' => 'Date',
            'late_reason' => 'Late Reason',
            'late_status' => 'Late Status',
            'approved_by' => 'Approved By',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmp()
    {
        return $this->hasOne(EmployeeMaster::className(), ['emp_id' => 'emp_id']);
    }
}
