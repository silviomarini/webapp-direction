<?php
session_start();
require_once("../server/db.php");

$current_event_id= $_GET['current_event_id'];
$id_domanda= $_GET['id_domanda'];
$nuovo_valore = 1;

if($id_domanda != "" ){

    $sql_stato="SELECT attiva FROM questions where ID='$id_domanda'";
    $domanda=mysqli_fetch_array(mysqli_query($con,$sql_stato));

    if($domanda["attiva"] == 1){

        $nuovo_valore = 0;
    }


    $sql_stato="UPDATE questions set attiva = 0 ";
    mysqli_query($con,$sql_stato);
    echo $sql_stato;
    
    if($nuovo_valore != 0){

        $sql_stato="UPDATE questions set attiva = $nuovo_valore where ID='$id_domanda'";
        mysqli_query($con,$sql_stato);
        echo $sql_stato;
    }

}



?>