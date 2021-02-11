<?php
session_start();
require_once("../server/db.php");


$ID_domanda= $_GET['val_sel'];
$stato= $_GET['stato'];

$domanda=mysqli_fetch_array(mysqli_query($con,"Select * from sondaggi where ID='$ID_domanda'"));
$durata= $domanda['durata'];

$sql_disattiva="UPDATE sondaggi set attiva=''";
mysqli_query($con,$sql_disattiva);


if($stato=="1"){
	$data_attivazione=time();
	$data_disattivazione=time()+($durata);
	
	if($stato=="1"){

		$sql_update="UPDATE sondaggi set attiva='$stato', data_attivazione='$data_attivazione', data_disattivazione='$data_disattivazione' where ID='$ID_domanda'";
		mysqli_query($con,$sql_update);
	}
}

?>