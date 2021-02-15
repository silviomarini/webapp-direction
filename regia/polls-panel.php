<?php 
session_start();
require_once("../server/db.php");
require_once("../server/helper.php");

$tabella_utenti="utenti";

$sessionId = $_COOKIE["session_id"];

$autorizzazione = $_SESSION['autorizzato_regia'];

$autorizzato = false;
if ($autorizzazione != "autorizzato_regia") {
	echo '<script language=javascript>document.location.href="login.php?unauthorized"</script>'; 
} else {
	$autorizzato = true;
	$ID_EVENTO=$_SESSION['id_evento'];
}

$tabella='polls_master';

$file_script='polls-panel.php';
$ArticoliPagina = 1000;

$campi_req = array("domanda","durata","tipo");

if(@$_REQUEST['azione']!="aggiorna" && !isset($_GET['aggiungi_nuovo']) && !isset($_GET['aggiornanuovo'])){$page = $_SERVER['PHP_SELF'];$sec = "5";header("Refresh: $sec; url=$page");}

require_once("../server/db.php");

$time_attuale= time();
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	
	<script src="../asset/js/jquery.js"></script>

    <link rel="stylesheet" href="../asset/css/style.css" type="text/css" />
    <link rel="stylesheet" href="../asset/css/font-awesome.css" />
	
	<title>Privilege Web App</title>

	<script src="../asset/charts/dist/Chart.js" ></script>
	<script src="../asset/charts/dist/Chart.min.js"></script>
	<script src="../asset/charts/samples/utils.js"></script>

	<style>
        td {
            padding:8px !important;
        }

        td a {
            color: black;
        }
        
        input#domanda , .campoInput{
            margin: 5px;
            padding: 4px;
        }

        button.mb-xs.mt-xs.mr-xs.btn.btn.btn-success {
            padding: 4px;
        }

        .testo11Rosso {
            font-size: smaller;
            color: red;
        }

        .reduce {
            height: auto;
            font-size: small;
            border-color: grey;
        }

        .ris_cont {
            margin-top: 15px;
        }

        .ris_button_plus {
            margin-top: 20px;
            padding: 5px;
            font-size: larger;
            color: darkgreen;
            border: 1px solid darkgreen;
            text-align: center;
            width: 35%;
            margin-left: auto;
            margin-right: auto;
        }
    </STYLE>
</head>

<body>
	<div id="wrapper">
        
    <div class="header" style="z-index: -1;">
        <div class="bg"></div>
        <div class="logo">
            <?php if(false){ ?>
                <a href="index.php">
                    <img src="images/back-arrow-white.png" width="40px" />     
                </a>
            <?php } ?>
        </div>
        
        <?php
            $evento = mysqli_fetch_array(mysqli_query($con,"Select * from streamings order by ID DESC LIMIT 1 "));
            $titolo = "Privilege Web App";
            if($evento != null){
                $titolo = $evento['nome'];
            }
            $cover = $evento["cover"];
        ?>


        <h1 class="title" style="margin-left: 20px;">
            <?php echo $titolo; ?>
        </h1>
        <div class="log">
            <?php if($page_type == "regia"){ ?>
                <a href="logout.php">
                    <p class="text">Logout</p>   
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if($cover == "") { $cover = "cover1608542135.jpeg"; }
        else {
            if (!file_exists("asset/event-covers/".$cover)) {
                $cover = "cover1608542135.jpeg";
            }

        }
     ?>
    <style>
        .header .bg {
            width: 100%;
            height: 100%;
            background-image: url("<?php echo "../asset/event-covers/".$cover; ?>") !important;
            background-size: cover;
        }
    </style>

		<div class="about">
            <div class="menu" >
				 <span class="menu "> <a href="index.php"> Questions </a> </span> 
				 <span class="menu active"> <a href="polls.php"> Polls </a> </span> 
            </div>
            <div class="body" style="min-height:165px">  
            <?php 
            if($autorizzato){ 

                
                if (!isset($_REQUEST['cke'])){$_REQUEST['cke']=null;}
                    switch ($_REQUEST['cke'])
                    {
                    case "":
                        if(isset($_GET['azione']) && $_GET['azione']=='del'){
  
                            $sql="DELETE FROM $tabella WHERE ID = '$_REQUEST[ID]'";
  
                            mysqli_query ($con,$sql); 
                            
                            $sql_del_risp="DELETE FROM polls_answers WHERE polls_id = '$_REQUEST[ID]'";
                            mysqli_query ($con,$sql_del_risp); 			
                            
                     
                            $esito_cancellazione=1;
                        }

                        if(isset($_GET['azione']) && $_GET['azione']=='delRisposte'){
                            $sql_del_risp="DELETE FROM polls_answers WHERE polls_id = '$_REQUEST[ID]'";
                            mysqli_query ($con,$sql_del_risp); 	 
                            
                            $esito_cancellazione=2;
                        }		 
                        
                        
                        if(isset($_GET['azione']) && $_GET['azione']=='ordina'){
                            $sqlG=NULL;

                         
                            if($_GET['ordine']>$_GET['nuovo']){
                                
                           
                                $sql1="UPDATE $tabella SET ordine = '$_GET[nuovo]' WHERE ID = '$_GET[ID_documento]'";
                                mysqli_query($con,$sql1);
                              
                                $sql="SELECT * FROM $tabella  WHERE ID != '$_GET[ID_documento]' and ordine >= '$_GET[nuovo]' order by ordine ASC";
                       
                                $ordine=$_GET['nuovo']+1;
                                $r_result=mysqli_query($con,$sql);
                                while($N=mysqli_fetch_array($r_result)){
                       
                                    $sql1="UPDATE $tabella SET `ordine` = '$ordine' WHERE ID = '$N[ID]' LIMIT 1 ";
                             
                                    mysqli_query($con,$sql1);
                                    $ordine++;
                                }
                            }
                            
                            if($_GET['ordine']<$_GET['nuovo']){
                                $sql1="UPDATE $tabella SET ordine = '$_GET[nuovo]' WHERE ID = '$_GET[ID_documento]'";
                                mysqli_query($con,$sql1);
                                
                                $sql="SELECT * FROM $tabella  WHERE ID != '$_GET[ID_documento]' and ordine <= '$_GET[nuovo]' order by ordine DESC";
                                
                                $ordine=$_GET['nuovo']-1;
                                $r_result=mysqli_query($con,$sql);
                                while($N=mysqli_fetch_array($r_result)){
                                    $sql1="UPDATE $tabella SET `ordine` = '$ordine' WHERE ID = '$N[ID]' LIMIT 1 ";
                                    mysqli_query($con,$sql1);
                                    $ordine--;
                                }
                            }
                            
                        }		 
                    break;
                    case "1":

                        if(isset($_REQUEST['azione']) && $_REQUEST['azione']=='nuovo'){
                            if($_POST['domanda']==''){
                                header("location: $file_script?noitem"); exit();
                            }
                        }
                        if(isset($_REQUEST['azione']) && $_REQUEST['azione']=='agg'){
                            $control_campi=0;

                          
                            
                                foreach($_POST as $key => $valore){
                                        $valore=str_replace("'","´",$valore);
                                        $valore = stripslashes(trim($valore));
                                     
                                        $_POST[$key]=$valore;
                                        
                                        
                                        if($key=='durata'){
                                            if($_POST[$key]==0){ 
                                                @$erro_msg .="";
                                                $_POST[$key]=''; 
                                                @$control_campi++;
                                                @$msg_errore[$key] .="<span style=\"font-weight:400;font-size:1em;padding-left:5px;\">Time not valid</span>";
                                            }
                                        }						
                                        
                                        
                                    if(in_array($key,$campi_req)){
                                        $key = ucfirst(stripslashes(trim($key)));
                                        $valore = stripslashes(trim($valore));
                                        $key = str_replace("_"," ",$key);
                                        if(trim($valore) == ""){ 
                                            $valore = "<span class=\"allert\">Not complete! </span>";
                                            @$erro_msg .= $key .": &nbsp;&nbsp;" .$valore."<br>";
                                            @$msg_errore[$key2] = "" .$valore." ";
                                            @$class_errore[$key2] = ' class="erroreform"';
                                            @$control_campi++;
                                        }	
                                    } 
                                } 
                        }
                     
                                
                        if(isset($control_campi) && $control_campi==0 && isset($_POST['azione'])){
                                $query="UPDATE $tabella SET
                                        domanda = '".@$_POST['domanda']."',
                                        tipo ='".@$_POST['tipo']."',
                                        durata = '".@$_POST['durata']."',
                                        answer_1 = '".@$_POST['answer_1']."',
                                        answer_2 = '".@$_POST['answer_2']."',
                                        answer_3 = '".@$_POST['answer_3']."',
                                        answer_4 = '".@$_POST['answer_4']."',
                                        answer_5 = '".@$_POST['answer_5']."',
                                        answer_6 = '".@$_POST['answer_6']."',
                                        answer_7 = '".@$_POST['answer_7']."',
                                        answer_8 = '".@$_POST['answer_8']."',
                                        answer_9 = '".@$_POST['answer_9']."',
                                        answer_10 = '".@$_POST['answer_10']."'
                                        WHERE ID = '$_POST[ID]'";
                            
                 
                            mysqli_query($con,$query) or die (mysqli_error($con));
                            
                        
                            header("location: $_SERVER[PHP_SELF]"); exit;
                        }
                        
                    break;
                    }
                
                
                
                ?>
                <div class="row">
        
                <div class="col-md-12 bg">
                    <div class="row">
						
						<?php
                            
                            if(!isset($_REQUEST['azione'])) $_REQUEST['azione']=null;
                            if(isset($_REQUEST['azione']) && $_REQUEST['azione']=='nuovo'){
                                
                                $domanda = htmlspecialchars(stripslashes(trim(str_replace("'","´",$_POST['domanda']))),ENT_QUOTES);
                                $data=time();
                                

                             
                                $ultimo_ordine= mysqli_fetch_array(mysqli_query($con,"Select * from $tabella order by ordine DESC LIMIT 1 "));
                                $ordine_nuovo= $ultimo_ordine['ordine']+1;

                                $sql="INSERT INTO $tabella (ID, domanda,ordine) VALUES (NULL, '$domanda','$ordine_nuovo')";
                                mysqli_query($con,$sql) or die (mysqli_error($con));
                                $_REQUEST['ID']=mysqli_insert_id($con);
                                $_REQUEST['azione']='aggiorna';
                            }
                            
                            if ($_REQUEST['azione']=='aggiorna'){
                                if(!isset($_REQUEST['ID'])){$ID=null;}else{$ID=$_REQUEST['ID'];$_SESSION['id_prodotto']=$_REQUEST['ID'];}
                                $row=mysqli_fetch_array(mysqli_query ($con,"SELECT * FROM $tabella WHERE ID = '$ID'"));
                                
                        
                                    $_POST['ID']  =$row['ID'];
                                    $_POST['domanda']  	=$row['domanda'];
                                    $_POST['tipo']  	=$row['tipo'];
                                    $_POST['durata']  	=$row['durata'];
                                    $_POST['answer_1']  	=$row['answer_1'];
                                    $_POST['answer_2']  	=$row['answer_2'];
                                    $_POST['answer_3']  	=$row['answer_3'];
                                    $_POST['answer_4']  	=$row['answer_4'];
                                    $_POST['answer_5']  	=$row['answer_5'];
                                    $_POST['answer_6']  	=$row['answer_6'];
                                    $_POST['answer_7']  	=$row['answer_7'];
                                    $_POST['answer_8']  	=$row['answer_8'];
                                    $_POST['answer_9']  	=$row['answer_9'];
                                    $_POST['answer_10']  	=$row['answer_10'];

                            }

                            if(isset($_GET["aggiornanuovo"]) || $_REQUEST['azione']=='aggiorna'){
                            ?>
                                        
                          
                                <div id="scheda_descrizione"> 
                                    <div id="contenitoreContenutiCentro" style="padding:20px">
                                        <div class="row">
                                            <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="form" enctype="multipart/form-data">
                                                <h3 class="col-md-12" style="margin-bottom:10px;">Question</h3> 
                                                <?php if(isset($control_campi)){?>
                                                <div class="card mb-3" >
                                                        <div class="alert alert-danger">
                                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                                                            <h5><strong>Fill the mandatory fields</strong></h5>
                                                        </div>
                                                </div>
                                                <?php }?>
                                                
                                                <div class="card mb-3" style="text-align: left;">
                                                    <table width="100%"  border="0" cellspacing="4" cellpadding="0">
                                                        <tr>
                                                            <td width="21%" style="padding-bottom:5px;"><strong>Question </strong></td>
                                                            <td width="79%" style="padding-bottom:5px;">
                                                                <?php $_POST['domanda']?>
                                                                <input name="domanda" type="text" class="campoInput" id="domanda" value="<?php echo stripslashes(@$_POST['domanda']); ?>" size="30">
                                                                <span class="testo11Rosso">(Mandatory)</span>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td style="padding-bottom:5px;"><strong> Time duration </strong><br><small>In seconds</small></td>
                                                            <td style="padding-bottom:5px;">
                                                                <?php $_POST['durata']?>
                                                                <?php 
                                                                    $temp = stripslashes(@$_POST['durata']);
                                                                    $durata_value = $temp != null && $temp != 0 ? $temp : "";
                                                                ?>
                                                                <input name="durata" type="text" class="campoInput" id="durata" value="<?php echo $durata_value; ?>" size="10" required>
                                                                
                                                                <span class="testo11Rosso">(Mandatory)</span>
                                                            </td>
                                                        </tr>
                                                    
                            
                                                        <tr>
                                                            <td style="padding-bottom:5px;"><strong>Question type:</strong></td>
                                                            <td style="padding-bottom:5px;">
                                                                <?php 
                                                                if(@$_POST['tipo']=='risp_multipla'){$checkedB1='checked="checked"';}
                                                                if(@$_POST['tipo']=='risp_aperta'){$checkedB2='checked="checked"';}
                                                                ?>
                                                                <input name="tipo" type="radio" value="risp_multipla" style="margin-right:5px;"  <?php echo @$checkedB1 ?>/><strong>Poll</strong>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                                <input name="tipo" type="radio" value="risp_aperta"  style="margin-right:5px;" <?php echo @$checkedB2 ?>/><strong>Open question</strong> 
                                                                <span class="testo11Rosso">(Mandatory)</span> 
                                                            </td>
                                                        </tr>
                                                        
                                                        <tr>
                                                        <td colspan="2" style="padding-bottom:5px;">
                                                            <?php $mostra_url_evento= ($_POST['tipo']!="" && $_POST['tipo']!="risp_aperta") ? "" : "style='display:none;'"; ?>
                                                            <input type="hidden" name="answer_1" id="answer_1" value="">
                                                            <input type="hidden" name="answer_2" id="answer_2" value="">
                                                            <input type="hidden" name="answer_3" id="answer_3" value="">
                                                            <input type="hidden" name="answer_4" id="answer_4" value="">
                                                            <input type="hidden" name="answer_5" id="answer_5" value="">
                                                            <input type="hidden" name="answer_6" id="answer_6" value="">
                                                            <input type="hidden" name="answer_7" id="answer_7" value="">
                                                            <input type="hidden" name="answer_8" id="answer_8" value="">
                                                            <input type="hidden" name="answer_9" id="answer_9" value="">
                                                            <input type="hidden" name="answer_10" id="answer_10" value="">
                                                            
                                                            <div id="didaTipoDomanda" <?php echo $mostra_url_evento;?>>
                                                                <span style="font-weight:bold;">Answer 1</span>
                                                                <input  name="answer_1" id="answer_1" class="form-control reduce" value="<?php echo @$_POST['answer_1'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 2</span>
                                                                <input  name="answer_2" id="answer_2" class="form-control reduce" value="<?php echo @$_POST['answer_2'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 3</span>
                                                                <input  name="answer_3" id="answer_3" class="form-control reduce" value="<?php echo @$_POST['answer_3'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 4</span>
                                                                <input  name="answer_4" id="answer_4" class="form-control reduce" value="<?php echo @$_POST['answer_4'] ?>">
                                                                
                                                                
                                                                
                                                                <div id="ris5" class="ris_cont" <?php if(isset($_POST['answer_5']) && $_POST['answer_5'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 5</span>
                                                                    <input  name="answer_5" id="answer_5" class="form-control reduce" value="<?php echo @$_POST['answer_5'] ?>">
                                                                </div>

                                                                <div id="ris6" class="ris_cont" <?php if(isset($_POST['answer_6']) && $_POST['answer_6'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 6</span>
                                                                    <input  name="answer_6" id="answer_6" class="form-control reduce" value="<?php echo @$_POST['answer_6'] ?>">
                                                                </div>

                                                                <div id="ris7" class="ris_cont" <?php if(isset($_POST['answer_7']) && $_POST['answer_7'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 7</span>
                                                                    <input  name="answer_7" id="answer_7" class="form-control reduce" value="<?php echo @$_POST['answer_7'] ?>">
                                                                </div>

                                                                <div id="ris8" class="ris_cont" <?php if(isset($_POST['answer_8']) && $_POST['answer_8'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 8</span>
                                                                    <input  name="answer_8" id="answer_8" class="form-control reduce" value="<?php echo @$_POST['answer_8'] ?>">
                                                                </div>

                                                                <div id="ris9" class="ris_cont" <?php if(isset($_POST['answer_9']) && $_POST['answer_9'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 9</span>
                                                                    <input  name="answer_9" id="answer_9" class="form-control reduce" value="<?php echo @$_POST['answer_9'] ?>">
                                                                </div>

                                                                <div id="ris10" class="ris_cont" <?php if(isset($_POST['answer_10']) && $_POST['answer_10'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 10</span>
                                                                    <input  name="answer_10" id="answer_10" class="form-control reduce" value="<?php echo @$_POST['answer_10'] ?>">
                                                                </div>

                                                                <div onClick="addNewAnswer()" class="ris_button_plus"> <i class="fa fa-plus-circle"></i> Add new answer</div>

                                                                <script>
                                                                    function addNewAnswer(){
                                                                        for(i=5; i<11; i++){
                                                                            if($("#ris"+i).css('display') == "none") {
                                                                                $("#ris"+i).show("slow");
                                                                                break;
                                                                            }
                                                                        }
                                                                    }
                                                                </script>
                                                            
                                                            </div>
                                                        </td>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                        </tr>

                                                    </table>
                                                </div>
                                                    
                                                <input type="hidden" name="cke" value="1">
                                                <input type="hidden" name="azione" value="agg">
                                                <input type="hidden" name="ID" value="<?php echo $_REQUEST['ID'] ?>">
                                                
                                                <br />
                                                
                                                <div class="col-md-12">
                                                    <button type="submit" class="mb-xs mt-xs mr-xs btn btn-success" style="font-size: large;margin: 5px;color: #ffffff; border-color: #51b451 !important;background-color: #51b451;"><i class="fa fa-save"></i> Save</button>
                                                    <?php 
                                                        if($_REQUEST['cke'] != ''){$url=$file_script;}
                                                        else{$url='index.php';}
                                                        ?>
                                                    <button type="button" style="font-size: large;margin: 5px;padding: 4px; color: #ffffff;text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);background-color: #d2322d;border-color: #d2322d;" class="mb-xs mt-xs mr-xs btn btn-danger" onClick="window.location='polls-panel.php'"><i class="fa fa-undo"></i> Cancel</button>
                                            </div>
                                            
                                        </form>     
                                        
                                        </div>
                                    </div>
                                </div>
                          

                            <?php } else { ?>
                        
                        <div class="offset-md-1 col-md-10 mobile pt-5" >

							<div class="card mb-3">
								<div class="card-body" style="padding:5px;">
									<div >
										<a href="polls.php"> <div class="pools-button" style="float:left;"> <i class="fa fa-arrow-circle-left"></i> Back to polls </div> </a>
									</div>
								</div>
                            </div>
                        </div>

                        <div class="offset-md-1 col-md-10 mobile pt-5" >

							<div class="card mb-3">
								<div class="card-body" style="padding:20px;">
									<div >
                                    <div class="row">
                                    <div class="col-md-12">
                                        <?php if(isset($_GET['noitem'])){ ?>
                                                <div class="alert alert-danger">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                                                    <h5><strong>Question empty, please enter the question</strong></h5>
                                                </div>
                                        <?php }?>
                                        <?php if(isset($esito_cancellazione)){?>
                                                <div class="submit-response failure">
                                                    <?php if($esito_cancellazione==1){ ?>    
                                                        <h5><strong>Question deleted!</strong></h5>
                                                    <?php } ?>
                                                    <?php if($esito_cancellazione==2){ ?>    
                                                        <h5><strong>Answers deleted!</strong></h5>
                                                    <?php } ?>  
                                                </div>
                                        <?php } ?>  
                                        
                                    
                                        <div id="boxInserisciNuovo" style="padding:3px; text-align: center; color: white; border: 2px solid white;border-radius:8px;background-color:brown;">
                                                <?php if(isset($_GET['aggiungi_nuovo'])){ ?>
                                                <form action="<?php echo $_SERVER['PHP_SELF'] ?>?aggiornanuovo" method="post" name="form" enctype="multipart/form-data">
                                                <strong style="color:white; font-weight: bold;">New Poll</strong>
                                                    <input name="domanda" type="text"  class="campoInput" id="domanda" value="" size="40" required>
                                                <input type="hidden" name="cke" value="1"> 
                                                <input type="hidden" name="azione" value="nuovo">
                                                <button type="submit" title="Aggiungi" class="mb-xs mt-xs mr-xs btn btn btn-success"> <i class="fa fa-plus-square"></i> Submit </button>
                                                </form>
                                                <?php }else{ ?>
                                                    <a href="<?php echo $_SERVER['PHP_SELF'] ?>?aggiungi_nuovo" style="color:white; font-weight: bold;">New Poll</a>
                                                <?php } ?>
                                            </div>
                                        
                                                    
                                    <div id="contenitoreContenutiCentro">
                                        <?php
                                                $sql =  "SELECT * FROM polls_master order by ordine";	
                                                $result = mysqli_query($con,$sql);
                                                $page = @ceil(@mysqli_num_rows($result)/$ArticoliPagina);
                                                if (!isset($_GET['sheet'])){$_GET['sheet']=null;}
                                                if (!$_GET['sheet']) { $_GET['sheet'] = 1; }
                                                $limit_down = ($_GET['sheet'] - 1)*$ArticoliPagina;
                                                @mysqli_free_result($result);
                                                    $sql .= " LIMIT $limit_down, $ArticoliPagina";
                                           
                                                $result = mysqli_query($con,$sql );
                                        
                                            
                                            ?>
                                            <div id="col-md-12" style="text-align:left; margin-top:30px;"> <strong>Pages</strong> 
                                                <?php	
                                                if(!isset($_GET['cerca'])){$_GET['cerca']=null;}
                                                if($_GET['sheet'] > 1) {
                                                    echo ('<a href="'.$_SERVER['PHP_SELF'].'?sheet='.($_GET['sheet'] - 1).'&cerca='.$_GET['cerca'].'" > <<< indietro </a>&nbsp;&nbsp;');
                                                }
                                                for( $loop = 0; $loop < $page; $loop++ ) {
                                                    if( $loop == ($_GET['sheet'] - 1) ) {
                                                        echo ('<strong> ['.($loop + 1).'] </strong>');
                                                    } else {
                                                        echo ('<a href="'.$_SERVER['PHP_SELF'].'?sheet='.($loop + 1).'&cerca='.$_GET['cerca'].'" > '.($loop + 1).' </a>');
                                                    }
                                                }
                                                if($_GET['sheet'] < $page)  {
                                                    echo ('&nbsp;&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?sheet='.($_GET['sheet'] + 1).'&cerca='.$_GET['cerca'].'"> avanti>>></a>');
                                                }
                                                ?>
                                            </div>
                                                            
                                            <div class="col-md-12" style="margin-top:30px;">
                                                <div class="table-responsive">
                                                <table class="table mb-none" width="100%">
                                                        <thead>
                                                            <tr class="info" style="background-color:beige;">
                                                                <td width="5%"  ><strong>Active</strong></td>
                                                                <td width="5%">-</td>
                                                                <td width="36%" height="26"  ><strong>&nbsp;&nbsp;Question</strong></td>
                                                                <td  ><strong>Type</strong></td>
                                                                <td colspan="2" ><strong>Actions</strong></td>
                                                            </tr> 
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $sql="SELECT * FROM $tabella";
                                                            @$N=mysqli_num_rows(mysqli_query($con,$sql));
                                                     
                                                            $a='0';
                                                            while(@$row=mysqli_fetch_array($result))  
                                                            {$a++; ?>
                                                                
                                                            <tr>
                                                                    <td>
                                                                        <?php 
                                                                        $time_ora= time();
                                                                        $checked_domanda= ($row['attiva']==1 && $time_ora<$row['disactivation_date']) ? "checked" : ""; ?>
                                                                        <input <?php echo $checked_domanda;?> type="checkbox" name="attiva_domanda" id="attiva_domanda" value="<?php echo $row['ID'];?>">
                                                                    </td>
                                                                    <td> 
                                                                        <?php if($N>1){?>
                                                                            <form name="form" id="form">
                                                                            <select name="jumpMenu" id="jumpMenu" onChange="menu_update('parent',this,0)" class="selectForm" style="padding:0px;">
                                                                                <?php for($i=1; $i<=$N; $i++ ){
                                                                                        if($i==$row['ordine']){$sel='selected'; }else{$sel=NULL;}?>
                                                                                <option value="<?php echo $_SERVER['PHP_SELF'].'?ID_documento='.$row['ID'].'&ordine='.$row['ordine'].'&nuovo='.$i.'&cke=&azione=ordina&sheet='.@$_GET['sheet']; ?>" <?php echo $sel; ?>><?php echo $i ?></option>
                                                                                <?php }?>
                                                                            </select>
                                                                            </form>
                                                                        <?php }?>
                                                                    </td>                                                           
                                                            
                                                                <td>
                                                                    <span>
                                                                    <?php echo stripslashes($row['domanda']) ; ?>
                                                                    </span>
                                                                </td>
                                                                
                                                                <td>
                                                                    <?php 
                                                                    if($row['tipo']=="risp_multipla"){
                                                                        echo "POLL";
                                                                    }
                                                                    if($row['tipo']=="risp_aperta"){
                                                                        echo "OPEN QUESTION";
                                                                    }
                                                                    ?>
                                                                </td>
                                                                
                                                                <td align="center"><a href="<?php echo "$_SERVER[PHP_SELF]?cke=1&ID=$row[ID]&azione=aggiorna"; ?>" ><i class="fa fa-pencil testo18 azzurro"></i></a></td>
                                                                <td align="center">
                                                                    <a href="<?php echo "$_SERVER[PHP_SELF]?cke=&ID=$row[ID]&azione=del"; ?>"  onClick="return confirm('Question: <?php echo $row['domanda'] ?> \n Confirm deletion? \n ATTENTION! IRREVERSIBLE ACTION');"><i class="fa fa-trash-o testo18 rosso"></i></a>
                                                                        
                                                                    <?php if (isset($_SESSION['sessione_mastercontol'])) { ?>
                                                                        <a href="<?php echo "$_SERVER[PHP_SELF]?cke=&ID=$row[ID]&azione=delRisposte"; ?>" title="ELIMINA RISPOSTE"  style="margin-left:10px;" onClick="return confirm('Vuoi eliminare le risposte di questa domanda? \n \n ** <?php echo $row['domanda'] ?> ** \n \n ATTENTION! IRREVERSIBLE ACTION!');"><i class="fa fa-eraser testo18 rosso"></i></a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php }	?>
                                                        </tbody>
                                                    </table>
                                                <span ><?php echo $row['provenienza_gilead']?></span> </div>
                                            </div>
                                        </div>
                                    </div>
									</div>
								</div>
                            </div>
                        </div>


            <?php } } ?>                      
            </div>
                
            </div> 
        </div> 
        </div>


        </div>
        </div>

        </section>		
                                                           
 

	</div>


	<div id="gotoTop" class="icon-angle-up"></div>

    
    <script type="text/javascript">
        $(document).ready(function(){

            $("input[name='tipo']").click(function(){
                var radioValue = $("input[name='tipo']:checked").val();
                if(radioValue == 'risp_multipla'){
                    $("#didaTipoDomanda").show();
                }
                if(radioValue == 'risp_aperta'){
                    $("#didaTipoDomanda").hide();
                }
            });
            
        });
        
        $("input[name='attiva_domanda']").click(function() {
            var val_sel= $(this).val(); 
            var stato;
            if ($(this).is(":checked"))
            {
                stato="1";
            }else{
                stato="";
            }
            
            $.ajax({
                url: "../api/post-poll-active.php",
                type: "get",
                crossDomain: true,
                data: 'val_sel='+val_sel + "&stato=" + stato,
                success: function(data){
                },
                error: function () {
                }
            });						
            
            
        });
        
        
        
        </script>

<script type="text/javascript">

    function menu_update(targ,selObj,restore){ 
    eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
    if (restore) selObj.selectedIndex=0;
    }
</script>

<?php mysqli_close($con); ?>

</body>
</html>