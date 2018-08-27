<?php

namespace app\Util;


class LeavesType
{
    const paid = 1;
    const casual = 2;
    const sick = 3;
    const other = 4;
    
    public static function getLeaveType($type) {
        switch ($type) {
            case 'paid':
                return 1;
                break;
            case 'casual':
                return 2;
                break;
            case 'sick':
                return 3;
                break;
            case 'other':
                return 4;
                break;
            default:
                return 'Invalid leave type';
        }
    }
    
    public static function getLeaveTypeFromValue($type) {
        switch ($type) {
            case 1:
                return 'paid';
                break;
            case 2:
                return 'casual';
                break;
            case 3:
                return 'sick';
                break;
            case 4:
                return 'other';
                break;
            default:
                return 'Invalid leave type';
        }
    }
    
}

?>