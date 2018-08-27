<?php

namespace app\models;

use yii;

class Mailer {
    const TYPE_PASS_RESET = 1;
    
    private static $renderFile;
    private static $renderParams = [];
    private static $from = ['sumansarkarwdo@gmail.com' => 'Suman Mailer'];
    private static $to;
    private static $subject;
    
    public static function validate($type, $model){
        switch ($type){
            case self::TYPE_PASS_RESET:
                if(empty($model->emp_id) || empty($model->emp_code) || empty($model->email_id) || empty($model->password_reset_token)){
                    return false;
                }
                self::$to = [$model->email_id];
                self::$subject = 'Password Reset';
                self::$renderFile = 'resetpassword';
                self::$renderParams = ['employee' => $model];
                break;
            
            case self::TYPE_PASSWORD_RESET:
                
                break;
            
            default:
                return false;
        }
        return true;
    }
    public static function send($type, $model){
        if(!self::validate($type, $model)){
            return false;
        }
        $message = \Yii::$app->mailer->compose(self::$renderFile, self::$renderParams);
        return $message->setFrom(self::$from)->setTo(self::$to)->setSubject(self::$subject)->send();
    }
}

