<?php
/* @var $this SiteController */
/* @var $error array */

//$this->pageTitle=Yii::app()->name . ' - Error';
//$this->breadcrumbs=array(
	//'Error',
//);
$error = urldecode(json_encode(array(
	'Code'=>$code, 
	'Message'=>urlencode($message
))));
//Error echo $code; 
//echo CHtml::encode($message); 
echo $error;

?>
