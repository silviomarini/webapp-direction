<?php 
session_start();

require_once("../server/helper.php");

$tabella_utenti="utenti";

if(isset($_GET['logout'])){
	unset($_SESSION['event_login']);
	header("location:/login.php");
}
//---------NOME TABELLA-----------//
$tabella='sondaggi';

$file_script='polls-panel.php';
//--------------- visualizzazoni ----------
$ArticoliPagina = 1000;//Numero articoli per pagina
//campi obbligatori
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
	<meta name="author" content="creativetown.it" />
	
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

<body class="stretched">
	<div id="wrapper" class="clearfix bgrTransparent">
        
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
            $evento = mysqli_fetch_array(mysqli_query($con,"Select * from eventi order by ID DESC LIMIT 1 "));
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
                <a href="index.php?logout">
                    <p class="text">Logout</p>   
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if($cover == "") { $cover = "cover1608542135.jpeg"; } ?>
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
            if(isset($_SESSION['login_evento'])){ 
            //sopra: controllo per mostrare la pagina o la modale di login se l'utente non è già loggato
            //sotto l'aggiornamento del Db in caso si torni dal pannello di aggiunta o modifica domanda.
                
                if (!isset($_REQUEST['cke'])){$_REQUEST['cke']=null;}
                    switch ($_REQUEST['cke'])
                    {
                    case "":
                        if(isset($_GET['azione']) && $_GET['azione']=='del'){
                            //cancello il record nel db
                            $sql="DELETE FROM $tabella WHERE ID = '$_REQUEST[ID]'";
                            //echo $sql.'<br>';
                            mysqli_query ($con,$sql); 
                            
                            //CANCELLO RISPOSTE
                            $sql_del_risp="DELETE FROM sondaggi_risposte WHERE s_ID_sondaggio = '$_REQUEST[ID]'";
                            mysqli_query ($con,$sql_del_risp); 			
                            
                            //exit;
                            $esito_cancellazione=1;
                        }

                        if(isset($_GET['azione']) && $_GET['azione']=='delRisposte'){
                            //CANCELLO RISPOSTE
                            $sql_del_risp="DELETE FROM sondaggi_risposte WHERE s_ID_sondaggio = '$_REQUEST[ID]'";
                            mysqli_query ($con,$sql_del_risp); 	 
                            
                            $esito_cancellazione=2;
                        }		 
                        
                        
                        if(isset($_GET['azione']) && $_GET['azione']=='ordina'){
                            $sqlG=NULL;

                            //sposto su
                            if($_GET['ordine']>$_GET['nuovo']){
                                
                                //Sposto  ordine
                                $sql1="UPDATE $tabella SET ordine = '$_GET[nuovo]' WHERE ID = '$_GET[ID_documento]'";
                                mysqli_query($con,$sql1);
                                //echo '<br><br>'.$sql1.'<br>'; 
                                
                                //Dopo avere spostato l'articolo risetto tutti gli ordini di visualizzazione
                                $sql="SELECT * FROM $tabella  WHERE ID != '$_GET[ID_documento]' and ordine >= '$_GET[nuovo]' order by ordine ASC";
                                //echo $sql.'<br />';
                                $ordine=$_GET['nuovo']+1;
                                $r_result=mysqli_query($con,$sql);
                                while($N=mysqli_fetch_array($r_result)){
                                    //$x=$ordine;
                                    $sql1="UPDATE $tabella SET `ordine` = '$ordine' WHERE ID = '$N[ID]' LIMIT 1 ";
                                    //echo '<br />'.$sql1;
                                    mysqli_query($con,$sql1);
                                    $ordine++;
                                }
                            }
                            
                            //sposto giù
                            if($_GET['ordine']<$_GET['nuovo']){
                                //Sposto  ordine
                                $sql1="UPDATE $tabella SET ordine = '$_GET[nuovo]' WHERE ID = '$_GET[ID_documento]'";
                                //echo '<br><br>'.$sql1.'<br>';
                                mysqli_query($con,$sql1);
                                
                                //Dopo avere spostato l'articolo risetto tutti gli ordini di visualizzazione
                                $sql="SELECT * FROM $tabella  WHERE ID != '$_GET[ID_documento]' and ordine <= '$_GET[nuovo]' order by ordine DESC";
                                //echo $sql.'<br />';
                                
                                $ordine=$_GET['nuovo']-1;
                                $r_result=mysqli_query($con,$sql);
                                while($N=mysqli_fetch_array($r_result)){
                                    //$x=$ordine;
                                    $sql1="UPDATE $tabella SET `ordine` = '$ordine' WHERE ID = '$N[ID]' LIMIT 1 ";
                                    //echo '<br />'.$sql1;
                                    mysqli_query($con,$sql1);
                                    $ordine--;
                                }
                            }
                            //exit;
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

                            //exit;
                            
                                foreach($_POST as $key => $valore){
                                        $valore=str_replace("'","´",$valore);
                                        $valore = stripslashes(trim($valore));
                                        //echo $key.' - '.$valore.'<br />';
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
                                        }	//fine if
                                    } //fine if
                                } 
                        }
                        //echo $erro_msg;
                                
                        if(isset($control_campi) && $control_campi==0 && isset($_POST['azione'])){
                                $query="UPDATE $tabella SET
                                        domanda = '".@$_POST['domanda']."',
                                        tipo ='".@$_POST['tipo']."',
                                        durata = '".@$_POST['durata']."',
                                        risposta_1 = '".@$_POST['risposta_1']."',
                                        risposta_2 = '".@$_POST['risposta_2']."',
                                        risposta_3 = '".@$_POST['risposta_3']."',
                                        risposta_4 = '".@$_POST['risposta_4']."',
                                        risposta_5 = '".@$_POST['risposta_5']."',
                                        risposta_6 = '".@$_POST['risposta_6']."',
                                        risposta_7 = '".@$_POST['risposta_7']."',
                                        risposta_8 = '".@$_POST['risposta_8']."',
                                        risposta_9 = '".@$_POST['risposta_9']."',
                                        risposta_10 = '".@$_POST['risposta_10']."'
                                        WHERE ID = '$_POST[ID]'";
                            
                            //echo $query;exit;
                            mysqli_query($con,$query) or die (mysqli_error($con));
                            
                            //exit;
                            header("location: $_SERVER[PHP_SELF]"); exit;
                        }
                        
                    break;
                    }
                
                
                
                ?>
                <div class="row">
        
                <div class="col-md-12 bg">
                    <div class="row">
						
						<?php
                            global $con, $page,$file_script, $azione, $azione2, $dati_modulo, $tabella, $tabella_CAT, $tabella_MCAT, $ID, $msg_errore, $erro_msg,$dir_immagini, $file_script ;
                            global  $AI, $SP, $DE, $FR; //lingue
                            global $vedi_codice_articolo, $vedi_prezzo, $vedi_breve_descrizioni_scheda, $vedi_descrizioni_scheda, $vedi_box_ins_immagini, $control_campi;
                            if(!isset($_REQUEST['azione'])) $_REQUEST['azione']=null;
                            if(isset($_REQUEST['azione']) && $_REQUEST['azione']=='nuovo'){
                                
                                $domanda = htmlspecialchars(stripslashes(trim(str_replace("'","´",$_POST['domanda']))),ENT_QUOTES);
                                $data=time();
                                

                                //ultimo ordine
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
                                
                                //foreach($row as $K => $V){echo $K.'<br>';}
                                    $_POST['ID']  =$row['ID'];
                                    $_POST['domanda']  	=$row['domanda'];
                                    $_POST['tipo']  	=$row['tipo'];
                                    $_POST['durata']  	=$row['durata'];
                                    $_POST['risposta_1']  	=$row['risposta_1'];
                                    $_POST['risposta_2']  	=$row['risposta_2'];
                                    $_POST['risposta_3']  	=$row['risposta_3'];
                                    $_POST['risposta_4']  	=$row['risposta_4'];
                                    $_POST['risposta_5']  	=$row['risposta_5'];
                                    $_POST['risposta_6']  	=$row['risposta_6'];
                                    $_POST['risposta_7']  	=$row['risposta_7'];
                                    $_POST['risposta_8']  	=$row['risposta_8'];
                                    $_POST['risposta_9']  	=$row['risposta_9'];
                                    $_POST['risposta_10']  	=$row['risposta_10'];

                            }

                            if(isset($_GET["aggiornanuovo"]) || $_REQUEST['azione']=='aggiorna'){
                            ?>
                                        
                                <!-- start: page -->
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
                                                            <input type="hidden" name="risposta_1" id="risposta_1" value="">
                                                            <input type="hidden" name="risposta_2" id="risposta_2" value="">
                                                            <input type="hidden" name="risposta_3" id="risposta_3" value="">
                                                            <input type="hidden" name="risposta_4" id="risposta_4" value="">
                                                            <input type="hidden" name="risposta_5" id="risposta_5" value="">
                                                            <input type="hidden" name="risposta_6" id="risposta_6" value="">
                                                            <input type="hidden" name="risposta_7" id="risposta_7" value="">
                                                            <input type="hidden" name="risposta_8" id="risposta_8" value="">
                                                            <input type="hidden" name="risposta_9" id="risposta_9" value="">
                                                            <input type="hidden" name="risposta_10" id="risposta_10" value="">
                                                            
                                                            <div id="didaTipoDomanda" <?php echo $mostra_url_evento;?>>
                                                                <span style="font-weight:bold;">Answer 1</span>
                                                                <input  name="risposta_1" id="risposta_1" class="form-control reduce" value="<?php echo @$_POST['risposta_1'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 2</span>
                                                                <input  name="risposta_2" id="risposta_2" class="form-control reduce" value="<?php echo @$_POST['risposta_2'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 3</span>
                                                                <input  name="risposta_3" id="risposta_3" class="form-control reduce" value="<?php echo @$_POST['risposta_3'] ?>">
                                                                <br>

                                                                <span style="font-weight:bold;">Answer 4</span>
                                                                <input  name="risposta_4" id="risposta_4" class="form-control reduce" value="<?php echo @$_POST['risposta_4'] ?>">
                                                                
                                                                
                                                                
                                                                <div id="ris5" class="ris_cont" <?php if(isset($_POST['risposta_5']) && $_POST['risposta_5'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 5</span>
                                                                    <input  name="risposta_5" id="risposta_5" class="form-control reduce" value="<?php echo @$_POST['risposta_5'] ?>">
                                                                </div>

                                                                <div id="ris6" class="ris_cont" <?php if(isset($_POST['risposta_6']) && $_POST['risposta_6'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 6</span>
                                                                    <input  name="risposta_6" id="risposta_6" class="form-control reduce" value="<?php echo @$_POST['risposta_6'] ?>">
                                                                </div>

                                                                <div id="ris7" class="ris_cont" <?php if(isset($_POST['risposta_7']) && $_POST['risposta_7'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 7</span>
                                                                    <input  name="risposta_7" id="risposta_7" class="form-control reduce" value="<?php echo @$_POST['risposta_7'] ?>">
                                                                </div>

                                                                <div id="ris8" class="ris_cont" <?php if(isset($_POST['risposta_8']) && $_POST['risposta_8'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 8</span>
                                                                    <input  name="risposta_8" id="risposta_8" class="form-control reduce" value="<?php echo @$_POST['risposta_8'] ?>">
                                                                </div>

                                                                <div id="ris9" class="ris_cont" <?php if(isset($_POST['risposta_9']) && $_POST['risposta_9'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 9</span>
                                                                    <input  name="risposta_9" id="risposta_9" class="form-control reduce" value="<?php echo @$_POST['risposta_9'] ?>">
                                                                </div>

                                                                <div id="ris10" class="ris_cont" <?php if(isset($_POST['risposta_10']) && $_POST['risposta_10'] != ""){ echo "style='display:block;'"; } else { echo "style='display:none;'"; } ?> >
                                                                    <span style="font-weight:bold;">Answer 10</span>
                                                                    <input  name="risposta_10" id="risposta_10" class="form-control reduce" value="<?php echo @$_POST['risposta_10'] ?>">
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
                                                <!--div class="col-md-6">
                                                    <section class="areeProdottoBig">
                                                            <header class="panel-heading">
                                                                <h2 class="panel-title"><strong>REGIA</strong></h2>
                                                            </header>
                                                            <div class="panel-body" style="padding-bottom:0px"> 
                                                            <table width="100%"  border="0" cellspacing="4" cellpadding="0">
                                                                <tr>
                                                                    <td>
                                                                        <strong>Password regia</strong><br>
                                                                        <?php  
                                                                        /*
                                                                        if(!isset($control_campi)){
                                                                            //decripta
                                                                            $ps=$_POST['codice_accesso'];
                                                                            $N_ps=strlen($ps);
                                                                            $N_fraz=$N_ps/5;
                                                                            $s=0;
                                                                            for($p=0; $p<$N_ps; $p++){
                                                                                if($s==5){@$codice_accesso .=$ps[$p];$s=0;}
                                                                                else{$s++;}
                                                                            }
                                                                        }else{
                                                                            @$codice_accesso= $_POST['codice_accesso'];
                                                                        }																
                                                                        */
                                                                        ?>
                                                                        <input name="password" type="text" class="campoInput" id="password" value="<?php echo  @$_POST['password']; ?>" size="20"><br>
                                                                        <small>Minimo 5 caratteri tra lettere e numeri</small>
                                                                        <br /><br />
                                                                </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </section>
                                                </div-->
                                                    
                                                <input type="hidden" name="cke" value="1">
                                                <input type="hidden" name="azione" value="agg">
                                                <input type="hidden" name="ID" value="<?php echo $_REQUEST['ID'] ?>">
                                                
                                                <br />
                                                
                                                <div class="col-md-12 txtAlignDestra">
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
                                <!-- end: page -->

                            <?php } else { ?>
                        
                        <div class="offset-md-1 col-md-10 paddingMobile pt-5" >

							<div class="card mb-3">
								<div class="card-body" style="padding:5px;">
									<div class="cont_risposta">
										<a href="polls.php"> <div class="pools-button" style="float:left;"> <i class="fa fa-arrow-circle-left"></i> Back to polls </div> </a>
									</div>
								</div>
                            </div>
                        </div>

                        <div class="offset-md-1 col-md-10 paddingMobile pt-5" >

							<div class="card mb-3">
								<div class="card-body" style="padding:20px;">
									<div class="cont_risposta">
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
                                        
                                        <?php //if (isset($_SESSION['sessione_mastercontol'])) { ?>
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
                                        <?php //} ?>
                                                    
                                    <div id="contenitoreContenutiCentro">
                                            <?php //----------------------Paginazine
                                                $sql =  "SELECT * FROM sondaggi order by ordine";	
                                                $result = mysqli_query($con,$sql);
                                            // Impostazione dei parametri per le pagine multiple!
                                                $page = @ceil(@mysqli_num_rows($result)/$ArticoliPagina);
                                                if (!isset($_GET['sheet'])){$_GET['sheet']=null;}
                                                if (!$_GET['sheet']) { $_GET['sheet'] = 1; }
                                                $limit_down = ($_GET['sheet'] - 1)*$ArticoliPagina;
                                                @mysqli_free_result($result);
                                                    $sql .= " LIMIT $limit_down, $ArticoliPagina";
                                            // Rifacciamo la query!
                                                $result = mysqli_query($con,$sql );
                                            //------------Creazione del browser-
                                            
                                            ?>
                                            <div id="col-md-12" style="text-align:left; margin-top:30px;"> <strong>Pages</strong> 
                                                <?php	
                                                //**************Creazione link Avanti Indietro*****************************************
                                                if(!isset($_GET['cerca'])){$_GET['cerca']=null;}
                                                if($_GET['sheet'] > 1) {
                                                    echo ('<a href="'.$_SERVER['PHP_SELF'].'?sheet='.($_GET['sheet'] - 1).'&cerca='.$_GET['cerca'].'" > <<< indietro </a>&nbsp;&nbsp;');
                                                }
                                                for( $loop = 0; $loop < $page; $loop++ ) {
                                                    // L'IF fa in modo che la pagina selezionata sia con il quadratino nero
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
                                                                <td width="5%" class="testo12" ><strong>Active</strong></td>
                                                                <td width="5%">-</td>
                                                                <td width="36%" height="26" class="testo12" ><strong>&nbsp;&nbsp;Question</strong></td>
                                                                <td class="testo12" ><strong>Type</strong></td>
                                                                <td colspan="2" class="testo12"><strong>Actions</strong></td>
                                                            </tr> 
                                                        </thead>
                                                        <tbody>
                                                            <?php 
                                                            $sql="SELECT * FROM $tabella";
                                                            @$N=mysqli_num_rows(mysqli_query($con,$sql));
                                                            //echo $sql.'<br />';
                                                            //echo $N;
                                                            $a='0';
                                                            while(@$row=mysqli_fetch_array($result))  
                                                            {$a++; ?>
                                                                
                                                            <tr>
                                                                    <td>
                                                                        <?php 
                                                                        $time_ora= time();
                                                                        $checked_domanda= ($row['attiva']==1 && $time_ora<$row['data_disattivazione']) ? "checked" : ""; ?>
                                                                        <input <?php echo $checked_domanda;?> type="checkbox" name="attiva_domanda" id="attiva_domanda" value="<?php echo $row['ID'];?>">
                                                                    </td>
                                                                    <td> 
                                                                        <?php if($N>1){?>
                                                                            <form name="form" id="form">
                                                                            <select name="jumpMenu" id="jumpMenu" onChange="MM_jumpMenu('parent',this,0)" class="selectForm testo11" style="padding:0px;">
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
                                                                    <a href="<?php echo "$_SERVER[PHP_SELF]?cke=&ID=$row[ID]&azione=del"; ?>" class="testo12neroBold" onClick="return confirm('Question: <?php echo $row['domanda'] ?> \n Confirm deletion? \n ATTENTION! IRREVERSIBLE ACTION');"><i class="fa fa-trash-o testo18 rosso"></i></a>
                                                                        
                                                                    <?php if (isset($_SESSION['sessione_mastercontol'])) { ?>
                                                                        <a href="<?php echo "$_SERVER[PHP_SELF]?cke=&ID=$row[ID]&azione=delRisposte"; ?>" title="ELIMINA RISPOSTE" class="testo12neroBold" style="margin-left:10px;" onClick="return confirm('Vuoi eliminare le risposte di questa domanda? \n \n ** <?php echo $row['domanda'] ?> ** \n \n ATTENTION! IRREVERSIBLE ACTION!');"><i class="fa fa-eraser testo18 rosso"></i></a>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php }	?>
                                                        </tbody>
                                                    </table>
                                                <span class="testo12"><?php echo $row['provenienza_gilead']?></span> </div>
                                            </div>
                                        </div>
                                    </div>
									</div>
								</div>
                            </div>
                        </div>


            <?php } }else{?>
                <div class="row">
			
                <div class="col-md-12 bg">
                    <div class="row-login">
                        <div class="login-top">
                            Login
                            </div>
                        <div class="login-bottom">
                            
                            <?php 
                            if(isset($_GET['msg'])){
                                if($_GET['msg']=="errpsw"){ ?>
                                    <div class="submit-response failure">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon-remove-sign"></i><strong>Wrong password</strong>
                                    </div>
                                <?php 
                                } 
                                if($_GET['msg']=="nopsw"){ ?>
                                    <div class="submit-response failure">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                        <i class="icon-remove-sign"></i><strong>Enter your password</strong>
                                    </div>
                                <?php } ?>											
                            <?php } ?>
                            
                            <form name="loginEvento" id="loginEvento" action="#" method="post">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="txtNormal intestazioneTextarea">Please enter the password:</label>
                                        <input type="password" name="psw_evento" id="psw_evento" class="form-control required" value="">
                                    </div>
                                </div>    
                                <div class="col-12 text-center">
                                    <input type="hidden" name="entraRegia" value="1">
                                    <input type="submit" name="entraLogin" id="entraLogin" value="Enter" class="home-button">
                                </div>                                                                                      	
                            </form>
                        </div>                                    
                    </div>
                </div>
            </div>                           
            <?php }?>                       
            </div>
                
            </div> 
        </div> 
        </div>


        </div>
        </div>

        </section>		
                                                           
        <!-- #content end -->

	</div><!-- #wrapper end -->

	<!-- Go To Top
	============================================= -->
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
    //per gestire l'ordine delle domande
    function MM_jumpMenu(targ,selObj,restore){ //v3.0
    eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
    if (restore) selObj.selectedIndex=0;
    }
</script>

<?php mysqli_close($con); ?>

</body>
</html>