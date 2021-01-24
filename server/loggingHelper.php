<?php

    //IMPORTANT: disable logging before production deployment
    $debugEnabled = true;

    class Slogger {

        function slog($class, $level, $message){
            global $debugEnabled;

            //print log only if debug is enabled
            if($debugEnabled){
                //removing all newline from the message
                $message = preg_replace("/\r|\n/", "", $message);

                error_log("SWC $level $class:  $message", 0);
            }
        }

        function debug($class, $message){
            $this->slog($class, "DEBUG", $message);
        }

        function info($class, $message){
            $this->slog($class, "INFO", $message);
        }

        function warn($class, $message){
            $this->slog($class, "WARN", $message);
        }
    }

    class Utility {
        function arrayToString(array $array){
            $result = "";
            foreach ($array as $key => $value){
                $result .= $key."->".$value.", ";
            }
            return rtrim($result, ", ");
        }

        function isAdmin(){
            global $user;
            //$slogger->debug($class, "Checking level for user = ".$user["username"].", level = ".$user["livello"]);
            if($user["livello"] == "admin"){
                return true;
            } else {
                return false;
            }
        }

        function datetimeToString($ore){
            $result = "";
            switch ($ore) {
                case 0:
                    $result = "-";
                    break;
                case 1:
                    $result = "1 ora";
                    break;
                default:
                    $result = $ore." ore";
                    break;
            }
            return $result;
        }

        function periodicitaToString($val){
            $periodicita = "";
            if($val == 0){
                $periodicita = "Senza scadenza";
            } else if($val == 1){
                $periodicita = $val." mese";
            } else {
                $periodicita = $val." mesi";
            }
            return $periodicita;
        }

        function formatDate($date){
            $d = new DateTime($date);
            return date_format($d,"d/m/Y");
        }

    }

    class DataService {

        function getCompaniesList($connessione){
            $query = "SELECT id,nome FROM azienda";
            return mysqli_query($connessione, $query);
        }
        
        function getCompanyTypesList($connessione){
            $query = "SELECT id,nome FROM tipologia_attivita";
            return mysqli_query($connessione, $query);
        }

        function getCompanyServicesList($connessione){
            $query = "SELECT id,nome FROM tipologia_servizio";
            return mysqli_query($connessione, $query);
        }

        function deleteTipologia($connessione, $table, $id){
            $query = "DELETE FROM ".$table." WHERE id = ".$id;
            return mysqli_query($connessione, $query);
        }

        function addTipologia($connessione, $table, $value){
            $query = "INSERT INTO ".$table." VALUES ('','".$value."')";
            return mysqli_query($connessione, $query);
        }

        function getReferenceList($connessione){
            $query = "SELECT id,nome,ore,validita,contenuti,note FROM referenza";
            return mysqli_query($connessione, $query);
        }

        function getTipologieMacchinari($connessione){
            $query = "SELECT id,nome FROM tipologia_macchinario";
            return mysqli_query($connessione, $query);
        }

        function getManutenzioni($connessione){
            $query = "SELECT id, nome, periodicita, misura FROM manutenzioni";
            return mysqli_query($connessione, $query);
        }

        function getDipendenti($connessione, $company){
            $cond = '';
            if($company != '' && $company != 0){
                $cond = ' WHERE id_azienda = '.$company;
            }
            $query = "SELECT d.id as id, d.nome as nome, cognome, ruolo, a.nome as azienda FROM dipendente d LEFT JOIN azienda a ON id_azienda = a.id ".$cond;
            return mysqli_query($connessione, $query);
        }

        //MANSIONI PER DIPENDENTE
        function getListaMansioni($connessione, $dipendente){
            $result = array();
            $query = "SELECT d2m.id as id, m.nome as nome
                    FROM dipendente2mansioni d2m 
                    LEFT JOIN mansioni m ON m.id = d2m.id_mansione
                    WHERE d2m.id_dipendente = ".$dipendente;
            $ris = mysqli_query($connessione, $query);
            if(mysqli_num_rows($ris) > 0){
                $i = 0;
                while($row = mysqli_fetch_array($ris)){
                    $result[$i] =  $row["nome"];
                    $i++;
                }
            }
            return $result;
        }

        //MANSIONI PER AZIENDA
        function getMansioniByAzienda($connessione, $id_azienda){
            $condition = "";
            if($id_azienda != ''){
                $condition = " WHERE m.id_azienda = ".$id_azienda." ";
            }
            $query = "SELECT m.id as id, m.nome as nome, a.nome as azienda
                    FROM mansioni m
                    LEFT JOIN azienda a ON m.id_azienda = a.id ".$condition;
            return mysqli_query($connessione, $query);
        }

        function deleteMansione($connessione, $id){
            $query = "DELETE FROM mansioni WHERE id = ".$id."";
            return mysqli_query($connessione, $query);
        }

        function addMansione($connessione, $value, $id_azienda){
            $query = "INSERT INTO mansioni (id, nome, id_azienda)  VALUES ('','".$value."','".$id_azienda."')";
            return mysqli_query($connessione, $query);
        }

        //TIPOLOGIE ATTIVITA
        function getTipologiaAttivitaNames($connessione, $list){
            //$attivita = explode(",", $list);
            $result = array();
            $query = "SELECT id,nome FROM tipologia_attivita WHERE id IN (".$list.")" ;
            $ris = mysqli_query($connessione, $query);
            if(mysqli_num_rows($ris) > 0){
                $i = 0;
                while($row = mysqli_fetch_array($ris)){
                    $result[$i] =  $row["nome"];
                    $i++;
                }
            }
            return $result;
        }

        //TIPOLOGIE ATTIVITA
        function getTipologiaServiziNames($connessione, $list){
            //$attivita = explode(",", $list);
            $result = array();
            $query = "SELECT id,nome FROM tipologia_servizio WHERE id IN (".$list.")" ;
            $ris = mysqli_query($connessione, $query);
            if(mysqli_num_rows($ris) > 0){
                $i = 0;
                while($row = mysqli_fetch_array($ris)){
                    $result[$i] =  $row["nome"];
                    $i++;
                }
            }
            return $result;
        }

    }

?>