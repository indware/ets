<?php
namespace app\controllers;

use yii\rest\ActiveController;
use app\models\EmployeeMaster;
use app\models\Mailer as SumanMailer;
use app\Util;
use app\models\EmpAttendance;


class EmployeeController extends ActiveController {
    public $modelClass = 'app\models\EmployeeMaster';
    
    public function actions() {
        $actions = parent::actions();
        
        unset($actions['create']);
        
        return $actions;
    }
    
    public function actionCreate() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_CREATE;
        $employee->attributes = \Yii::$app->request->post();
        
        if($employee->validate()){
            $employee->password = $employee->passwordEncrypt(\Yii::$app->request->post('password'));
            if($employee->save()){
                return array('data' => 'Employee registration successfull', 'status' => Util\ReturnCodes::DATA_CREATED);
            } else {
                return array('data' => 'Registration error!', 'status' => Util\ReturnCodes::FAILURE);
            }
        } else {
            return array('data' => $employee->getErrors(), 'status' => Util\ReturnCodes::FAILURE);
        }
    }
    
    public function actionForgotPassword() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_CHECK_EMAIL;
        $employee->attributes = \Yii::$app->request->post();
        $employee = EmployeeMaster::findOne(['email_id' => $employee->email_id]);
        
        if(empty($employee)) {
            return array('data' => 'No user with this email id', 'status' => Util\ReturnCodes::DATA_NOT_FOUND);
        } else {
            $employee->setPasswordResetToken();
            $employee->save();
            SumanMailer::send(SumanMailer::TYPE_PASS_RESET, $employee);
            return array('data' => 'An OTP has been sent to your registered email address!', 'status' => Util\ReturnCodes::SUCCESS);
        }
    }
    
    public function actionResetPassword() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_FORGOT_PASSWORD;
        $employee->attributes = \Yii::$app->request->post();
        $employee = EmployeeMaster::findOne([
            'email_id' => $employee->email_id,
            'password_reset_token' => $employee->password_reset_token
        ]);
        
        if(empty($employee)){
            return array('data' => 'Unauthorized access!', 'status' => Util\ReturnCodes::AUTHENTICATION_FAILED);
        } elseif (empty(\Yii::$app->request->post('password'))) {
            return array('data' => 'Password can\'t be empty', 'status' => Util\ReturnCodes::FAILURE);
        } else {
            $employee->setPassword(\Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password')));
            $employee->password_reset_token = NULL;
            $employee->save();
            return array('data' => 'Password reset successful!', 'status' => Util\ReturnCodes::SUCCESS);
        }
    }
    
    public function actionLogin() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_LOGIN;
        $employee->attributes = \Yii::$app->request->post();
        
        $user = EmployeeMaster::findOne(['email_id' => \yii::$app->request->post('email_id')]);
        if(empty($user)) {
            return array('data' => 'No employee registered with this Email Id', 'status' => Util\ReturnCodes::DATA_NOT_FOUND);
        } else {
            if($employee::passwordCheck(\yii::$app->request->post('password'), $user->password)) {
                $user->auth_token = $user->setAuthToken();
                if($user->save()) {
                    return array('data' => 'Login successful!', 'token' => $user->auth_token, 'status' => Util\ReturnCodes::SUCCESS);
                } else {
                    return array('data' => 'Could not save Authentication token!', 'status' => Util\ReturnCodes::FAILURE);
                }
            } else {
                return array('data' => 'Invalid credentials!', 'status' => Util\ReturnCodes::AUTHENTICATION_FAILED);
            }
        }
    }
    
    public function actionLogout() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_LOGOUT;
        
        // validate if request is authenticated
        if(EmployeeMaster::requestValidator(\yii::$app->request->post('email_id'), \yii::$app->request->post('auth_token')) != null){
            $user = EmployeeMaster::requestValidator(\yii::$app->request->post('email_id'), \yii::$app->request->post('auth_token'));
            
            // set auth_token to null
            $user->auth_token = NULL;
            $time_out_lat = \yii::$app->request->post('time_out_lat');
            $time_out_lng = \yii::$app->request->post('time_out_lng');
            $created_date = Util\DaysUtil::todayDate();
            
            // check if user logout and out-time-details updated
            if($user->save() && EmpAttendance::onLogout($user->emp_id, $created_date, $time_out_lat, $time_out_lng)) {
                return array('data' => 'Logout successful!', 'status' => Util\ReturnCodes::SUCCESS);
            } else {
                return array('data' => 'Error logout!', 'status' => Util\ReturnCodes::SUCCESS);
            }
        } else {
            return array('data' => 'Error logout!', 'status' => Util\ReturnCodes::AUTHENTICATION_FAILED);
        }
    }
    
    public function actionAttendence() {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $employee = new EmployeeMaster();
        $employee->scenario = EmployeeMaster::SCENARIO_ATTENDENCE;
        $today = Util\DaysUtil::todayIs();
        
        // validate if request is authenticated
        if(EmployeeMaster::requestValidator(\yii::$app->request->post('email_id'), \yii::$app->request->post('auth_token')) != null){
            
            // initialize user/employee with the given email & auth_token from post request
            $user = EmployeeMaster::requestValidator(\yii::$app->request->post('email_id'), \yii::$app->request->post('auth_token'));
            
            $week_off = $user->week_of;
            
            // check if it's users week off day
            if(EmployeeMaster::canGiveAttendence($week_off, $today)){
                $attendence = new EmpAttendance();
                $attendence->user_id = $user->emp_id;
                $attendence->time_in_lat = \Yii::$app->request->post('time_in_lat');
                $attendence->time_in_lng = \Yii::$app->request->post('time_in_lng');
                $attendence->time_in = new \yii\db\Expression('NOW()');
                $attendence->created_date = Util\DaysUtil::todayDate();
                
                // check if already given attendance for today
                if(EmpAttendance::isAttendenceAlreadyGiven($user->emp_id, Util\DaysUtil::todayDate())){
                    return array('data' => 'Attendence already given for today!', 'status' => Util\ReturnCodes::FAILURE);
                } else {
                    // check if attendance successfully saved
                    if($attendence->save()) {
                        return array('data' => 'Attendence successful!', 'status' => Util\ReturnCodes::SUCCESS);
                    }
                    else {
                        return array('data' => $attendence->getErrors(), 'status' => Util\ReturnCodes::FAILURE);
                    }
                }
            } else {
                return array('data' => 'Cannot give attendence on your week off!', 'status' => Util\ReturnCodes::FAILURE);
            }
        } else {
            return array('data' => 'Authentication error!', 'status' => Util\ReturnCodes::AUTHENTICATION_FAILED);
        }
    }
}

