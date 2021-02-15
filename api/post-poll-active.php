<?php
session_start();
require_once("../server/db.php");


$ID_domanda= $_GET['val_sel'];
$status= $_GET['stato'];

$domanda=mysqli_fetch_array(mysqli_query($con,"Select * from polls_master where ID='$ID_domanda'"));
$durata= $domanda['durata'];

$sql_disattiva="UPDATE polls_master set attiva=''";
mysqli_query($con,$sql_disattiva);


if($status=="1"){
	$activation_date=time();
	$disactivation_date=time()+($durata);
	
	if($status=="1"){
	
		$sql_update="UPDATE polls_master set attiva='$status', activation_date='$activation_date', disactivation_date='$disactivation_date' where ID='$ID_domanda'";
		mysqli_query($con,$sql_update);
	}
}

?>