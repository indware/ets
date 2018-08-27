<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_master".
 *
 * @property string $id
 * @property int $project_id
 * @property string $project_code
 * @property string $project_name
 * @property string $work_address
 * @property double $latitude
 * @property double $longitude
 * @property string $zone_id
 * @property string $state_id
 * @property string $city_id
 * @property string $is_active
 * @property string $created_date
 * @property int $created_by
 * @property string $modified_on
 * @property int $modified_by
 * @property string $soft_delete 0=Not Deleted, 1=Deleted
 *
 * @property UserEmpProjectMapping[] $userEmpProjectMappings
 */
class ProjectMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'project_code', 'project_name', 'work_address', 'latitude', 'longitude', 'zone_id', 'state_id', 'city_id', 'is_active', 'created_by'], 'required'],
            [['project_id', 'zone_id', 'state_id', 'city_id', 'created_by', 'modified_by'], 'integer'],
            [['work_address', 'is_active'], 'string'],
            [['latitude', 'longitude'], 'number'],
            [['created_date', 'modified_on'], 'safe'],
            [['project_code', 'project_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'work_address' => 'Work Address',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'zone_id' => 'Zone ID',
            'state_id' => 'State ID',
            'city_id' => 'City ID',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_on' => 'Modified On',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmpProjectMappings()
    {
        return $this->hasMany(UserEmpProjectMapping::className(), ['project_id' => 'id']);
    }
}
