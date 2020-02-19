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
		<h1>Comicadia Network</h1>
		<p>Comicadia Network links coming soon. Until then, please enjoy this brief overview of upcoming projects in development. Note that order listed on this page does not indicate the current developmental status of these projects.</p>
		<h2>MAX</h2>
		<p>MAX is a monthly, automated art exchange that assigns participants for that round an artist who they must draw for, while simultaneously having someone draw for them. In a lot of ways it is like a Secret Santa art exchange on a monthly basis: you get tasked to draw for someone in the round while someone else draws something for you. Furthermore, through the automation, you are not likely to get the same person as often as the pool of artists expands from round to round.</p>
		<p>Please visit the existing <a href="http://www.comicadia.com/MAX/">MAX page</a> for current information. More details will be made available soon as MAX is the first of the major Comicadia initatives.</p>
		<h2>Comicadia Presents</h2>
		<p><i>Comicadia Presents</i> is an exciting short-form webcomic anthology initiative that pays creators to run their work in <i>Comicadia Presents</i>.</p>
		<h2>The Comicadia Herald</h2>
		<p><i>The Comicadia Herald</i> is am ambitious new approach to webcomics journalism with an emphasis on egalitarian presentations of creators and their works.</p>
		<h3>Comicadia Podcast</h3>
		<p>Currently untitled, the Comicadia Podcast is a sub-project of <i>The Comicadia Herald</i>.</p>
		<h2>Webcomic Sandbox</h2>
		<p>More details within the next few months.</p>
		<h2>Quilt</h2>
		<p>More details within the next few months.</p>
	</div> <!-- End IndexMain -->
	<?php
	buildSidebar();
	?>
	<div class="clear"></div>
	<?php
	buildFooter();
	?>
	</div> <!-- end BodyMain -->
</div><!-- End MainContent -->
</body>
</html>