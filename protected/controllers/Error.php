<?php

class Error extends Controller
{
	function __construct($code, $param=NULL, $msg = NULL, $data=NULL)
	{
		$message = NULL;
		switch ($code)
		{
		case 1:
			$message = "Succeed";
			if ($data !=NULL )
				$message = json_encode($data);
			break;
		case 2:
			$message = "邮箱已被使用";
			break;
		case 3:
			$message = $msg . " format error";
			break;
		case 4:
			$param_message = NULL;
			if ($param != NULL)
			{
				if (is_array($param))
				{
					foreach($param as $pa)
					{
						$param_message = $param_message . $pa . "; ";
					}
				}
				$param_message = $param . "; ";
			}
			$message = "lack of param: " . $param_message;
			break;
		case 5:
			$message = $msg;
			break;
		case 6:
			$message = "user not exist";
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
