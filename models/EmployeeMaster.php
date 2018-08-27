<?php

namespace app\models;
use app\Util\DaysUtil;
use yii\db\Query;
use Yii;
use app\models\UserMapping;
use app\models\StateMaster;
use app\models\ZoneMaster;
use app\models\CityMaster;
/**
 * This is the model class for table "employee_master".
 *
 * @property string $emp_id
 * @property string $emp_code
 * @property string $supervisor_id
 * @property string $city_id
 * @property string $pincode
 * @property string $emp_name
 * @property string $phone
 * @property string $email_id
 * @property string $dob
 * @property string $password_reset_token
 * @property string $auth_token
 * @property string $password
 * @property string $time_from
 * @property string $time_to
 * @property string $week_off
 * @property string $attendence_policy_id
 * @property string $on_travel "0:IN","1:OUT"
 * @property string $unpaid_leaves
 * @property string $leaves
 * @property string $filename
 * @property string $is_active
 * @property int $created_by
 * @property string $modified_on
 * @property string $created_date
 * @property int $modified_by
 * @property string $soft_delete 0=Not Deleted, 1=Deleted
 *
 *  @property Attendencepolicy $attendencePolicy
 * @property Landmark[] $landmarks
 * @property LateCount[] $lateCounts
 * @property UserMapping $userMappings
 * @property UserEmpProjectMapping $userEmpProjectMappings
 */
class EmployeeMaster extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supervisor_id', 'city_id', 'attendence_policy_id', 'created_by', 'modified_by'], 'integer'],
            [['dob', 'time_from', 'time_to', 'modified_on', 'created_date'], 'safe'],
            [['on_travel', 'is_active'], 'string'],
            [['emp_code', 'pincode', 'emp_name', 'phone', 'email_id', 'password_reset_token', 'auth_token', 'password', 'week_off', 'leaves', 'filename'], 'string', 'max' => 255],
            [['unpaid_leaves'], 'string', 'max' => 50],
            [['emp_code'], 'unique'],
            [['emp_name'], 'unique'],
            [['phone'], 'unique'],
            [['attendence_policy_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attendencepolicy::className(), 'targetAttribute' => ['attendence_policy_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'emp_id' => 'Emp ID',
            'emp_code' => 'Emp Code',
            'supervisor_id' => 'Supervisor ID',
            'city_id' => 'City ID',
            'pincode' => 'Pincode',
            'emp_name' => 'Emp Name',
            'phone' => 'Phone',
            'email_id' => 'Email ID',
            'dob' => 'Dob',
            'password_reset_token' => 'Password Reset Token',
            'auth_token' => 'Auth Token',
            'password' => 'Password',
            'time_from' => 'Time From',
            'time_to' => 'Time To',
            'week_off' => 'Week Off',
            'attendence_policy_id' => 'Attendence Policy ID',
            'on_travel' => 'On Travel',
            'unpaid_leaves' => 'Unpaid Leaves',
            'leaves' => 'Leaves',
            'filename' => 'Filename',
            'is_active' => 'Is Active',
            'created_by' => 'Created By',
            'modified_on' => 'Modified On',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendencePolicy()
    {
        return $this->hasOne(Attendencepolicy::className(), ['id' => 'attendence_policy_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLandmarks()
    {
        return $this->hasMany(Landmark::className(), ['user_id' => 'emp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLateCounts()
    {
        return $this->hasMany(LateCount::className(), ['emp_id' => 'emp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserMappings()
    {
        return $this->hasOne(UserMapping::className(), ['user_id' => 'emp_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEmpProjectMappings()
    {
        return $this->hasOne(UserEmpProjectMapping::className(), ['user_emp_id' => 'emp_id']);
    }



    /*
     * Encrypt Password
     */
    public static function passwordEncrypt($password) {
        $hash_format = '$2y$10$';
        $salt_length = 22;
        $salt = self::generateSalt($salt_length);
        $format_and_salt = $hash_format . $salt;
        $hash = crypt($password, $format_and_salt);
        return $hash;
    }
    /*
     * Generate Salt for passwrod encryption
     */
    public static function generateSalt($lenght) {
        $unique_random_string = md5(uniqid(mt_rand(100000000, 999999999), true));
        $base64_string = base64_encode($unique_random_string);
        $modified_base64_string = str_replace('+', '.', $base64_string);
        $salt = substr($modified_base64_string, 0, $lenght);
        return $salt;
    }

    /*
     * @return login response
     */

    public static function passwordCheck($password, $existing_hash) {
        $hash = md5($password, $existing_hash);
        if($hash === $existing_hash) {
            return true;
        } else {
            return false;
        }
    }
    /*
     * Generate auth_token for future requests
     */
    public static function generateAuthToken() {
        return self::passwordEncrypt(self::generateSalt(22));
    }

    /*
     * Is employee on travel
     * returns true or false
     */
    public static function isOnTravel(EmployeeMaster $employee) {
        return $employee->on_travel == 1 ? true : false;
    }

    /*
     * Is today week off for employee
     */

    public static function isTodayWeekOff(EmployeeMaster $em) {
        $week_offs = $em->week_off;
        $week_offs = json_decode($week_offs);
        $count = 0;
        foreach ($week_offs as $week_off) {
            $wo = WeekMaster::findOne(['day_id' => $week_off]);
            if(!empty($wo)) {
                if(strtolower($wo->day_name) == DaysUtil::todayIs()) {
                    $count++;
                }
            }
        }

        return $count == 0 ? false : true;
    }

    /*
     * Tells if the employee has already given his/her attendance
     */
    public static function notGivenAttendance(EmployeeMaster $em) {
        if(EmpAttendance::findOne(['user_id' => $em->emp_id, 'date' => DaysUtil::todayDate()]) != null) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * Tells if the employee has already given his/her attendance
     */
    public static function notGivenLateAttendance(EmployeeMaster $em) { // added on 08-10-2018
        if(LateCount::findOne(['emp_id' => $em->emp_id, 'date' => DaysUtil::todayDate()]) != null) {
            return false;
        } else {
            return true;
        }
    }

    public function isNormalEmployee(EmployeeMaster $emp_id) { // added on 08-08-2018
        if($emp_id->userMappings->user_type == 0) {
            return true;
        } else {
            return false;
        }
    }
}
