<?php
$MerchantID = '4fbcc844-edd0-4c46-b027-0f445ee8aeb5';
function readypeyment($userid,$name,$price,$type,$typevalue,$db,$siteurl){
	$peymentid = 'zarinpal';
	global $MerchantID;
	$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
	
	$result = $client->PaymentRequest(
						array(
								'MerchantID' 	=> $MerchantID,
								'Amount' 	=> $price,
								'Description' 	=> "$name",
								'Email' 	=> '',
								'Mobile' 	=> '',
								'CallbackURL' 	=> $siteurl.'get/pay.php'
							)
	);
	$timenow = time();
	query("insert into factor(user_id,type,type_value,peyment_id,price,hash,name,time_stamp) values($userid,$type,$typevalue,'$peymentid',$price,'$res','$name',$timenow)",$db);
	$c = qselect("select id from factor where user_id = $userid order by id desc limit 1",$db);
	ob_start();
	setcookie('peymenthash',$c['id'],time() + 3600,'/');
	setcookie('peymentid',$peymentid,time() + 3600,'/');
	return $result->Authority;
	ob_end_flush();
}
function getpeymentstatus($factorid,$au,$db){
	$c = qselect("select * from factor where id = $factorid limit 1",$db);
	$price = $c['price'];
	global $MerchantID;
	$client = new SoapClient('https://de.zarinpal.com/pg/services/WebGate/wsdl', array('encoding' => 'UTF-8')); 
	$result = $client->PaymentVerification(
						  	array(
									'MerchantID'	 => $MerchantID,
									'Authority' 	 => $au,
									'Amount'	 => $price
								)
		);
	
	if($result->Status == 100){
		return true;
	}
	else{
		return false;
	}
}