<?php 
session_start();
require_once("../server/db.php");
require_once("../server/helper.php");

$tabella_utenti="utenti";

if(isset($_GET['logout'])){
	unset($_SESSION['event_login']);
	header("location:/login.php");
}

$time_attuale= time();
?>
<!DOCTYPE html>
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
		.meta-risposte {
			width: 49%;
			float: left;
		}

		@media screen and (max-width: 992px) {
			.meta-risposte {
				width: 100%;
				float: none;
			}
		}

		.meta-graph {
			width:60%;
		}

		@media screen and (max-width: 992px) {
			.meta-graph {
				width: 100%;
			}
		}

	</style>

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
            if(isset($_SESSION['event_login'])){ ?>
            <div class="row">
        
                <div class="col-md-12 bg">
                    <div class="row">
						
						<?php
							
							$fp = fopen('export.csv', 'w');

							$sql_sondaggi="Select * from sondaggi";
							$r_sondaggi= mysqli_query($con,$sql_sondaggi);
							
							while($sondaggi= mysqli_fetch_array($r_sondaggi)){ 
								fputcsv($fp, $sondaggi);
							}
							
							fclose($fp);
							
							if(isset($_GET["export"])){
								echo "<div style='margin-bottom:20px;'> Download will start automatically, if not, click <a href='export.csv' style='color:brown;'> here to download </a> </div>";
							}

							if(isset($_GET["reset"])){
								$sql_sondaggi="TRUNCATE TABLE sondaggi";
								mysqli_query($con,$sql_sondaggi);
							}
						?>


                        <div class="offset-md-1 col-md-10 paddingMobile pt-5" >

							<div class="card mb-3">
								<div class="card-body" style="padding:5px;">
									<div class="cont_risposta">
										<a href="polls-panel.php"> <div class="pools-button" style="float:left;"> Pools panel </div> </a>
										<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
										<a href="regia_sondaggi.php?reset=all" onclick="return confirm('Are you sure? All polls will be deleted');"> <div class="pools-button alert" style=""> Reset </div> </a>
									</div>
								</div>
							</div>					


                            <?php
                            $sql_sondaggi="Select * from sondaggi order by ID desc";
                            $r_sondaggi= mysqli_query($con,$sql_sondaggi);
                            $ultimi_id_domande_aperte="";
                            $risposte_multiple="";
                            while($sondaggi= mysqli_fetch_array($r_sondaggi)){ ?>
                                <div class="card mb-3">
                                    <div class="card-header" >
                                        <strong><?php echo $sondaggi['domanda'];?></strong>
                                        <div class="float-right" id="stato_domanda_<?php echo $sondaggi['ID'];?>">
                                                    <?php if($sondaggi['attiva']==1 && $sondaggi['data_disattivazione']>=$time_attuale && $sondaggi['data_attivazione']<$time_attuale){ ?>
                                                <img src="<?php echo $path;?>../asset/images/active.png" width="25px" class="vertTop"> until <?php echo date("H:i", $sondaggi['data_disattivazione']);?>
                                            <?php }else{ ?>
                                                <img src="<?php echo $path;?>../asset/images/inactive.png" width="25px" class="vertTop">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="card-body" id="contDomande_<?php echo $sondaggi['ID'];?>">
                                        <?php if($sondaggi['tipo']=="risp_aperta"){ 
                                            $sql_risp_aperte=mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' order by s_data_risposta DESC");
                                            while($risp_aperte=mysqli_fetch_array($sql_risp_aperte)){ ?>
                                                <div class="cont_risposta">
                                                    <div class="fontWeight700"><strong><?php echo formatDateAndTime($risp_aperte['s_data_risposta']);?></strong></div>
                                                    <?php echo nl2br($risp_aperte['s_risposta']);?>
                                                </div>
                                            <?php } 
                                        
                                            $sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' order by s_data_risposta desc LIMIT 1"));
                                            $ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
                    
                                            if(!isset($ultimo_id_inserito)){
                                                $ultimo_id_inserito=0;	
                                            } 
                                            $ultimi_id_domande_aperte.= $sondaggi['ID'].",".$ultimo_id_inserito."|";
                                            ?>
                                            <div class="cont_risposta" style="border-bottom:none;"></div>

                                        
                                        <?php } //fine risposta aperta ?>
                                            
                                        <?php if($sondaggi['tipo']=="risp_multipla"){
											echo '<div class="meta-risposte">';

                                            $risposte_multiple.= $sondaggi['ID']."-";
                                            $tot_risp_multiple=mysqli_num_rows(mysqli_query($con,"select ID from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]'"));
                                            ?>
                                            
                                            <?php if($sondaggi['risposta_1']!=""){ 
                                                $cont_risp_1=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_1'"));
                                                $risposte_multiple.="1,"; 
                                                if($cont_risp_1>0){
                                                    $perc_risp_1=(100*$cont_risp_1)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_1=0;	
                                                }
                                                $perc_risp_1= number_format($perc_risp_1,2,'.','');
                                                ?>
                                                <div class="cont_risposta_multipla" >
                                                    <div>
                                                        <?php echo $sondaggi['risposta_1'];?>:
                                                        <span id="cont_risposta_1_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_1;?> (<?php echo $perc_risp_1;?>%)</span>
                                                    </div>
                                                </div>                                                
                                            <?php } ?>
                                            <?php if($sondaggi['risposta_2']!=""){ 
                                                    $cont_risp_2=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_2'"));
                                                    $risposte_multiple.="2,"; 
                                                    if($cont_risp_2>0){
                                                        $perc_risp_2=(100*$cont_risp_2)/$tot_risp_multiple; 
                                                    }else{
                                                        $perc_risp_2=0;	
                                                    }
                                                    $perc_risp_2= number_format($perc_risp_2,2,'.','');																	
                                                    ?>
                                                    <div class="cont_risposta_multipla">
                                                        <div>
                                                            <?php echo $sondaggi['risposta_2'];?>:
                                                            <span id="cont_risposta_2_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_2;?> (<?php echo $perc_risp_2;?>%)</span>
                                                        </div>
                                                    </div>                                                
                                            <?php } ?>
                                            <?php if($sondaggi['risposta_3']!=""){ 
                                                $cont_risp_3=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_3'"));
                                                $risposte_multiple.="3,";
                                                if($cont_risp_3>0){
                                                    $perc_risp_3=(100*$cont_risp_3)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_3=0;	
                                                }
                                                $perc_risp_3= number_format($perc_risp_3,2,'.','');																	
                                                ?>
                                                <div class="cont_risposta_multipla">
                                                    <div>
                                                        <?php echo $sondaggi['risposta_3'];?>:
                                                        <span id="cont_risposta_3_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_3;?> (<?php echo $perc_risp_3;?>%)</span>
                                                    </div>
                                                </div>                                                
                                            <?php } ?>
                                            <?php if($sondaggi['risposta_4']!=""){
                                                $cont_risp_4=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_4'"));
                                                $risposte_multiple.="4,";
                                                if($cont_risp_4>0){
                                                    $perc_risp_4=(100*$cont_risp_4)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_4=0;	
                                                }
                                                $perc_risp_4= number_format($perc_risp_4,2,'.','');																	
                                                ?>
                                                <div class="cont_risposta_multipla">
                                                    <div>
                                                        <?php echo $sondaggi['risposta_4'];?>:
                                                        <span id="cont_risposta_4_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_4;?> (<?php echo $perc_risp_4;?>%)</span>
                                                    </div>
                                                </div>                                                
											<?php } ?> 
											
											<?php if($sondaggi['risposta_5']!=""){
												$cont_risp_5=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_5'"));
												$risposte_multiple.="5,";
												if($cont_risp_5>0){
													$perc_risp_5=(100*$cont_risp_5)/$tot_risp_multiple; 
												}else{
													$perc_risp_5=0;	
												}
												$perc_risp_5= number_format($perc_risp_5,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_5'];?>:
														<span id="cont_risposta_5_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_5;?> (<?php echo $perc_risp_5;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($sondaggi['risposta_6']!=""){
												$cont_risp_6=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_6'"));
												$risposte_multiple.="6,";
												if($cont_risp_6>0){
													$perc_risp_6=(100*$cont_risp_6)/$tot_risp_multiple; 
												}else{
													$perc_risp_6=0;	
												}
												$perc_risp_6= number_format($perc_risp_6,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_6'];?>:
														<span id="cont_risposta_6_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_6;?> (<?php echo $perc_risp_6;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($sondaggi['risposta_7']!=""){
												$cont_risp_7=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_7'"));
												$risposte_multiple.="7,";
												if($cont_risp_7>0){
													$perc_risp_7=(100*$cont_risp_7)/$tot_risp_multiple; 
												}else{
													$perc_risp_7=0;	
												}
												$perc_risp_7= number_format($perc_risp_7,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_7'];?>:
														<span id="cont_risposta_7_<?php echo $sondaggi['ID'];?>" class="fontWeight700"><?php echo $cont_risp_7;?> (<?php echo $perc_risp_7;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($sondaggi['risposta_8']!=""){
												$cont_risp_8=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_8'"));
												$risposte_multiple.="8,";
												if($cont_risp_8>0){
													$perc_risp_8=(100*$cont_risp_8)/$tot_risp_multiple; 
												}else{
													$perc_risp_8=0;	
												}
												$perc_risp_8= number_format($perc_risp_8,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_8'];?>:
														<span id="cont_risposta_8_<?php echo $sondaggi['ID'];?>" class="fontWeight800"><?php echo $cont_risp_8;?> (<?php echo $perc_risp_8;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($sondaggi['risposta_9']!=""){
												$cont_risp_9=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_9'"));
												$risposte_multiple.="9,";
												if($cont_risp_9>0){
													$perc_risp_9=(100*$cont_risp_9)/$tot_risp_multiple; 
												}else{
													$perc_risp_9=0;	
												}
												$perc_risp_9= number_format($perc_risp_9,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_9'];?>:
														<span id="cont_risposta_9_<?php echo $sondaggi['ID'];?>" class="fontWeight900"><?php echo $cont_risp_9;?> (<?php echo $perc_risp_9;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($sondaggi['risposta_10']!=""){
												$cont_risp_10=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_sondaggio='$sondaggi[ID]' and s_risposta='risposta_10'"));
												$risposte_multiple.="10,";
												if($cont_risp_10>0){
													$perc_risp_10=(100*$cont_risp_10)/$tot_risp_multiple; 
												}else{
													$perc_risp_10=0;	
												}
												$perc_risp_10= number_format($perc_risp_10,2,'.','');																	
												?>
												<div class="cont_risposta_multipla">
													<div>
														<?php echo $sondaggi['risposta_10'];?>:
														<span id="cont_risposta_10_<?php echo $sondaggi['ID'];?>" class="fontWeight1000"><?php echo $cont_risp_10;?> (<?php echo $perc_risp_10;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>
											                                                                                                                                                        
                                        <?php 
                                            $risposte_multiple=substr($risposte_multiple, 0, strlen($risposte_multiple)-1);
											$risposte_multiple.="|";
											
											//retrieve data for the pie chart
											$id_sondaggio = $sondaggi["ID"];
											$an1 = $an2 = $an3 = $an4 = $an5 = $an6 = $an7 = $an8 = $an9 = $an10 = 0;
											$query = mysqli_query($con,"
												SELECT s_risposta,COUNT(*) AS conta
												FROM `sondaggi_risposte` 
												WHERE s_ID_sondaggio= ".$id_sondaggio."
												GROUP BY s_risposta
											");
											while($poll_answers= mysqli_fetch_array($query)){
												switch ($poll_answers["s_risposta"]){
													case "risposta_1" :
														$an1 = $poll_answers["conta"];
														break;
													case "risposta_2" :
														$an2 = $poll_answers["conta"];
														break;
													case "risposta_3" :
														$an3 = $poll_answers["conta"];
														break;
													case "risposta_4" :
														$an4 = $poll_answers["conta"];
														break;
													case "risposta_5" :
														$an5 = $poll_answers["conta"];
														break;
													case "risposta_6" :
														$an6 = $poll_answers["conta"];
														break;
													case "risposta_7" :
														$an7 = $poll_answers["conta"];
														break;
													case "risposta_8" :
														$an8 = $poll_answers["conta"];
														break;
													case "risposta_9" :
														$an9 = $poll_answers["conta"];
														break;
													case "risposta_10" :
														$an10 = $poll_answers["conta"];
														break;
												}
											}

											//prepare answer for pie chart
											$stack = array();
											$id_sondaggio = $sondaggi["ID"];
											$query = mysqli_query($con,"
												SELECT * 
												FROM `sondaggi` 
												WHERE ID = ".$id_sondaggio."
												LIMIT 1
											");
											$poll_temp = mysqli_fetch_array($query);
											for($i=1;$i<11;$i++){
												if($poll_temp["risposta_".$i] != ""){
													array_push($stack, $poll_temp["risposta_".$i] );
												}
											}
											//print_r($stack);
											$colors = array("red", "orange","yellow", "green","blue", "purple","grey", "darkslategray","yellowgreen", "lightblue");
											
										
											echo '</div>';
											echo '<div class="meta-risposte">';
										?>		
											<div id="canvas-holder_<?php echo $sondaggi['ID'];?>" class="meta-graph" >
												<canvas id="chart-area_<?php echo $sondaggi['ID'];?>"></canvas>
											</div>

											<script>
												var randomScalingFactor = function() {
													return Math.round(Math.random() * 100);
												};

												var config_<?php echo $sondaggi['ID'];?> = {
													type: 'pie',
													data: {
														datasets: [{
															data: [
																<?php 
																	$first = true;
																	for($i=0; $i< sizeof($stack); $i++){
																		if(!$first) { echo ","; }
																		switch ($i+1){
																			case "1":
																				echo $an1;
																				break;
																			case "2":
																				echo $an2;
																				break;
																			case "3":
																				echo $an3;
																				break;
																			case "4":
																				echo $an4;
																				break;
																			case "5":
																				echo $an5;
																				break;
																			case "6":
																				echo $an6;
																				break;
																			case "7":
																				echo $an7;
																				break;
																			case "8":
																				echo $an8;
																				break;
																			case "9":
																				echo $an9;
																				break;
																			case "10":
																				echo $an10;
																				break;
																		}
																		$first = false;
																	}
																?>
															],
															backgroundColor: [
																<?php
																	for($i=0; $i< sizeof($stack); $i++){
																		echo "window.chartColors.".$colors[$i].",";
																	}
																?>
															],
															label: 'Dataset 1'
														}],
														labels: [
															<?php
																for($i=1; $i<= sizeof($stack); $i++){
																	echo $i.",";
																}
															?>
														]
													},
													options: {
														responsive: true
													}
												};

												
												var ctx = document.getElementById('chart-area_<?php echo $sondaggi['ID'];?>').getContext('2d');
												window.myPie = new Chart(ctx, config_<?php echo $sondaggi['ID'];?>);
											

												var colorNames = Object.keys(window.chartColors);

											</script>

										<?php
											echo '</div>';
										} 
										?>     
                                        
                                    </div>
                                </div>    
                            <?php } ?>
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
        
        
       <?php
	   $ultimi_id_domande_aperte=substr($ultimi_id_domande_aperte, 0, strlen($ultimi_id_domande_aperte)-1);
	   $risposte_multiple=substr($risposte_multiple, 0, strlen($risposte_multiple)-1);
	   ?> 
		<input type="hidden" name="tab_utenti" id="tab_utenti" value="<?php echo $tabella_utenti;?>">
		<input type="hidden" name="rif_evento" id="rif_evento" value="<?php echo $ID_EVENTO;?>">
		<input type="hidden" name="ultimi_id_domande_aperte" id="ultimi_id_domande_aperte" value="<?php echo $ultimi_id_domande_aperte;?>">                                                    
		<input type="hidden" name="risposte_multiple" id="risposte_multiple" value="<?php echo $risposte_multiple;?>">                                                    
        <!-- #content end -->

	</div><!-- #wrapper end -->

	<!-- Go To Top
	============================================= -->
	<div id="gotoTop" class="icon-angle-up"></div>

	<!-- External JavaScripts
	============================================= -->
	<script src="<?php echo $path;?>/js/jquery.js"></script>
	<script src="<?php echo $path;?>/js/plugins.min.js"></script>
	<script src="<?php echo $path;?>/js/functions.js"></script>

	<!-- Footer Scripts
	============================================= -->
    <script type="text/javascript">
	window.setInterval(function(){
		var rif_evento = $('#rif_evento').val();
		var tab_utenti = $('#tab_utenti').val();

		// AGGIORNAMENTO REALTIME DOMANDE A RISPOSTA LIBERA
		var ultimi_id_domande_aperte = $('#ultimi_id_domande_aperte').val();
		$.ajax({
			url: "../api/get-open-answers.php",
			type: "get",
			crossDomain: true,
			data: 'ultimi_id_domande_aperte='+ultimi_id_domande_aperte + "&rif_evento=" + rif_evento + "&tab_utenti=" + tab_utenti,
			success: function(data){
				//console.log(data);
				
				var ultimo_ID_new="";
				
				var sondaggi= data.split('|');
				
				for (var i = 0; i < sondaggi.length; i++) {
					//console.log(sondaggi[i]+"_____________");	
					var risposte_sondaggio= sondaggi[i].split('***');
					var id_sondaggio= risposte_sondaggio[0];
					var ultima_risposta_sondaggio= risposte_sondaggio[1];
					
					ultimo_ID_new+=id_sondaggio+","+ultima_risposta_sondaggio+"|";
					
					//console.log(id_sondaggio+"::"+risposte_sondaggio[2]+"---");
					
					if(typeof risposte_sondaggio[2]!=="undefined")	{			
						var risposte= risposte_sondaggio[2].split('$$$');
					
						for (var j = 0; j < risposte.length; j++) {
							$("#contDomande_"+id_sondaggio+" .cont_risposta:first").before(risposte[j]);
						}
					}
				}
				
				ultimo_ID_new=ultimo_ID_new.substring(0,ultimo_ID_new.length-1);
				$("#ultimi_id_domande_aperte").val(ultimo_ID_new);
			
			},
			error: function () {
			}
		});	
		
		// AGGIORNAMENTO REALTIME DOMANDE A RISPOSTA MULTIPLA
		var risposte_multiple= $('#risposte_multiple').val();
		$.ajax({
			url: "../api/get-multiple-answers.php",
			type: "get",
			crossDomain: true,
			data: 'risposte_multiple='+risposte_multiple + "&rif_evento=" + rif_evento + "&tab_utenti=" + tab_utenti,
			success: function(data){
				//console.log(data);
				var sondaggi= data.split('|');

				for(var a=0;a<sondaggi.length;a++){
					var array_sondaggio= sondaggi[a].split('_');
					sondaggio= array_sondaggio[0];
					risposte= array_sondaggio[1].split(',');
					
					//console.log(sondaggio+":\n");
					
					for(var b=0;b<risposte.length;b++){
						var risp= risposte[b].split('-');
						//console.log("cont_risposta_"+risp[0]+"_"+sondaggio+"="+risp[1]+"\n");	
						
						$("#cont_risposta_"+risp[0]+"_"+sondaggio).html(risp[1]);
					}
				}
			},
			error: function () {
			}
		});			
				
		//AGGIORNO IN REALTIME GLI STATI DELLE DOMANDE
		$.ajax({
			url: "../api/get-all-questions-status.php",
			type: "get",
			crossDomain: true,
			data: "&rif_evento=" + rif_evento + "&tab_utenti=" + tab_utenti,
			success: function(data){
				//console.log(data);
				var stati_sondaggi= data.split('|');
				for (var k= 0; k < stati_sondaggi.length; k++) {
					var stato_s= stati_sondaggi[k].split(',');
					//console.log(stato_s[0]+":"+stato_s[1]);
					$("#stato_domanda_"+stato_s[0]).html(stato_s[1]);
				}
			},
			error: function () {
			}
		});	
	  
	}, 5000);
	
	// 20000 -> 20 sec
    </script>    
    
</body>
</html>