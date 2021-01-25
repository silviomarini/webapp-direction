
<?php
    include "../server/db.php";
    //recupero i valori e faccio real escape
    $question="";
    $data = json_decode(file_get_contents('php://input'), true);
        foreach($data as $key => $value){
            $question = $value;
        }
    $getdata=date("Y-m-d h:i:s");
    //init query
    $query = "INSERT INTO domande (d_ID_partecipante, d_domanda, d_evento, d_data_domanda) VALUES (1, '$question', 1, '$getdata')";
    
    $ris = mysqli_query($con,$query);
    
    //mando la risposta
    if($ris){
        // set response code - 201 created
        http_response_code(200);
        // tell the user
        echo json_encode(array("message" => "OK"));
    } else {
        // set response code - 400 bad request
        http_response_code(400);
        // tell the user
        echo json_encode(array("message" => "Errore"));
    } 
?>