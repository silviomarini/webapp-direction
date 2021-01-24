<!DOCTYPE html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="author" content="creativetown.it" />

    <link rel="stylesheet" href="<?php echo $path; ?>asset/css/style.css" type="text/css" />
	<title>Privilege Web App</title>
</head>

<body>
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
	</div>

	<div id="polls">
		Poll
	</div>

	<div id="questions">
		Question
	</div> 

	<div id="gotoTop" class="icon-angle-up"></div>
	<script src="asset/js/jquery.js"></script>
</body>
</html>