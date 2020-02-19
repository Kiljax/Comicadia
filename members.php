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

function hasName(SearchBy)
{
	return SearchBy == 'Name';
}

function searchMembers()
{
	var Keyword = document.getElementById("searchMembersText").value;
	if(Keyword.trim() == '')
	{
		document.getElementById("searchMSG").innerHTML = 'Search requires anything but empty spaces';
	}
	else
	{
		window.location = "http://www.comicadia.com/members.php" + "?Search="+Keyword;
	}
}

</script>
<meta name="description" content="Browse through all members who are subscribed to Comicadia. Find an artist you may want to comission for an art piece or find out who else works on a comic you enjoy!" />

<?php 
	loadGoogleAds();
?>
</head>
<title>Comicadia - Members</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
		<?php 	
		$articlesPerPage = 10;
		if(isset($_REQUEST["Search"]))
		{
			$Search = $_REQUEST['Search'];
			$totalArticles = getMembersCountByKeyword($Search);
		}
		else
		{
			$totalArticles = getMemberCount();
		}
		
		$totalPages = ceil($totalArticles / $articlesPerPage);
		
		
		
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
		
		if(isset($_GET["MemberAlias"]))
		{
			$MemberAlias = $_GET["MemberAlias"];
			buildMemberProfile($MemberAlias);
		}
		
		$startArticle = ($pageNumber - 1) * $articlesPerPage;
		print("<div id='membersList'>	
		<h3><i class='fa fa-address-book'></i> Subscribers </h3>");
		
		buildMemberSearch();
		
		if(isset($_REQUEST['Search']))
		{
			$Search = $_REQUEST['Search'];
			buildMemberListFromSearchAsPerPagination($Search, $startArticle, $articlesPerPage);
		}
		else
		{
			buildMemberListAsPerPagination($startArticle, $articlesPerPage);
		}	
		print("</div>");
		
		if($totalArticles != 0)
		{
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
					if(isset($_GET['Search']))
					{
						print("<a href=\"?page=".$page."&Search=".$Search."\">$page</a>");
					}
					else
					{
						echo '<a href="?page=' . $page . '">' . $page . '</a>';
					}
				}
				else
				{
				}
			}
			print("</div>");
		}
		else
		{
			print("No Users found with that particular keyword");
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

		<?php
		buildSidebar();
		?>
		</div>
		<div class="clear"></div>
	</div><div class="clear"></div></div><div class="clear"></div>
	<?php 
	buildFooter();
	?>
</body>
</html>