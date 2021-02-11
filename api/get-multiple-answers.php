<?php
session_start();
$_SESSION['lang']='it';
require_once("admin/config.php");

$risposte_multiple= $_GET['risposte_multiple'];

$sond_risp_multipla=explode("|",$risposte_multiple);


$contatori_risposte="";
for($y=0;$y<count($sond_risp_multipla);$y++){
	$sondaggio= explode("-",$sond_risp_multipla[$y]);
	
	$contatori_risposte.=$sondaggio[0]."_";
	
	$risp_sondaggio= explode(",",$sondaggio[1]);
	
	for($j=0;$j<count($risp_sondaggio);$j++){

		$tot_risp_multiple=mysqli_num_rows(mysqli_query($con,"select ID from sondaggi_risposte where s_ID_sondaggio='$sondaggio[0]'"));
		$cont_risp=mysqli_num_rows(mysqli_query($con,"Select ID from sondaggi_risposte where s_ID_sondaggio='$sondaggio[0]' and s_risposta='risposta_$risp_sondaggio[$j]'"));
		
		if($cont_risp>0){		
			$perc_risp=(100*$cont_risp)/$tot_risp_multiple;	
		}else{
			$perc_risp=0;	
		}
		$perc_risp= number_format($perc_risp,2,'.','');
		
		$contatori_risposte.=$risp_sondaggio[$j]."-".$cont_risp." (".$perc_risp."%),";		
	}
	$contatori_risposte=substr($contatori_risposte, 0, strlen($contatori_risposte)-1);
	$contatori_risposte.="|";
	
}

$contatori_risposte=substr($contatori_risposte, 0, strlen($contatori_risposte)-1);
echo $contatori_risposte;

?>