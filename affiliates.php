<?php

include './php/GUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="../font-awesome/css/font-awesome.min.css">
<link href="../style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="../js/defaultLoad.js"></script>

<script type="text/javascript">

</script>

</head>
<title>Comicadia - Affiliates</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id='IndexContent'>
		<p>Here is a directory of Comicadia associates and partners.</p>
		<p><a href="https://aradiacollective.weebly.com/"><img src="http://www.comicadia.com/affiliate_link_aradia.png"></a></p>
		<p>Aradia: A Magical Girls Comic Collective</p>
		<p><a href="https://discord.gg/zJjnVk6"><img src="http://www.comicadia.com/affiliate_link_learningcomics.png"></a></p>
		<p>Learning Comics Discord Server</p>
		
	</div> <!-- End IndexContent -->
	<?php
	buildSidebar();
	?>
		<div class="clear"></div>

	</div> <!-- end BodyMain -->
	<?php
	buildFooter();
	?>
</div><!-- End MainContent -->
</body>
</html>
