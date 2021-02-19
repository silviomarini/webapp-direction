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

    $current_event_id= $_GET['current_event_id'];
    $supp_condition = "";
    $active_type = "";
    switch ($_GET["filter"]) {
        case 'selected':
            $active_type = "SELECTED";
            break;
        case 'deleted':
            $active_type = "DELETED";
            break;
        case 'done':
            $active_type = "DONE";
            break;
        case 'all':
            $active_type = "ALL";
            break;
            
        default:
            # Do nothing
            break;
    }

    $sql_q="Select ID,question,question_timestamp,question_status from questions where ID=".$_GET["id"];
    $and_questions= mysqli_query($con,$sql_q);
    $ris="";
    while($questions= mysqli_fetch_array($and_questions)){

        $statusClass = "";
        if($questions['question_status']=="y"){$statusClass="yes";}
        if($questions['question_status']=="n"){$statusClass="no";}
        if($questions['question_status']=="d"){$statusClass="done";}
        if($questions['question_status']==""){$statusClass="reset";}

        $ris.='
        
        <div class="card mb-3" id="domanda_'.$questions['ID'].'" style="display:none;">
          <div class="card-header">
              <div class="row">
                  <div class="header-info sx"><strong>'.$questions['ID'].')</strong> h <strong>'.formatDate($questions['question_timestamp']).'</strong></div>
                <div class="header-info center">
                    <div class="float-right">';
    
        
															
                        //  <!-- YES -->
                        if($active_type == "" || $active_type == "ALL"){ 
                                $ris .= '<span class="statoDomanda statoVerde" valore="y" id="'.$questions['ID'].'" onClick="change_status(this)">
                                                <i class="fa fa-check"></i></span> ';
                        }
                        //<!-- NO -->	
                        if($active_type == "" || $active_type == "ALL"){ 
                            $ris .= '  <span class="statoDomanda statoRosso" valore="n" id="'.$questions['ID'].'" onClick="change_status(this)">
                                            <i class="fa fa-times"></i></span> ';
                        }
                                    
                        // <!-- RESET -->	
                        if($active_type == "" || $active_type == "SELECTED" || $active_type == "DELETED"){
                            $ris.='	<span class="statoDomanda statoAzzera" valore="azzera" id="'.$questions["ID"].'" onClick="change_status(this)" title="Reset">
                                            <i class="fa fa-refresh"></i></span> ';
                        }
                                    
                        $ris.='<span class="statoDomanda statoAzzera" style="visibility:hidden;"> </span>'; 
                    
                    $ris.='</div>'; //float-right END
                    
                    $ris.=' <div class="float-right" style="grid-template-columns: 100%;">';
                    if( $active_type == "SELECTED"){
                        $ris.='<span class="statoDomanda statoVerde" onClick="go_live(this)" title="GO Live" id="'.$questions['ID'].'" style="padding:8px;">
                                    <i class="fa fa-upload" style="font-size: x-large;"></i></span>';
                    }
                    $ris.='</div>';



                    
            $ris .= '</div>	
                <div class="header-info dx">';
    
            // <!-- DONE -->
            if( $active_type == "" || $active_type == "SELECTED"){
                $ris.='	<div style="display: inline-grid;"> <span class="statoDomanda statoGiallo" valore="d"';
                if(!$active_options){ $ris.= 'style="width: fit-content;"'; }
                $ris.=' id="'.$questions["ID"].'" onClick="change_status(this)"><i class="fa fa-thumbs-up"></i></span> </div>';
            }
    
    
            $ris.=' <span style="display:none"> Status: </span>
                    <span style="display:none" class="barraStato '.$statusClass.'" id="barra_stato_'.$questions['ID'].'"></span>
                </div>		
              </div>
          </div>
            <div class="card-body">
                <p class="card-text">'.nl2br($questions['question']).'</p>
            </div>
        </div>	
        ';                                 
    
    }

    echo $ris;
?>