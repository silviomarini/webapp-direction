<?php
session_start();
require_once("../server/db.php");

$azione= $_GET['azione'];
$current_event_id= $_GET['current_event_id'];
$id_domanda= $_GET['id_domanda'];
$stato_domanda= $_GET['stato_domanda'];

if($stato_domanda=="y"){
	$sql_stato="UPDATE questions set question_status='y' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="n"){
	$sql_stato="UPDATE questions set question_status='n' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="d"){
	$sql_stato="UPDATE questions set question_status='d' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="azzera"){
	$sql_stato="UPDATE questions set question_status='' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;

}



?>