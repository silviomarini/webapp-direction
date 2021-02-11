<?php
session_start();
require_once("../server/db.php");

$rif_evento= $_GET['rif_evento'];
$str_domande="";

$sql_domande="Select * from questions where event_id='".$rif_evento."' order by question_timestamp desc";
$r_domande=mysqli_query($con,$sql_domande);
while($questions=mysqli_fetch_array($r_domande)){
	$str_domande.= $questions['ID'].",".$questions['question_status']."|";
}

$str_domande=substr($str_domande, 0, strlen($str_domande)-1);

echo $str_domande;

?>