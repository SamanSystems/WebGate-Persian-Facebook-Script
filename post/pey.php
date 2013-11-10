<?php
if(!isset($_COOKIE['userid'])){die;}
include('../core/session.php');
$userid = giveuserid($_COOKIE['userid']);
include('../core/config.php');
if(isset($_POST['type'],$_POST['value'],$_POST['price'])){
	if(!is_numeric($_POST['type']) or !is_numeric($_POST['value']) or !is_numeric($_POST['price'])){die;}
	$type = $_POST['type'];
	$value = $_POST['value'];
	$price = $_POST['price'];
	$db =db();
	if($type == 1){
		$esm = 'خرید امتیاز';
		if($value == 1000){
			if($price != 2500){die;}
		}
		elseif($value == 2500){
			if($price != 5700){die;}
		}
		elseif($value == 5000){
			if($price != 10000){die;}
		}
		elseif($value == 7000){
			if($price != 13000){die;}
		}
		elseif($value == 10000){
			if($price != 18000){die;}
		}
		else{die;}
	}
	elseif($type == 2){
		$esm = 'خرید درجه';
		if($value == 7){
			if($price != 8000){die;}
		}
		else{die;}
		$c = qselect("select group_id from user where id = $userid limit 1",$db);
		$c = $c['group_id'];
		$expire = time() + 2592000;
		$timedel = time() - 172800;
		$timenow = time();
		query("delete from factor_group where(time_stamp < $timedel and active = 0)",$db);
		//bargardoondan
		$qselectall = qselectall("select * from factor_group where expire < $timenow",$db);
		while($f = mysqli_fetch_array($qselectall)){
			$fuserid = $f['user_id'];
			$fgrp = $f['back_to'];
			$id = $f['id'];
			query("delete from factor_group where id = $id limit 1",$db);
			query("update user set group_id = $fgrp where id = $fuserid limit 1",$db);
		}
		query("insert into factor_group(user_id,expire,time_stamp,back_to) values($userid,$expire,$timenow,$c)",$db);
	}
	include('../core/peyment/zarinpal.php');
	$res = readypeyment($userid,$esm,$price,$type,$value,$db,$siteurl);
	$time3day = time() - 172800;
	query("delete from factor where(time_stamp < $time3day and status = 0)",$db);
	qclose($db);echo $res;
	//header('Location: https://www.zarinpal.com/users/pay_invoice/'.$res);
}