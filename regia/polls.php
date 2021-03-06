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

$time_attuale= time();
?>
<!DOCTYPE html>
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
		.answers-content {
			width: 49%;
			float: left;
		}

		@media screen and (max-width: 992px) {
			.answers-content {
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
				 <span class="menu "> <a href="index.php?filter=all"> Questions </a> </span> 
				 <span class="menu active"> <a href="polls.php"> Polls </a> </span> 
            </div>
            <div class="body" style="min-height:165px">
            <?php 
            if($autorizzato){ ?>
            <div class="row">
        
                <div class="col-md-12 bg">
                    <div class="row">
						
						<?php
							
							$fp = fopen('export.csv', 'w');

							$sql_sondaggi="Select * from polls_master";
							$polls_ans= mysqli_query($con,$sql_sondaggi);
							
							while($polls_master= mysqli_fetch_array($polls_ans)){ 
								fputcsv($fp, $polls_master);
							}
							
							fclose($fp);
							
							if(isset($_GET["export"])){
								echo "<div style='margin-bottom:20px;'> Download will start automatically, if not, click <a href='export.csv' style='color:brown;'> here to download </a> </div>";
							}

							if(isset($_GET["reset"])){
								$sql_sondaggi="TRUNCATE TABLE polls_master";
								mysqli_query($con,$sql_sondaggi);
							}
						?>


                        <div>

							<div class="card">
								<div class="card-body" style="padding:5px;">
									<div>
										<a href="polls-panel.php"> <div class="pools-button" style="float:left;"> Pools panel </div> </a>
										<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
										<a href="polls.php?reset=all" onclick="return confirm('Are you sure? All polls will be deleted');"> <div class="pools-button alert" style=""> Reset </div> </a>
									</div>
								</div>
							</div>					


                            <?php
                            $sql_sondaggi="Select * from polls_master order by ID desc";
                            $polls_ans= mysqli_query($con,$sql_sondaggi);
                            $question_id="";
                            $poll_closed="";
                            while($polls_master= mysqli_fetch_array($polls_ans)){ ?>
                                <div class="card mb-3">
                                    <div class="card-header" >
                                        <strong><?php echo $polls_master['domanda'];?></strong>
                                        <div class="float-right" id="stato_domanda_<?php echo $polls_master['ID'];?>">
                                                    <?php if($polls_master['attiva']==1 && $polls_master['disactivation_date']>=$time_attuale && $polls_master['activation_date']<$time_attuale){ ?>
                                                <img src="<?php echo $path;?>../asset/images/active.png" width="25px" > until <?php echo date("H:i", $polls_master['disactivation_date']);?>
                                            <?php }else{ ?>
                                                <img src="<?php echo $path;?>../asset/images/inactive.png" width="25px" >
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="card-body" id="contDomande_<?php echo $polls_master['ID'];?>">
                                        <?php if($polls_master['tipo']=="risp_aperta"){ 
                                            $sql_risp_aperte=mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' order by answer_datetime DESC");
                                            while($risp_aperte=mysqli_fetch_array($sql_risp_aperte)){ ?>
                                                <div >
                                                    <div ><strong><?php echo formatDateAndTime($risp_aperte['answer_datetime']);?></strong></div>
                                                    <?php echo nl2br($risp_aperte['poll_answer']);?>
                                                </div>
                                            <?php } 
                                        
                                            $sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' order by answer_datetime desc LIMIT 1"));
                                            $ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
                    
                                            if(!isset($ultimo_id_inserito)){
                                                $ultimo_id_inserito=0;	
                                            } 
                                            $question_id.= $polls_master['ID'].",".$ultimo_id_inserito."|";
                                            ?>
                                            <div  style="border-bottom:none;"></div>

                                        
                                        <?php } //fine risposta aperta ?>
                                            
                                        <?php if($polls_master['tipo']=="risp_multipla"){
											echo '<div class="answers-content">';

                                            $poll_closed.= $polls_master['ID']."-";
                                            $tot_risp_multiple=mysqli_num_rows(mysqli_query($con,"select ID from polls_answers where polls_id='$polls_master[ID]'"));
                                            ?>
                                            
                                            <?php if($polls_master['answer_1']!=""){ 
                                                $cont_risp_1=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_1'"));
                                                $poll_closed.="1,"; 
                                                if($cont_risp_1>0){
                                                    $perc_risp_1=(100*$cont_risp_1)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_1=0;	
                                                }
                                                $perc_risp_1= number_format($perc_risp_1,2,'.','');
                                                ?>
                                                <div>
                                                    <div>
                                                        <?php echo $polls_master['answer_1'];?>:
                                                        <span id="cont_risposta_1_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_1;?> (<?php echo $perc_risp_1;?>%)</span>
                                                    </div>
                                                </div>                                                
                                            <?php } ?>
                                            <?php if($polls_master['answer_2']!=""){ 
                                                    $cont_risp_2=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_2'"));
                                                    $poll_closed.="2,"; 
                                                    if($cont_risp_2>0){
                                                        $perc_risp_2=(100*$cont_risp_2)/$tot_risp_multiple; 
                                                    }else{
                                                        $perc_risp_2=0;	
                                                    }
                                                    $perc_risp_2= number_format($perc_risp_2,2,'.','');																	
                                                    ?>
                                                    <div>
                                                        <div>
                                                            <?php echo $polls_master['answer_2'];?>:
                                                            <span id="cont_risposta_2_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_2;?> (<?php echo $perc_risp_2;?>%)</span>
                                                        </div>
                                                    </div>                                                
                                            <?php } ?>
                                            <?php if($polls_master['answer_3']!=""){ 
                                                $cont_risp_3=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_3'"));
                                                $poll_closed.="3,";
                                                if($cont_risp_3>0){
                                                    $perc_risp_3=(100*$cont_risp_3)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_3=0;	
                                                }
                                                $perc_risp_3= number_format($perc_risp_3,2,'.','');																	
                                                ?>
                                                <div >
                                                    <div>
                                                        <?php echo $polls_master['answer_3'];?>:
                                                        <span id="cont_risposta_3_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_3;?> (<?php echo $perc_risp_3;?>%)</span>
                                                    </div>
                                                </div>                                                
                                            <?php } ?>
                                            <?php if($polls_master['answer_4']!=""){
                                                $cont_risp_4=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_4'"));
                                                $poll_closed.="4,";
                                                if($cont_risp_4>0){
                                                    $perc_risp_4=(100*$cont_risp_4)/$tot_risp_multiple; 
                                                }else{
                                                    $perc_risp_4=0;	
                                                }
                                                $perc_risp_4= number_format($perc_risp_4,2,'.','');																	
                                                ?>
                                                <div >
                                                    <div>
                                                        <?php echo $polls_master['answer_4'];?>:
                                                        <span id="cont_risposta_4_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_4;?> (<?php echo $perc_risp_4;?>%)</span>
                                                    </div>
                                                </div>                                                
											<?php } ?> 
											
											<?php if($polls_master['answer_5']!=""){
												$cont_risp_5=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_5'"));
												$poll_closed.="5,";
												if($cont_risp_5>0){
													$perc_risp_5=(100*$cont_risp_5)/$tot_risp_multiple; 
												}else{
													$perc_risp_5=0;	
												}
												$perc_risp_5= number_format($perc_risp_5,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_5'];?>:
														<span id="cont_risposta_5_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_5;?> (<?php echo $perc_risp_5;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($polls_master['answer_6']!=""){
												$cont_risp_6=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_6'"));
												$poll_closed.="6,";
												if($cont_risp_6>0){
													$perc_risp_6=(100*$cont_risp_6)/$tot_risp_multiple; 
												}else{
													$perc_risp_6=0;	
												}
												$perc_risp_6= number_format($perc_risp_6,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_6'];?>:
														<span id="cont_risposta_6_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_6;?> (<?php echo $perc_risp_6;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($polls_master['answer_7']!=""){
												$cont_risp_7=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_7'"));
												$poll_closed.="7,";
												if($cont_risp_7>0){
													$perc_risp_7=(100*$cont_risp_7)/$tot_risp_multiple; 
												}else{
													$perc_risp_7=0;	
												}
												$perc_risp_7= number_format($perc_risp_7,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_7'];?>:
														<span id="cont_risposta_7_<?php echo $polls_master['ID'];?>" ><?php echo $cont_risp_7;?> (<?php echo $perc_risp_7;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($polls_master['answer_8']!=""){
												$cont_risp_8=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_8'"));
												$poll_closed.="8,";
												if($cont_risp_8>0){
													$perc_risp_8=(100*$cont_risp_8)/$tot_risp_multiple; 
												}else{
													$perc_risp_8=0;	
												}
												$perc_risp_8= number_format($perc_risp_8,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_8'];?>:
														<span id="cont_risposta_8_<?php echo $polls_master['ID'];?>"><?php echo $cont_risp_8;?> (<?php echo $perc_risp_8;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($polls_master['answer_9']!=""){
												$cont_risp_9=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_9'"));
												$poll_closed.="9,";
												if($cont_risp_9>0){
													$perc_risp_9=(100*$cont_risp_9)/$tot_risp_multiple; 
												}else{
													$perc_risp_9=0;	
												}
												$perc_risp_9= number_format($perc_risp_9,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_9'];?>:
														<span id="cont_risposta_9_<?php echo $polls_master['ID'];?>"><?php echo $cont_risp_9;?> (<?php echo $perc_risp_9;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>

											<?php if($polls_master['answer_10']!=""){
												$cont_risp_10=mysqli_num_rows(mysqli_query($con,"Select * from polls_answers where polls_id='$polls_master[ID]' and poll_answer='risposta_10'"));
												$poll_closed.="10,";
												if($cont_risp_10>0){
													$perc_risp_10=(100*$cont_risp_10)/$tot_risp_multiple; 
												}else{
													$perc_risp_10=0;	
												}
												$perc_risp_10= number_format($perc_risp_10,2,'.','');																	
												?>
												<div >
													<div>
														<?php echo $polls_master['answer_10'];?>:
														<span id="cont_risposta_10_<?php echo $polls_master['ID'];?>"><?php echo $cont_risp_10;?> (<?php echo $perc_risp_10;?>%)</span>
													</div>
												</div>                                                
											<?php } ?>
											                                                                                                                                                        
                                        <?php 
                                            $poll_closed=substr($poll_closed, 0, strlen($poll_closed)-1);
											$poll_closed.="|";
											
											//retrieve data for the pie chart
											$id_sondaggio = $polls_master["ID"];
											$an1 = $an2 = $an3 = $an4 = $an5 = $an6 = $an7 = $an8 = $an9 = $an10 = 0;
											$query = mysqli_query($con,"
												SELECT poll_answer,COUNT(*) AS conta
												FROM `polls_answers` 
												WHERE polls_id= ".$id_sondaggio."
												GROUP BY poll_answer
											");
											
											while($poll_answers= mysqli_fetch_array($query)){
												switch ($poll_answers["poll_answer"]){
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

										
											$stack = array();
											$id_sondaggio = $polls_master["ID"];
											$query = mysqli_query($con,"
												SELECT * 
												FROM `polls_master` 
												WHERE ID = ".$id_sondaggio."
												LIMIT 1
											");
											$poll_temp = mysqli_fetch_array($query);
											for($i=1;$i<11;$i++){
												if($poll_temp["answer_".$i] != ""){
													array_push($stack, $poll_temp["answer_".$i] );
												}
											}
											
											$colors = array("red", "orange","yellow", "green","blue", "purple","grey", "darkslategray","yellowgreen", "lightblue");
											
										
											echo '</div>';
											echo '<div class="answers-content">';
										?>		
											<div id="canvas-holder_<?php echo $polls_master['ID'];?>" class="meta-graph" >
												<canvas id="chart-area_<?php echo $polls_master['ID'];?>"></canvas>
											</div>

											<script>
												var randomScalingFactor = function() {
													return Math.round(Math.random() * 100);
												};

												var config_<?php echo $polls_master['ID'];?> = {
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

												
												var ctx = document.getElementById('chart-area_<?php echo $polls_master['ID'];?>').getContext('2d');
												window.myPie = new Chart(ctx, config_<?php echo $polls_master['ID'];?>);
											

												var colorNames = Object.keys(window.chartColors);

											</script>

										<?php
											echo '</div>';
										} 
										?>     
                                        
                                    </div>
                                </div>    
                            <?php } ?>
                        <?php }  ?>                    
                        </div>
                        
                    </div> 
                </div> 
            </div>
    
    
            </div>
        </div>

        </section>		
        
        
       <?php
	   $question_id=substr($question_id, 0, strlen($question_id)-1);
	   $poll_closed=substr($poll_closed, 0, strlen($poll_closed)-1);
	   ?> 
		<input type="hidden" name="current_event_id" id="current_event_id" value="<?php echo $ID_EVENTO;?>">
		<input type="hidden" name="question_id" id="question_id" value="<?php echo $question_id;?>">                                                    
		<input type="hidden" name="poll_closed" id="poll_closed" value="<?php echo $poll_closed;?>">                                                    
     

	</div>


	<div id="gotoTop" class="icon-angle-up"></div>

    <script type="text/javascript">
	window.setInterval(function(){
		var current_event_id = $('#current_event_id').val();

	
		var question_id = $('#question_id').val();
		$.ajax({
			url: "../api/get-open-answers.php",
			type: "get",
			crossDomain: true,
			data: 'question_id='+question_id + "&current_event_id=" + current_event_id ,
			success: function(data){
				
				var ultimo_ID_new="";
				
				var polls_master= data.split('|');
				
				for (var i = 0; i < polls_master.length; i++) {
					var answers= polls_master[i].split('***');
					ultimo_ID_new+=answers[0]+","+answers[1]+"|";
					
					if(typeof answers[2]!=="undefined")	{			
						var answers_poll= answers[2].split('$$$');
					
						for (var j = 0; j < answers_poll.length; j++) {
							$("#contDomande_"+answers[0]+" .cont_risposta:first").before(answers_poll[j]);
						}
					}
				}
				
				ultimo_ID_new=ultimo_ID_new.substring(0,ultimo_ID_new.length-1);
				$("#question_id").val(ultimo_ID_new);
			
			},
			error: function () {
			}
		});	
		
	
		var poll_closed= $('#poll_closed').val();
		$.ajax({
			url: "../api/get-multiple-answers.php",
			type: "get",
			crossDomain: true,
			data: 'poll_closed='+poll_closed + "&current_event_id=" + current_event_id ,
			success: function(data){
				var polls_master= data.split('|');

				for(var a=0;a<polls_master.length;a++){
					var poll_array= polls_master[a].split('_');
					answers_stat = poll_array[1].split(',');
					for(var b=0;b<answers_stat.length;b++){
						var current= answers_stat[b].split('-');
						
						$("#cont_risposta_"+current[0]+"_"+poll_array[0]).html(current[1]);
					}
				}
			},
			error: function () {
			}
		});			
				
		$.ajax({
			url: "../api/get-all-question-status.php",
			type: "get",
			crossDomain: true,
			data: "&current_event_id=" + current_event_id,
			success: function(data){
				var question_status= data.split('|');
				for (var k= 0; k < question_status.length; k++) {
					var stato_s= question_status[k].split(',');
					$("#stato_domanda_"+stato_s[0]).html(stato_s[1]);
				}
			},
			error: function () {
			}
		});	
	  
	}, 5000);
	
    </script>    
    
</body>
</html>