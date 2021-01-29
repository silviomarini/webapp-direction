<?php
session_start();
$_SESSION['lang']='it';
require_once("admin/config.php");

$rif_evento= $_GET['rif_evento'];
$tab_utenti= $_GET['tab_utenti'];

$time_attuale= time();
$stati_sondaggi="";

$sql_sondaggi="Select ID,data_attivazione,data_disattivazione,attiva from sondaggi order by ordine";
$r_sondaggi= mysqli_query($con,$sql_sondaggi);

while($sondaggi= mysqli_fetch_array($r_sondaggi)){
	$stati_sondaggi.=$sondaggi['ID'].",";
	
	if($sondaggi['attiva']==1 && $sondaggi['data_disattivazione']>=$time_attuale && $sondaggi['data_attivazione']<$time_attuale){
		$stati_sondaggi.="<img src='./images/attiva.png' width='25px' class='vertTop'> until ".date("H:i", $sondaggi['data_disattivazione']);
	}else{
		$stati_sondaggi.="<img src='./images/non_attiva.png' width='25px' class='vertTop'>";
	}
	
	$stati_sondaggi.="|";	
	
}
$stati_sondaggi=substr($stati_sondaggi, 0, strlen($stati_sondaggi)-1);
echo $stati_sondaggi;

?>