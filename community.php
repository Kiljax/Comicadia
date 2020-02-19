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
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
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
	<h2>Community</h2>
		<p>Comicadia is a community that works in three primary forms. Please consider engaging with us in all three ways!</p>
	<h3>Comics</h3>
	<p>First of all, Comicadia’s core is the community that arises around the member webcomics. We encourage readers to follow their favorite creators and engage with them via the comic sites and social media. Our creators can be found all over the web, so be sure to check their profiles to find out where they can be found.</p> 
	<p>You can use the <a href="http://www.comicadia.com/members.php">member directory</a> to discover more about Comicadia creators and where they can be found on the web.</p>
	<h3>Discord</h3>
	<p>While building our community around the individual creators is the heart of Comicadia, one of the most important areas of our community is our Discord chat. If you are not familiar with Discord, <a href="https://discordapp.com/features">here is a great overview.</a></p>
	<a href="https://discord.gg/JHmKedD"><img src="http://www.comicadia.com/comicadia_discord.png"></a>
	<p>The Comicadia Discord provides always-online chat with various channels devoted to different topics both creative and general, and also serves as a hub of specific discussions for member’s webcomics and their Patron-exclusive channels. We also have scheduled chats and online workshops on the way, all through Discord.</p>
	<h3>Social Media</h3>
	<p>Comicadia is available across all your most used forms of social media, so check us out below.</p>
	<?php
	buildCommunitySocialMedia();
	?>
	</div>
		<?php
	buildSidebar();	
		?>
		<div class="clear"></div>
	</div>
	<?php
	buildFooter();
	?>
</div>
</body>
</html>