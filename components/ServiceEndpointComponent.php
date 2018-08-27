<?php

namespace app\components;

use Yii;
use yii\base\Component;

class ServiceEndpointComponent extends Component
{
	public $serviceHelper;
	public $return;


	function __construct()
	{
		$this->serviceHelper = new ServicehelperComponent();
		$this->return = array();
	}


    public function Login($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.Login with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LoginHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    public function CheckIn($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.CheckIn with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->CheckInHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    public function LateCheckIn($inputJSON) // added today
    {
        Yii::info('Inside ServiceEndpointComponent.LateCheckIn with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LateCheckInHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    public function CheckOut($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.CheckOut with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->CheckOutHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    public function LeaveApply($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveApply with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveApplyHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function LeaveCount($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveCount with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveCountHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function LeaveShow($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveShow with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveShowHelper($inputJSON);
        print_r(json_encode($this->return));

    }

    public function LeaveStatus($inputJSON) // added 02-08-2018
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveStatus with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveStatusHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function LeaveGrant($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveGrant with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveGrantHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function LeaveReject($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.LeaveReject with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LeaveRejectHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    public function OutOfCity($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.OutOfCity with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->OutOfCityHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function AutoCheckOut($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.AutoCheckOut with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->AutoCheckOutHelper($inputJSON);
        print_r(json_encode($this->return));

    }
    
    public function Logout($inputJSON)
    {
        Yii::info('Inside ServiceEndpointComponent.Logout with Input parameter' . $inputJSON . 'service');

        $this->return = $this->serviceHelper->LogoutHelper($inputJSON);
        print_r(json_encode($this->return));

    }
}