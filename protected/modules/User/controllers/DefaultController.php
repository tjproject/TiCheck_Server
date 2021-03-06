<?php
namespace User\controllers;
class DefaultController extends \Controller
{
	protected $tiUser;
	public function actionIndex()
	{
		//$this->render('index');
	}

	public function prepareUser()
	{
		if (!isset($_POST['User']))
		{
			new \Error(4, "User");
		}
		$user = json_decode($_POST['User']);
		//echo var_dump($user);

		if (property_exists($user, 'Password') && $user->Password!=NULL)
		{
			$tiUser = \TiUser::model()->findByAttributes(
				array('Email'=>$user->Email,'Password'=>$user->Password) 
			);
		}
		else
		{
			$tiUser = \TiUser::model()->findByAttributes(
				array('Email'=>$user->Email) 
			);
		}

		if ($tiUser)
			$this->tiUser = $tiUser;
		else
		{
			new \Error(6);
		}
	}

	/*
	public function actionCreate()
	{
		if (isset($_POST['User']))
		{
			$user = json_decode($_POST['User']);
			//echo var_dump($user);

			if (!$this->verifyInfo($user))
			{
				//echo "user ok";			
				return false;
			}
			$tiUser = new TiUser;

			foreach ($user as $name=>$value)
			{
				$tiUser->$name = $value;
			}

			echo $tiUser->Account;
			try
			{
				$tiUser->save();
			}
			catch(Exception $e)
			{
				echo "save to database wrong". $e->getMessage();
				return false;
			}

			echo "create TiUser record<br>";
			return true;
		}
		else
		{
			echo "no user posted";
			return false;
		}
	}

	// Utilities
	private function verifyEmail($email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			    echo "This ($email) email address is considered valid.<br>";
				return true;
		}
	}

	private function verifyAccount($account)
	{
		$pattern = "/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/";
		if (preg_match($pattern,$account))
		{
			echo "account ($account) ok <br>";
			return true;
		}
		else
		{
			echo "not valid account<br>";
		}
		
		return false;
	}

	private function verifyPassword($passwd)
	{
		$pattern = "/[\s|\S]{5,64}$/";
		if (preg_match($pattern,$passwd))
		{
			echo "length of ($passwd) ok <br>";
			return true;
		}
		else
		{
			echo "not valid passwd<br>";
		}
		
		return false;
	}


	private function verifyInfo($user)
	{
		$email = $user->Email;
		if (!$this->verifyAccount($user->Account))
			return false;
		if (!$this->verifyEmail($email))
			return false;
		if (!$this->verifyPassword($user->Password))
			return false;
		return true;
	}
	 */
}
