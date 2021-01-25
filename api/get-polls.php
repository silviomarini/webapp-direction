<?php
    include('../server/db.php');
    
    $ultimo_ID= $_GET['ultimo_ID'];
    
    $time_attuale=time();
    $contr_sondaggio_attivo=mysqli_num_rows(mysqli_query($con,"Select ID from sondaggi where attiva=1 and data_disattivazione>=$time_attuale and data_attivazione<$time_attuale"));
    
    $ultimo_id_inserito=$ultimo_ID;
    
    $ris="";
    
    if($contr_sondaggio_attivo>0){
        $sondaggio_attivo=mysqli_fetch_array(mysqli_query($con,"Select ID,domanda,tipo,risposta_1,risposta_2,risposta_3,risposta_4 from sondaggi where attiva=1 and data_disattivazione>=$time_attuale and data_attivazione<$time_attuale"));
        $ultimo_id_inserito=$sondaggio_attivo['ID'];
        $contr_sondaggio_norisp=mysqli_num_rows(mysqli_query($con,"Select ID from sondaggi_risposte where s_ID_partecipante='$_COOKIE[utente_evento]' and s_ID_sondaggio='$sondaggio_attivo[ID]'"));
    
        if($contr_sondaggio_norisp==0){
            $ris.='
            <div class="titolo-domanda"><strong>'.$sondaggio_attivo['domanda'].'</strong></div>
            <div class="card-body">';
                
                if($sondaggio_attivo['tipo']=="risp_aperta"){
                    $ris.='
                            <div class="form-group text-center">
                            <textarea placeholder="Inserisci la tua risposta qui!" 
                                name="risposta_aperta" id="risposta_aperta" 
                                style="width:70%; border: 3px solid rgb(148 49 50);padding:5px;font-family: Tahoma, sans-serif; height:200px;"
                                row="10"
                            ></textarea>
                            
                        </div>
                        <div>
                            <input type="submit" class="home-button" name="invia_risposta_aperta" id="invia_risposta_aperta" value="Invia la risposta" /> 
                        </div>';
                } 
                if($sondaggio_attivo['tipo']=="risp_multipla"){
                        if($sondaggio_attivo['risposta_1']!=""){ $ris.='<div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_1" class="trCkLarge">'.$sondaggio_attivo["risposta_1"].'</label></div>'; } 
                        if($sondaggio_attivo['risposta_2']!=""){ $ris.='<div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_2" class="trCkLarge">'.$sondaggio_attivo["risposta_2"].'</label></div>'; }
                        if($sondaggio_attivo['risposta_3']!=""){ $ris.='<div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_3" class="trCkLarge">'.$sondaggio_attivo["risposta_3"].'</label></div>'; }
                        if($sondaggio_attivo['risposta_4']!=""){ $ris.='<div class="risposta-sondaggio"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_4" class="trCkLarge">'.$sondaggio_attivo["risposta_4"].'</label></div>'; }
                }
            
            
            $ris.='</div>';
            $ris.='<input type="hidden" name="id_sondaggio" id="id_sondaggio" value="'.$sondaggio_attivo["ID"].'">';
            
            $ris.='
                <script type="text/javascript"> 
                 
                    // AJAX SALVATAGGIO DOMANDA A RISPOSTA APERTA
                    $("#invia_risposta_aperta").click(function() {
                        var risposta_aperta = $(\'#risposta_aperta\').val();
                        risposta_aperta = encodeURIComponent(risposta_aperta);
                
                        var rif_evento = $(\'#rif_evento\').val();
                        var ultimo_ID = $("#ultimo_ID").val();
                        var id_sondaggio = $(\'#id_sondaggio\').val();
                
                        if(risposta_aperta!=""){
                            $.ajax({
                                url: "ajax_invia_risposta_aperta.php",
                                type: "get",
                                crossDomain: true,
                                data: \'azione=risp_aperta&risposta_aperta=\' + risposta_aperta + \'&ultimo_ID=\'+ultimo_ID+ \'&rif_evento=\'+rif_evento+ \'&id_sondaggio=\'+id_sondaggio,
                                success: function(data){
                                    //console.log(data);
                                    $("#alertDomanda").show();
                                    $("#contDomande").hide();
                                    $("#risposta_aperta").val(\'\');
                                },
                                error: function () {
                                    //alert(\'Errore AJAX\');
                                }
                            });	
                        }else{
                            alert("Please enter an answer");	
                        }
                
                    });	
            
                    // AJAX SALVATAGGIO DOMANDA A RISPOSTA MULTIPLA
                    $("input[name=\'risposte_s\']").click(function(){
                        var radioValue = $("input[name=\'risposte_s\']:checked").val();
                        var id_sondaggio= $("#id_sondaggio").val();
                            var ultimo_ID = $("#ultimo_ID").val();
                        var rif_evento = $(\'#rif_evento\').val();
        
                            $.ajax({
                                url: "ajax_invia_risposta_aperta.php",
                                type: "get",
                                crossDomain: true,
                                data: \'azione=risp_multipla&risposta=\' + radioValue + \'&ultimo_ID=\'+ultimo_ID+ \'&rif_evento=\'+rif_evento+ \'&id_sondaggio=\'+id_sondaggio,
                                success: function(data){
                                    console.log(data);
                                    $("#alertDomanda").show();
                                    $("#contDomande").hide();
                                    //$("#risposta_aperta").val(\'\');
                                },
                                error: function () {
                                    //alert(\'Errore AJAX\');
                                }
                            });		
                    });
                                                
                </script>	
            ';
        }	
    }
    
    $ris.="|||".$ultimo_id_inserito;
    
    echo $ris;
    ?>
    
    