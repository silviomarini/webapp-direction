<?php
session_start();
include('../server/db.php');
$poll_closed= $_GET['poll_closed'];

$sond_risp_multipla=explode("|",$poll_closed);


$polls_counters="";
for($y=0;$y<count($sond_risp_multipla);$y++){
	$sondaggio= explode("-",$sond_risp_multipla[$y]);
	
	$polls_counters.=$sondaggio[0]."_";
	
	$polls_answers= explode(",",$sondaggio[1]);
	
	for($j=0;$j<count($polls_answers);$j++){
		
		$tot_risp_multiple=mysqli_num_rows(mysqli_query($con,"select ID from polls_answers where polls_id='$sondaggio[0]'"));
		$cont_risp=mysqli_num_rows(mysqli_query($con,"Select ID from polls_answers where polls_id='$sondaggio[0]' and poll_answer='risposta_$polls_answers[$j]'"));
		
		if($cont_risp>0){		
			$perc_risp=(100*$cont_risp)/$tot_risp_multiple;	
		}else{
			$perc_risp=0;	
		}
		$perc_risp= number_format($perc_risp,2,'.','');
		
		$polls_counters.=$polls_answers[$j]."-".$cont_risp." (".$perc_risp."%),";		
	}
	$polls_counters=substr($polls_counters, 0, strlen($polls_counters)-1);
	$polls_counters.="|";
	
}

$polls_counters=substr($polls_counters, 0, strlen($polls_counters)-1);
echo $polls_counters;

?>