<?php
session_start();
include('../server/db.php');

function data_formatDate($data){
	$split=explode(" ", $data);
	$split1=explode("-", $split[0]);
	$split2=explode(":", $split[1]);
	$data_visualizzata=$split1[2].'-'.$split1[1].'-'.$split1[0].' '.$split2[0].':'.$split2[1];
	return $data_visualizzata;
}
function formatDate($data){
	$split=explode(" ", $data);
	$split1=explode("-", $split[0]);
	$split2=explode(":", $split[1]);
	$data_visualizzata= $split2[0].':'.$split2[1];
	return $data_visualizzata;
}

$question_id= $_GET['question_id'];
$current_event_id= $_GET['current_event_id'];


$array_ultime_risposte=explode("|",$question_id);

$question_id_new="";
$ultime_risposte="";

for($i=0;$i<count($array_ultime_risposte);$i++){
	
	$polls_data=explode(",",$array_ultime_risposte[$i]);

	$sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select ID from polls_answers where polls_id='$polls_data[0]' order by answer_datetime desc LIMIT 1"));
	$ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
	if(!isset($ultimo_id_inserito)){
		$ultimo_id_inserito=0;	
	}
	$question_id_new.= $polls_data[0]."***".$ultimo_id_inserito."***";
	
  	$sql_risposte="Select poll_answer,answer_datetime from polls_answers where polls_id='$polls_data[0]' and ID>'$polls_data[1]' order by answer_datetime desc";
	$answers_res= mysqli_query($con,$sql_risposte);
	
	while($risposte= mysqli_fetch_array($answers_res)){
		
		$question_id_new.="
			<div class='cont_risposta'>
				<div class='fontWeight700'><strong>".data_formatDate($risposte['answer_datetime'])."</strong></div>
				". nl2br($risposte['poll_answer'])."
			</div>		
		";
		
		$question_id_new.="$$$";	
	}
	$question_id_new=substr($question_id_new, 0, strlen($question_id_new)-3);
	$question_id_new.="|";
	
}

$question_id_new=substr($question_id_new, 0, strlen($question_id_new)-1);

echo $question_id_new;
?>

