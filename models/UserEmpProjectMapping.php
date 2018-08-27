<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_emp_project_mapping".
 *
 * @property string $id
 * @property string $user_emp_id
 * @property string $user_type 1:Emp,2:User
 * @property string $project_id
 * @property string $is_active
 * @property string $created_date
 * @property string $modified_on
 */
class UserEmpProjectMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_emp_project_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_emp_id'], 'required'],
            [['user_emp_id'], 'integer'],
            [['user_type', 'is_active'], 'string'],
            [['created_date', 'modified_on'], 'safe'],
            [['project_id'], 'string', 'max' => 255],
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
            'project_id' => 'Project ID',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
            'modified_on' => 'Modified On',
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
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(ProjectMaster::className(), ['id' => 'project_id']);
    }
}
