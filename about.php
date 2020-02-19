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
	xmlhttp.open("POST", "php/actions.php?F=Login&Email="+Email+"&Password="+Pass, true);
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

function sendContactMessage()
{
	var Name = document.getElementById('contactName').value;
	var Type= document.getElementById('contactTypeSELECT').value;
	var Email = document.getElementById('contactEmail').value;
	var Message = document.getElementById('contactMessage').value;
	var Success = true;
	var error = "Message not sent.";
	if(Name.trim() == '' )
	{
		Success = false;
		error = error + '<br>Name is required';
	}
	if(Email.trim() == '' )
	{
		Success = false;
		error = error + '<br>Email is required';
	}
	if(Message.trim() == '')
	{
		Success = false;
		error = error + '<br>Message cannot be blank';
	}
	if(Success)
	{
		xmlhttp = getxml();
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Message sent')
				{
				document.getElementById('contactMSG').innerHTML = response;
				document.getElementById('contactName').value = '';
				document.getElementById('contactEmail').value = '';
				document.getElementById('contactMessage').value = '';
				}
				else
				{
					document.getElementById('contactMSG').innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=submitContactMessage&Email="+Email+"&Name="+Name+"&Message="+Message+"&Type="+Type, true);
		xmlhttp.send();			
	}
	else
	{
		document.getElementById('contactMSG').innerHTML = error;
	}
}

</script>
<?php 
	loadGoogleAds();
?>
<meta name="description" content="Want to know more about the ins and outs of Comicadia? Then take a gander here and see what we're all about!" />

</head>
<title>Comicadia - Comics</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
		<h2>What is Comicadia?</h2>
		<p>Comicadia was established in 2017 as a webcomic collective with an emphasis on creating a collaborative and resourceful network of creators who not only help each other, but aim to help the webcomic community as a whole. Comicadia’s membership expands annually through <a href="https://www.comicadia.com/submissions.php">submission</a> drives. Submission periods will be advertised a month prior to the actual two week submission period.</p>
<p>Though Comicadia is primarily driven by member’s webcomics, Comicadia, as part of its mission of enriching the webcomic community, also operates several other services meant to foster collaboration. One such service, <a href="https://www.comicadia.com/MAX/">MAX</a>, short for <i>Monthly Artist eXchange</i>, has participants in an automated system draw fan-art for other members in the round; it’s a great way to build a library of fan-art of your original characters! Best of all, participants frequently include Comicadia creators and their close friends, so you never know who will be drawing your character.</p>
<h2>Who runs Comicadia?</h2>
<h3>Founder and Chief Programmer - <b>Jim Perry</b></h3>
<img src="https://comicadia.com/g_jim_bio.jpg" alt="Jim" align="left" hspace="5">
<p>Jim Perry is married to Alli Perry and the two have two wonderful, active, hyper children together. Graduated from CEGEP Heritage College in Quebec. Is an aspiring writer and writes for the webcomics My Hero!, Scrapper and the currently on hiatus Out of My Element. He is a computer software programmer by trade and programs side projects whenever he finds the spare time. Enjoys Roleplaying games, video games, board games, card games - ok, almost anything with the word 'games' in it. Probably insane, but we haven't been able to tie him down long enough to evaluate.</p>
<p>Jim serves as the Chief Developer of Comicadia and upcoming Comicadia projects. He seems to understand and harness the powers of 1s and 0s that make the website work. His magic is beyond us.</p>
<div class="clear"></div>
<h3>Branding and Marketing Lead - <b>David Davis</b></h3>
<img src="https://comicadia.com/g_david_bio.jpg" alt="David" align="left" hspace="5">
<p>David Davis is a graduate level student at CSU San Marcos studying literature and writing rhetoric. When he’s not banging his head against piles of books he writes and draws the webcomics Cosmic Dash and RGBots in addition to story collections and essays. He loves comics, science fiction, and horror to an alarming degree. He seems to be more beard than being these days.</p>
<p>David serves as the Branding and Marketing Lead and tries to figure out how to spread the word about the site, often screaming at people about the logo. We don’t know who he blackmailed to get to this position...</p>
<div class="clear"></div>
<h3>Community Manager - <b>Alli Perry</b></h3>
<img src="https://comicadia.com/g_alli_bio.jpg" alt="Alli" align="left" hspace="5">
<p>Alli Perry lives in Gatineau, Quebec, with her husband, Jim Perry, and her two adorably mischievious children. By day, she's an insurance broker. By night, she works diligently upon her current Webcomic: My Hero! or playing a videogame with her ever-needy husband. Known for her colourful art and the facial expressions she draws, Alli has been working on her craft since she can hold a pencil. She is colourblind, but don't let that fool you, she can sense movement!</p>
<p>Alli serves as the Community Manager of Comicadia and judging by that role she seems to find some perverse enjoyment in herding cats or pushing boulders uphill.</p>
<div class="clear"></div>
<h3>Site Designer - <b>Monica N. Galvan</b></h3>
<img src="https://comicadia.com/g_monica_bio.jpg" alt="Monica" align="left" hspace="5">
<p>Teacher, author, illustrator, web designer, witch & freak. Wrote a dozen of novels, does webcomics for fun & creates fantasy stuff! Loves tea and spicy food. You should read her current comic, MoonSlayer.</p> 
<p>Monica serves as Comicadia’s Site Designer. Though admittedly terse in her own self-description, her sweeping and elegant design skills have given Comicadia its lovely visual identity. We have no idea what sort of spells she utilized in making that happen.</p>
<div class="clear"></div>
<h3>Community Manager - <b>Kevin Hayman</b></h3>
<img src="https://comicadia.com/g_kevin_bio.jpg" alt="Kevin" align="left" hspace="5">
<p>Born in rural Mississippi, Kevin Hayman moved to the state capitol at a young age. Through the guidance of several of the greatest art teachers in the south he cultivated a love of drawing that he promptly did absolutely nothing with for six years. He graduated Magna Cum Laude in 2010 with an AA in graphic design. He currently lives in Clinton with his fiance, two roommates, two cats, and a basement that is DEFINITELY NOT USED FOR MURDER.</p> 
<p>Kevin’s inspirations are varied. From Akira Toriyama to Carl Barks and from Berkeley Breathed to Jim Lee. His writing style is heavily influenced by Douglas Adams, Robert Jordan, and sketch comedy troupes like Monty Python and The Kids in the Hall. He is also an avid fan of Mystery Science Theater 3000.</p>
<p>In 2000 he launched “Kota’s World”, a comic that was as popular as it was hard to describe. 
His current projects are “The Errant Apprentice”, a modern fantasy/alt-history series that many consider his magnum opus and “Mailbox Rocketship”, a revamp of Kota’s World that is as hard to describe as the original.
</p>
<p>He is a community manager with Comicadia. He is currently not classified as a squid.</p>
<div class="clear"></div>
<h2>Can my webcomic join Comicadia?</h2>
<p>Comicadia does run submission periods to expand the list of comics under the collective. Please visit the submissions page for further information.</p>
<h2>How can I support Comicadia?</h2>
<p>Comicadia was founded on the principle of “artists first,” so by supporting an artist directly you support Comicadia as a whole. Please consider disabling your ad-blocker on the sites in the Comicadia network or contributing directly to the Patreon accounts of your favorite creators!</p>
<p>
<ul style="list-style-type:square">
  <li><a href="https://www.patreon.com/Novasiri"><i>Aether Eternius</i></a></li>
  <li><a href="https://www.patreon.com/hpkomic"><i>Cosmic Dash</i> and <i>RGBots</i></a></li>
  <li><a href="https://www.patreon.com/Sketchmazoid"><i>Grapple Seed</i></a></li>
  <li><a href="https://www.patreon.com/everywhencomics/"><i>The Errant Apprentice</i> and <i>Mailbox Rocketship</i></a></li>
  <li><a href="https://www.patreon.com/julie"><i>Monster Soup</i></a></li>
  <li><a href="https://www.patreon.com/monicang"><i>MoonSlayer</i></a></li>
  <li><a href="https://www.patreon.com/alliperry"><i>My Hero</i></a></li>
  <li><a href="https://www.patreon.com/Silversong"><i>Silversong</i></a></li>
  <li><a href="https://www.patreon.com/terracomic"><i>Terra</i></a></li>
  <li><a href="https://www.patreon.com/jamilsart"><i>Jasper Gold</i></a></li>
</ul>
</p>
<h2>Advertise on Comicadia</h2>
<p>If you would like to advertise across the network of Comicadia websites, including the webcomics, please use the form below and select “advertising” from the drop down list. Generally our advertising preferences are all-ages in nature but we’ll look into any advertising request with an open mind.</p>
<h2>Want to work with us?</h2> 
<p>If you wish to work with Comicadia is some capacity we are happy to consult with you. Our artists and writers in the network are true talents and have many skills to offer. Please use the form below and select “work opportunity” from the drop down list for any inquiries. Some services we may be able to offer are brand illustrations, comics, writing, and more.</p>
<h2>Contact Form</h2>
<?php 
buildContactForm();
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
</body>
</html>
