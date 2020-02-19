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
<meta name="description" content="Want to be a part of Comicadia? This page will give you the details you need to know before you apply to join the collective." />
</head>
<title>Comicadia - Submission Process</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
		<p><img src="https://www.comicadia.com/submissions_header.png" alt="Determined Cadence" align="middle"></p>
		<h2>The upcoming submission period is <b>TBD</b>.</h2>
<p>While Comicadia does not have an open submission policy, we encourage future applicants to make themselves noticeable in our existing community outside of annual submission periods. You never know if we might send out a special invite to someone who catches our eye. A great way to get yourself noticed is to pop into our <a href="https://discord.gg/JHmKedD">discord</a> community to share your work and engage in general discussion.</p>
<p>Comicadia encourages ambitious creators who want to go beyond just doing a webcomic and passively displaying a banner on their website. We prioritize creators who bring new ideas and resources to the table and are willing to put in work on building up the community in some aspect, whether it is engaging in the monthly <a href="https://www.comicadia.com/MAX/">MAX</a> rounds, or contributing articles and tutorials to <a href="http://undertheink.net/"><i>Under the Ink</i></a>. We're always on the lookout for additional skill sets that could help bolster our ranks and aid in developing features for the larger webcomic scene.</p>
<h2>Submission Requirements</h2>
<p>We require the following for any Comicadia submission during the submission period:</p>
<p>A submission package that covers the following:</p>
<ul>
<li>A title and link to the comic, obviously, with a list of all people attached to the creation of the comic including contact information</li>
<li>5 - 10 sample comics</li>
<li>An outline of an upcoming couple months of material (brief)</li>
<li>Why did you and your team decide to make a webcomic?</li>
<li>What are your aspirations for your webcomic?</li>
<li>Why do you want to be a member of Comicadia?</li>
<li>What is one improvement or suggestion you have for Comicadia or the webcomic community as a whole?</li>
<li>Were you referred by an existing member of Comicadia?</li>
</ul>
<p>Packets should be in .docx, PDF, .RTF forms. A Google Doc is acceptable as well.</p>
<h2>Comicadia Membership Requirements</h2>
<p>Comicadia members are required to:</p>
<ul>
<li>Display appropriate and unobtrusive Comicadia branding on their webcomic site</li>
<li>Display at least one of the network's ads</li>
<li>Participate in good faith with the community</li>
<li>Respond to monthly member discussion/catch up</li>
</ul>
<h2>Submission Process</h2>
<p>Your submission requires the following to move into the applicant pool:</p>
<ul>
<li>e-mail your submission package to <b>comicadiacollective@gmail.com</b> with the subject "Submission"</li>
<li>Fill out this <b><a href="https://goo.gl/forms/qnKiLa6XAnMtZsNE3">Submission Agreement</a></b></li>
</ul>
<p>Failure to follow this process may result in your submission being missed or moved aside in the submission review process. Voting and interviews will occur throughout the month of November. Good luck!</p>
</div>
		<?php buildSidebar(); ?>		
<div class="clear"></div>
	</div>
<?php 
	buildFooter();
	?>
</body>
</html>
