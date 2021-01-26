<?php
session_start();
include('../server/db.php');

$azione= $_GET['azione'];
$evento=  1;
$id_sondaggio= $_GET['id_sondaggio'];

if($azione=="risp_aperta"){
	$risposta_dom =$_GET['risposta_aperta'];
	$risposta_dom=str_replace("'","´",$risposta_dom);				
}

if($azione=="risp_multipla"){
	$risposta_dom =$_GET['risposta'];
}

$sql_insert_domanda="INSERT INTO `sondaggi_risposte`
					(`ID`, `s_ID_partecipante`,`s_ID_sondaggio`, `s_nominativo`, `s_risposta`, `s_evento`) VALUES 
					(NULL,'".$_COOKIE['utente_evento']."','$id_sondaggio','','$risposta_dom','$evento')";
	
mysqli_query($con,$sql_insert_domanda);

echo $sql_insert_domanda;
?>