<?php
session_start();
require_once("../server/db.php");

$current_event_id= $_GET['current_event_id'];
$str_domande="";

$sql_domande="Select * from questions where event_id='".$current_event_id."' order by question_timestamp desc";
$and_questions=mysqli_query($con,$sql_domande);
while($questions=mysqli_fetch_array($and_questions)){
	$str_domande.= $questions['ID'].",".$questions['question_status']."|";
}

$str_domande=substr($str_domande, 0, strlen($str_domande)-1);

echo $str_domande;

?>