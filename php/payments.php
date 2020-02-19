<?php

include 'functions.php';

//Paypal Settings


$paypal_email = 'ComicadiaCollective@gmail.com';
$return_url = 'https://www.comicadia.com/php/payment-successful.html';
$cancel_url = 'https://www.comicadia.com/php/payment-cancelled.html';
$notify_url = 'https://www.comicadia.com/rotator/Listener.php';
$cmd = "_xclick";
$no_note = "1";
$currency_code = "USD";
$lc = "US";
$bn = "PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest";

//Set to false to use sandbox

$enableSandbox = false;

if($enableSandbox == true)
{
	$paypal_email = "comicadiacollective-facilitator@gmail.com";
}

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]))
{
	$Success = true;
	$querystring = '';

	// Firstly Append paypal account to querystring
	$querystring .= "?business=".rawurlencode($paypal_email)."&";
	$querystring .= "cmd=$cmd&";
	$querystring .= "no_note=$no_note&";
	$querystring .= "currency_code=$currency_code&";
	$querystring .= "lc=$lc&";
	$querystring .= "bn=$bn&";
	$querystring .= "rm=2&";
	$querystring .= "no_shipping=1&";
	
	if(isset($_POST["Function"]))
	{
		$Function = $_POST['Function'];
		if($Function == 'purchaseAdCampaign')
		{
			$CampaignID = $_POST['CampaignID'];
			$Campaign = getCampaignDetails($CampaignID);
			$CampaignName = $Campaign->value("Name");
			$CampaignCost = $Campaign->value("Cost");
			$UserAlias = $_POST['Alias'];
			$UserDetails = getUserDetails($UserAlias);
			$First = $UserDetails->value("FirstName");
			$Last = $UserDetails->value("LastName");
			$Email = $UserDetails->value("Email");
			$StartDate = formatDateToNeo4JDateStamp($_POST['StartDate']);
			$AdID = $_POST['AdID'];
			$querystring .= "item_name=".rawurlencode("Comicadia $CampaignName Ad Campaign")."&";
			$querystring .= "item_number=".rawurlencode($CampaignID)."&";
			$querystring .= "amount=".rawurlencode(".01")."&";
			//$querystring .= "amount=".urlencode($CampaignCost)."&";
			$querystring .= "first_name=".rawurlencode($First)."&";
			$querystring .= "last_name=".rawurlencode($Last)."&";
			$querystring .= "email=".rawurlencode($Email)."&";
			$querystring .= "custom=".rawurlencode($AdID).")(".rawurlencode($StartDate).")(".rawurlencode($UserAlias)."&";
			if($enableSandbox == true)
			{
				$querystring .= "test_ipn=1&";
				
			}
			if(checkifAdIsCurrentlyRunningACampaign($AdID))
			{
				$Success = false;
			}
		}
	}
	else
	{
		// Append amount& currency (CDN) to quersytring so it cannot be edited in html

		//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
		
		$querystring .= "item_name=".rawurlencode($item_name)."&";
		$querystring .= "amount=".rawurlencode($item_amount)."&";
		
		//loop for posted values and append to querystring
		
		
	}
	foreach($_POST as $key => $value)
	{
		$value = rawurlencode(stripslashes($value));
		$querystring .= "$key=$value&";
	}
	// Append paypal return addresses
	$querystring .= "return=".rawurlencode(stripslashes($return_url))."&";
	$querystring .= "cancel_return=".rawurlencode(stripslashes($cancel_url))."&";
	$querystring .= "notify_url=".rawurlencode($notify_url);

	// Append querystring with custom field
	//$querystring .= "&custom=".USERID;

	if($Success)
	{
		// Redirect to paypal IPN
		if($enableSandbox == true)
		{
			$Subject = "Query string sent to Paypal sandbox";
			$Text .= "The string data: \r\n";
			$Text .= $querystring;
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
			header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
		}
		else
		{
			$Subject = "Query string sent to Paypal live";
			$Text .= "The string data: \r\n";
			$Text .= $querystring;
			sendGenericEmailFromNoReply("Kiljax@gmail.com", $Subject, $Text);
			header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
		}
		exit();
	}
	else
		print("That ad is already running");
	
}	
else 
{
	
}




?>
