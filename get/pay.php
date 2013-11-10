<?php
if(!isset($_COOKIE['peymenthash'],$_COOKIE['peymentid'])){die;}
include('../core/config.php');
if($_COOKIE['peymentid'] == 'zarinpal'){
	$hash = $_COOKIE['peymenthash'];
	if(!is_numeric($hash)){die;}
	$peymentid = $_COOKIE['peymentid'];
	$db = db();
	$c = qselect("select * from factor where(id = $hash and peyment_id = '$peymentid' and status = 0) order by id desc limit 1",$db);
	include('../core/peyment/zarinpal.php');
	$Authority = $_GET['Authority'];
	if($_GET['Status'] != 'OK'){
		die('Peyment is NOT complete');
	}
	if(getpeymentstatus($c['id'],$Authority,$db) != true){
		die('Error! its not complete call admin !');
	}
	$ok = 1;
	$factorid = $c['id'];
}
if(isset($ok)){
	$db = db();
	$c = qselect("select * from factor where id = $factorid limit 1",$db);
	query("update factor set hash = 0,hash2 = 0,status = 1 where id = $factorid limit 1",$db);
	if($c['type'] == 1){
		$addpoint = $c['type_value'];
		include('../core/point.php');
		addpoint($c['user_id'],$addpoint,$db);
		qclose($db);
	}
	elseif($c['type'] == 2){
		$rank = $c['type_value'];
		$userid = $c['user_id'];
		query("update factor_group set active = 1 where user_id = $userid order by id desc limit 1",$db);
		query("update user set group_id = $rank where id = $userid limit 1",$db);
		
	}
	else{die;}
	header("location: $siteurl");
}