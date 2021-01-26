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
                            <div class="home-button" name="answer" id="answer" onclick="postOpenPoll()"> Send answer </div>
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
        }	
    } else {
        $ris = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
    }
    
    //$ris.="|||".$ultimo_id_inserito;
    
    echo $ris;
    ?>
    
    