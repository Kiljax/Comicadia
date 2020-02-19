<?php
include 'php/GUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">


    <!-- Optional Bootstrap theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<meta name="description" content="Comicadia is a webcomic collective which aims at improving the webcomic scene through developing tools for its community and supporting the creators of its fine works. Comicadia puts creators first." />
<?php 
	loadGoogleAds();
?>
</head>
<title>Comicadia Webcomic Collective</title>
<body>
<div id="MainContent">
			<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">

		<div id="IndexContent">
		<?php
				buildSplashPreview();
		?>		
			<div class="clear"></div>
			<div id="RecentlyUpdated">
				<h2><i class="fa fa-leaf" aria-hidden="true"></i> Comicadia Comics</h2>
			
				<?php buildHorizontalRSS(); ?>
			</div>
			<div class="clear"></div>
		</div>
		<?php
		buildSidebar();
		?>
	</div><div class="clear"></div></div>
	<?php
	buildFooter();
	?>
</body>
</html>