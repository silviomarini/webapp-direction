<?php
session_start();
require_once("../server/db.php");

$azione= $_GET['azione'];
$rif_evento= $_GET['rif_evento'];
$id_domanda= $_GET['id_domanda'];
$stato_domanda= $_GET['stato_domanda'];

if($stato_domanda=="y"){
	$sql_stato="UPDATE domande set d_stato='y' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="n"){
	$sql_stato="UPDATE domande set d_stato='n' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="d"){
	$sql_stato="UPDATE domande set d_stato='d' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;
}

if($stato_domanda=="azzera"){
	$sql_stato="UPDATE domande set d_stato='' where ID='$id_domanda'";
	mysqli_query($con,$sql_stato);
	echo $sql_stato;

}



?>