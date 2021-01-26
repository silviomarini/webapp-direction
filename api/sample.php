
<?php
    
    //recupero i valori e faccio real escape
    $value = mysqli_real_escape_string($connessione, $_POST["value"]);

    //init query
    $query = "";
    
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