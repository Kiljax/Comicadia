<?php

include '../php/GUI.php';
include './php/MAXGUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="https://www.comicadia.com/font-awesome/css/font-awesome.min.css">
<link href="../style.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional Bootstrap theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
  $('#login-trigger').click(function(){
    $(this).next('#login-content').slideToggle();
    $(this).toggleClass('active');          
    
    if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
      else $(this).find('span').html('&#x25BC;')
    })
});

function goHome()
{
	location.href = 'https://www.comicadia.com/index.php';	
}

function AttemptLogin()
{	
	var Email = document.getElementById('username').value;
	var Pass = document.getElementById('password').value;
	
	var xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			if(xmlhttp.responseText != 'Success')
			{
				document.getElementById('PassMSG').innerHTML=xmlhttp.responseText;
			}
			else 
			{
				location.reload();				
			}
		}
	}
	xmlhttp.open("POST", "../php/actions.php?F=Login&Email="+Email+"&Password="+Pass, true);
	xmlhttp.send();
}

function getxml()
{
	if (window.XMLHttpRequest) 
	{
		xmlhttp = new XMLHttpRequest();
	} 
	else 
	{
  		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;	
}
</script>

<meta name="description" content="Want to know how to participate in MAX? Check out this page!" />

</head>
<title>Comicadia - About MAX</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
	<h2>MAX User Guide</h2>
	<h3>How to register</h3>
	<p>So you want to join in on the fun? That’s great! The first thing to do is register for MAX. So how do we do that?</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapRegister.jpg' alt='The option to register is found in the top right of all Comicadia pages'/></div>
	<p>Simply go to the header of the site (Comicadia.com) and use the registration link. You could also <a href='https://www.comicadia.com/register.php' target='_blank'>click right here</a> as well, I guess. But hey, you’ll need to log in at some point and it’s located right next to the registration link in the header.</p>
	<p>But hey, here is our super-minimal (and highly-temporary) registration form!</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapRegistrationPage.jpg' alt='A screencap of the registration page'/></div>
	<p>Please note that we’re so not about collecting your personal info. We just need a name and e-mail address. Most of the time you’ll just be known by your alias, or username.</p>
	<p>Once you’re all registered, just log back in using the link in the header, and we’re ready to get going with MAX!</p>
	<h3>Managing your profile</h3>
	<p>One of the first things we have to do as MAX users is manage our MAX profile. </p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapManageProfile.jpg' alt='A screencap of the MAX sidebar with Manage Profile highlighted'/></div>
	<p>A MAX profile allows us to manage our characters, join rounds, and generally be involved with the round to round experience. Click on that option on the menu of any MAX-related webpage, and you’ll see this:</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapProfile.jpg' alt='A screencap of Manage Profile page of MAX'/></div>
	<p>Okay, so… not quite that. You’re not hpkomic, are you? I didn’t think so…</p>
	<p>Anyway, what you are looking at is your profile page, it serves as our hub for managing your characters and offers a list of every round you’ve been attached to. Take a look here:</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapCharacters.jpg' alt='A screencap of Profile Character options'/></div>
	<p>Unfortunately there have been no rounds yet, so it’s a bit… empty.</p>
	<p>Anyway, let’s click on that “add character” because MAX revolves around characters!</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapAddCharacters.jpg' alt='A screencap of Profile Character options'/></div>
	<p>Clicking on the “add character” button generates a form where you’ll be able to fill out biographical info about your character. You don’t need to fill it all out, but we find it helps to do so.</p>
	<p>We also suggest that if you do a bio that you keep them short, or, even better, include a link to more reference images, or a link to the webcomic they originate from. You see, while we allow you to upload reference images internally, we only allow for about 5 or 6 pictures. So if you have a nice gallery of images of the character somewhere the bio might be the best way to share that info. Your artist for that round will thank you.</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapCompletedCharacterProfile.jpg' alt='A screencap of Profile Character options'/></div>
	<p>So… here is what Anda Bandit’s bio looks like. Once you hit “save” you’re done and can start adding images… or go back to the character in your manage characters pane and make any changes with “edit.”</p>
	<p>So how about we upload some reference images!</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapBanditExample.jpg' alt='A screencap of the Bandit character as an example'/></div>
	<p>Clicking on the “references” button generates the reference pane that lets you upload a new reference, or delete references, or even set one reference as a thumbnail for the character.</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapBanditSetReference.jpg' alt='A screencap of the Bandit character thumbnail being set as the primary reference'/></div>
	<p>Uploading is pretty easy. Just select upload from machine (your computer) or from the web, and then hit upload. You’ll get a confirmation… and then that is it.</p>
	<div class='screencrap'><img src='https://www.comicadia.com/MAX/media/screencapUploadReference.jpg' alt='A screencap of the upload reference process'/></div>
	<h3>Joining a Round</h3>
	<p>In order to join upcoming round, simply visit the MAX homepage and check under our lovely splash image of myself and Maxine. The current sign-ups will be listed under the “Current MAX Round” heading.</p>
	<p>Once you’ve joined you’ll be given a user when the round starts, under the “Current MAX Round” heading.</p>
	</div> <!-- End IndexContent-->
	<?php
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
	}
	else
	{
		$alias = '';
	}
	buildMAXSidebar($alias);
	?>		
	
	<div class="clear"></div>

	</div>  <!-- End BodyMain-->
	<?php 
	buildFooter();
	?>
	
</div>
</body>
</html>