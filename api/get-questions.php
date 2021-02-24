<?php
    session_start();
    include('../server/db.php');

    function formatDate($data){
        $split=explode(" ", $data);
        $split1=explode("-", $split[0]);
        $split2=explode(":", $split[1]);
        $formattedDate= $split2[0].':'.$split2[1];
        return $formattedDate;
    }
    
    $ultimo_ID= $_GET['ultimo_ID'];
    $current_event_id= $_GET['current_event_id'];
    $supp_condition = "";
    $active_type = "";
    switch ($_GET["filter"]) {
        case 'selected':
            $supp_condition = " AND question_status='y' ";
            $active_type = "SELECTED";
            break;
        case 'deleted':
            $supp_condition = " AND question_status='n' ";
            $active_type = "DELETED";
            break;
        case 'done':
            $supp_condition = " AND question_status='d' ";
            $active_type = "DONE";
            break;
        case 'all':
            $supp_condition = " AND (question_status='' OR question_status IS NULL) ";
            $active_type = "ALL";
            break;
            
        default:
            # Do nothing
            break;
    }
    
    
    $sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select ID from questions where event_id='".$current_event_id."' order by ID desc LIMIT 1"));
    $ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
    if(!isset($ultimo_id_inserito)){
        $ultimo_id_inserito=0;	
    }
    
    $sql_domande="Select ID,question,question_timestamp from questions where event_id='".$current_event_id."' and ID>'$ultimo_ID' and hidden_question='0' ".$supp_condition." order by ID desc";
    $and_questions= mysqli_query($con,$sql_domande);
    $ris="";
    while($questions= mysqli_fetch_array($and_questions)){
        $ris.='
        
        <div class="card mb-3" id="domanda_'.$questions['ID'].'">
          <div class="card-header">
              <div class="row">
                  <div class="header-info sx"><strong>'.$questions['ID'].')</strong> h <strong>'.formatDate($questions['question_timestamp']).'</strong></div>
                <div class="header-info center">
                    <div class="float-right">';
    
                    // <!-- GO LIVE -->
                    if( $active_type == "SELECTED"){ 
                        $ris.=' <span class="statoDomanda statoVerde" onClick="go_live(this)" title="GO Live" id="'.$questions["ID"].'">
                        <i class="fa fa-upload"></i></span> ';
                    }
                    // <!-- YES -->
                    if($active_type == "" || $active_type == "ALL"){
                        $ris.='<span class="statoDomanda statoVerde" valore="y" id="'.$questions["ID"].'" onClick="change_status(this)">
                                <i class="fa fa-check"></i></span>';
                    }
                    // <!-- NO -->	
                    if($active_type == "" || $active_type == "ALL"){
                        $ris.='	<span class="statoDomanda statoRosso" valore="n" id="'.$questions["ID"].'" onClick="change_status(this)">
                            <i class="fa fa-times"></i></span> ';
                    }
                    
                    // <!-- RESET -->	
                    if($active_type == "" || $active_type == "SELECTED" || $active_type == "DELETED"){
                        $ris.='	<span class="statoDomanda statoAzzera" valore="azzera" id="'.$questions["ID"].'" onClick="change_status(this)" title="Reset">
                            <i class="fa fa-refresh"></i></span> ';
                    }
                    
                    $ris.='<span class="statoDomanda statoAzzera" style="visibility:hidden;"> </span>';
    
        $ris.='
                    </div>
                </div>	
                <div class="header-info dx">';
    
                    // <!-- DONE -->
                    if( $active_type == "" || $active_type == "SELECTED"){
                        $ris.='	<div style="display: inline-grid;"> <span class="statoDomanda statoGiallo" valore="d"';
                        if(!$active_options){ $ris.= 'style="width: fit-content;"'; }
                        $ris.=' id="'.$questions["ID"].'" onClick="change_status(this)"><i class="fa fa-thumbs-up"></i></span> </div>';
                    }
    
    
        $ris.=' <span style="display:none"> Status: </span>
                    <span style="display:none" class="barraStato" id="barra_stato_'.$questions['ID'].'"></span>
                </div>		
              </div>
          </div>
            <div class="card-body">
                <p class="card-text">'.nl2br($questions['question']).'</p>
            </div>
        </div>	
        ';                                 
    
    }
    
    $ris.="|||".$ultimo_id_inserito;
    
    echo $ris;


?>