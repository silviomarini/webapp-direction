<?php
session_start();
include('../server/db.php');
$current_event_id= $_GET['current_event_id'];

$time_attuale= time();
$stati_sondaggi="";

$sql_sondaggi="Select ID,activation_date,disactivation_date,attiva from polls_master order by ordine";
$r_sondaggi= mysqli_query($con,$sql_sondaggi);

while($polls_master= mysqli_fetch_array($r_sondaggi)){
	$stati_sondaggi.=$polls_master['ID'].",";
	
	if($polls_master['attiva']==1 && $polls_master['disactivation_date']>=$time_attuale && $polls_master['activation_date']<$time_attuale){
		$stati_sondaggi.="<img src='../asset/images/active.png' width='25px' class='vertTop'> until ".date("H:i", $polls_master['disactivation_date']);
	}else{
		$stati_sondaggi.="<img src='../asset/images/inactive.png' width='25px' class='vertTop'>";
	}
	
	$stati_sondaggi.="|";	
	
}
$stati_sondaggi=substr($stati_sondaggi, 0, strlen($stati_sondaggi)-1);
echo $stati_sondaggi;

?>