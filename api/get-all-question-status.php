<?php
session_start();
$_SESSION['lang']='it';
require_once("admin/config.php");

$rif_evento= $_GET['rif_evento'];
$tab_utenti= $_GET['tab_utenti'];

$time_attuale= time();
$stati_sondaggi="";

$sql_sondaggi="Select ID,activation_date,disactivation_date,attiva from polls_master order by ordine";
$r_sondaggi= mysqli_query($con,$sql_sondaggi);

while($polls_master= mysqli_fetch_array($r_sondaggi)){
	$stati_sondaggi.=$polls_master['ID'].",";
	
	if($polls_master['attiva']==1 && $polls_master['disactivation_date']>=$time_attuale && $polls_master['activation_date']<$time_attuale){
		$stati_sondaggi.="<img src='./images/attiva.png' width='25px' class='vertTop'> until ".date("H:i", $polls_master['disactivation_date']);
	}else{
		$stati_sondaggi.="<img src='./images/non_attiva.png' width='25px' class='vertTop'>";
	}
	
	$stati_sondaggi.="|";	
	
}
$stati_sondaggi=substr($stati_sondaggi, 0, strlen($stati_sondaggi)-1);
echo $stati_sondaggi;

?>