<?php

namespace app\components;

use yii\helpers\ArrayHelper;
use yii\base\Component;
use yii\web\UploadedFile;
use yii\helpers\Url;
use YII;
use app\Util\ReturnCodes;
use app\Util\DaysUtil;
use app\Util\LeavesType;
use \yii\db\Expression;
use app\models\EmployeeMaster;
use app\models\UserMapping;
use app\models\EmpAttendance;
use app\models\LateCount;
use app\models\LeaveApply;
use app\models\EmployeeOnVisit;

class ServicehelperComponent extends Component
{
    private $serviceUitl;

    public function __construct()
    {
        $this->serviceUitl = new ServiceUtilComponent();
    }

    public function LoginHelper($inputJSON)
    {
        try {
            $input = json_decode($inputJSON, TRUE);

            $employee = EmployeeMaster::findOne(["email_id" => $input["email_id"],"password" => md5($input["password"]), "soft_delete" => "0"]);
            //Check if employee exist with the given email_id
            if(empty($employee)) {
                return array(
                    "data" => [
                        "message" => "Authentication error, invalid credentials"
                    ],
                    "status" => ReturnCodes::FAILURE
                );
            } else {
                // check if password is valid
                $given_password = $input["password"];
                //return md5($given_password);

                $existing_password = $employee->password;

                //if(md5($given_password)==md5($existing_password)) {
                 $user_type = $employee->userMappings->user_type;
//                 $work_address = $employee->userEmpProjectMappings->project->work_address;
//                 $project_lat = $employee->userEmpProjectMappings->project->latitude;
//                 $project_lng = $employee->userEmpProjectMappings->project->longitude;

                //return "success";

                $generated_auth_token = EmployeeMaster::generateAuthToken();

                $employee->auth_token = $generated_auth_token;
                //return $employee->auth_token;

                if($employee->save()) {
                    return array(
                        "data" => [
                            "emp_id" => $employee->emp_id,
                            "emp_code" => $employee->emp_code,
                            "email_id" => $employee->email_id,
                            "user_type" => $user_type,
                            "img_link" => "loading",
                            "name" => $employee->emp_name,
                            "login_time" => DaysUtil::currentTime(),
//                            "work_address" => $work_address,
//                            "project_lat" => $project_lat,
//                            "project_lng" => $project_lng,
                            "late" => DaysUtil::isLate($employee->time_from) ? "1" : "0",
                            "on_travel" => $employee->on_travel,
                            "check_in" => "1",
                            "check_out" => "0",
                            "auth_token" => $generated_auth_token
                        ],
                        "status" => ReturnCodes::SUCCESS
                    );
                } else {
                    return array(
                        "data" => [
                            "message" => "Something went wrong!"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                }
                //}
                /*else {
                    return array(
                        "data" => [
                            "message" => "Authentication error, invalid credentials"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                }*/
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function CheckInHelper($inputJSON) // edited on 08-10-2018
    {
        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                if(DaysUtil::isTodayHoliday())  { // if today is a Holiday

                    return array(
                            "data" => [
                                "message" => "Today is a holiday"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );

                } else {  // if today is Workday

                    if(EmployeeMaster::isTodayWeekOff($employee)) { // if its employee"s off day

                        return array(
                            "data" => [
                                "message" => "Today is your week off"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );

                    } else { // if its not employees"s week off day

                        if(EmployeeMaster::isOnTravel($employee)) { // if on travel

                            $emp_attendance = new EmpAttendance();
                            $emp_attendance->user_id = $employee->emp_id;
                            $emp_attendance->time_in_lat = $input["time_in_lat"];
                            $emp_attendance->time_in_lng = $input["time_in_lng"];
                            $emp_attendance->time_in = DaysUtil::currentTime();
                            $emp_attendance->date = DaysUtil::todayDate();

                            if(EmployeeMaster::notGivenAttendance($employee)) { // if not given attendance
                                if($emp_attendance->save()) {
                                    return array(
                                        "data" => [
                                            "check_out" => "1",
                                            "attendance_time" => $emp_attendance->time_in,
                                            "message" => "Attendance successfull!"
                                        ],
                                        "status" => ReturnCodes::SUCCESS
                                    );
                                } else {
                                    return array(
                                        "data" => [
                                            "message" => "Something went wrong!"
                                        ],
                                        "status" => ReturnCodes::FAILURE
                                    );
                                }
                            } else {
                                return array(
                                    "data" => [
                                        "message" => "Attendance already given for today"
                                    ],
                                    "status" => ReturnCodes::FAILURE
                                );
                            } // if(EmployeeMaster::notGivenAttendance($employee))

                        } else { // if not on travel

                            if(EmployeeMaster::notGivenAttendance($employee)) { // if not given attendance

                                if(DaysUtil::isLate($employee->time_from)) { // if late
                                    $late_count = new LateCount();
                                    $late_count->emp_id = $employee->emp_id;
                                    $late_count->date = DaysUtil::todayDate();
                                    $late_count->check_in_time = DaysUtil::currentTime();
                                    $late_count->time_in_lat = $input["time_in_lat"];
                                    $late_count->time_in_lng = $input["time_in_lng"];

                                    $late_reason = $late_count->late_reason = $input["late_reason"];

                                    if(empty($late_reason)) {
                                        return array(
                                            "data" => [
                                                "late" => "1",
                                                "message" => "Must provide a reason of coming late!"
                                            ],
                                            "status" => ReturnCodes::FAILURE
                                        );
                                    } else {
                                        if(EmployeeMaster::notGivenLateAttendance($employee)) {
                                            if($late_count->save()) {
                                                return array(
                                                    "data" => [
                                                        "late" => "1",
                                                        "check_out" => "1",
                                                        "attendance_time" => DaysUtil::currentTime(),
                                                        "message" => "Late submitted!"
                                                    ],
                                                    "status" => ReturnCodes::SUCCESS
                                                );
                                            } else {
                                                return array(
                                                    "data" => [
                                                        "message" => "Something went wrong!"
                                                    ],
                                                    "status" => ReturnCodes::FAILURE
                                                );
                                            }
                                        } else {
                                            return array(
                                                "data" => [
                                                    "message" => "Late already submitted!"
                                                ],
                                                "status" => ReturnCodes::FAILURE
                                            );
                                        }
                                    }

                                } else { // if not late

                                    $emp_attendance = new EmpAttendance();
                                    $emp_attendance->user_id = $employee->emp_id;
                                    $emp_attendance->date = DaysUtil::todayDate();
                                    $emp_attendance->time_in_lat = $input["time_in_lat"];
                                    $emp_attendance->time_in_lng = $input["time_in_lng"];
                                    $emp_attendance->time_in = DaysUtil::currentTime();

                                    if($emp_attendance->save()) {
                                        return array(
                                            "data" => [
                                                "late" => "0",
                                                "check_out" => "1",
                                                "attendance_time" => $emp_attendance->time_in,
                                                "message" => "Attendance successfull!"
                                            ],
                                            "status" => ReturnCodes::SUCCESS
                                        );
                                    } else {

                                        return array(
                                            "data" => [
                                                "message" => "Something went wrong!"
                                            ],
                                            "status" => ReturnCodes::FAILURE
                                        );

                                    }
                                }

                            } else { // attendance already given for today

                                return array(
                                    "data" => [
                                        "message" => "Attendance already given for today"
                                    ],
                                    "status" => ReturnCodes::FAILURE
                                );

                            } // if(EmployeeMaster::notGivenAttendance($employee))
                        } // if(EmployeeMaster::isOnTravel($employee))
                    } // if(EmployeeMaster::isTodayWeekOff($employee))
                } // if(DaysUtil::isTodayHoliday())
            } else {
                return array(
                    "data" => [
                            "message" => "Unauthrorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LateCheckInHelper($inputJSON) // added today
    {
        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                if(DaysUtil::isTodayHoliday())  { // if today is a Holiday

                    return array(
                            "data" => [
                                "message" => "Today is a holiday"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );

                } else {  // if today is Workday

                    if(EmployeeMaster::isTodayWeekOff($employee)) { // if its employee"s off day

                        return array(
                            "data" => [
                                "message" => "Today is your week off"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );

                    } else { // if its not employees"s week off day

                        $late_reason = trim($input["late_reason"]);

                        if(empty($late_reason)) {
                            return array(
                                "data" => [
                                    "message" => "Must provide a reason of coming late!"
                                ],
                                "status" => ReturnCodes::FAILURE
                            );
                        } else {
                            if(EmployeeMaster::notGivenAttendance($employee)) { // if not given attendance

                                $emp_attendance = new EmpAttendance();
                                $emp_attendance->user_id = $employee->emp_id;
                                $emp_attendance->date = DaysUtil::todayDate();
                                $emp_attendance->time_in_lat = $input["time_in_lat"];
                                $emp_attendance->time_in_lng = $input["time_in_lng"];
                                $emp_attendance->time_in = DaysUtil::currentTime();

                                $late_count = new LateCount();
                                $late_count->emp_id = $employee->emp_id;
                                $late_count->date = DaysUtil::todayDate();
                                $late_count->late_reason = $late_reason;

                                if($emp_attendance->save() && $late_count->save()) {
                                    return array(
                                        "data" => [
                                            "check_out" => 1,
                                            "attendance_time" => $emp_attendance->time_in,
                                            "message" => "Attendance successfull!"
                                        ],
                                        "status" => ReturnCodes::SUCCESS
                                    );
                                } else {
                                    return array(
                                        "data" => [
                                            "message" => "Something went wrong!"
                                        ],
                                        "status" => ReturnCodes::FAILURE
                                    );
                                }

                            } else { // attendance already given for today

                                return array(
                                    "data" => [
                                        "message" => "Attendance already given for today"
                                    ],
                                    "status" => ReturnCodes::FAILURE
                                );

                            } // if(EmployeeMaster::notGivenAttendance($employee))
                        }
                        return $input["action"];

                    } // if(EmployeeMaster::isTodayWeekOff($employee))
                } // if(DaysUtil::isTodayHoliday())
            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function CheckOutHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {
                $today = DaysUtil::todayDate();
                $user_id = $employee->emp_id;

                $emp_attendance = EmpAttendance::findOne(["date" => $today, "user_id" => $user_id]);

                if(!empty($emp_attendance)) { // if the employee has a check in record
                    $emp_attendance->time_out = DaysUtil::currentTime();
                    $emp_attendance->time_out_lat = $input["time_out_lat"];
                    $emp_attendance->time_out_lng = $input["time_out_lng"];

                    if($emp_attendance->save()) {

                        return array(
                            "data" => [
                                "meassage" => "Check out successfull!",
                                "check_out" => "0"
                            ],
                            "status" => ReturnCodes::SUCCESS
                        );

                    } else {

                        return array(
                            "data" => [
                                "message" => "Something went wrong!"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );

                    }

                } else { // if employee never checked in
                    return array(
                        "data" => [
                            "message" => "Something went wrong!"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                }

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthrorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveApplyHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                if(LeaveApply::hasOnePendingLeave($employee)) { // if one leave request is pending
                    return array(
                        "data" => [
                                "message" => "A request for leave is already pending!"
                            ],
                            "status" => ReturnCodes::FAILURE
                    );
                } else { // if no request is pending

                    $leave_apply = new LeaveApply();
                    $leave_apply->user_emp_id = $employee->emp_id;
                    $leave_apply->user_type = $employee->userMappings->user_type;
                    $leave_apply->financial_year = $input["financial_year"];
                    $leave_apply->start_date = $input["start_date"];
                    $leave_apply->end_date = $input["end_date"];
                    $leave_apply->applied_by = $employee->emp_id;
                    $leave_apply->leave_reason = $input["leave_reason"];
                    $leave_apply->leave_type = LeavesType::getLeaveType($input["leave_type"]);


                    if($leave_apply->save()) {
                        return array(
                            "data" => [
                                "message" => "Leave requested Successfully!"
                            ],
                            "status" => ReturnCodes::SUCCESS
                        );
                    } else {
                        return array(
                            "data" => [
                                "message" => "Something went wrong!"
                            ],
                            "status" => ReturnCodes::FAILURE
                        );
                    }
                }

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveCountHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $leaves = $employee->leaves;
                $leaves_decoded = json_decode($leaves);

                return array(
                    "data" => [
                            "leaves" => $leaves_decoded
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveShowHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $all_request_ids = LeaveApply::pending_leave_requests($employee);

                $all_erquests = array();

                foreach ($all_request_ids as $request_id) {
                    $leave_request = LeaveApply::findOne($request_id);
                    $leave_request_details = array(
                        "leave_id" => $leave_request->id,
                        "user_emp_id" => $leave_request->user_emp_id,
                        "start_date" => $leave_request->start_date,
                        "end_date" => $leave_request->end_date,
                        "leave_type" => LeavesType::getLeaveTypeFromValue($leave_request->leave_type),
                        "leave_reason" => $leave_request->leave_reason
                    );

                    array_push($all_erquests, $leave_request_details);
                }

                if(empty($all_erquests)) {
                    return array(
                        "data" => [
                                "leave_requests" => "0",
                                "message" => "No pending request."
                            ],
                            "status" => ReturnCodes::SUCCESS
                    );
                } else {
                    return array(
                        "data" => [
                                "leave_requests" => $all_erquests
                            ],
                            "status" => ReturnCodes::SUCCESS
                    );
                }


            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveStatusHelper($inputJSON) { // added 02-08-2018
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                    $leave = LeaveApply::findOne(["user_emp_id" => $employee->emp_id, "leave_status" => "2"]);
                    $emp_id = $employee->emp_id;
                    $leave = LeaveApply::find()
                        ->where(["user_emp_id" => $emp_id])
                        ->orderBy("id DESC")
                        ->all();

                    foreach ($leave as $l) {
                        $leave_array = array(
                            "start_date" => $l["start_date"],
                            "end_date" => $l["end_date"],
                            "leave_status" => $l["leave_status"]
                        );

                        return array(
                            "data" => [
                                "leave" => $leave_array
                            ],
                            "status" => ReturnCodes::SUCCESS
                        );
                    }


            } else {
                return array(
                    "data" => [
                        "message" => "Unauthorised request!"
                    ],
                    "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveGrantHelper($inputJSON) { // modified on 08-08-2018
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $leave_apply = LeaveApply::findOne(["id" => $input["leave_id"]]);

                if($employee->isNormalEmployee($employee)) { // normal user can't grant leaves
                    return array(
                        "data" => [
                            "message" => "You do not have enough privileges for this action"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                } else {
                    // cant grant or reject own leave_request
                    if($employee->emp_id == $leave_apply->user_emp_id) {
                        return array(
                            "data" => [
                                "message" => "Can not grant your own leave request!"
                            ],
                            "status" => ReturnCodes::SUCCESS
                        );
                    } else {

                        $leave_apply->approved_by = $employee->emp_id;
                        $leave_apply->leave_status = "1";

                        if($leave_apply->save()) {
                            return array(
                                "data" => [
                                    "message" => "Leave granted successfully!"
                                ],
                                "status" => ReturnCodes::SUCCESS
                            );
                        } else {
                            return array(
                                "data" => [
                                    "message" => "Something went wrong!"
                                ],
                                "status" => ReturnCodes::FAILURE
                            );
                        }
                    }
                }

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LeaveRejectHelper($inputJSON) { // modified on 08-08-2018
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $leave_apply = LeaveApply::findOne(["id" => $input["leave_id"]]);

                if($employee->isNormalEmployee($employee)) {
                    return array(
                        "data" => [
                            "message" => "You do not have enough privileges for this action"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                } else {
                    // cant grant or reject own leave_request
                    if($employee->emp_id == $leave_apply->user_emp_id) {
                        return array(
                            "data" => [
                                "message" => "Can not grant your own leave request!"
                            ],
                            "status" => ReturnCodes::SUCCESS
                        );
                    } else {

                        $leave_apply->approved_by = $employee->emp_id;
                        $leave_apply->leave_status = "0";

                        if($leave_apply->save()) {
                            return array(
                                "data" => [
                                    "message" => "Leave rejected successfully!"
                                ],
                                "status" => ReturnCodes::SUCCESS
                            );
                        } else {
                            return array(
                                "data" => [
                                    "message" => "Something went wrong!"
                                ],
                                "status" => ReturnCodes::FAILURE
                            );
                        }
                    }
                }

            } else {
                return array(
                    "data" => [
                        "message" => "Unauthorised request!"
                    ],
                    "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function OutOfCityHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $outOfCity = new EmployeeOnVisit();

                $outOfCity->emp_master_id = $employee->emp_id;
                $outOfCity->from_date = $input["from_date"];
                $outOfCity->to_date = $input["to_date"];
                $outOfCity->address = $input["address"];
                $outOfCity->created_by = $employee->emp_id;

                if($outOfCity->save()) {
                    return array(
                        "data" => [
                            "message" => "Successfully!"
                        ],
                        "status" => ReturnCodes::SUCCESS
                    );
                } else {
                    return array(
                        "data" => [
                            "message" => "Something went wrong!"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                }

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function AutoCheckOutHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $all_attn_today = EmpAttendance::findAll(["date" => DaysUtil::todayDate(), "time_out" => null]);
            $a = array();

            foreach($all_attn_today as $attn) {
                $attn_id = $attn["attendance_id"];
                $attndence = EmpAttendance::findOne(["attendance_id" => $attn_id]);
                $attndence->time_out = "00:00:00";
                $attndence->save();
            }
            // return "yo";
            // will return nothing

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }

    public function LogoutHelper($inputJSON) {
        $input = json_decode($inputJSON, TRUE);

        try {
            $input = json_decode($inputJSON, TRUE);

            $emp_id = $input["emp_id"];
            $user_auth_token = $input["auth_token"];

            $employee = EmployeeMaster::findOne(["emp_id" => $emp_id, "auth_token" => $user_auth_token]);

            if(!empty($employee)) {

                $employee->auth_token = null;

                if($employee->save()) {
                    return array(
                        "data" => [
                            "message" => "Logout Successfully!"
                        ],
                        "status" => ReturnCodes::SUCCESS
                    );
                } else {
                    return array(
                        "data" => [
                            "message" => "Something went wrong!"
                        ],
                        "status" => ReturnCodes::FAILURE
                    );
                }

            } else {
                return array(
                    "data" => [
                            "message" => "Unauthorised request!"
                        ],
                        "status" => ReturnCodes::AUTHENTICATION_FAILED
                );
            }

        } catch (\Exception $ex) {
            return $status = ["status" => ReturnCodes::SYSTEM_ERROR, "data" => $ex->getMessage()];
        }
    }
}

