<?php
    include('../server/db.php');

    $supp_condition = "";
    $active_type = "";
    $start_from_id = $_GET["start_from"];
    $id_evento = $_GET["evento"];

    switch ($_GET["filter"]) {
        case 'selected':
            $supp_condition = " AND d_stato='y' ";
            $active_type = "SELECTED";
            break;
        case 'deleted':
            $supp_condition = " AND d_stato='n' ";
            $active_type = "DELETED";
            break;
        case 'done':
            $supp_condition = " AND d_stato='d' ";
            $active_type = "DONE";
            break;
        case 'all':
            $supp_condition = " AND d_stato='' ";
            $active_type = "ALL";
            break;
            
        default:
            # Do nothing
            break;
    }


    
    $query="
        SELECT ID,d_domanda,d_data_domanda 
        from domande where d_evento='".$id_evento."' and ID>'$start_from_id' and d_non_visibile='0' ".$supp_condition." 
        order by d_data_domanda desc";
    $result= mysqli_query($con,$query);
    $ris="";
    while($domande= mysqli_fetch_array($result)){
        if($domande['attiva']){$attiva =  "domanda-attiva";}

        $ris .= '
        <div class="card mb-3 '.$attiva.'" id="domanda_'.$domande['ID'].'">
            <div class="card-header">
                <div class="row">
                    <div class="header-info sx"><strong>'.$domande['ID'].')</strong> h <strong>'.$domande['d_data_domanda'].'</strong></div>
                    <div class="header-info center">

                        <div class="float-right">
                        <!-- GO LIVE -->
                ';

                //    <!-- YES -->
                if($active_type == "" || $active_type == "ALL"){
                    $ris .= ' 
                            <span class="statoDomanda statoVerde" valore="y" id="'.$domande['ID'].'" onClick="change_status(this)">
                                <i class="fa fa-check"></i></span> 
                        ';
                    }

                //    <!-- NO -->	
                if($active_type == "" || $active_type == "ALL"){
                    $ris .= '
                            <span class="statoDomanda statoRosso" valore="n" id="'.$domande['ID'].'" onClick="change_status(this)">
                            <i class="fa fa-times"></i></span> 
                        ';
                    }
                        
                //    <!-- RESET -->	
                if($active_type == "" || $active_type == "SELECTED" || $active_type == "DELETED"){  
                    $ris .= '
                                <span class="statoDomanda statoAzzera" valore="azzera" id="'.$domande['ID'].'" onClick="change_status(this)" title="Reset">
                                <i class="fa fa-refresh"></i></span> 
                        ';
                    }

                    $ris .= '
                            <span class="statoDomanda statoAzzera" style="visibility:hidden;"> <i class="fa fa-refresh"></i></span>  </span>
                        </div>
                        <div class="float-right" style="grid-template-columns: 100%;"> ';

                    if( $active_type == "SELECTED"){
                        $ris .= '<span class="statoDomanda statoVerde" onClick="go_live(this)" title="GO Live" id="'.$domande['ID'].'" style="padding:8px;">
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
                                    $ris .= 'id="'.$domande['ID'].'" onClick="change_status(this)">
                                    <i class="fa fa-thumbs-up"></i></span>';
                              }

                              $ris .= '
                            </div>

                            <span> Status: </span>
                            <span class="barraStato" id="barra_stato_'.$domande['ID'].'" style="background-color: '.$col_bg.';"></span>

                        </div>
                    </div>



            </div>
            <div class="card-body">
                <p class="card-text">'.$domande['d_domanda'].'</p>
            </div>
        </div>  
        ';
    }

    
?>