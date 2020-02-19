<?php
include 'TestFunctions.php';

function buildAdsPanel()
{
	print("<input type='submit' id='GoToDashboard' class='leftPanelItem' value='Dashboard' onclick=\"window.location.href = 'https://www.comicadia.com/ads.php'\">");
    print("<form action='?' method='GET'>");
	print("<input type='submit' id='ManageCampaigns' class='leftPanelItem' value='Manage Your Campaigns' name='submit'>");
	print("<input type='submit' id='ManageBids' class='leftPanelItem' value='Manage Your Bids' name='submit'>");
	print("<input type='submit' id='ManageEntities' class='leftPanelItem' value='Manage Your Entities' name='submit'>");
	print("<input type='submit' id='ManageAds' class='leftPanelItem' value='Manage Your Ads' name='submit'>");
	print("<input type='submit' id='ViewStats' class='leftPanelItem' value='View Your Stats' name='submit'> ");
	print("<input type='submit' id='ContactUs' class='leftPanelItem' value='Contact Comicadia' name='submit'> ");
	print("</form>");
}

function buildAdvertiseWithUsWelcome($Alias)
{
	if($Alias == '')
	{
		buildGenericAdvertisementWelcome();
	}
	else
	{
		buildClientDashboard($Alias);
	}
}

function buildClientDashboard($Alias)
{
	print("Dashboard goes here.");
}


function buildManageYourComicadiaAds($Alias)
{
	print("<h2>Manage your Ads</h2>");
	buildCreateAd($Alias);
	buildManageCurrentAds($Alias);
}

function buildManageYourComicadiaEntities($Alias)
{
	print("<h2>Manage your Entities</h2>");
	buildCreateNewEntity($Alias);
	buildManageCurrentEntities($Alias);
}


function buildManageYourComicadiaCampaigns($Alias)
{
	print("<h2>Manage your Campaigns</h2>");
	buildCreateAdCampaign($Alias);
	buildManageCurrentAdCampaigns($Alias);
}
function buildManageCurrentAds($Alias)
{
	$AdCount = getCountOfAdsByAlias($Alias);
	print("<input type='button' id='viewCurrentAdsClickable' value='Your Current Ads'>");
	
	print("<div id='viewCurrentAdsInternal' class='Internal'>");
	
	print("</div><!-- end userCurrentAds -->");
	print("<script>");
	print("$('#viewCurrentAdsClickable').click
		(
		function()
		{
			updateCurrentAds('$Alias');
			$('#viewCurrentAdsInternal').slideToggle();
		});");
	print("</script>");
}

function buildCreateAd($Alias)
{
	print("<input type='button' id='testCreateEntireTransaction' value='Test Creating Stuff' onclick='testCreation()'>");
	print("<div id='testingstuff'></div>");
	print("<input type='button' id='createAdCampaignClickable' value='Create an Advertisement'>");
	print("<div id='createAdCampaignInternal' class='Internal'>");
	print("<strong>Ad Name:</strong> <input type='text' id='adNameText'><br>");
	print("<strong>Entity:</strong> <select id='useCurrentEntitySelect'>");
	$userEntities = getEntitiesOfUser($Alias);
	foreach($userEntities->getRecords() as $Entity)
	{
		$ComicID = $Entity->value("ComicID");
		$EntityID = $Entity->value("EntityID");
		$Name = $Entity->value("Name");
		if($ComicID == '' AND $EntityID != '')
		{
			$ID = $EntityID;
		}
		elseif($ComicID != '' AND $EntityID == '')
		{
			$ID = $ComicID;
		}
		else
		{
			$ID = '';
		}
			print("<option value='$ID'>$Name</option>");
	}
	print("</select>");
	print("<div id='createAdLink'><strong>Link address:</strong> <input type='text' id='createAdLinkText'><sup>*If left blank, the URL of the entity will be used</sup><br></div><!-- end createAdLink -->");
	print("<div id='availableAdTypes'>");
	print("<strong>Available ad types:</strong> <select id='adTypeSelect'>");
	$AdTypes = getValidAdTypes();
	foreach($AdTypes->getRecords() as $AdType)
	{
		$AdName = $AdType->value("Name");
		$AdHeight = $AdType->value("Height");
		$AdWidth = $AdType->value("Width");
		print("<option value='$AdName'>$AdName ($AdWidth pixels x $AdHeight pixels)</option>");
	}
	print("</select>");
	print("<h2>Media</h2>");
	print("<h3>Add Media</h3>");
	print("<input type='button' id='addMediaClickable' value='Add media'>");
	print("<div id='addMediaInternal' class='Internal'>");
	print("<input type='button' id='addMediaFromLocalClickable' value='From local machine'>");
	print("<input type='button' id='addMediaFromWebClickable' value='From the web'>");
	
	print("<div id='addMediaFromLocalInternal'>");
	print("<strong>Location:</strong> <input type='file' id='addMediaFromLocalFile'>");
	print("<br><input type='button' id='uploadMediaFromLocalButton' value='Upload' onclick=\"uploadMediaFromLocal('$Alias');\">");
	print("</div><!-- addMediaFromLocalInternal -->");
	
	print("<div id='addMediaFromWebInternal'>");
	print("URL: <input type='text' id='addMediaFromWebText'>");
	print("<br><input type='button' id='uploadMediaFromWebButton' value='Upload' onclick=\"uploadMediaFromWeb('$Alias');\">");
	
	print("</div><!-- addMediaFromWebInternal -->");
	print("<div id='uploadMediaMSG'></div>");
	print("</div><!-- end addMediaInternal -->");
	
	print("<h3>Your available media</h3>");
	print("<div id='currentlyAvailableMedia'></div>");
	print("</div><!-- End availableAdTypes -->");
	print("<div class='adControls'><input type='button' id='createNewAd' onclick=\"createNewAd('$Alias');\" value='Create Ad'></div><!-- end adControls -->");
	print("<div id='createNewAdMSG' class='errMSG'></div> <!-- end createNewAdMSG -->");
	print("</div><!-- End createAddCampaignInternal -->");
	print("<script>");
	print("$('#createAdCampaignClickable').click
		(
		function()
		{
			$('#createAdCampaignInternal').slideToggle();
		});");
	print("$('#addMediaClickable').click
		(
		function()
		{
			$('#addMediaInternal').slideToggle();
		});");
	print("$('#addMediaFromWebClickable').click
		(
		function()
		{
			$('#addMediaFromLocalInternal').hide();
			$('#addMediaFromWebInternal').show();
		});");
	print("$('#addMediaFromLocalClickable').click
		(
		function()
		{
			$('#addMediaFromLocalInternal').show();
			$('#addMediaFromWebInternal').hide();
		});");
	print("$('#adTypeSelect').change
		(
		function() 
		{
			updateAvailableAds();
		});");
	print("$('#useCurrentEntitySelect').change
		(
		function() 
		{
			updateAvailableAds();
		});");
	print("</script>");
}

function buildCreateNewEntity($Alias)
{
	print("<input type='button' id='createNewEntityClickable' value='Create new entity'>");
	print("<div id='createNewEntityInternal' class='Internal'>");
	print("<strong>Name of Entity:</strong> <input type='text' id='newEntityNameText'><br>");
	print("<strong>Website of Entity:</strong> <input type='text' id='newEntityURLText'><br>");
	print("</div> <!-- end createNewEntityInternal -->");
	print("<script>");
	print("$('#createNewEntityClickable').click
		(
		function()
		{
			$('#createNewEntityInternal').slideToggle();
		});");
	print("</script>");
}

function buildManageCurrentEntities($Alias)
{
	print("<input type='button' id='manageCurrentEntitiesClickable' value='Manage current entities'>");
	print("<div id='manageCurrentEntitiesInternal' class='Internal'>");
	
	print("Current Entities go here!");
	
	print("</div> <!-- end manageCurrentEntitiesClickable -->");
	print("<script>");
	print("$('#manageCurrentEntitiesClickable').click
		(
		function()
		{
			$('#manageCurrentEntitiesInternal').slideToggle();
		});");
	print("</script>");
}

function buildCreateAdCampaign($Alias)
{
	$AvailableEntities = getEntitiesforUser($Alias);
	$SelectText = '';
	foreach($AvailableEntities->getRecords() as $Entity)
	{
		$RoleList = $Entity->value("Role");
		$IsCreator = false;
		
		foreach($RoleList as $Role)
		{
			if($Role == 'Creator' || $Role == 'Co-Creator')
			{
				$IsCreator = true;
			}
		}
		if($IsCreator)
		{
			$EntityName = $Entity->value("Name");
			$ComicID = $Entity->value("ComicID");
			$EntityID = $Entity->value("EntityID");
			if($ComicID != '')
			{
				$EntityID = $ComicID;
			}
			
			$SelectText .= "<option value='$EntityID'>$EntityName</option>";
		}
	}
	if($SelectText == '')
	{
		print("You have no Entities registered<br>");
		print("<form action='?' method='GET'>");
		print("<input type='submit' value='Manage Entities' id='ManageEntitiesButton'>");
		print("</form><br>");
	}
	else if(getAvailableAdBlockCountForUser($Alias) == 0)
	{
		print("You have no ads available to run at this time.<br>");
	}
	else
	{
		$AvailableAdBlock = getAvailableAdBlocksForUser($Alias);	
		print("<h3>Start an ad campaign</h3>");
		print("<strong>Ad block to run: </strong>");
		print("<select id='chooseAdBlockForCampaignSelect'>");
		foreach($AvailableAdBlock->getRecords() as $AdBlock)
		{
			$AdName = $AdBlock->value("Name");
			$AdID = $AdBlock->value("AdID");
			print("<option value='$AdID'>$AdName</option>");
		}
		print("</select><br>");
		print("<strong>Campaign:</strong> <select id='campaignTypeSelection'>");
		$CampaignList = getCampaigns();
		foreach($CampaignList->getRecords() as $Campaign)
		{
			$Cost = $Campaign->value("Cost");
			$Views = $Campaign->value("Views");
			$Name = $Campaign->value("Name");
			$CampaignID = $Campaign->value("CampaignID");
			print("<option value='$CampaignID'>$Name: $$Cost =  ".number_format($Views)." views </option>");
		}
		print("</select><br>");
		print("<strong>Requested Start Date:</strong> <input type='text' id='userCurrentEntityStartDate' name='currentEntityStartDate'>");
		print("<br>");
		print("<input type='button' id='submitCampaignWithCurrentEntityForReview' value='Submit Campaign for review' onclick=\"submitCampaignForReview('$Alias');\">");
		print("<div id='submitCampaignForReviewMSG' class='errMSG'></div>");
		print("<script>");
		print("$(function(){
				$('*[name=currentEntityStartDate]').appendDtpicker({
										\"dateFormat:\": \"DD-MM-YYYY\",
										\"futureOnly\": true,
										\"dateOnly\": true											
				});
			});");
		print("</script>");
	}	
}

function buildManageCurrentAdCampaigns($Alias)
{
	print("<h3>Campaigns Being Processed</h3>");
	print("<input type='button' value='View Campaigns in Process' id='viewCampaignsInProcessButton'>");
	print("<div id='campaignsInProcessInternal' class='Internal'>");
	if(getCountOfAdsInTransit($Alias) > 0)
	{
		$CampaignList = getUserCampaignsInTransit($Alias);
		foreach($CampaignList->getRecords() as $Campaign)
		{
			$AdID = $Campaign->value("AdID");
			$AdStatus = $Campaign->value("Status");
			$DateSubmitted = $Campaign->value("DateCreated");
			$DateSubmitted = date('F jS, Y', $DateSubmitted/1000);
			$DateRequested = $Campaign->value("RequestedDate");
			$DateRequested = date('F jS, Y', $DateRequested/1000);
			$AdName = $Campaign->value("AdName");
			$AdType = $Campaign->value("AdType");
			$CampaignName = $Campaign->value("CampaignName");
			$Cost = $Campaign->value("Cost");
			$Views = $Campaign->value("Views");
			print("<div class='adInTransitBlock'>");
			print("<strong>Name: </strong>$AdName<br>");
			print("<strong>Date Submitted: </strong>$DateSubmitted<br>");
			print("<strong>Requested start date:</strong> $DateRequested<br>");
			print("<strong>Ad Type:</strong> $AdType<br>");
			print("<strong>Campaign Requested: </strong>$CampaignName<br>");
			print("<strong>Value:</strong> ".number_format($Views)." views for $$Cost<br>");
			print("<strong>Status:</strong> $AdStatus<br>");
			print("</div><!-- end adInTransitBlock -->");
		}
	}
	else
	{
		print("You have no ads currently in transit");
	}
	print("</div><!-- end campaignsInProcessInternal -->");
	
	print("<script>");
	print("$('#viewCampaignsInProcessButton').click
		(
		function()
		{
			$('#campaignsInProcessInternal').slideToggle();
		});");
	print("</script>");
	
	print("<h3>Active Campaigns</h3>");
	print("<input type='button' value='View Active Campaigns' id='viewActiveCampaignsButton'>");
	print("<div id='activeCampaignsInternal' class='Internal'>");
	$RunningAds = getAllActiveAdsByAlias($Alias);
	foreach($RunningAds->getRecords() as $ActiveAd)
	{
		$AdRunID = $ActiveAd->value("AdRunID");
		$CampaignName = $ActiveAd->value("CampaignName");
		$ViewsPurchased = $ActiveAd->value("PurchasedViews");
		$DatePurchased = date('jS, M, Y', $AdRunID /1000);
		$RequestedDate = $ActiveAd->value("RequestedDate");
		$RequestedDate = date('jS, M, Y',$RequestedDate /1000);
		$CurrentViews = getViewsForAdRun($AdRunID);
		
		print("$AdRunID<br>$CampaignName<br>$ViewsPurchased<br>$DatePurchased<br>$RequestedDate<br>$CurrentViews");
	}
	print("</div><!-- end activeCampaignsInternal -->");
	print("<script>");
	print("$('#viewActiveCampaignsButton').click
		(
		function()
		{
			$('#activeCampaignsInternal').slideToggle();
		});");
	print("</script>");
}

function buildComicBiddingFrame($AdSpaceID, $Alias)
{
	$UserHasBids = false;	
	if(isUserBiddingOnSpecificComicAdSpace($AdSpaceID, $Alias))
	{
		$UserHasBids = true;
		$Comic = getComicDetailsByAdSpaceID($AdSpaceID);
		$ComicName = $Comic->value("Name");
		$ComicID = $Comic->value("ComicID");
		$BidDetails = getBidDetailsForAdSpace($AdSpaceID, $Alias);
		$BidID = $BidDetails->value("BidID");
		$BidStatus = $BidDetails->value("Status");
		$CurrentBid = $BidDetails->value("CurrentBid");
		$MaxBid = $BidDetails->value("MaxBid");
		$AdSpaceType = getAdSpaceType($AdSpaceID);
				
	}
	else
	{
		$BidID = '';
		$BidStatus = 'None';
		$Comic = getComicDetailsByAdSpaceID($AdSpaceID);
		$ComicName = $Comic->value("Name");
		$ComicID = $Comic->value("ComicID");
		$CurrentBid = 0;
		$MaxBid = 0;
		$AdSpaceType = getAdSpaceType($AdSpaceID);
	}
	if(checkIfUserHasAnyActiveAdsThatMatchAdType($AdSpaceType,$Alias))
	{
		$UserHasMatchingAds = true;
	}
	else
	{
		$UserHasMatchingAds = false;
	}
	print("<div id='bidDetails$AdSpaceID' class='".$BidStatus."-bid-frame'>");
	if($UserHasBids)
	{
		
		if(IsUserCurrentlyWinningBidOnAdSpace($Alias,$AdSpaceID))
		{
			print("<div class='currently-winning-bid'>");
			if($BidStatus == 'Active'){
				print("<div class='current-bid-winner-img'></div><!-- end current-bid-winner-img -->");
				print("You are currently winning this bid!");
			}
			elseif($BidStatus == 'Paused')
			{
				print("<div class='potential-bid-winner-img'></div><!-- end potential-bid-winner-img -->");
				print("You could be winning this bid!");
			}
			else
			{
				print("Unknown bid status. Please contact the administrator");
			}
			print("</div><!-- end currently-winning-big' -->");
		}
		else
		{
			print("<div class='currently-not-winning-bid'></div>");
			print("<div class='not-winning-bid'>");
			print("You are not currently winning this bid");
			print("</div>");
		}
	}
	print("<div id='bidFrame$AdSpaceID' class='bid-details'>");
	print("<strong>Comic Name:</strong> $ComicName<br>");
	$AdDimensions = getMediaTypeDimensions($AdSpaceType);
	$AdHeight = $AdDimensions->value("Height");
	$AdWidth = $AdDimensions->value("Width");
	print("<div class='AdType'><div class='$AdSpaceType-example'></div><!-- end $AdSpaceType-example --><div class='adtype-caption'><sub>".$AdSpaceType.": $AdWidth x $AdHeight</sub></div><!-- end adtype-caption --></div><!-- end AdType -->");
	$CurrentWinningBid = getCurrentWinningBidOnComicAdSpace($AdSpaceID);
	$CurrentWinningBid = $CurrentWinningBid->value("CurrentBid");
	if($CurrentWinningBid == '')
	{
		$CurrentWinningBid = 0;
	}
	print("<strong>Current winning bid:</strong> <div id='current-winning-bid-$AdSpaceID'><strong>$CurrentWinningBid</strong></div><br>");
	print("<input type='hidden' id='current-winning-bid-value-$AdSpaceID' value='$CurrentWinningBid'>");
	print("<input type='button' id='refreshCurrentBidDetails$AdSpaceID' value='Refresh' onclick='refreshCurrentBidDetails(\"$Alias\",\"$AdSpaceID\");'><br>");
	print("<strong>Your current bid:</strong> <input type='text' id='currentBid".$AdSpaceID."Text' value='$CurrentBid'><br>");
	if($CurrentBid > 0)
	{
		$CostPerRotation = getCostPerRotation($CurrentBid);
		print("Approximate cost per 5 minute rotation: $CostPerRotation<br>");
	}
	print("<strong>Your maximum bid:</strong> <input type='text' id='maximumBid".$AdSpaceID."Text' value='$MaxBid'><br>");
	if($MaxBid >0)
	{
		$MaxCostPerRotation = getCostPerRotation($MaxBid);
		print("Approximate cost per 5 minute rotation: $MaxCostPerRotation<br>");
	}
	if($UserHasBids)
	{
		$AdList = getCurrentAdUserSetForBid($Alias, $AdSpaceID);
		$CurrentAdID = $AdList->value("AdID");
		$URLList = $AdList->value("URL");
		$AvailableAdsList = getUserActiveAdsThatMatchAdType($AdSpaceType,$Alias);
		print("<select id='advertisement-Select-$AdSpaceID'>");
		foreach($AvailableAdsList->getRecords() as $Ad)
		{
			$Selected = '';
			$AdID = $Ad->value("AdID");
			$AdName = $Ad->value("AdName");
			if($AdID == $CurrentAdID)
			{
				$Selected = 'Selected';
			}
			print("<option value='$AdID' $Selected>$AdName</option>");
		}
		print("</select><br>");
		print("Current Advertisement being used: ");
		foreach($URLList as $URL)
		{
			print("<div class='ad-preview-$AdSpaceType'><img src='$URL' /></div><!-- end ad-preview-$AdSpaceType -->");
		}
		print("<div id='user-current-ad-$AdSpaceType'></div>");
	}
	else
	{
		if($UserHasMatchingAds)
		{
			$AvailableAdsList = getUserActiveAdsThatMatchAdType($AdSpaceType,$Alias);
			print("<select id='advertisement-Select-$AdSpaceID'>");
			foreach($AvailableAdsList->getRecords() as $Ad)
			{
				$AdID = $Ad->value("AdID");
				$AdName = $Ad->value("AdName");
				print("<option value='$AdID'>$AdName</option>");
			}
			print("</select><br>");
		}
		else
		{
			print("You currently have no Active advertisements available for this ad type<br>");
		}
	}
	if($UserHasBids)
	{
		if($BidStatus =='Active')
		{
			print("<input type='button' id='pauseBid$AdSpaceID' value='Pause' class='pause-bid-button' value='Pause' onclick='pauseBidding(\"$Alias\",\"$AdSpaceID\");'>");
			print("<input type='button' id='resumeBid$AdSpaceID' value='Resume' class='resume-bid-button' value='Resume' onclick='resumeBidding(\"$Alias\",\"$AdSpaceID\");' style='display: none;'>");
		}
		else
		{
			print("<input type='button' id='pauseBid$AdSpaceID' value='Pause' class='pause-bid-button' pauseBidding(\"$Alias\",\"$AdSpaceID\");' style='display: none;'>");
			print("<input type='button' id='resumeBid$AdSpaceID' value='Resume' class='resume-bid-button' onclick='resumeBidding(\"$Alias\",\"$AdSpaceID\");'>");
		}
	}
	print("<input type='hidden' id='status".$AdSpaceID."Hidden' value='".$BidStatus."'>");
	if($UserHasMatchingAds)
	{
		print("<input type='button' id='submitNewCurrentBid$AdSpaceID' value='Place Bid' onclick='submitNewCurrentBid(\"$Alias\",\"$AdSpaceID\");'>");
		print("<div id='action-response-$AdSpaceID'></div><!-- end action-response-$AdSpaceID -->");
	}
	
	print("</div><!-- end bidFrame$AdSpaceID -->");
	print("</div> <!-- end bidDetails$AdSpaceID -->");
	print("<input type='button' onclick='testCalculate();' value='Test Calculate winner' id='testingButton'>");
	print("<div id='testresponse'></div>");
}

function buildComicsUserHasNoBidsOnPanel($Alias, $StartResult, $NumberOfResultsPerPage)
{
	
	$ComicList = getComicsUserHasNoBidsOn($Alias,$StartResult,$NumberOfResultsPerPage);
	foreach($ComicList->getRecords() as $Comic)
	{
		$ComicID = $Comic->value("ComicID");
		$AdSpaceID = $Comic->value("AdSpaceID");
		buildComicBiddingFrame($AdSpaceID, $Alias);
	}
}

function buildComicsUserCurrenltyHasBidsOn($Alias)
{
	$ComicList = getAllWebcomicsUserIsCurrentlyBiddingOn($Alias);
	foreach($ComicList->getRecords() as $Comic)
	{
		$ComicID = $Comic->value("ComicID");
		$AdSpaceID = $Comic->value("AdSpaceID");
		buildComicBiddingFrame($AdSpaceID, $Alias);
	}
}

function buildManageBidsPanel($Alias)
{
	
	print("<div id='CurrentAdsUserHasBidOn' class='Internal'>");
	print("</div> <!-- end CurrentUserAds -->");
	print("<div id='CurrentAdsWithNoBids' class='Internal'>");
	print("</div><!-- end CurrentAdsWithNoBids' -->>");
}

function buildGenericAdvertisementWelcome()
{
	print("This will be the generic message telling you to log in to see your ads or to sign up for your ads, as well as the sales pitch for why you should advertise with Comicadia.");
}
?>