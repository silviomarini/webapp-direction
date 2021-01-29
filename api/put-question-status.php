<?php
session_start();
require_once("../server/db.php");

$rif_evento= $_GET['rif_evento'];
$str_domande="";

$sql_domande="Select * from domande where d_evento='".$rif_evento."' order by d_data_domanda desc";
$r_domande=mysqli_query($con,$sql_domande);
while($domande=mysqli_fetch_array($r_domande)){
	$str_domande.= $domande['ID'].",".$domande['d_stato']."|";
}

$str_domande=substr($str_domande, 0, strlen($str_domande)-1);

echo $str_domande;

?>