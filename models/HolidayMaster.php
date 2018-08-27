<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "holiday_master".
 *
 * @property string $id
 * @property string $holiday_name
 * @property string $date
 * @property string $is_active 0='Inactive',1='Active'
 * @property string $created_date
 * @property int $created_by
 * @property string $modified_date
 * @property int $modified_by
 */
class HolidayMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'holiday_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['holiday_name', 'date'], 'required'],
            [['date', 'created_date', 'modified_date'], 'safe'],
            [['is_active'], 'string'],
            [['created_by', 'modified_by'], 'integer'],
            [['holiday_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'holiday_name' => 'Holiday Name',
            'date' => 'Date',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_date' => 'Modified Date',
            'modified_by' => 'Modified By',
        ];
    }
}
