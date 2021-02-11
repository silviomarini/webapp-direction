<?php
    session_start();
    include('../server/db.php');

    /*$supp_condition = "";
    $active_type = "";
    $start_from_id = $_GET["start_from"];
    $id_evento = $_GET["evento"];

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
            $supp_condition = " AND question_status='' ";
            $active_type = "ALL";
            break;
            
        default:
            # Do nothing
            break;
    }


    
    $query="
        SELECT ID,question,question_timestamp 
        from questions where event_id='".$id_evento."' and ID>'$start_from_id' and hidden_question='0' ".$supp_condition." 
        order by question_timestamp desc";
    $result= mysqli_query($con,$query);
    $ris="";
    while($questions= mysqli_fetch_array($result)){
        if($questions['attiva']){$attiva =  "domanda-attiva";}

        $ris .= '
        <div class="card mb-3 '.$attiva.'" id="domanda_'.$questions['ID'].'">
            <div class="card-header">
                <div class="row">
                    <div class="header-info sx"><strong>'.$questions['ID'].')</strong> h <strong>'.$questions['question_timestamp'].'</strong></div>
                    <div class="header-info center">

                        <div class="float-right">
                        <!-- GO LIVE -->
                ';

                //    <!-- YES -->
                if($active_type == "" || $active_type == "ALL"){
                    $ris .= ' 
                            <span class="statoDomanda statoVerde" valore="y" id="'.$questions['ID'].'" onClick="change_status(this)">
                                <i class="fa fa-check"></i></span> 
                        ';
                    }

                //    <!-- NO -->	
                if($active_type == "" || $active_type == "ALL"){
                    $ris .= '
                            <span class="statoDomanda statoRosso" valore="n" id="'.$questions['ID'].'" onClick="change_status(this)">
                            <i class="fa fa-times"></i></span> 
                        ';
                    }
                        
                //    <!-- RESET -->	
                if($active_type == "" || $active_type == "SELECTED" || $active_type == "DELETED"){  
                    $ris .= '
                                <span class="statoDomanda statoAzzera" valore="azzera" id="'.$questions['ID'].'" onClick="change_status(this)" title="Reset">
                                <i class="fa fa-refresh"></i></span> 
                        ';
                    }

                    $ris .= '
                            <span class="statoDomanda statoAzzera" style="visibility:hidden;"> <i class="fa fa-refresh"></i></span>  </span>
                        </div>
                        <div class="float-right" style="grid-template-columns: 100%;"> ';

                    if( $active_type == "SELECTED"){
                        $ris .= '<span class="statoDomanda statoVerde" onClick="go_live(this)" title="GO Live" id="'.$questions['ID'].'" style="padding:8px;">
                                <i class="fa fa-upload" style="font-size: x-large;"></i></span> ';
                    }

                    $ris .= '</div>
                    </div>

                        <div class="header-info dx">

                            <div style="display: inline-grid;">';
                            // <!-- DONE -->
                              if( $active_type == "" || $active_type == "SELECTED"){
                                $ris .= '
                                    <span class="statoDomanda statoGiallo" valore="d" ';
                                    if(!$active_options){  $ris .= 'style="width: fit-content;"'; } 
                                    $ris .= 'id="'.$questions['ID'].'" onClick="change_status(this)">
                                    <i class="fa fa-thumbs-up"></i></span>';
                              }

                              $ris .= '
                            </div>

                            <span> Status: </span>
                            <span class="barraStato" id="barra_stato_'.$questions['ID'].'" style="background-color: '.$col_bg.';"></span>

                        </div>
                    </div>



            </div>
            <div class="card-body">
                <p class="card-text">'.$questions['question'].'</p>
            </div>
        </div>  
        ';
    }  */

    function ora_X_DB($data){
        $split=explode(" ", $data);
        $split1=explode("-", $split[0]);
        $split2=explode(":", $split[1]);
        $data_visualizzata= $split2[0].':'.$split2[1];
        return $data_visualizzata;
    }
    
    $ultimo_ID= $_GET['ultimo_ID'];
    $rif_evento= $_GET['rif_evento'];
    $tab_utenti= $_GET['tab_utenti'];
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
            $supp_condition = " AND question_status='' ";
            $active_type = "ALL";
            break;
            
        default:
            # Do nothing
            break;
    }
    
    
    $sql_ultimo_id_inserito= mysqli_fetch_array(mysqli_query($con,"Select ID from questions where event_id='".$rif_evento."' order by question_timestamp desc LIMIT 1"));
    $ultimo_id_inserito= $sql_ultimo_id_inserito['ID'];
    if(!isset($ultimo_id_inserito)){
        $ultimo_id_inserito=0;	
    }
    
    $sql_domande="Select ID,question,question_timestamp from questions where event_id='".$rif_evento."' and ID>'$ultimo_ID' and hidden_question='0' ".$supp_condition." order by question_timestamp desc";
    $r_domande= mysqli_query($con,$sql_domande);
    $ris="";
    while($questions= mysqli_fetch_array($r_domande)){
        $ris.='
        
        <div class="card mb-3" id="domanda_'.$questions['ID'].'">
          <div class="card-header">
              <div class="row">
                  <div class="header-info sx"><strong>'.$questions['ID'].')</strong> h <strong>'.ora_X_DB($questions['question_timestamp']).'</strong></div>
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
    
    
        $ris.=' <span> Status: </span>
                    <span class="barraStato" id="barra_stato_'.$questions['ID'].'"></span>
                </div>		
              </div>
          </div>
            <div class="card-body">
                <p class="card-text">'.nl2br($questions['question']).'</p>
            </div>
        </div>
        
        <script type="text/javascript"> 
        // CAMBIO LO STATO DELLA DOMANDA AL CLICK
        $(".statoDomanda_'.$questions['ID'].'").click(function() {
            var rif_evento = $(\'#rif_evento\').val();
            var id_domanda= $(this).attr("id");
            var stato_domanda= $(this).attr("valore");
                    
            $.ajax({
                url: "get-question-status.php",
                type: "get",
                crossDomain: true,
                data: \'id_domanda=\' + id_domanda + \'&stato_domanda=\'+stato_domanda + \'&rif_evento=\'+rif_evento,
                success: function(data){
                    //console.log(data);
                    
                    if(stato_domanda=="y"){	
                        $("#barra_stato_"+id_domanda).css("background-color","#2aa900");
                    }
                    if(stato_domanda=="n"){	
                        $("#barra_stato_"+id_domanda).css("background-color","#e81f1f");
                    }
                    if(stato_domanda=="azzera"){	
                        $("#barra_stato_"+id_domanda).css("background-color","transparent");
                    }								
                },
                error: function () {
                    alert(\'Errore AJAX\');
                }
            });			
    
        });	
        
        </script> 	
        ';                                 
    
    }
    
    $ris.="|||".$ultimo_id_inserito;
    
    echo $ris;


?>