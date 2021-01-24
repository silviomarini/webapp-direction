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
		<div class="row" >
			
			<div class="col-md-12 bg">
				<div class="row">
					
				
				<div class="offset-md-1 col-md-10 paddingMobile pt-5"> 
					<div class="card mb-3">
						<div class="card-body ALL " style="padding:5px;">
							<div class="cont_risposta">
								 
								<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
							</div>
							<div class="cont_risposta" style="float:left;">
								<div class="active-status" style=""> LIVE QUESTIONS </div>
								 
									<a href="regia.php?filter=done"> <div class="pools-button small done" style=""> PROCESSED <BR/> QUESTIONS </div> </a>
																			 
									<a href="regia.php?filter=deleted"> <div class="pools-button small alert" style=""> DELETED <BR/> QUESTIONS </div> </a>
																			 
									<a href="regia.php?filter=selected"> <div class="pools-button small selected" style=""> SELECTED <BR/> QUESTIONS </div> </a>
								</div>
							</div>
						</div>

						<div class="" id="alert-domande-attivate">
						
						</div>
					</div>
				
					<div class="offset-md-1 col-md-10 paddingMobile pt-5" id="contDomande">
						<div class="card mb-3 " id="domanda_2">
							<div class="card-header">
								<div class="row">
									<div class="header-info sx"><strong>2)</strong> h <strong>18:05</strong></div>
										<div class="header-info center">
											<div class="float-right">
												<!-- GO LIVE -->
												
												<!-- YES -->
													 
														<span class="statoDomanda statoVerde" valore="y" id="2" onClick="change_status(this)">
															<i class="fa fa-check"></i></span> 
																												<!-- NO -->	
													 
														<span class="statoDomanda statoRosso" valore="n" id="2" onClick="change_status(this)">
														<i class="fa fa-times"></i></span> 
																												
												<!-- RESET -->	
														<span class="statoDomanda statoAzzera" style="visibility:hidden;"> <i class="fa fa-refresh"></i></span>  </span>
											</div>
											<div class="float-right" style="grid-template-columns: 100%;">
											</div>
										</div>

										<div class="header-info dx">

											<div style="display: inline-grid;">
											<!-- DONE -->
																												</div>

											<span> Status: </span>
											<span class="barraStato" id="barra_stato_2" style="background-color: transparent;"></span>

										</div>
									</div>
								</div>
								<div class="card-body">
									<p class="card-text">two</p>
								</div>
							</div>    
																
							<div class="card mb-3">
								<div class="card-body ALL " style="padding:5px; display:none;">
									<div class="cont_risposta">
											
											<a href="export.csv"> <div class="pools-button" style=""> Export </div> </a>
									</div>
									<div class="cont_risposta" style="float:left;">
										<div class="active-status" style=""> LIVE QUESTIONS </div>
											
											<a href="regia.php?filter=done"> <div class="pools-button small done" style=""> PROCESSED <BR/> QUESTIONS </div> </a>
																						
											<a href="regia.php?filter=deleted"> <div class="pools-button small alert" style=""> DELETED <BR/> QUESTIONS </div> </a>
																						
											<a href="regia.php?filter=selected"> <div class="pools-button small selected" style=""> SELECTED <BR/> QUESTIONS </div> </a>
										</div>
									</div>
							
								</div>


						</div> 
					</div> 
        		</div>

        <div id="regia_sondaggi">

        </div>
    
    </div>
</body>