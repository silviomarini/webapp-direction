
<?php
    include "../server/db.php";
    $question="";
    $data = json_decode(file_get_contents('php://input'), true);
        foreach($data as $key => $value){
            $question = $value;
        }
    $getdata=date("Y-m-d h:i:s");
    $query = "INSERT INTO questions (customer_id, question, event_id, question_timestamp) VALUES (1, '$question', 1, '$getdata')";
    
    $ris = mysqli_query($con,$query);
    
    if($ris){

        http_response_code(200);

        echo json_encode(array("message" => "OK"));
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "Errore"));
    } 
?>