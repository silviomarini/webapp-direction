<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="creativetown.it" />

    <link rel="stylesheet" href="<?php echo $path; ?>asset/css/style.css" type="text/css" />
	<title>Privilege Web App</title>
</head>

<body>
	<div id="wrapper" class="clearfix bgrTransparent">
				
		<div class="header">
		<div class="bg"></div>
		<div class="logo">
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
                <a class="item" href="#"><span>Polls</span></a>
				<a class="item" href="#"><span>Questions</span></a>
            </div>
        </div>
	</div>

	<div id="polls" style="display:none;">
        <div class="col-md-12 paddingMobile">
            <div class="card mb-3" id="contDomande">
                <?php
                $time_attuale=time();
                $contr_sondaggio_attivo=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi where attiva=1 and data_disattivazione>=$time_attuale and data_attivazione<$time_attuale"));
                
                if($contr_sondaggio_attivo>0){
                    $sondaggio_attivo=mysqli_fetch_array(mysqli_query($con,"Select * from sondaggi where attiva=1 and data_disattivazione>=$time_attuale and data_attivazione<$time_attuale"));
                    $contr_sondaggio_norisp=mysqli_num_rows(mysqli_query($con,"Select * from sondaggi_risposte where s_ID_partecipante='$_COOKIE[utente_evento]' and s_ID_sondaggio='$sondaggio_attivo[ID]'"));
                    if($contr_sondaggio_norisp==0){ ?>
                        <div class="titolo-domanda"><strong><?php echo $sondaggio_attivo['domanda'];?></strong></div>
                        <div class="card-body">
                            <?php if($sondaggio_attivo['tipo']=="risp_aperta"){ ?>
                                <div class="form-group text-center">
                                    <textarea placeholder="Enter here your answer!" 
                                        name="risposta_aperta" id="risposta_aperta" 
                                        style="width:70%; border: 3px solid rgb(148 49 50);padding:5px;font-family: Tahoma, sans-serif; height:200px;"
                                        row="10"
                                    ></textarea>
                                    
                                </div>
                                <div>
                                    <input type="submit" class="home-button" name="invia_risposta_aperta" id="invia_risposta_aperta" value="Submit the answer" /> 
                                </div>
                            <?php } ?>
                            <?php if($sondaggio_attivo['tipo']=="risp_multipla"){ ?>
                                <?php if($sondaggio_attivo['risposta_1']!=""){?><div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_1" class="trCkLarge"> <?php echo $sondaggio_attivo['risposta_1'];?></label></div><?php } ?>
                                <?php if($sondaggio_attivo['risposta_2']!=""){?><div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_2" class="trCkLarge"> <?php echo $sondaggio_attivo['risposta_2'];?></label></div><?php } ?>
                                <?php if($sondaggio_attivo['risposta_3']!=""){?><div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_3" class="trCkLarge"> <?php echo $sondaggio_attivo['risposta_3'];?></label></div><?php } ?>
                                <?php if($sondaggio_attivo['risposta_4']!=""){?><div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_4" class="trCkLarge"> <?php echo $sondaggio_attivo['risposta_4'];?></label></div><?php } ?>
                                <?php } ?>
                        </div>
                    <?php 
                    } else{echo "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";}// fine if risp sondaggio
                    }else{
                        echo "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
                } ?>
                
                <input type="hidden" name="id_sondaggio" id="id_sondaggio" value="<?php echo $sondaggio_attivo['ID'];?>"> 
        
                <script type="text/javascript"> 
                    // AJAX SALVATAGGIO DOMANDA A RISPOSTA APERTA
                $("#invia_risposta_aperta").click(function() {
                    var risposta_aperta = $('#risposta_aperta').val();
                    risposta_aperta = encodeURIComponent(risposta_aperta);
            
                    var rif_evento = $('#rif_evento').val();
                        var ultimo_ID = $("#ultimo_ID").val();
                    var id_sondaggio = $('#id_sondaggio').val();

                    if(risposta_aperta!=""){
                        $.ajax({
                            url: "ajax_invia_risposta_aperta.php",
                            type: "get",
                            crossDomain: true,
                            data: 'azione=risp_aperta&risposta_aperta=' + risposta_aperta + '&ultimo_ID='+ultimo_ID+ '&rif_evento='+rif_evento+ '&id_sondaggio='+id_sondaggio,
                            success: function(data){
                                //console.log(data);
                                $("#alertDomanda").show();
                                $("#contDomande").hide();
                                $("#risposta_aperta").val('');
                            },
                            error: function () {
                                //alert('Errore AJAX');
                            }
                        });	
                    }else{
                        alert("Inserisci la tua risposta");	
                    }
            
                });	

                // AJAX SALVATAGGIO DOMANDA A RISPOSTA MULTIPLA
                $("input[name='risposte_s']").click(function(){
                    var radioValue = $("input[name='risposte_s']:checked").val();
                    var id_sondaggio= $("#id_sondaggio").val();
                        var ultimo_ID = $("#ultimo_ID").val();
                    var rif_evento = $('#rif_evento').val();

                        $.ajax({
                            url: "ajax_invia_risposta_aperta.php",
                            type: "get",
                            crossDomain: true,
                            data: 'azione=risp_multipla&risposta=' + radioValue + '&ultimo_ID='+ultimo_ID+ '&rif_evento='+rif_evento+ '&id_sondaggio='+id_sondaggio,
                            success: function(data){
                                console.log(data);
                                $("#alertDomanda").show();
                                $("#contDomande").hide();
                                //$("#risposta_aperta").val('');
                            },
                            error: function () {
                                //alert('Errore AJAX');
                            }
                        });											
                });
                </script>
            </div>  
            <input type="hidden" name="ultimo_ID" id="ultimo_ID" value="<?php echo @$sondaggio_attivo['ID']; ?>">
            <input type="hidden" name="rif_evento" id="rif_evento" value="1">
            
            <div class="submit-response success" style="display:none;" id="alertDomanda">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <strong>Answer sent!</strong>
            </div>
        </div>
	</div>

	<div id="questions" style="display:none;">
        <div class="about">
            <div class="title">
				<a href="index.php"><h4 style="color:black;">  
					<i class="fa fa-arrow-circle-left"></i> Back	
				</h4> </a>
                <h2 class="text">Enter your question below</h2>
            </div>
            <div class="body">   
				<div class="col-md-10 paddingMobile " id="chat_evento">

					<form id="formDomanda" action="">
						<div>
							<div >
								<textarea placeholder="Write here, feel free to add your name." 
										name="domanda_evento" 
										id="domanda_evento"
										style="width:70%; border: 3px solid rgb(148 49 50);
										padding:5px;font-family: Tahoma, sans-serif; height:200px;
										font-size: x-large;"
										row="10"
									></textarea>
							</div>
						</div>
						<div class="submit-response success" style="display:none;" id="alertDomanda2">
							<strong>Question sent!</strong>
						</div> 
						
						<div>
							<input type="hidden" name="tokenUtente" id="tokenUtente" value="<?php echo @$_SESSION['login_ID'];?>">
							<!--input type="submit" name="inviaDomanda" id="inviaDomanda" value="Invia la domanda" class="btnDarkRed"-->
							<button type="submit" class="home-button" name="inviaDomanda" id="inviaDomanda"><span>Submit the question</span></button>                                    
						</div>                                    
					
					</form>
					
					<input type="hidden" name="rif_evento" id="rif_evento" value="1">
					<input type="hidden" name="token_utente" id="token_utente" value="">
					
				</div> 
            </div>
        </div>
	</div> 

	<div id="gotoTop" class="icon-angle-up"></div>
	<script src="asset/js/jquery.js"></script>
</body>
</html>