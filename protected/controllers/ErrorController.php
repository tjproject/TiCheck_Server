<?php

class Error extends Controller
{
	function __construct($code, $param=NULL)
	{
		$message = NULL;
		switch ($code)
		{
		case 1:
			$message = "Succeed";
			break;
		case 2:
			$message = "邮箱已被使用";
			break;
		case 3:
			$message = "用户名/邮箱/密码格式错误";
			break;
		case 4:
			$param_message = NULL;
			if ($param != NULL)
			{
				if (is_array($param))
				{
					foreach($param as $pa)
					{
						$param_message = $param_message . "lack of " . $param . "; ";
					}
				}
				$param_message = "lack of ". $pa . "; ";
			}
			$message = "lack of param: " . $param_message;
			break;
		case 1:
			break;
		case 1:
			break;
		}
		throw new CHttpException($code, $message);
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}
