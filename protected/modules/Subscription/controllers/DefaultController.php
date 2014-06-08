<?php
namespace Subscription\controllers;
class DefaultController extends \User\controllers\DefaultController
{

	protected $_subs;
	protected $_user_subs;
	public function actionIndex()
	{
		$this->render('index');
	}

	protected function prepareUserSubscription()
	{
		$user_subs = new \UserSubscription;
		$user_subs->ID_user = $this->tiUser->ID;
		$user_subs->ID_subscription = $this->_subs->ID;
		$user_subs_adp = $user_subs->search();
		if ($user_subs_adp->itemCount)
		{
			$this->_user_subs = $user_subs_adp->getData()[0];
			return;
		}
	}

	protected function prepareSubscription()
	{
		if (!isset($_POST['Subscription']))
			new \Error(4, 'Subscription');
		$subs = json_decode($_POST['Subscription'], true);
		//var_dump($subs);

		if ($subs['DepartCity'] == NULL ||
			$subs['ArriveCity'] == NULL ||
			$subs['StartDate'] == NULL ||
			$subs['EndDate'] == NULL)
			new \Error(4, array('DepartCity', 'ArriveCity', 'StartDate', 'EndDate'));

		$tiSubs = new \Subscription;
		$tiSubs->attributes = $subs;
		$subs_adp = $tiSubs->search();
		if ($subs_adp->itemCount)
		{
			$this->_subs = $subs_adp->getData()[0];
		}
		else
		{
			/*
			$lowestPrice = new \D_LowestPrice;
			$tiSubs->CurrentPrice = (int)$lowestPrice->searchFlight($tiSubs);
			 */
			try
			{
				if (!$tiSubs->save())
				{
					new \Error(5, null, json_encode($tiSubs->getErrors()));
				}
			}
			catch(Exception $e)
			{
				new \Error(5, NULL, $e->getMessage());
			}
			$this->_subs = $tiSubs;
		}
	}

	/*
	protected function prepareUser()
	{
		
		$user = json_decode($_POST['User'], true);
		if ($user['Email'] == NULL &&
			$user['Account'] == NULL)
		{
			throw new CDException("not enough data");
		}
		$tiUser = new \TiUser;
		$tiUser->attributes = $user;
		$user_adp = $tiUser->search(false);
		if (!$user_adp->itemCount)
		{
			throw new CDException("user info. error");
		}
		$this->tiUser = $user_adp->getData()[0];
	}
	 */
}
