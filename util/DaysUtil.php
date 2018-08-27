<?php

namespace app\Util;

use app\models\HolidayMaster;

class DaysUtil {
    const SUNDAY = 'sunday';
    const MONDAY = 'monday';
    const TUESDAY = 'tuesday';
    const WEDNESDAY = 'wednesday';
    const THURSDAY = 'thursday';
    const FRIDAY = 'friday';
    const SATURDAY = 'saturday';
    
    public static function todayIs() {
        $time = time();
        $day = strtolower(date("l", $time));
        return $day;
    }
    public static function todayDate() {
        $time = time();
        $date = strtolower(date("o" . "-" . "m" . "-" . "d", $time));
        return $date;
    }
    public static function currentTime() {
        $time = time();
        return date("H:i:s");
    }
    public static function timestamp() {
        $time = time();
        return $day = date("Y-m-d H:i:s", $time);
    }
    
    public static function isLate($time) {
        if($time > self::currentTime()) {
            return false;
        } else {
            return true;
        }
    }
    
    public static function isTodayHoliday() {
        $today = self::todayDate();
        
        $holiday = HolidayMaster::findOne(['date' => $today]);
        
        if(empty($holiday)) {
            return false;
        } else {
            return true;
        }
    }
    
}

?>