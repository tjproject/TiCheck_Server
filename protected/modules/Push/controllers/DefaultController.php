<?php
//include_once ('/Library/WebServer/Documents/TiCheck_Server/SDK.config.php');
//include_once (Yii::app()->basePath . '/../SDK.config.php');
//include_once (ABSPATH.'sdk/API/Flight/D_FlightSearch.php');

class DefaultController extends Controller
{
	private $_deviceToken;
	private $_message;
//	private $_flight;
	private $_date;
	private $_price=NULL;

	public function actionIndex()
	{
		// push
		// Put your device token here (without spaces):
		$deviceToken = $this->_deviceToken;

		// Put your private key's passphrase here:
		$passphrase = 'TiCheck';

		// Put your alert message here:
		$message = $this->_message;

		////////////////////////////////////////////////////////////////////////////////

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', '/Library/WebServer/Documents/TiCheck_Server/ck.pem');
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server
		$apns_con = stream_socket_client(
			'ssl://gateway.sandbox.push.apple.com:2195', $err,
			$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		var_dump($apns_con);

		if (!$apns_con)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		echo 'Connected to APNS' . PHP_EOL;

		// Create the payload body
		$body['aps'] = array(
			'alert' => urlencode($message),
			'sound' => 'default'
			);

		// Encode the payload as JSON
		$payload = urldecode(json_encode($body));

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($apns_con, $msg, strlen($msg));

		if (!$result)
			echo 'Message not delivered' . PHP_EOL;
		else
			echo 'Message successfully delivered' . PHP_EOL;
		var_dump($result);

		// Close the connection to the server
		fclose($apns_con);
	}


	public function actionSearch()
	{
		//echo dirname(__FILE__);
		set_time_limit(0);
		while(1)
		{
			$array_subs = Subscription::model()->with('userSubscriptions')->findALl();
			var_dump($array_subs);
			foreach ($array_subs as $tiSubs)
			{
				//echo var_dump($tiSubs);
				//echo "xxxxxxxxxxxxxxxx<br>";
				$lowestPrice = new D_LowestPrice;
				$this->_price = $lowestPrice->searchFlight($tiSubs);
				$this->_date = $lowestPrice->date;

				$this->createHistoryPrice($tiSubs, (int)$this->_price);

				$isModified = $this->modifySubscription($tiSubs, (int)$this->_price);

				if ($isModified)
				{
					$array_user_subs = $tiSubs->userSubscriptions;
					foreach ($array_user_subs as $user_tiSubs)
					{
						$tiUser = $user_tiSubs->iDUser;
						if (!$tiUser->Pushable)
							continue;
						if ($this->_price < $user_tiSubs->PriceLimit || $user_tiSubs->PriceLimit == NULL)
						{
							$user_devices = $tiUser->userDevices;
							foreach ($user_devices as $user_device)
							{
								$this->_deviceToken = $user_device->Device_token;
								//$this->_deviceToken = "70a10324b2a2e4e6daaa8eee74a30c8bb196db31be43043cc94cb149d117aeb7";
								//$this->_message = "asdf";
								$this->_message = "您订阅的{$tiSubs->DepartCity}至{$tiSubs->ArriveCity}价格已更新至{$this->_price}";
								$this->actionIndex();
							}
						}
					}
				}
			}
			sleep(300);
		}
	}
	
	/*
	private function searchFlight(Subscription $subs)
	{
		$date = new DateTime($subs->StartDate);
		$endDate = $subs->EndDate;
		while ($date->format('Y-m-d') != $endDate)
		{
			$D_FlightSearch=new get_D_FLightSearch();
			$D_FlightSearch->DepartCity=$subs->DepartCity;
			$D_FlightSearch->ArriveCity=$subs->ArriveCity;
			$D_FlightSearch->DepartDate=$date->format('Y-m-d');
			$D_FlightSearch->EarliestDepartTime=$subs->EarliestDepartTime;
			$D_FlightSearch->LatestDepartTime=$subs->LatestDepartTime;
			$D_FlightSearch->AirlineDibitCode=$subs->AirlineDibitCode;
			$D_FlightSearch->IsLowestPrice="true";
			$D_FlightSearch->OrderBy="Price";
			$D_FlightSearch->main();
			$returnXML=$D_FlightSearch->ResponseXML;//返回的数据是一个XML
			//可以将返回的数据直接用json转换一下，打印出来，方便查看节点名称和数据
			//echo  json_encode($returnXML);
			//echo $returnXML->DomesticFlightData;
			//echo json_encode($returnXML->FlightSearchResponse->FlightRoutes->DomesticFlightRoute->RecordCount);
			//var_dump($returnXML);
			$flights = $returnXML->FlightSearchResponse->FlightRoutes->DomesticFlightRoute->FlightsList->DomesticFlightData;
			
			if ($this->_price > $flights[0]->Price || $this->_price==NULL)
			{
				$this->_price = $flights[0]->Price;	
				$this->_date = $date;
			}
			$date->add(new DateInterval('P1D'));
		}
		//echo json_encode($flights);
	}
	 */

	private function createHistoryPrice(Subscription $subs, $price)
	{
		$date = getDateYMD("-");
		$history_price = HistoryPrice::model()->findByAttributes(array(
			'ID_subscription'=>$subs->ID,
			'Date'=>$date
		));
		if ($history_price==NULL || $history_price->count()==0)
		{
			$history_price = new HistoryPrice;
			$history_price->ID_subscription = $subs->ID;
			$history_price->Price = $price;
			$history_price->Date = $date;
			if (!$history_price->save())
				throw new CDbException("update old history_price fail");
		}
		else
		{
			var_dump($history_price);
			return;
			$history_price = $history_price[0];
			$history_price->Price = ($price < $history_price->Price)?$price:$history_price;
			if (!$history_price->save())
				throw new CDbException("save new history_price fail");
		}
	}


	private function modifySubscription(Subscription $subs, $price)
	{
		$old_price = (int)$subs->CurrentPrice;

		if ($price != $old_price)
		{
			$subs->CurrentPrice = $price;
			if (!$subs->save())
			{
				throw new CDbException("save new subs failed");
			}
			return true;
		}
		return false;
	}

}
