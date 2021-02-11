<?php session_start();
    include "../server/db.php";

    $sessionId = $_COOKIE["session_id"];

    $autorizzazione = $_SESSION['autorizzato'];
    $id_utente= $_SESSION['cod'];
    $livello = $_SESSION['livello'];

    if ($autorizzazione != "autorizzato") {
        echo '<script language=javascript>document.location.href="login.php?unauthorized"</script>'; 
    }

?>
<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="creativetown.it" />
	
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

            .modifica-text {
                float: left;
                width: 40%;
            }
        }

        @media screen and (max-width: 800px) {
            .modifica-input {
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

        .modifica-input {
            padding: 10px;
        }

    </style>
<?php

$success_update = 0;


if (isset($_POST['save_event'])){ 

    //save new cover to the server

    $coverImage = "";

    if(isset($_FILES['event_cover'])){

        $info = pathinfo($_FILES['event_cover']['name']);

        $ext = $info['extension']; // get the extension of the file

        $coverImage = "cover".time().".".$ext; 



        $target = 'event-covers/'.$coverImage;

        move_uploaded_file( $_FILES['event_cover']['tmp_name'], $target);

    }





    //save event to DB

    $sql="

    UPDATE streamings

    SET 

        identifier='".htmlentities($_POST['event_id'])."' ,

        nome='".htmlentities($_POST['event_name'])."' ,

        data='".htmlentities($_POST['event_date'])."' ,

        tipo='".htmlentities($_POST['event_type'])."' ,

        note='".htmlentities($_POST['event_note'])."' ,

        password='".htmlentities($_POST['event_pass'])."',

        cover='".$coverImage."'

    WHERE ID = ".$_POST['db_id']."";

    //echo $sql;

    mysqli_query($con,$sql) or die (mysqli_error($con));

    $success_update = 1;

}



//prepare data for event

if($query = mysqli_query($con,"Select * from streamings order by ID DESC LIMIT 1 ")){

    $event = mysqli_fetch_array($query);

    $bd_id = $event["ID"];

    $event_name = $event["nome"];

    $event_id = $event["identifier"];

    $event_date = $event["data"];

    $event_start_time = $event["start_time"];

    $event_end_time = $event["end_time"];

    $event_type = $event["tipo"];

    $event_note = $event["note"];

    $event_pass = $event["password"];

    $event_cover = $event["cover"];


} 

?>

<!-- cambio password -->

<?php


            //update event on save

            $active_tab="event";

			if (isset($_POST['save_password'])){ 
                $active_tab="user";


                $result="";

                $currentPass="";

                $new_pass = $_POST["new_pass"];

                

                //check old password

                if($query = mysqli_query($con,"Select password from admin WHERE ID = ".$_SESSION["ID_operatore"]." ")){

                    $user = mysqli_fetch_array($query);

                    $currentPass = $user["password"];

                } 



                if(md5($_POST["old_pass"]) != $currentPass){

                    $result = "OLD_PASS_NOT_MATCH";

                } else {

                    if($new_pass != "" && $new_pass == $_POST["retyped_pass"]) {

                    //update with the new one if ok

                    $sql="

                    UPDATE admin

                    SET 

                        password='".md5($new_pass)."' 

                    WHERE ID = ".$_SESSION["ID_operatore"]."";

                    //echo $sql;

                    mysqli_query($con,$sql) or die (mysqli_error($con));

                    $result = "PASS_UPDTED";

                } else {

                    $result = "NEW_PASS_NOT_MATCH";

                }

                }

			}

		?>


	<div id="wrapper" class="clearfix bgrTransparent">
				
		<div class="header">
		<div class="bg"></div>
		<div class="logo">
		</div>
		



		<h1 class="title" style="margin-left: 20px;">
			ADMIN PANEL
		</h1>
        <div class="log">
            <a href="logout.php">
                <p class="text">Logout</p>   
            </a>
        </div>
		
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
                
            </div>
            <div class="body" style="min-height:165px">   
				<a class="item <?php if ($active_tab=="event") { echo "active";}?>" id="questionsMenu" href="#" onclick="switchTab('questions')"><span>Event Data</span></a>
				<a class="item <?php if ($active_tab=="user") { echo "active";}?>" id="pollsMenu" href="#" onclick="switchTab('polls')"><span>Admin User</span></a>
            </div>
        </div>



        
		<div id="polls" <?php if ($active_tab=="event") { echo "style='display:none;'";}?>>
		<div id="top1">
        <?php
            if($result == "OLD_PASS_NOT_MATCH"){

                echo '

                    <div class="submit-response failure" id="alertDomanda2">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>

                        <strong>Old password is wrong!</strong>

                    </div> 

                ';

            } else if($result == "NEW_PASS_NOT_MATCH") {

                echo '

                    <div class="submit-response failure" id="alertDomanda2">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>

                        <strong>New passwords not match or it is invalid!</strong>

                    </div> 

                ';

            } else if($result == "PASS_UPDTED") {

                echo '

                    <div class="submit-response success" id="alertDomanda2">

                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>

                        <strong>Password successfully updated!</strong>

                    </div> 

                ';

            }



        ?>

        <form name="update_pass" method="POST">

            <?php echo '<input type="hidden" name="db_id" value="'.$bd_id.'" />'; ?>	

                <div class="form-row">

                    <div class="modifica-text"> <strong >Old password:</strong> </div>

                    <input type="password" class="modifica-input" name="old_pass" value="" />	

                </div>

                <div class="form-row">

                    <div class="modifica-text"> <strong>New password:</strong></div>

                    <input type="password" class="modifica-input" name="new_pass" value="" />

                </div>

                

                <div class="form-row">

                    <div class="modifica-text"> <strong>Re-type new password:</strong></div>

                    <input type="password" class="modifica-input" name="retyped_pass" value="" />

                </div>



                <div class="col-sm-4 text-right" style="width:100%; min-height: 50px;">

                    <input type="submit" name="save_password" value="Save" class="btn btn-primary hidden-xs" style="right: 0;margin-right: 0;float: right;display:block !important;" />

                </div>

            </form>

            </div>
                    </div>

                    <div id="questions" <?php if ($active_tab=="user") { echo "style='display:none;'";}?>>
                    <div id="top1">
                    <form name="event_update" method="POST" enctype='multipart/form-data'>

            <?php echo '<input type="hidden" name="db_id" value="'.$bd_id.'" />'; ?>	

                <div class="form-row">

                    <div class="modifica-text"> <strong >Event name:</strong> </div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_name" value="'.$event_name.'" />';

                    } else { 

                        echo "<span> ".$event_name." </span>";

                    } ?>

                </div>

                <div class="form-row">

                    <div class="modifica-text"> <strong>Event id:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_id" value="'.$event_id.'" />';
                        echo '<style>@media screen and (max-width: 800px) {#top1 {height: 800px;}}</style>';
                    } else { 

                        echo "<span> ".$event_id." </span>";

                    } ?>

                </div>

                

                <div class="form-row">

                    <div class="modifica-text"> <strong>Event date:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_date" value="'.$event_date.'" />';

                    } else { 

                        echo "<span> ".$event_date." </span>";

                    } ?>

                </div>



                <div class="form-row">

                    <div class="modifica-text"> <strong>Event type:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_type" value="'.$event_type.'" />';

                    } else { 

                        echo "<span> ".$event_type." </span>";

                    } ?>

                </div>



                <div class="form-row">

                    <div class="modifica-text"> <strong>Event password:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_pass" value="'.$event_pass.'" />';

                    } else { 

                        echo "<span> ".$event_pass." </span>";

                    } ?>

                </div>



                <div class="form-row">

                    <div class="modifica-text"> <strong>Event note:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="text" class="modifica-input" name="event_note" value="'.$event_note.'" />';

                    } else { 

                        echo "<span> ".$event_note." </span>";

                    } ?>

                </div>



                <div class="form-row">

                    <div class="modifica-text"> <strong>Event cover:</strong></div>

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="file" class="modifica-input" name="event_cover" value="'.$event_cover.'" />';

                    } else { 

                        echo "<span> ".$event_cover." </span>";

                    } ?>

                </div>



                <div class="col-sm-4 text-right" style="width:100%; min-height: 50px; margin-top:30px;">

                    <?php if (isset($_GET['modifica_evento']) && $success_update == 0){ 

                        echo '<input type="submit" name="save_event" value="Save" class="btn btn-primary hidden-xs" style="right: 0;margin-right: 0;float: right;display:block !important;" />';

                        echo '<a href="index.php"> <div class="btn btn-primary hidden-xs" style="right: 0; margin-right: 10px; float: right; width: 150px; border: 1px solid #943132; background-color: #943132; display: block !important; color: white; padding: 13px;">Cancel</div> </a>';

                    } else { 

                        echo '<a href="index.php?modifica_evento=1"> <div class="btn btn-primary hidden-xs" style="right: 0;margin-right: 5px;float: right;width:150px;display:block !important;">Edit</div> </a>';

                    } ?>

                    

                </div>

            </form>
            </div>
            <div id="top2">
            <div class="form-row">

                <div class="modifica-text"> <strong>Reset all questions for this event:</strong></div>

            

                <?php

                    if(isset($_GET["reset"])){

                        $sql_sondaggi="TRUNCATE TABLE questions";

                        mysqli_query($con,$sql_sondaggi);

                        echo '<div class="btn btn-primary hidden-xs" style="color: green; right: 0;float: right;width:182px;border:1px solid green;background-color:white;display:block !important;"> All questions deleted </div>';

                    } else {

                ?>

                    <a href="index.php?reset=questions"> <div id="reset_btn" class="btn btn-primary hidden-xs">Reset</div> </a>

                <?php		

                    }									

                ?>



                

        </div>
        </div>
		</div> 

		
	</div>

	

	<div id="gotoTop" class="icon-angle-up"></div>
	<script src="../asset/js/customers.js"></script>
	
</body>
</html>