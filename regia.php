<?php
//TODO:session handler

//includes
include('server/db.php');

//check filter to update queries
$supp_condition = "";
$active_options = true;
$active_title = "";
$active_type = "";
if(isset($_GET["filter"])){
	switch ($_GET["filter"]) {
		case 'selected':
			$supp_condition = " AND d_stato='y' ";
			$active_options = false;
			$active_title = "SELECTED QUESTIONS";
			$active_type = "SELECTED";
			break;
		case 'deleted':
			$supp_condition = " AND d_stato='n' ";
			$active_title = "DELETED QUESTIONS";
			$active_type = "DELETED";
			break;
		case 'done':
			$supp_condition = " AND d_stato='d' ";
			$active_title = "PROCESSED QUESTIONS";
			$active_type = "DONE";
			break;
		case 'all':
			$supp_condition = " AND d_stato='' ";
			$active_title = "LIVE QUESTIONS";
			$active_type = "ALL";
			break;
			
		default:
			//nothing
			break;
	}
}

?>

<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="creativetown.it" />

    <link rel="stylesheet" href="<?php echo $path; ?>asset/css/style.css" type="text/css" />
	<title>Privilege Web App</title>
</head>

<body class="stretched">
	<div id="wrapper" class="clearfix bgrTransparent">
				
		<div class="header">
		<div class="bg"></div>
		<div class="logo">
		</div>
		
		<?php
			$evento = mysqli_fetch_array(mysqli_query($con,"Select * from eventi order by ID DESC LIMIT 1 "));
			$titolo = "Privilege Web App";
			if($evento != null){
				$titolo = $evento['nome'];
			}
			$cover = $evento["cover"];
		?>
        
		<h1 class="title" style="margin-left: 20px;">
			<?php echo $titolo; ?>
		</h1>
		<div class="log">
			<?php if($page_type == "regia"){ ?>
				<a href="./regia.php?logout">
					<p class="text">Logout</p>   
				</a>
			<?php } ?>
		</div>
		</div>

		<?php if($cover != "") { ?>
			<style>
				.header .bg {
					width: 100%;
					height: 100%;
					background-image: url("<?php echo "asset/event-covers/".$cover; ?>") !important;
					background-size: cover;
				}
			</style>
		<?php } ?>
				
        <div class="about">
            <div class="title">
                <h2 class="text">Welcome to Privilege Web App!</h2>
            </div>
            <div class="body" style="min-height:165px">   
                <a class="item" href="#"><span>Polls</span></a>
				<a class="item" href="#"><span>Questions</span></a>
            </div>
        </div>
        

        <div id="regia_domande">
                
        </div>

        <div id="regia_sondaggi">

        </div>
    
    </div>
</body>