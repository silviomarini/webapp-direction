<?php session_start();if(!isset($_COOKIE['utente_evento'])){setcookie("utente_evento",time(),time()+31556926 ,'/');	}
	include "server/db.php";
?>
<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	
	<script src="asset/js/jquery.js"></script>

	<link rel="stylesheet" href="asset/css/style.css" type="text/css" />
	
	<title>Privilege Web App</title>
</head>



<body>
	<div id="wrapper">
				
		<div class="header">
		<div class="bg"></div>
		<div class="logo">
		</div>
		
		<?php
			$evento = mysqli_fetch_array(mysqli_query($con,"Select * from streamings order by ID DESC LIMIT 1 "));
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

		<?php 
			if($cover == "") { $cover = "cover1608542135.jpeg"; } 
			else {
				if (!file_exists("asset/event-covers/".$cover)) {
					$cover = "cover1608542135.jpeg";
				}

			}
		?>
		<style>
			.header .bg {
				width: 100%;
				height: 100%;
				background-image: url("<?php echo "asset/event-covers/".$cover; ?>") !important;
				background-size: cover;
			}
		</style>
				
        <div class="about">
            <div class="title">
                
            </div>
            <div class="body" style="min-height:165px">   
				<a class="item active" id="questionsMenu" href="#" onclick="switchTab('questions')"><span>Questions</span></a>
				<a class="item" id="pollsMenu" href="#" onclick="switchTab('polls')"><span>Polls</span></a>
            </div>
        </div>

		<div id="polls" style="display:none;">
			<div id="currentPoll" style="text-align:center;">
				
			</div>
			<div class="submit-response success" style="display:none;" id="alertDomanda">
				<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
				<strong>Answer sent!</strong>
			</div>
		</div>

		<div id="questions" style="display:block;">
			
				<div class="content">   
					<div id="chat_evento">

							<div id="questionInsert">
								<div >
									<textarea placeholder="Write here, feel free to add your name." 
											name="question" 
											id="question"
											style="width:70%; border: 3px solid rgb(148 49 50);
											padding:5px;font-family: Tahoma, sans-serif; height:200px;
											font-size: x-large;"
											row="10"
										></textarea>
								</div>
							</div>
							<div class="submit-response success" style="display:none;" id="alertQuestion">
								<a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
								<strong>Question sent!</strong>
							</div> 
							
							<div>
								<div class="home-button" name="submitQuestion" id="submitQuestion" onclick="submitQuestion()">
									Submit the question
								</div>                                 
							</div>                                    
						
					</div> 
				</div>
		</div> 

		
	</div>

	

	<div id="gotoTop" class="icon-angle-up"></div>
	<script src="asset/js/customers.js"></script>
	
</body>
</html>