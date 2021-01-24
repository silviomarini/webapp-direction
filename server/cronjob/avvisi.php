<?php

    //TODO authentication


    include '../db.php'; 
    include '../loggingHelper.php';
    $class = "avvisi.php";

    $slogger = new Slogger;
    $utility = new Utility;
    $dataService = new DataService;

    echo "<h2> scadenze dipendenti </h2> ";

    //recupero tutte le scadenze per i dipendenti
    $query = "SELECT r.nome as referenza, d2r.data as data, DATE_ADD(d2r.data, INTERVAL r.validita MONTH) AS data_scadenza, d.nome as nomeDip, d.cognome as cognomeDip
            , a.nome as azienda, DATE_ADD(DATE_ADD(d2r.data, INTERVAL r.validita MONTH), INTERVAL -d2r.avviso DAY) AS data_avviso, d2r.id_dipendente as id_dipendente, 
            d2r.id_referenza as id_referenza
            FROM dipendente2referenza d2r 
            LEFT JOIN dipendente d ON d.id = d2r.id_dipendente
            LEFT JOIN referenza r ON r.id = d2r.id_referenza
            LEFT JOIN azienda a ON d.id_azienda = a.id
            WHERE d2r.data != 0
        ";
    $query .= "ORDER BY data_scadenza ASC";
    //$slogger->debug($class , "Scadenze dipendenti: ".$query);
    echo $query."<br/>";
    
    $ris = mysqli_query($connessione, $query);
    if(mysqli_num_rows($ris) > 0){
        while($row = mysqli_fetch_array($ris)){
            $stato = "";
            if($row["data_scadenza"] < date("Y-m-d")){
                $stato = 'scaduta';
            } else if($row["data_avviso"] < date("Y-m-d")){
                $stato = 'in scadenza';
            } else {
                $stato = 'attiva';
            }
            echo "Stato = ".$stato."<br/>";

            if($stato != 'attiva'){
                $check_avvisi_query = "SELECT count(*) FROM avvisi WHERE dipendente_macchinario = ".$row["id_dipendente"]." 
                                    AND referenza_manutenzione = ".$row["id_referenza"]." 
                                    AND stato = '".$stato."'";
                echo $check_avvisi_query."<br/>";
                $check_result = mysqli_query($connessione, $check_avvisi_query);
                $row_result = mysqli_fetch_row($check_result);
                echo "Righe trovate = ".$row_result[0]."<br/>";

                if($row_result[0] == 0){
                    $insert_avviso = "INSERT INTO avvisi(dipendente_macchinario, referenza_manutenzione, stato) 
                                        VALUES(".$row["id_dipendente"].",".$row["id_referenza"].",'".$stato."')";
                    echo $insert_avviso."<br/>";
                    if(mysqli_query($connessione, $insert_avviso)){
                        echo "inserito <br/>";
                    } else {
                        echo "ERRORE!!!";
                    }
                } else {
                    echo "Avviso già presente, SKIP! <br/>";
                }
            } else {
                echo "referenza attiva, SKIP!";
            }
            echo "<br/>";
        }
    }

    echo "<h2> scadenze macchinari </h2> ";

    //recupero tutte le scadenze per i macchinari
    $query = "SELECT man.nome as manutenzione, DATE_ADD(m2m.data, INTERVAL man.periodicita MONTH) AS data_scadenza,
                DATE_ADD(DATE_ADD(m2m.data, INTERVAL man.periodicita MONTH), INTERVAL -m2m.avviso DAY) AS data_avviso, m2m.id_macchinario as id_macchinario, m2m.id_manutenzione as id_manutenzione,
                mac.descrizione as macchinario, a.nome as azienda
            FROM macchinario2manutenzione m2m 
            LEFT JOIN macchinario mac ON mac.id = m2m.id_macchinario
            LEFT JOIN manutenzioni man ON man.id = m2m.id_manutenzione
            LEFT JOIN azienda a ON a.id = mac.id_azienda
            WHERE man.misura = 'mesi' AND m2m.data != 0
        ";
    $query .= "ORDER BY data_scadenza ASC";
    //$slogger->debug($class , "Scadenze dipendenti: ".$query);
    echo $query."<br/>";
    
    $ris = mysqli_query($connessione, $query);
    if(mysqli_num_rows($ris) > 0){
        while($row = mysqli_fetch_array($ris)){
            $stato = "";
            if($row["data_scadenza"] < date("Y-m-d")){
                $stato = 'scaduta';
            } else if($row["data_avviso"] < date("Y-m-d")){
                $stato = 'in scadenza';
            } else {
                $stato = 'attiva';
            }
            echo "Stato = ".$stato."<br/>";

            if($stato != 'attiva'){
                $check_avvisi_query = "SELECT count(*) FROM avvisi WHERE dipendente_macchinario = ".$row["id_macchinario"]." 
                                    AND referenza_manutenzione = ".$row["id_manutenzione"]." 
                                    AND stato = '".$stato."'";
                echo $check_avvisi_query."<br/>";
                $check_result = mysqli_query($connessione, $check_avvisi_query);
                $row_result = mysqli_fetch_row($check_result);
                echo "Righe trovate = ".$row_result[0]."<br/>";

                if($row_result[0] == 0){
                    $insert_avviso = "INSERT INTO avvisi(dipendente_macchinario, referenza_manutenzione, stato) 
                                        VALUES(".$row["id_macchinario"].",".$row["id_manutenzione"].",'".$stato."')";
                    echo $insert_avviso."<br/>";
                    if(mysqli_query($connessione, $insert_avviso)){
                        echo "inserito <br/>";
                    } else {
                        echo "ERRORE!!!";
                    }
                } else {
                    echo "Avviso già presente, SKIP! <br/>";
                }
            } else {
                echo "referenza attiva, SKIP!";
            }
            echo "<br/>";
        }
    }





?>