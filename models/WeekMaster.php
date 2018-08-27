<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "week_master".
 *
 * @property int $day_id
 * @property string $day_name
 */
class WeekMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'week_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['day_name'], 'required'],
            [['day_name'], 'string', 'max' => 255],
            [['day_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'day_id' => 'Day ID',
            'day_name' => 'Day Name',
        ];
    }
}
