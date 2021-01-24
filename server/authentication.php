<?php session_start();
    $slogger = new Slogger;
    $utility = new Utility;
    $class = "authentication.php";

    $sessionId = $_COOKIE["session_id"];
    $slogger->debug($class, "Current session id = ".$sessionId);

    $autorizzazione = $_SESSION['autorizzato'];
    $id_utente= $_SESSION['cod'];
    $livello = $_SESSION['livello'];

    $slogger->debug($class, "Session attributes retrieved: autorizzato = ".$autorizzazione." , id_utente = ".$id_utente." , livello = ".$livello."");

    //print_r($_SESSION);
    if ($autorizzazione != "autorizzato") {
        $slogger->warn($class, "Unauthorized access tentative, redirecting to login");
        echo '<script language=javascript>document.location.href="login.php"</script>'; 
    }

    /*retrieve current user data
        USER: nome, cognome, username, level
        AZIENDA: id, nome, dati vari
    */

    $query = "SELECT u.id as idUtente, id_azienda, firstname, lastname, username, a.nome as nomeAzienda, a.tipo as tipoAzienda, level, 
                    piva, indirizzo, ta.nome as tipo_att, ta.id as id_tipo_att, tipologia_servizio 
            FROM users u 
            LEFT JOIN azienda a ON u.id_azienda = a.id 
            LEFT JOIN tipologia_attivita ta ON a.tipologia_attivita = ta.id
            LEFT JOIN tipologia_servizio ts ON a.tipologia_servizio = ts.id
            WHERE u.username = '$id_utente'";
    $slogger->debug($class, "Running query ".$query);
    $ris = mysqli_query($connessione, $query);
    $riga= mysqli_fetch_array($ris);
    $user = array(
        "username"=> $riga["username"] , 
        "nome"=> $riga["firstname"] , 
        "cognome"=>$riga["lastname"], 
        "nome_azienda"=>$riga["nomeAzienda"],
        "tipo_azienda"=>$riga["tipoAzienda"],
        "piva"=>$riga["piva"],
        "indirizzo"=>$riga["indirizzo"],
        "id_tipologia_attivita"=>$riga["id_tipo_att"],
        "tipologia_attivita"=>$riga["tipo_att"],
        "tipologia_servizi"=>$riga["tipologia_servizio"],
        "livello"=>$riga["level"],
        "id_utente"=>$riga["idUtente"],
        "id_azienda"=>$riga["id_azienda"]
    );

    
    $_SESSION['id_azienda'] = $user["id_azienda"];

    $slogger->debug($class, "User data retrieved: ". $utility->arrayToString($user) );


?>