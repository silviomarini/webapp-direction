<?php
    include('../server/db.php');
    
    $ultimo_ID= $_GET['ultimo_ID'];
    
    $time_attuale=time();
    $active_polls_cont=mysqli_num_rows(mysqli_query($con,"Select ID from polls_master where attiva=1 and disactivation_date>=$time_attuale and activation_date<$time_attuale"));
    
    $ultimo_id_inserito=$ultimo_ID;
    
    $ris="";
    
    if($active_polls_cont>0){
        $active_poll=mysqli_fetch_array(mysqli_query($con,"Select ID,domanda,tipo,answer_1,answer_2,answer_3,answer_4,answer_5,answer_6,answer_7,answer_8,answer_9,answer_10 from polls_master where attiva=1 and disactivation_date>=$time_attuale and activation_date<$time_attuale"));
        $ultimo_id_inserito=$active_poll['ID'];
        $active_polls_cont_noans=mysqli_num_rows(mysqli_query($con,"Select ID from polls_answers where customer_id='$_COOKIE[utente_evento]' and polls_id='$active_poll[ID]'"));
    
        if($active_polls_cont_noans==0){
            $ris.='
            <div class="titolo-domanda"><strong>'.$active_poll['domanda'].'</strong></div>
            <div class="card-body">';
                
                if($active_poll['tipo']=="risp_aperta"){
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

                if($active_poll['tipo']=="risp_multipla"){
                    for($i = 0; $i<11; $i++){
                        if($active_poll['answer_'.$i]!=""){
                            $ris.='<div class="poll-answer"><label class="big"><input type="radio" name="risposte_s" id="risposte_s" value="risposta_'.$i.'" class="trCkLarge" onclick="postClosedPoll()">'.$active_poll["answer_".$i].'</label></div>';
                        }
                    }
                }
            
            
            $ris.='</div>';
            $ris.='<input type="hidden" name="id_sondaggio" id="id_sondaggio" value="'.$active_poll["ID"].'">';
        } else {
            $ris = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
        }
    } else {
        $ris = "<div class='text-center fontWeight700 pb-1 pt-1'> Wait the next poll... </div>";
    }
    
    
    echo $ris;
    ?>
    
    