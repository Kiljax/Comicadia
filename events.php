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
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>
<script type="text/javascript">

function hasName(SearchBy)
{
	return SearchBy == 'Name';
}

function confirmAttendance(DateCreated,Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('eventMSG'+DateCreated).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=confirmAttendance"+"&Alias="+Alias+"&DateCreated="+DateCreated, true);
	xmlhttp.send();
	
}

function cancelAttendance(DateCreated,Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('eventMSG'+DateCreated).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=cancelAttendance"+"&Alias="+Alias+"&DateCreated="+DateCreated, true);
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
<meta name="description" content="Comicadia events are hosted by members of Comicadia and can range from birthday announcments to creator-reader meet-up planning to convention attendance. There is always something happening in Comicadia!" />
<?php 
	loadGoogleAds();
?> 

</head>
<title>Comicadia - Events</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
	<h3><i class="fa fa-calendar" aria-hidden='true'></i>  Events </h3>
		<?php 	
		$articlesPerPage = 6;
		if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
		{
			$alias = $_SESSION['Alias'];
			$email = $_SESSION['Email'];
			$type = getUserType($alias);
			$User = getUserDetails($alias);
			
			if($type == 'Member')
			{
				$totalArticles = getMemberEventsCount();
			}
			else if( $type == 'Admin' )
			{
				$totalArticles = getAllEventsCount();
			}
			else
			{
				$totalArticles = getPublicEventsCount();
				$type = 'Public';
			}
			$totalPages = ceil($totalArticles / $articlesPerPage);
		}
		else
		{
			$totalArticles = getPublicEventsCount();
			$type ='Public';
			$totalPages = ceil($totalArticles / $articlesPerPage);
			$alias = '';
		}
		if(!isset($_GET['page']))
		{
			$pageNumber = 0;
		}
		else
		{
			$pageNumber = (int)$_GET['page'];
			// Convert the page number to an integer
		}

		// If the page number is less than 1, make it 1.
		if($pageNumber < 1)
		{
			$pageNumber = 1;
			// Check that the page is below the last page
		}
		else if($pageNumber > $totalPages)
		{
			$pageNumber = $totalPages;
		}
		
		if(isset($_GET["EventID"]))
		{
			$EventID = $_GET["EventID"];
			buildSpecificEvent($EventID);
		}
		
		$startArticle = ($pageNumber - 1) * $articlesPerPage;
		print("<div id='eventPanel'>");
		buildEventPanelByTypeAsPerPagination($type, $startArticle, $articlesPerPage,$alias);
		
		print("<div id='paginationDiv'>");
		foreach(range(1, $totalPages) as $page)
		{
			// Check if we're on the current page in the loop
			if($page == $pageNumber)
			{
				echo '<span class="currentpage">' . $page . '</span>';
			}
			else if($page == 1 || $page == $totalPages || ($page >= $pageNumber - 2 && $page <= $pageNumber + 2))
			{
				echo '<a href="?page=' . $page . '">' . $page . '</a>';
			}
			print("</div>");
		}
		print("</div>");
		/*
		if(isset($_REQUEST['newsArticle']))
		{
			$NewsID = $_REQUEST['newsArticle'];
			$Author = $_REQUEST['Alias'];
			buildSpecificNews($NewsID,$Alias);
			buildAllNewsPanel();
		}
		else
		{
			buildNewsSearch();
			buildAllNewsPanel();
		}
		*/
		
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