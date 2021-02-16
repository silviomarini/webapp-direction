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
	//$page_type = "regia";
}

//check filter to update queries
$supp_condition = "";
$active_options = true;
$active_title = "";
$active_type = "";
if(isset($_GET["filter"])){
	switch ($_GET["filter"]) {
		case 'selected':
			$supp_condition = " AND question_status='y' ";
			$active_options = false;
			$active_title = "SELECTED QUESTIONS";
			$active_type = "SELECTED";
			break;
		case 'deleted':
			$supp_condition = " AND question_status='n' ";
			$active_title = "DELETED QUESTIONS";
			$active_type = "DELETED";
			break;
		case 'done':
			$supp_condition = " AND question_status='d' ";
			$active_title = "PROCESSED QUESTIONS";
			$active_type = "DONE";
			break;
		case 'all':
			$supp_condition = " AND question_status='' ";
			$active_title = "LIVE QUESTIONS";
			$active_type = "ALL";
			break;
			
		default:
			//nothing
			break;
	}
}

?>
<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	
	<script src="../asset/js/jquery.js"></script>

    <link rel="stylesheet" href="../asset/css/style.css" type="text/css" />
    <link rel="stylesheet" href="../asset/css/font-awesome.css" />
	
	<title>Privilege Web App</title>
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
			//check if the cover value is a valid image
			if (!file_exists("../asset/event-covers/".$cover)) {
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
				 <span class="menu active"> <a href="index.php"> Questions </a> </span> 
				 <span class="menu"> <a href="polls.php"> Polls </a> </span> 
            </div>
            <div class="body" style="min-height:165px">   
				
				<?php 
				if($autorizzato){ 
					
					$sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select * from questions where event_id='".$ID_EVENTO."' order by question_timestamp desc LIMIT 1"));
					$ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];

					if(!isset($ultimo_id_inserito)){
						$ultimo_id_inserito=0;	
					}
																
					?>
						<div class="row" >
			
						<div class="col-md-12 bg">
							<div class="row">
								
							<?php
								
								$fp = fopen('export.csv', 'w');

								$sql_sondaggi="Select * from questions";
								$polls_ans= mysqli_query($con,$sql_sondaggi);
								
								while($polls_master= mysqli_fetch_array($polls_ans)){ 
									fputcsv($fp, $polls_master);
								}
								
								fclose($fp);

								if(isset($_GET["export"])){ echo "<div style='margin-bottom:20px;'> Export ready, click <a href='export.csv' style='color:brown;'> here for download </a> </div>"; }

								if(isset($_GET["reset"])){
									$sql_sondaggi="TRUNCATE TABLE questions";
									mysqli_query($con,$sql_sondaggi);
								}
							?>

							<div class="offset-md-1 col-md-10 mobile pt-5"> 
								<div class="card mb-3">
									<div class="card-body <?php echo $active_type; ?> " style="padding:5px;">
										<div >
											<?php if($active_type == "ALL" || $active_type == "") { ?> 
												<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
											<?php } ?>
										</div>
										<div  style="float:left;">
											<div class="active-status" style=""> <?php echo $active_title; ?> </div>
											<?php if($active_type != "DONE") { ?> 
												<a href="index.php?filter=done"> <div class="pools-button small done" style=""> PROCESSED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "DELETED") { ?> 
												<a href="index.php?filter=deleted"> <div class="pools-button small alert" style=""> DELETED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "SELECTED") { ?> 
												<a href="index.php?filter=selected"> <div class="pools-button small selected" style=""> SELECTED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "ALL") { ?> 
												<a href="index.php?filter=all"> <div class="pools-button small all" style=""> LIVE <BR/> QUESTIONS </div> </a>
											<?php } ?>
											</div>
									</div>

								</div>

								<div class="" id="alert-questions-attivate">
									
								</div>
							</div>
							
							<div class="offset-md-1 col-md-10 mobile pt-5" id="contDomande">

								


									<?php
									$sql_domande="Select * from questions where event_id='".$ID_EVENTO."' ".$supp_condition." order by question_timestamp desc";
									$and_questions= mysqli_query($con,$sql_domande);
									
									while($questions= mysqli_fetch_array($and_questions)){
									if($questions['hidden_question']=="0"){ 
											if($questions['question_status']=="y"){$col_bg="#2aa900";}
											if($questions['question_status']=="n"){$col_bg="#e81f1f";}
											if($questions['question_status']=="d"){$col_bg="#fbfb00";}
											if($questions['question_status']==""){$col_bg="transparent";}
											?>
											<div class="card mb-3 <?php if($questions['attiva']){echo "domanda-attiva";} ?>" id="domanda_<?php echo $questions['ID'];?>">
												<div class="card-header">
													<div class="row">
														<div class="header-info sx"><strong><?php echo $questions['ID'];?>)</strong> h <strong><?php echo formatTime($questions['question_timestamp']);?></strong></div>
														<div class="header-info center">

															<div class="float-right">
															<!-- GO LIVE -->
															
															<!-- YES -->
																<?php if($active_type == "" || $active_type == "ALL"){ ?> 
																	<span class="statoDomanda statoVerde" valore="y" id="<?php echo $questions['ID'];?>" onClick="change_status(this)">
																		<i class="fa fa-check"></i></span> 
																<?php } ?>
															<!-- NO -->	
																<?php if($active_type == "" || $active_type == "ALL"){ ?> 
																	<span class="statoDomanda statoRosso" valore="n" id="<?php echo $questions['ID'];?>" onClick="change_status(this)">
																	<i class="fa fa-times"></i></span> 
																<?php } ?>
															
															<!-- RESET -->	
																<?php if($active_type == "" || $active_type == "SELECTED" || $active_type == "DELETED"){ ?> 
																	<span class="statoDomanda statoAzzera" valore="azzera" id="<?php echo $questions['ID'];?>" onClick="change_status(this)" title="Reset">
																	<i class="fa fa-refresh"></i></span> 
																<?php } ?>
																<span class="statoDomanda statoAzzera" style="visibility:hidden;"> <i class="fa fa-refresh"></i></span>  </span>
															</div>
															<div class="float-right" style="grid-template-columns: 100%;">
																<?php if( $active_type == "SELECTED"){ ?>
																	<span class="statoDomanda statoVerde" onClick="go_live(this)" title="GO Live" id="<?php echo $questions['ID'];?>" style="padding:8px;">
																	<i class="fa fa-upload" style="font-size: x-large;"></i></span>
																<?php } ?>
															</div>
														</div>

															<div class="header-info dx">

																<div style="display: inline-grid;">
																<!-- DONE -->
																	<?php if( $active_type == "" || $active_type == "SELECTED"){ ?>
																		<span class="statoDomanda statoGiallo" valore="d" <?php if(!$active_options){ echo 'style="width: fit-content;"'; } ?> id="<?php echo $questions['ID'];?>" onClick="change_status(this)">
																		<i class="fa fa-thumbs-up"></i></span>
																	<?php } ?>
																</div>

																<span> Status: </span>
																<span class="barraStato" id="barra_stato_<?php echo $questions['ID'];?>" style="background-color: <?php echo $col_bg;?>;"></span>

															</div>
														</div>



												</div>
												<div class="card-body">
													<p class="card-text"><?php echo nl2br($questions['question']);?></p>
												</div>
											</div>    
										<?php } ?>
									<?php } ?>

									<div class="card mb-3"></div>

								</div>
								
							</div> 
						</div> 
				</div>
				<?php } else {
					echo '<script language=javascript>document.location.href="login.php?unauthorized"</script>';
				}?>
			


				<div class="offset-md-1 col-md-10 mobile pt-5"> 
		
	</div>


            </div>
        </div>
		<input type="hidden" name="ultimo_ID" id="ultimo_ID" value="<?php echo $ultimo_id_inserito;?>">
		<input type="hidden" name="current_event_id" id="current_event_id" value="<?php echo $ID_EVENTO;?>">
	</div>

    <script type="text/javascript">
	// AGGIORNO REALTIME LE questions
	window.setInterval(function(){
		var ultimo_ID = $('#ultimo_ID').val();
		var current_event_id = $('#current_event_id').val();

		$.ajax({
			url: "../api/get-questions.php<?php echo "?filter=".$_GET["filter"]; ?>",
			type: "get",
			crossDomain: true,
			data: 'ultimo_ID='+ultimo_ID + "&current_event_id=" + current_event_id ,
			success: function(data){
				var data_split= data.split('|||');
				var dataDomande= data_split[0];
				
				$("#contDomande .card:first").before(dataDomande);
				$("#ultimo_ID").val(data_split[1]);
			},
			error: function () {
				alert('errore');
			}
		});	

	}, 5000);

	window.setInterval(function(){
		var current_event_id = $('#current_event_id').val();
		$.ajax({
			url: "../api/put-question-status.php",
			type: "get",
			crossDomain: true,
			data: "current_event_id=" + current_event_id,
			success: function(data){

				var data_split= data.split('|');
				$("#alert-questions-attivate").html("");
				for (var i = 0; i < data_split.length; i++) {
					var dati_domande= data_split[i].split(',');

					
					if(dati_domande[1]=="y"){	
						$("#barra_stato_"+dati_domande[0]).css("background-color","#2aa900");
							
					}
					if(dati_domande[1]=="n"){	
						$("#barra_stato_"+dati_domande[0]).css("background-color","#e81f1f");
					}
					if(dati_domande[1]=="d"){	
						$("#barra_stato_"+dati_domande[0]).css("background-color","#fbfb00");
					}	
					if(dati_domande[1]==""){	
						$("#barra_stato_"+dati_domande[0]).css("background-color","transparent");
					}										
				}
			},
			error: function () {
			}
		});	

	}, 5000);

	$(".statoDomanda").click(function() {

	});

	function go_live(el){
		var current_event_id = $('#current_event_id').val();
		var id_domanda= el.id;
		var classname = "domanda-attiva";

		$.ajax({
			url: "../api/put-live.php",
			type: "get",
			crossDomain: true,
			data: 'id_domanda=' + id_domanda + '&current_event_id='+current_event_id,
			success: function(data){
		
				if($("#domanda_"+id_domanda).hasClass(classname)){
					$("#domanda_"+id_domanda).removeClass( classname );
				} else {
					$("div").removeClass( classname );
					$("#domanda_"+id_domanda).addClass( classname);
				}
			},
			error: function () {
				alert('Go live error!');
			}
		});
		
	}

	function change_status(el){
		var current_event_id = $('#current_event_id').val();
		var id_domanda= el.id;
		var stato_domanda= el.getAttribute("valore");
		
		$.ajax({
			url: "../api/get-question-status.php",
			type: "get",
			crossDomain: true,
			data: 'id_domanda=' + id_domanda + '&stato_domanda='+stato_domanda + '&current_event_id='+current_event_id,
			success: function(data){
				
				if(stato_domanda=="y"){	
					$("#barra_stato_"+id_domanda).css("background-color","#2aa900");
				}
				if(stato_domanda=="n"){	
					$("#barra_stato_"+id_domanda).css("background-color","#e81f1f");
				}
				if(stato_domanda=="d"){	
					$("#barra_stato_"+id_domanda).css("background-color","#fbfb00");
				}	
				if(stato_domanda=="azzera"){	
					$("#barra_stato_"+id_domanda).css("background-color","transparent");
				}
				if("<?php echo $_GET["filter"]; ?>" != ""){
					$("#domanda_"+id_domanda).hide(200);
				}
			},
			error: function () {
				alert('Errore AJAX');
			}
		});			

	}
	
    </script>    
    
</body>
</html>