<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "attendencepolicy".
 *
 * @property string $id
 * @property string $name
 * @property string $paid
 * @property string $casual
 * @property string $sick
 * @property string $other
 * @property int $is_active
 * @property int $created_by
 * @property string $created_date
 * @property int $modified_by
 * @property string $modified_date
 *
 * @property EmployeeMaster[] $employeeMasters
 */
class Attendencepolicy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attendencepolicy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'paid', 'casual', 'sick', 'other', 'created_by', 'modified_by'], 'required'],
            [['paid', 'casual', 'sick', 'other', 'is_active', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'paid' => 'Paid',
            'casual' => 'Casual',
            'sick' => 'Sick',
            'other' => 'Other',
            'is_active' => 'Is Active',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
            'modified_date' => 'Modified Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployeeMasters()
    {
        return $this->hasMany(EmployeeMaster::className(), ['attendence_policy_id' => 'id']);
    }
}
