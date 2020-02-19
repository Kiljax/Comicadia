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

<meta name="description" content="Want to know more about the Multi-Artist eXchange? This is the spot!" />

</head>
<title>Comicadia - About MAX</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
	
	<h3>About MAX</h3>
	<div id='cadenceWantsYou'><img src='https://www.comicadia.com/MAX/media/CadenceWantsYou.png' /></div>
	<p>Hello and welcome, my name is Cadence, and I am here to introduce you to the Multi-Artist eXchange, or as we refer to it… MAX!</p> 
	<p>MAX is a monthly art exchange where a user is randomly assigned another user and will need to draw one of their characters.. Pairings are randomly automated so more than likely you are not drawing for someone who is drawing for you in that round. At the end of the month all is revealed and a gallery is generated of all the drawings uploaded during the month. Then we do it all over again!</p>
	<p>The founder of MAX, Reed Hawker (of the webcomic <a href='http://cultureshockcomic.com/' target='_blank'>Culture Shock</a>) has this to say about the history of MAX:</p>
	<div id='HawkQuote'>
	<p>The Multi-Artist eXchange launched in December of 2001 as a way for artists to exchange artwork and see their original characters drawn in another artist's style. Though beginning on a humble Tripod website, it grew into its own webspace a year later with an expanded roster of artists and its own improv-styled comic. In 2007, a year after its 100th round, MAX moved to Comic Dish, a comic hosting site that helped partially automate MAX's galleries and art assignments. In December of 2013, after its twelve-year anniversary, MAX's founder Hawk took a long-needed rest while exchanges continued privately amongst members in various art communities.</p>

	<p>Now, after a refreshing break, MAX is returning through the Comicadia comic community, with the same goal of bringing artists together to exchange original character art and hone their art skills by drawing something new and exciting.</p>
	</div>
	<p>So, what are some features of MAX at a glance?<p>
	<ul>
	<li>MAX takes <strong>quality control</strong> seriously and encourages users to put forth their best effort to remain involved.</li>
	<li><strong>No-shows result in users being blacklisted</strong> until they meet their obligations, so no gaming the system for free art!</li>
	<li>We allow users to <strong>upload reference art directly to the MAX website</strong> to make MAX a hub for the round, though we also encourage users to provide links to even more reference images!</li>
	<li>Our automated system works <strong>to reduce the chance you are drawing for the same person over and over again</strong>, and to reduce the odds of two users drawing for each other during that round.</li>
	<li>Each round includes an <strong>optional theme</strong> that you can specify if you would like to join in. For example, costume contests on Halloween!</li>
	<li><strong>Comicadia/MAX makes no claims in your IP or artwork</strong>, so don’t expect us to reprint and profit from what you upload. Additionally, you own your artwork, while the artist you draw for retains their IP. So if you wish to sell the art you do for another person please contact them accordingly.</li>
	</ul>
	<p>So why join MAX? Here are five reasons:</p>
	<ol>
	<li><strong>Increase your art skills</strong> by drawing characters outside of your comfort zone or general genre/style. If you’ve struggled with robots, why not challenge yourself to draw a robot character belonging to a user one month? Ever wanted to learn how to draw anthropomorphic characters, find a character that has those features.</li>
	<li>Technically <strong>it’s like getting fan art every month.</strong> That adds up into a huge gallery of fun interpretations of your characters. Even better, you might get an art from an artist you look up to. Imagine the joy of seeing your character drawn by a Comicadia comic creator!</li>
	<li><strong>Test new character concepts!</strong> Have an idea you’d love to explore? Why not include the character in your MAX roster and see what the reaction is. It might be a good way to see how marketable your new character could possibly be.</li>
	<li><strong>Friendships!</strong> Previous incarnations of MAX have generated great working relationships and friendships between users.. and even two marriages!</li>
	<li><strong>It’s completely free</strong> and you have an entire month to draw a picture. The commitment level is very low and designed to give everyone a comfortable amount of time to perform their best.</li>
	</ol>
	<p>
	So does all that sound pretty good to you? If so please check out our <a href='https://www.comicadia.com/MAX/guide.php'>User Guide</a> for information on how to join in on the fun!
	</p>
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