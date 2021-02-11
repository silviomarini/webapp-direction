<?php
//TODO:session handler
session_start();

if(isset($_GET['logout'])){
	unset($_SESSION['login_evento']);
	header("location:". $path."/regia.php");
}

//includes
include('server/db.php');

if(isset($_POST['entraRegia'])){
	if($_POST['psw_evento']!=""){
		$sql_controllo="select * from streamings where password='".$_POST['psw_evento']."'";
		if($evento=mysqli_fetch_array(mysqli_query($con,$sql_controllo))){
			$_SESSION['login_evento']=$evento['ID'];
			$ID_EVENTO=$_SESSION['login_evento'];
		}else{
			header("location: regia.php?msg=errpsw"); exit;	
		}
	}else{
		header("location: regia.php?msg=nopsw"); exit;	
	}
}

if(isset($_SESSION['login_evento'])){
	$sql_controllo="select * from streamings where ID='".$_SESSION['login_evento']."'";
	if($evento=mysqli_fetch_array(mysqli_query($con,$sql_controllo))){
		$ID_EVENTO=$_SESSION['login_evento'];
	}else{
		header("location: regia.php?msg=errpsw"); exit;	
	}	
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
	<meta name="author" content="creativetown.it" />

    <link rel="stylesheet" href="<?php echo $path; ?>asset/css/style.css" type="text/css" />
	<title>Privilege Web App</title>
</head>

<body class="stretched">
	<div id="wrapper" class="clearfix bgrTransparent">
				
		<div class="header">
		<div class="bg"></div>
		<div class="logo">
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
				<a href="./regia.php?logout">
					<p class="text">Logout</p>   
				</a>
			<?php } ?>
		</div>
		</div>

		<?php if($cover != "") { ?>
			<style>
				.header .bg {
					width: 100%;
					height: 100%;
					background-image: url("<?php echo "asset/event-covers/".$cover; ?>") !important;
					background-size: cover;
				}
			</style>
		<?php } ?>
				
        <div class="about">
            <div class="title">
                <h2 class="text">Welcome to Privilege Web App!</h2>
            </div>
            <div class="body" style="min-height:165px">   
				
				<?php 
				if(isset($_SESSION['login_evento'])){ 
					
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
								$r_sondaggi= mysqli_query($con,$sql_sondaggi);
								
								while($polls_master= mysqli_fetch_array($r_sondaggi)){ 
									fputcsv($fp, $polls_master);
								}
								
								fclose($fp);

								if(isset($_GET["export"])){ echo "<div style='margin-bottom:20px;'> Export ready, click <a href='export.csv' style='color:brown;'> here for download </a> </div>"; }

								if(isset($_GET["reset"])){
									$sql_sondaggi="TRUNCATE TABLE questions";
									mysqli_query($con,$sql_sondaggi);
								}
							?>

							<div class="offset-md-1 col-md-10 paddingMobile pt-5"> 
								<div class="card mb-3">
									<div class="card-body <?php echo $active_type; ?> " style="padding:5px;">
										<div class="cont_risposta">
											<?php if($active_type == "ALL" || $active_type == "") { ?> 
												<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
											<?php } ?>
										</div>
										<div class="cont_risposta" style="float:left;">
											<div class="active-status" style=""> <?php echo $active_title; ?> </div>
											<?php if($active_type != "DONE") { ?> 
												<a href="regia.php?filter=done"> <div class="pools-button small done" style=""> PROCESSED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "DELETED") { ?> 
												<a href="regia.php?filter=deleted"> <div class="pools-button small alert" style=""> DELETED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "SELECTED") { ?> 
												<a href="regia.php?filter=selected"> <div class="pools-button small selected" style=""> SELECTED <BR/> QUESTIONS </div> </a>
											<?php } ?>
											<?php if($active_type != "ALL") { ?> 
												<a href="regia.php?filter=all"> <div class="pools-button small all" style=""> LIVE <BR/> QUESTIONS </div> </a>
											<?php } ?>
											</div>
									</div>

								</div>

								<div class="" id="alert-questions-attivate">
									
								</div>
							</div>
							
							<div class="offset-md-1 col-md-10 paddingMobile pt-5" id="contDomande">

								


									<?php
									$sql_domande="Select * from questions where event_id='".$ID_EVENTO."' ".$supp_condition." order by question_timestamp desc";
									$r_domande= mysqli_query($con,$sql_domande);
									
									while($questions= mysqli_fetch_array($r_domande)){
									if($questions['hidden_question']=="0"){ 
											if($questions['question_status']=="y"){$col_bg="#2aa900";}
											if($questions['question_status']=="n"){$col_bg="#e81f1f";}
											if($questions['question_status']=="d"){$col_bg="#fbfb00";}
											if($questions['question_status']==""){$col_bg="transparent";}
											?>
											<div class="card mb-3 <?php if($questions['attiva']){echo "domanda-attiva";} ?>" id="domanda_<?php echo $questions['ID'];?>">
												<div class="card-header">
													<div class="row">
														<div class="header-info sx"><strong><?php echo $questions['ID'];?>)</strong> h <strong><?php echo ora_X_DB($questions['question_timestamp']);?></strong></div>
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
				<?php }else{?>
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
									
									<form name="loginEvento" id="loginEvento" action="regia.php?filter=all" method="post">
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
			


				<div class="offset-md-1 col-md-10 paddingMobile pt-5"> >
            </div>
        </div>
        

       
    
    </div>
</body>