<?php

namespace app\controllers;

use Yii;
use yii\filters\Cors;

class ServiceController extends \yii\web\Controller
{
    public function behaviors()
    {
        return array_merge([
            'cors' => [
                'class' => Cors::className(),
                #special rules for particular action
                'actions' => [
                    'index' => [
                        #web-servers which you alllow cross-domain access
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => ['POST'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Allow-Credentials' => null,
                        'Access-Control-Max-Age' => 86400,
                        'Access-Control-Expose-Headers' => [],
                    ],
                    'call' => [
                        #web-servers which you alllow cross-domain access
                        'Origin' => ['*'],
                        'Access-Control-Request-Method' => ['POST'],
                        'Access-Control-Request-Headers' => ['*'],
                        'Access-Control-Allow-Credentials' => null,
                        'Access-Control-Max-Age' => 86400,
                        'Access-Control-Expose-Headers' => [],
                    ],
                ],
                #common rules
                'cors' => [
                    'Origin' => [],
                    'Access-Control-Request-Method' => [],
                    'Access-Control-Request-Headers' => [],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 0,
                    'Access-Control-Expose-Headers' => [],
                ]
            ],
        ], parent::behaviors());
    }

    public function beforeAction($action)
    {

        if ($action->id == 'index') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    public function actionIndex()
    {
        Yii::info('Inside ServiceController.actionCall', 'service');
        header('Content-type: application/json');
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);

        switch ($input['action']) {
            case 'Login':
                Yii::$app->serviceendpointcomp->Login($inputJSON);
                break;
            case 'CheckIn':
                Yii::$app->serviceendpointcomp->CheckIn($inputJSON);
                break;
            case 'LateCheckIn': // added today
                Yii::$app->serviceendpointcomp->LateCheckIn($inputJSON);
                break;
            case 'CheckOut':
                Yii::$app->serviceendpointcomp->CheckOut($inputJSON);
                break;
            case 'LeaveApply':
                Yii::$app->serviceendpointcomp->LeaveApply($inputJSON);
                break;
            case 'LeaveCount':
                Yii::$app->serviceendpointcomp->LeaveCount($inputJSON);
                break;
            case 'LeaveShow':
                Yii::$app->serviceendpointcomp->LeaveShow($inputJSON);
                break;
            case 'LeaveStatus':
                Yii::$app->serviceendpointcomp->LeaveStatus($inputJSON); // added 02-08-2018
                break;
            case 'LeaveGrant':
                Yii::$app->serviceendpointcomp->LeaveGrant($inputJSON);
                break;
            case 'LeaveReject':
                Yii::$app->serviceendpointcomp->LeaveReject($inputJSON);
                break;
            case 'OutOfCity':
                Yii::$app->serviceendpointcomp->OutOfCity($inputJSON);
                break;
            case 'AutoCheckOut':
                Yii::$app->serviceendpointcomp->AutoCheckOut($inputJSON);
                break;
            case 'Logout':
                Yii::$app->serviceendpointcomp->Logout($inputJSON);
                break;
            default:
                echo json_encode('Action Not Found');
        }

    }

}
