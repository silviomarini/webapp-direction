<?php
session_start();
include('../server/db.php');
$current_event_id= $_GET['current_event_id'];

$time_attuale= time();
$polls_status="";

$sql_sondaggi="Select ID,activation_date,disactivation_date,attiva from polls_master order by ordine";
$polls_ans= mysqli_query($con,$sql_sondaggi);

while($polls_master= mysqli_fetch_array($polls_ans)){
	$polls_status.=$polls_master['ID'].",";
	
	if($polls_master['attiva']==1 && $polls_master['disactivation_date']>=$time_attuale && $polls_master['activation_date']<$time_attuale){
		$polls_status.="<img src='../asset/images/active.png' width='25px'> until ".date("H:i", $polls_master['disactivation_date']);
	}else{
		$polls_status.="<img src='../asset/images/inactive.png' width='25px'>";
	}
	
	$polls_status.="|";	
	
}
$polls_status=substr($polls_status, 0, strlen($polls_status)-1);
echo $polls_status;

?>