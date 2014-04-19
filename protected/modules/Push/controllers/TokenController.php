<?php
Yii::import('webroot.protected.modules.Push.default');
class TokenController extends Controller
{
	private $_user;
	//private $_device_token;

	public function actionCreate()
	{
		if (!(isset($_POST['User']) && isset($_POST['DeviceToken'])))
			throw new CException("param not enough");
		$c_default = new DefaultController();
		$c_default->actionIndex();
		return;

		// user
		$this->prepareUser();
		// device
		$this->prepareDeviceToken();


		//$this->render('create');
	}

	private function prepareUser()
	{
		$user = json_decode($_POST['User'], true);
		if ($user['Email'] == NULL &&
			$user['Account'] == NULL)
		{
			throw new CException("not enough data");
		}
		$tiUser = new TiUser;
		$tiUser->attributes = $user;
		$user_adp = $tiUser->search(false);
		if (!$user_adp->itemCount)
		{
			throw new CException("user info. error");
		}
		$this->_user = $user_adp->getData()[0];
	}

	private function prepareDeviceToken()
	{
		$token = json_decode($_POST['DeviceToken'], true);
		$tiUser_Device = new UserDevice;
		$tiUser_Device->Device_token = $token;
		$tiUser_Device->ID_user = $this->_user->ID;
		$tiUser_Device->save();
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
