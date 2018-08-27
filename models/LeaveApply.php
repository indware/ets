<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "leave_apply".
 *
 * @property string $id
 * @property string $user_emp_id
 * @property string $user_type
 * @property string $financial_year
 * @property string $start_date
 * @property string $end_date
 * @property string $applied_by
 * @property string $leave_reason
 * @property string $leave_status 0='denied',1='approved',2='pending'
 * @property string $approved_by
 * @property string $created_date
 * @property int $leave_type 1=paid, 2=casual, 3=sick, 4=other
 */
class LeaveApply extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_emp_id', 'financial_year', 'start_date', 'end_date', 'applied_by'], 'required'],
            [['user_emp_id', 'applied_by', 'approved_by', 'leave_type'], 'integer'],
            [['user_type', 'leave_reason', 'leave_status'], 'string'],
            [['start_date', 'end_date', 'created_date'], 'safe'],
            [['financial_year'], 'string', 'max' => 20],
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
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'applied_by' => 'Applied By',
            'leave_reason' => 'Leave Reason',
            'leave_status' => 'Leave Status',
            'approved_by' => 'Approved By',
            'created_date' => 'Created Date',
            'leave_type' => 'Leave Type',
        ];
    }
    
    public static function hasOnePendingLeave(EmployeeMaster $em) {
        $leave_count = self::findOne(['user_emp_id' => $em->emp_id, 'leave_status' => '2']);
        
        if($leave_count == null) {
            return false;
        } else {
            return true;
        }
    }
    
    public static function left_leaves(EmployeeMaster $em){
        $paid_leaves = (int) $em->attendencePolicy->paid;
        $casual_leaves = (int) $em->attendencePolicy->casual;
        $sick_leaves = (int) $em->attendencePolicy->sick;
        $other_leaves = (int) $em->attendencePolicy->other;
        
        $total_taken_paid_leaves = count(self::findAll(['user_emp_id' => $em->emp_id, 'leave_status' => '1', 'leave_type' => '1']));
        $total_taken_casual_leaves = count(self::findAll(['user_emp_id' => $em->emp_id, 'leave_status' => '1', 'leave_type' => '2']));
        $total_taken_paid_leaves = count(self::findAll(['user_emp_id' => $em->emp_id, 'leave_status' => '1', 'leave_type' => '3']));
        $total_taken_paid_leaves = count(self::findAll(['user_emp_id' => $em->emp_id, 'leave_status' => '1', 'leave_type' => '4']));
        
        $left_paid_leaves = $paid_leaves - $total_taken_paid_leaves;
        $left_casual_leaves = $casual_leaves - $total_taken_casual_leaves;
        $left_sick_leaves = $sick_leaves - $total_taken_paid_leaves;
        $left_other_leaves = $other_leaves - $total_taken_paid_leaves;
        
        return array(
            'paid_leaves' => $left_paid_leaves,
            'casual_leaves' => $left_casual_leaves,
            'sick_leaves' => $left_sick_leaves,
            'other_leaves' => $left_other_leaves
        );
    }
    
    public static function pending_leave_requests(EmployeeMaster $em) {
        $my_employees = EmployeeMaster::findAll(['supervisor_id' => $em->emp_id]);
        $all_requests = array();
        foreach ($my_employees as $employee) {
            $emp_id = $employee['emp_id'];
            
            $leave_request = self::findOne(['user_emp_id' => $emp_id, 'leave_status' => '2']);
            
            if($leave_request != null) {
                
                $leave_request_id = $leave_request->id;
                array_push($all_requests, $leave_request_id);
                
            }
        }
        return $all_requests;
    }
}
