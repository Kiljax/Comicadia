<?php
include 'functions.php';
include 'testGUI.php';

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

$function = $_REQUEST['F'];

if($function == 'reloadAvailableMediaForType')
{
	$ID = $_REQUEST['ID'];
	$AdType = $_REQUEST['AdType'];
	$AdCount = getCountOfAllPotentialMediaForEntityByIDAndType($ID, $AdType);
	if($AdCount > 0)
	{
		$AdList = getAllPotentialMediaForEntityByIDAndType($ID, $AdType);
		foreach($AdList->getRecords() as $Ad)
		{
			$Alt = $Ad->value("Alt");
			$URL = $Ad->value("URL");
			$ImgURL = str_replace(" ","%20",$URL);
			$Status = $Ad->value("Status");
			print("<div class='previewMedia'>");
			print("Selected: <input type='checkbox' name='potentialMediaCheckboxes' class='adMediaSelectCheckboxes' checked value='$URL'>");
			print("<img src='$ImgURL' class='previewMedia$AdType' />");
			print("</div><!-- end previewMedia -->");
		}
	}
	else
	{
		print("You do not currently have any ads that match the entity and type");
	}
}

if($function == 'uploadMediaFromLocalForAd')
{
	$AdType = $_REQUEST['AdType'];
	$Alias = $_REQUEST['Alias'];
	$EntityID = $_REQUEST['EntityID'];
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	
	$EntityName = getNameForEntityByID($EntityID);
	
	$Desc = $AdType.' for '.$EntityName;
	
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		$FileName = str_replace(" ","%20",$FileName);
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
		
	}
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$AcceptableDimensions = getMediaDimensionsByType($AdType);
		$AcceptableHeight = $AcceptableDimensions->value("Height");
		$AcceptableWidth = $AcceptableDimensions->value("Width");
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		if((int)$Width != (int)$AcceptableWidth && (int)$Height != (int)$AcceptableHeight)
		{
			
			$AddError = $AddError."<br>$Type media can only be $AcceptableWidth x $AcceptableHeight";
			$AddSuccess = FALSE;
		}
	}
	else
	{
		$AddError = $AddError.'<br>Please upload only jpgs, gifs or pngs.';
		$AddSuccess = FALSE;
	}
	
	if($AddSuccess) 
	{
		//check success
		if(file_put_contents("../media/$name",$FileContent) )
		{
			$ImgURL = 'https://www.comicadia.com/media/'.$name;
			
			addMediaForEntity($AdType, $ImgURL, $EntityID, $Alias, $Desc);
			print("Success");
		}
		else 
		{
			print($AddError. "Image upload unsuccessful, please check your folder permission<br>");
		}
	}
	else
	{
		print($AddError);
	}
	
}

if($function == 'uploadMediaFromWebForAdd')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	
	$EntityID = $_REQUEST["EntityID"];
	$AdType = $_REQUEST["AdType"];
	$ImgURL = $_REQUEST["URL"];
	$Alias = $_REQUEST['Alias'];
	$OriginalLocation = $_REQUEST["URL"];
	
	$EntityName = getNameForEntityByID($EntityID);
	$Desc = $AdType.' for '.$EntityName;
	
	$checkURL = checkURL($ImgURL);
	$name = basename($ImgURL);
	$name = str_replace(" ","",$name);
	list($txt, $ext) = explode(".", $name);
	$name = $txt.time();
	$name = $name.".".$ext;
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($ImgURL);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		$AcceptableDimensions = getMediaDimensionsByType($AdType);
		$AcceptableHeight = $AcceptableDimensions->value("Height");
		$AcceptableWidth = $AcceptableDimensions->value("Width");
		
		if((int)$Width != (int)$AcceptableWidth && (int)$Height != (int)$AcceptableHeight)
		{
			$AddError = $AddError."<br>$Type pictures can only be $AcceptableWidth x $AcceptableHeight";
			$AddSuccess = FALSE;
		}
	}
	else
	{
		$AddError = $AddError.'<br>Please upload only jpgs, gifs or pngs.';
		$AddSuccess = FALSE;
	}
	if($checkURL != 'Website')
	{
		if(file_exists($OriginalLocation)) 
		{
			
		} 
		else 
		{
			$AddSuccess=false;
			$AddError= $AddError.'<br>File not found.';
		}
	}
	if($AddSuccess == true) 
	{

		//here is the actual code to get the file from the url and save it to the uploads folder
		//get the file from the url using file_get_contents and put it into the folder using file_put_contents
		$upload = file_put_contents("../media/$name",file_get_contents($OriginalLocation));
		//check success
		if($upload)  
		{
			$ImgURL = 'https://www.comicadia.com/media/'.$name;
			addMediaForEntity($AdType, $ImgURL, $EntityID, $Alias, $Desc);
			print("Succes");
		}
		else 
		{
			print($AddError. "Image upload unsuccessful, please check your folder permission<br>");
		}
			
	}
	else 
	{
		print $AddError;
	}		
}

if($function == 'createNewAd')
{
	$AdName = $_REQUEST['AdName'];
	
	$Alias = $_REQUEST['Alias'];
	$ImgURLs = $_REQUEST['URLs'];
	$AdType = $_REQUEST['AdType'];
	$EntityID = $_REQUEST['EntityID'];
	$EntityName = getNameForEntityByID($EntityID);
	$ImgURLs = rtrim(trim($ImgURLs),',');
	$ImgURLs = explode(',',$ImgURLs);
	$AdLink = $_REQUEST['AdLink'];
	
	$Success = true;
	$Error = '';

	if($AdLink == '')
	{
		$AdLink = getEntityURLByID($EntityID);
		print("No URL provided, defaulting to: $AdLink<br>");
	}
	else
	{
		$AdLink = test_input($AdLink);
	}
	
	
	if(!isValidUrl($AdLink))
	{
		$Success = false;
		$Error .= "Ad not created - Link provided not a valid URL.";
	}

	
	if(trim($AdName) == '')
	{
		$AdName = $AdType.' ad for '.$EntityName;
		print("No Ad Name provided, defaulting to $AdName<br>");
	}
	
	if($Success)
	{
		createNewAd($AdName, $AdType, $ImgURLs, $Alias, $EntityID, $AdLink);
		print("Your ad has been submitted for review. Comicadia will be contacting you shortly with its decision.");
	}
	else
	{
		print($Error);
	}
}

if($function == 'submitCampaignForReview')
{
	$CampaignID = $_REQUEST['CampaignID'];
	$AdID = $_REQUEST['AdID'];
	$DateText = $_REQUEST['StartDate'];
	$RequestedStartDate = formatDateToNeo4JDateStamp($DateText);
	if(checkIfAdIsInTransit($AdID))
	{
		$AdStatus = getAdCampaignStatus($AdID);
		print("This ad is current $AdStatus.");
	}
	else
	{
		submitAdCampignForReview($AdID, $CampaignID, $RequestedStartDate);
		print("A request has been sent to Comicadia. An admin will email you with further details.");
	}
}

if($function == 'reloadAdsForAlias')
{
	$Alias = $_REQUEST['Alias'];
	$AdCount = getCountOfAdsByAlias($Alias);
	if($AdCount > 0)
	{
		print("<div id='clientCurrentAds'>");
		$UserAds = getAdsOfAlias($Alias);
		foreach($UserAds->getRecords() as $Ad)
		{
			
			$AdID = $Ad->value("AdID");
			$AdName = $Ad->value("AdName");
			$AdStatus = $Ad->value("Status");
			$AdType = $Ad->value("Type");
			$AdURLs = $Ad->value("URL");
			$AdLink = $Ad->value("AdLink");
			$AdViews = getAdTotalViewCountByAdID($AdID);
			$AdClicks = getAdTotalClickCountByAdID($AdID);
			
			print("<div id='adDetails$AdID' class='adBlock$AdType'>");
			print("<strong>Name:</strong> $AdName<br>");
			print("<strong>Links to:</strong> <a href='$AdLink'>$AdLink</a><br>");
			print("<strong>Total views to date:</strong> $AdViews<br>");
			print("<strong>Total clicks to date:</strong> $AdClicks<br>");
			if($AdStatus == 'Approved')
			{
				print("<strong>Status:</strong> Approved<br>");
				if(checkifAdIsCurrentlyRunningACampaign($AdID))
				{
					print("<strong>Campaign Status:</strong><br>");
					$ThisCampaign = getAdMostRecentCampaignRunDetails($AdID);
					$CampaignStatus = $ThisCampaign->value("Status");
					$DateStarted = $ThisCampaign->value("DateStarted");
					$DateRequested = $ThisCampaign->value("DateRequested");
					$DateSubmitted = $ThisCampaign->value("DateSubmitted");
					$Started = date('F jS, Y', $DateStarted/1000);
					$Requested = date('F jS, Y', $DateRequested/1000);
					$Requested = date('F jS, Y', $DateRequested/1000);
					$Submitted = date('F jS, Y', $DateSubmitted/1000);
					print("<strong>Current Status:</strong> $CampaignStatus <br>");
					print("Date Submitted: $Submitted<br>");
					print("Date Requested: $Requested<br>");
					if($CampaignStatus == 'Active')
					{
						print("<div class='adRunning'>");
						
						print("Campaign Started: $Started");
						$ViewsToDate = getViewsFromDateToNowOfAdByID($AdID, $DateStarted);
						$ClicksToDate = getClicksFromDateToNowOfAdByID($AdID, $DateStarted);
						print("Views to date: $ViewsToDate<br>");
						print("Clicks to date: $ClicksToDate");
						print("</div><--! end AdRunning -->");
					}
				}
				else
				{	
					print("<strong>Campaign Status:</strong> No campaign has been selected for this ad.<br>");
					print("<input type='button' value='Delete Ad' id='deleteAd$AdID' onclick=\"deleteAd('$AdID', '$Alias');\"><br>");
				}
					
			}
			elseif($AdStatus == 'Pending')
			{
				print("<strong>Status:</strong> This ad is still under review<br>");
			}
			elseif($AdStatus == 'Rejected')
			{
				print("<strong>Status: $AdStatus</strong><br>");
				$RejectionInfo = getRejectionReasonForAd($AdID);
				$Reason = $RejectionInfo->value("Reason");
				$DateRejected = $RejectionInfo->value("DateRejected");
				$DateRejected = date('F jS, Y', $DateRejected/1000);
				print("<strong>Date Rejected:</strong> $DateRejected<br>");
				print("<strong>Reason:</strong> $Reason<br>");
				print("<input type='button' value='Delete Ad' id='deleteAd$AdID' onclick=\"deleteAd('$AdID', '$Alias');\"><br>");
			}
			else
			{
				print("Status: Current status is unknown. Please contact an administrator to resolve this issue<br>");
			}
			print("<strong>Media being used:</strong><br>");
			foreach($AdURLs as $MediaURL)
			{
				$MediaDetails = getMediaDetails($MediaURL);
				$MediaViews = getMediaTotalViews($MediaURL);
				$MediaClicks = getMediaTotalClicks($MediaURL);
				$MediaStatus = $MediaDetails->value("Status");
				
				print("<div id='mediaDetailsForAd$AdID' class='mediaDetails'>");
				print("<div class='adPreview$AdType'><img src='$MediaURL' class='previewMedia$AdType'/></div>");
				print("<div class='adStats'>");
				print("<strong>Views:</strong> $MediaViews<br>");
				print("<strong>Clicks:</strong> $MediaClicks<br>");
				print("<strong>Current Status:</strong> $MediaStatus");
				print("</div><!-- end adStats -->");
				
				print("</div><!-- end mediaDetailsForAd$AdID -->");
			}
			print("</div><!-- AdDetails$AdID -->");
		}
		print("</div><!-- end clientCurrentAds -->");
	}
	else
	{
		print("You do not have any ads currently registered with us.");
	}
}

if($function == 'deleteAd')
{
	$AdID = $_REQUEST['AdID'];
	deleteAd($AdID);
}

if($function == 'getUserFirstNameByAlias')
{
	$Alias = $_REQUEST['Alias'];
	$UserDetails = getUserDetails($Alias);
	$First_Name = $UserDetails->value("FirstName");
	print("$First_Name");
}

if($function == 'getUserLastNameByAlias')
{
	$Alias = $_REQUEST['Alias'];
	$UserDetails = getUserDetails($Alias);
	$Last_Name = $UserDetails->value("LastName");
	print("$Last_Name");
}

if($function == 'getUserEmailByAlias')
{
	$Alias = $_REQUEST['Alias'];
	$UserDetails = getUserDetails($Alias);
	$Email = $UserDetails->value("Email");
	print("$Email");
}

if($function == 'getCampaignNameByID')
{
	$CampaignID = $_REQUEST['CampaignID'];
	$Campaign = getCampaignDetails($CampaignID);
	$CampaignName = $Campaign->value("Name");
	print("$CampaignName");
}

if($function == 'getCampaignCostByID')
{
	$CampaignID = $_REQUEST['CampaignID'];
	$Campaign = getCampaignDetails($CampaignID);
	$CampaignCost = $Campaign->value("Cost");
	print("$CampaignCost");
}

if($function == 'checkifAdIsCurrentlyRunningACampaign')
{
	$AdID = $_REQUEST['AdID'];
	if(checkifAdIsCurrentlyRunningACampaign($AdID))
		print("Active");
	else
		print("Inactive");
}

if($function == 'testFinalStep')
{
		$txn_id = "12345";
		$Alias = "Chippy";
		$PaymentAmount = 0.01;
		$PaymentStatus= "Completed";
		$StartDate = 15278573457547;
		$CampaignID = 152770406585624;
		$AdID = 15278573457547;
		$TransactionID = createAdCampaignTransaction($txn_id, $PaymentAmount, $PaymentStatus, $Alias, $StartDate, $CampaignID, $AdID);
		print $TransactionID;
}

if($function == 'PauseBid')
{
	$AdSpaceID = $_REQUEST['AdSpaceID'];
	$Alias = $_REQUEST['Alias'];
	try{
		setBidStatusToPaused($Alias, $AdSpaceID);
		print("Success");
	}
	catch(Exception $e)
	{
		print("$e");
	}
	
}

if($function == 'ResumeBid')
{
	$AdSpaceID = $_REQUEST['AdSpaceID'];
	$Alias = $_REQUEST['Alias'];
	try{
		setBidStatusToActive($Alias, $AdSpaceID);
		print("Success");
	}
	catch(Exception $e)
	{
		print("$e");
	}
}

if($function == 'submitNewCurrentBid')
{
	$Alias = $_REQUEST["Alias"];
	$AdSpaceID = $_REQUEST["AdSpaceID"];
	$UserBid = $_REQUEST["Bid"];
	$MaxBid = $_REQUEST['MaxBid'];
	$Status = $_REQUEST['Status'];
	$AdID = $_REQUEST['AdID'];
	$FundsAvailable = true;
	$Error = "Bid not placed.<br>";
	
	if($UserBid == '')
	{
		$UserBid = 0;
	}
	if($MaxBid == '')
	{
		$MaxBid = 0;
	}
	$Bidding = false;
	if(is_numeric($MaxBid) && is_numeric($UserBid))
	{
		if($MaxBid > 0 && $UserBid > 0)
		{
			if($MaxBid < $UserBid)
			{
				$Bidding = false;
				$Message = "Maximum bid must be either 0 or higher than current bid";
			}
			else
			{
				$Bidding = true;
			}
		}
		elseif($MaxBid == 0 && $UserBid > 0)
		{
			$Bidding = true;
		}
		elseif($MaxBid > 0 && $UserBid == 0)
		{
			$Bidding = true;
		}
		elseif($MaxBid == 0 && $UserBid == 0)
		{
			updateBidOfUser($Alias, $AdSpaceID,$UserBid, $MaxBid, "Paused",$AdID);
			calculateCurrentWinnerOfAdSpace($AdSpaceID);
			print("Reload");
		}
		else
		{
			print("Negative numbers are not allowed");
		}
		if($Bidding)
		{
			$MaxBid = number_format($MaxBid,2);
			$UserBid = number_format($UserBid,2);
			$UserBidFor5Minutes = getCostPerRotation($UserBid);
			$UserMaxBidFor5Minutes = getCostPerRotation($MaxBid);
			if(checkIfUserHasEnoughForBid($Alias, $UserBidFor5Minutes))
			{
				
				$Error .= "Insufficient funds for current bid<br>";
				$FundsAvailable = false;
			}
			if(checkIfUserHasEnoughForBid($Alias, $UserMaxBidFor5Minutes))
			{
				$Error .= "Insufficient funds for Max bid<br>";
				$FundsAvailable = false;
			}
			if($FundsAvailable)
			{
				updateBidOfUser($Alias, $AdSpaceID,$UserBid, $MaxBid, $Status,$AdID);
				calculateCurrentWinnerOfAdSpace($AdSpaceID);
				print("Reload");
			}
			else
			{
				print("$Error");
			}
		}
		else
		{
			print($Message);
		}
	}
	else
	{
		print("Invalid input provided. Only numbers are allowed");
	}
}

if($function == 'rebuildAdFrame')
{
	$AdSpaceID = $_REQUEST['AdSpaceID'];
	$Alias = $_REQUEST['Alias'];
	buildComicBiddingFrame($AdSpaceID, $Alias);
}

if($function == 'calculateCurrentWinnerOfAdSpace')
{
	$AdSpaceID = $_REQUEST['AdSpaceID'];
	calculateCurrentWinnerOfAdSpace($AdSpaceID);
}
?>