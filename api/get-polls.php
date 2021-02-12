<?php
    include('../server/db.php');
    
    $ultimo_ID= $_GET['ultimo_ID'];
    
    $time_attuale=time();
    $contr_sondaggio_attivo=mysqli_num_rows(mysqli_query($con,"Select ID from polls_master where attiva=1 and disactivation_date>=$time_attuale and activation_date<$time_attuale"));
    
    $ultimo_id_inserito=$ultimo_ID;
    
    $ris="";
    
    if($contr_sondaggio_attivo>0){
        $sondaggio_attivo=mysqli_fetch_array(mysqli_query($con,"Select ID,domanda,tipo,answer_1,answer_2,answer_3,answer_4,answer_5,answer_6,answer_7,answer_8,answer_9,answer_10 from polls_master where attiva=1 and disactivation_date>=$time_attuale and activation_date<$time_attuale"));
        $ultimo_id_inserito=$sondaggio_attivo['ID'];
        $contr_sondaggio_norisp=mysqli_num_rows(mysqli_query($con,"Select ID from polls_answers where customer_id='$_COOKIE[utente_evento]' and polls_id='$sondaggio_attivo[ID]'"));
    
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
                    for($i = 0; $i<11; $i++){
                        if($sondaggio_attivo['answer_'.$i]!=""){
                            $ris.='<div class="poll-answer"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_'.$i.'" class="trCkLarge" onclick="postClosedPoll()">'.$sondaggio_attivo["answer_".$i].'</label></div>';
                        }
                    }
                }
            
            
            $ris.='</div>';
            $ris.='<input type="hidden" name="id_sondaggio" id="id_sondaggio" value="'.$sondaggio_attivo["ID"].'">';
        } else {
            $ris = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
        }
    } else {
        $ris = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
    }
    
    
    echo $ris;
    ?>
    
    