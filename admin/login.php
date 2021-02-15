<?php session_start(); 
	include "../server/db.php";
?>
<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	
	<script src="../asset/js/jquery.js"></script>

	<link rel="stylesheet" href="../asset/css/style.css" type="text/css" />
	
	<title>Admin - Privilege Web App</title>
</head>



<body>
    <style>

        @media screen and (min-width: 800px) {
            #top1 {
                width: 850px;
            }
            #top2 {
                width: 850px;
            }

            .body {
                min-height: 165px;
                max-width: 1050px;
                margin-left: auto;
                margin-right: auto;
            }

            .form-update {
                float: left;
                width: 40%;
            }
        }

        @media screen and (max-width: 800px) {
            .form-input {
                width: 100%;
                margin-top: 10px;
            }

            .btn-primary {
                float: left !important;
                margin-left: 12px !important;
                margin-top: 20px !important;
            }

            #top2 {
                height: 100px;
            }

            #reset_btn {
                margin-left: 0px !important;
                margin-top:30px;
            }
        }

        #top1 {
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #943132;
            text-align: left;
            padding: 50px;
            margin-bottom:20px;
        }

        #top2 {
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #943132;
            text-align: left;
            padding: 50px;
            margin-bottom:40px;
            padding-top: 38px;
        }

        .form-row {
            padding:10px;
        }

        .btn-primary {
            border: 1px solid #943132;
            text-align: center;
            padding: 15px;
            color: #943132;
            width:182px;
        }

        #reset_btn {
            right: 0;
            float: right;
            background-color:#943132;
            color:white; 
            margin-top:-20px; 
            display:block !important;
            width:142px;
        }

        .form-input {
            padding: 10px;
        }

        #login {
            width: 70%;
            margin-left: auto;
            margin-right: auto;
            padding-top: 50px;
            text-align: center;
        }

        .form-group{
            margin: 20px;
        }

    </style>

    <div id="wrapper">
                    
        <div class="header">
        <div class="bg"></div>
        <div class="logo">
        </div>
        
        <h1 class="title" style="margin-left: 20px;">
            ADMIN PANEL
        </h1>
        
        </div>

        <style>
            .header .bg {
                width: 100%;
                height: 100%;
                background-color:#043B69;
            }
        </style>
                
        <div class="about">
            <div class="title">
                Login
            </div>
        </div>
        
        <?php
        if (isset($_POST['username'])) {

            //variabili POST con anti sql Injection
            $username=mysqli_real_escape_string($con, $_POST['username']); //faccio l'escape dei caratteri dannosi
            $password=mysqli_real_escape_string($con, $_POST['password']);
            $errorMessage = "";

            $query = "SELECT * FROM admin WHERE username = '$username' AND password = '".md5($password)."' ";
            $ris = mysqli_query($con, $query);
            $riga= mysqli_fetch_array($ris);   
            
            //echo $query;

            /*Prelevo l'identifier dell'utente */
            $cod=$riga['username'];
            $enabled=$riga['attivo'];

            $livello=$riga['permessi'];

            /* Effettuo il controllo */
            if ($cod == NULL) $trovato = 0 ;
            else $trovato = 1;  

            /* Username e password corrette */
            if(($trovato == 1) AND ($enabled == 1)){

                $_SESSION['autorizzato'] = 'autorizzato' ;

                /*Registro il codice dell'utente*/
                $_SESSION['cod'] = $cod;

                $session_id = rand(100000,999999);
                
                $cookie_name = "USERNAME";
                $cookie_value = $cod;
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                
                $cookie_name = "SESSION_ID";
                $cookie_value = $session_id;
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

                echo '<script language=javascript>document.location.href="index.php"</script>'; 

            } else {
                if($enabled != 1){
                    $errorMessage = "disabled user";
                } else {
                    $errorMessage = "Wrong credentials";
                }
            }
        }
        ?>

        <?php if($errorMessage != ""){ ?> 
            <div class="submit-response failure" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <strong>Error!</strong> <?php echo $errorMessage; ?>.
            </div>
        <?php } ?>

        <div id="login">
            <form method="post">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Insert username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Insert password" required>
                </div>

                <div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Accedi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>
    