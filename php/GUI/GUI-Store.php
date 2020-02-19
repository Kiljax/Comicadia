<?php

include '../php/Functions/Functions-Store.php';
/*

Store code

*/

function buildGenericMerchPage($startArticle, $articlesPerPage)
{
	$MerchList = getAllMerchByPaginationSortedByNew($startArticle,$articlesPerPage);
	buildMerchPanelFromList($MerchList);
}

function buildMerchPageForComic($ComicID, $startArticle, $articlesPerPage)
{
	$MerchList = getAllMerchForComic($ComicID,$startArticle,$articlesPerPage);
	buildMerchPanelFromList($MerchList);
}

function buildMerchPageByType($Type, $startArticle, $articlesPerPage)
{
	$MerchList = getAllMerchByType($Type,$startArticle,$articlesPerPage);
	buildMerchPanelFromList($MerchList);
}

function buildMerchDetails($ItemID)
{
	$Item = getItemDetails($ItemID);
	buildItemDetailsPanelOrFull($Item, "full");
}

function buildMerchPanelFromList($MerchList)
{
	if($MerchList->getRecord())
	{
		foreach($MerchList->getRecord() as $Item)
		{
			
			buildItemDetailsPanelOrFull($Item, "panel");
		}
	}
	else
	{
		print("No Merch, yet.");
	}
}

function buildItemDetailsPanelOrFull($Item, $ViewType)
{
	if($ViewType == "panel" || $ViewType =="full")
	{
		$ItemID = $Item->value("ItemID");
		$Title = $Item->value("Title");
		$Type = $Item->value("Type");
		$ComicID = $Item->value("ComicID");
		$CreatorAlias = $Item->value("Alias");
		$URL = $Item->value("URL");
		$Desc = $Item->value("Desc");
		$ImgURL = $Item->value("ImgURL");
		print("<div id='".$ViewType."IndividualMerchWrap".$ItemID."' class='".$ViewType."IndividualMerchWrap'>");
		/* Merch Preview Image */
		print("<div class='".$ViewType."PreviewMerchIMG'>");
		print("<img src='$ImgURL' class='panelMerchPreview'>");
		print("</div> <!-- End panelPreviewMerchIMG -->");
		/* Merch Details */
		print("<div id='".$ViewType."MerchDetails".$ItemID."' class='panelmerchTextDetails'>");
		print("<p class='".$ViewType."MerchArticleID'><strong>Article:</strong> ".$ItemID."</p>");
		print("<p class='".$ViewType."MerchTitle'><strong>Title:</strong> ".$Title."</p>");
		print("<p class='".$ViewType."MerchType'><strong>Type:</strong> ".$Type."</p>");
		print("<p class='".$ViewType."MerchCreator'><strong>Creator:</strong> ".$CreatorAlias."</p>");
		if($ComicID)
		{
			$Comic = getComicDetails();
			$ComicName = $Comic->value("Name");
			print("<p class=\"".$ViewType."MerchComic\"><strong>Inspired by:</strong> ".$ComicName."</p>");
		}
		if($ViewType == 'full')
		{
			print("<p class='".$ViewType."MerchDetails'><strong>Details:</strong></p>");
			print("<p class='".$ViewType."MerchDescription'>$Desc</p>");
		}
		print("<div id='".$ViewType."checkItOutButton".$ItemID."Div'>");
		print("<a href=\"$URL\" target=\"_blank\"><div class='".$ViewType."goToMerchPageButton'><p class='".$ViewType."goToMerchPageButtonInternal'>Check it out!</p></a>");
		print("</div> <!-- End ".$ViewType."goToMerchPageButton -->");
		print("</div> <!-- End ".$ViewType."checkItOutButton".$ItemID."Div -->");
		print("</div> <! -- End ".$ViewType."MerchTextDetails -->");
		print("</div> <!-- End ".$ViewType."IndividualMerchWrap".$ItemID." -->");
	}
	else
		print("Not a valid viewtype.");
}

function buildPagination($totalArticles, $pageNumber,$totalPages,$Type,$Specific)
{
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
				if($Type == "Generic")
				{
					$additive = "<a href=\"?page=".$page."\">".$page."\"</a>";
				}
				elseif($Type == "Type")
				{
					$additive = "<a href=\"?page=".$page."&Type=".$Specific."\">$page</a>";
				}
				elseif($Type == "Comic")
				{
					$additive = "<a href=\"?page=".$page."&Comic=".$Specific."\">$page</a>";
				}
				elseif($Type == "Search")
				{
					$additive = "<a href=\"?page=".$page."&Search=".$Specific."\">$page</a>";
				}
				else
				{
					$additive = "<a href=\"?page=".$page."\">".$page."\"</a>";
				}
				print($additive);
			}
			else
			{
				
			}
		}
		print("</div><!-- end paginationDiv -->");
	}
	else
	{
		print("No merchandise is currently unavailable");
	}
}
?>