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
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>

<!-- Loading basic jquery -->
<?php

if(isset($_GET["NewsID"]))
{
	$NewsID = $_GET['NewsID'];
	if(doesNewExistByID($NewsID))
	{
		buildComicadiaNewsDesc($NewsID);
	}
	else
	{
		print("<meta name='description' content='This News article was either deleted or never existed.' />");
	}
}
else
{
	
	print("<meta name='description' content='Comicadia news aims at informing the webcomic community about important resources, updates, conventions or other such news worthy pieces of information that could help benefit the community as a whole! It doesn't necessarily mean the news is directly related to Comicadia, but it is a nice place where you can find creators sharing links to articles.' />");
}
?>

<?php 
	loadGoogleAds();
?>
</head>
<title>Comicadia - News</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id='IndexContent'>
		<?php 	
		$articlesPerPage = 8;
		if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
		{
			$alias = $_SESSION['Alias'];
			$email = $_SESSION['Email'];
			$type = getUserType($alias);
			$User = getUserDetails($alias);
			
			if($type == 'Member')
			{
				$totalArticles = getMemberNewsCount();
			}
			else if( $type == 'Admin' )
			{
				$totalArticles = getAllNewsCount();
			}
			else
			{
				$totalArticles = getPublicNewsCount();
				$type = 'Public';
			}
			$totalPages = ceil($totalArticles / $articlesPerPage);
		}
		else
		{
			$totalArticles = getPublicNewsCount();
			$type ='Public';
			$totalPages = ceil($totalArticles / $articlesPerPage);
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
		if(isset($_GET["NewsID"]))
		{
			$NewsID = $_GET["NewsID"];
			buildSpecificNews($NewsID);
		}
		$startArticle = ($pageNumber - 1) * $articlesPerPage;
		print("	<h3><i class='fa fa-newspaper-o'></i> News </h3>
				<div id='newsPanel'>");
		buildNewsPanelByTypeAsPerPagination($type, $startArticle, $articlesPerPage);
		
		
		if($totalArticles != 0)
		{
			print("<div class='clear'></div> <div id='paginationDiv'>");
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
				else
				{
				}
			}
			print("</div>");
		}
		else
		{
			print("<div id='paginationDiv'>");
			print("No News at this time.");
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
	</div>
	<?php 
	buildFooter();
	?>
</body>
</html>