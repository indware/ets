<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "emp_attendance".
 *
 * @property string $attendance_id
 * @property string $user_id
 * @property string $date
 * @property double $time_in_lat
 * @property double $time_in_lng
 * @property string $time_in
 * @property string $time_out
 * @property double $time_out_lat
 * @property double $time_out_lng
 * @property string $created_date
 */
class EmpAttendance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'emp_attendance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'date', 'time_in'], 'required'],
            [['user_id'], 'integer'],
            [['date', 'time_in', 'time_out', 'created_date'], 'safe'],
            [['time_in_lat', 'time_in_lng', 'time_out_lat', 'time_out_lng'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'attendance_id' => 'Attendance ID',
            'user_id' => 'User ID',
            'date' => 'Date',
            'time_in_lat' => 'Time In Lat',
            'time_in_lng' => 'Time In Lng',
            'time_in' => 'Time In',
            'time_out' => 'Time Out',
            'time_out_lat' => 'Time Out Lat',
            'time_out_lng' => 'Time Out Lng',
            'created_date' => 'Created Date',
        ];
    }
}
