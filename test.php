<?php

include 'php/GUI.php';
include 'php/testGUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Loading basic jquery -->
<script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>

<script type="text/javascript">

</script>

</head>
<title>Comicadia - Comics</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id='IndexContent'>
		<?php
		buildCadenceTweets();
		?>
	<?php
	buildSidebar();
	?>
		<div class="clear"></div>
	</div> <!-- End IndexMain -->
	</div> <!-- end BodyMain -->
	<?php
	buildFooter();
	?>
</div><!-- End MainContent -->
</body>
</html>