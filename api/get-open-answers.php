<?php
session_start();
$_SESSION['lang']='it';
require_once("admin/config.php");

function data_ora_X_DB($data){
	$split=explode(" ", $data);
	$split1=explode("-", $split[0]);
	$split2=explode(":", $split[1]);
	$data_visualizzata=$split1[2].'-'.$split1[1].'-'.$split1[0].' '.$split2[0].':'.$split2[1];
	return $data_visualizzata;
}
function ora_X_DB($data){
	$split=explode(" ", $data);
	$split1=explode("-", $split[0]);
	$split2=explode(":", $split[1]);
	$data_visualizzata= $split2[0].':'.$split2[1];
	return $data_visualizzata;
}

$ultimi_id_domande_aperte= $_GET['ultimi_id_domande_aperte'];
$rif_evento= $_GET['rif_evento'];
$tab_utenti= $_GET['tab_utenti'];

//echo $ultimi_id_domande_aperte;

$array_ultime_risposte=explode("|",$ultimi_id_domande_aperte);
//print_r($array_ultime_risposte);
//echo "<br>";

$ultimi_id_domande_aperte_new="";
$ultime_risposte="";

for($i=0;$i<count($array_ultime_risposte);$i++){
	
	$dati_sondaggio=explode(",",$array_ultime_risposte[$i]);

	$sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select ID from sondaggi_risposte where s_ID_sondaggio='$dati_sondaggio[0]' order by s_data_risposta desc LIMIT 1"));
	$ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
	if(!isset($ultimo_id_inserito)){
		$ultimo_id_inserito=0;	
	}
	$ultimi_id_domande_aperte_new.= $dati_sondaggio[0]."***".$ultimo_id_inserito."***";
	
  	$sql_risposte="Select s_risposta,s_data_risposta from sondaggi_risposte where s_ID_sondaggio='$dati_sondaggio[0]' and ID>'$dati_sondaggio[1]' order by s_data_risposta desc";
	//$sql_risposte="Select * from sondaggi_risposte where s_ID_sondaggio='$dati_sondaggio[0]' and ID<'$dati_sondaggio[1]' order by s_data_risposta desc";
	$r_risposte= mysqli_query($con,$sql_risposte);
	
	while($risposte= mysqli_fetch_array($r_risposte)){
		//$ultimi_id_domande_aperte_new.="***";
		
		$ultimi_id_domande_aperte_new.="
			<div class='cont_risposta'>
				<div class='fontWeight700'><strong>".data_ora_X_DB($risposte['s_data_risposta'])."</strong></div>
				". nl2br($risposte['s_risposta'])."
			</div>		
		";
		
		$ultimi_id_domande_aperte_new.="$$$";	
	}
	$ultimi_id_domande_aperte_new=substr($ultimi_id_domande_aperte_new, 0, strlen($ultimi_id_domande_aperte_new)-3);
	$ultimi_id_domande_aperte_new.="|";
	
}

$ultimi_id_domande_aperte_new=substr($ultimi_id_domande_aperte_new, 0, strlen($ultimi_id_domande_aperte_new)-1);

echo $ultimi_id_domande_aperte_new;
?>
