
<?php
    
    //recupero i valori e faccio real escape
    $question = mysqli_real_escape_string($connessione, $_POST["question"]);
    $data=date();
    //init query
    $query = "INSERT INTO domande (d_ID_partecipante, d_domanda, d_evento, d_data_domanda) VALUES (1, '$question', 1, $date);
    
    $ris = mysqli_query($connessione, $query);
    
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
        echo json_encode(array("message" => "Error message"));
    } 
?>