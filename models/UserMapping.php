<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_mapping".
 *
 * @property string $mapping_id
 * @property string $user_id
 * @property string $user_type 0->Normal User,1-> Zonal,2->State,3->City,4->Supervisor
 * @property int $type_id
 * @property int $created_by
 * @property string $created_date
 *
 * @property EmployeeMaster $user
 */
class UserMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_by'], 'required'],
            [['user_id', 'type_id', 'created_by'], 'integer'],
            [['user_type'], 'string'],
            [['created_date'], 'safe'],
            [['user_id', 'user_type', 'type_id'], 'unique', 'targetAttribute' => ['user_id', 'user_type', 'type_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => EmployeeMaster::className(), 'targetAttribute' => ['user_id' => 'emp_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mapping_id' => 'Mapping ID',
            'user_id' => 'User ID',
            'user_type' => 'User Type',
            'type_id' => 'Type ID',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(EmployeeMaster::className(), ['emp_id' => 'user_id']);
    }
}
