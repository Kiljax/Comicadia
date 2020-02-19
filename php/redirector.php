<?php
include '../php/functions.php';

if(isset($_REQUEST['F']))
{
	$Success = true;
	$function = $_REQUEST['F'];
	$Error = 'Click on media did not work.<br>';
	if($function == 'Generic' OR $function == 'Default' OR $function == 'Specific')
	{
		$ViewedAdType = $_REQUEST["F"];
		
		if(isset($_REQUEST['AdRunID']))
		{
			$AdRunID = $_REQUEST['AdRunID'];
			if(checkIfAdRunIDIsValid($AdRunID))
			{
				
			}
			else
			{
				$Success = false;
				$Error .= "AdRunID was invalid<br>";
			}
		}
		else
		{
			$Success = false;
			$Error .="AdRunID was not supplied<br>";
		}
		if(isset($_REQUEST['MediaID']))
		{
			$MediaID = $_REQUEST['MediaID'];
			if(checkIfMediaIDIsValid($MediaID))
			{
			}
			else
			{
				$Success = false;
				$Error .= "MediaID supplied was invalid<br>";
			}
		}
		else
		{
			$Success = false;
			$Error .="MediaID was not supplied";
		}

		if(isset($_SERVER['REMOTE_ADDR']))
		{
			$ClickerIP = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$Success = false;
			$Error .= "<br>Could not retrieve remote IP address for records";
		}
		if(isset( $_SERVER['HTTP_REFERER']))
		{
			$Referer = parse_url($_SERVER['HTTP_REFERER']);
			$Referer = $Referer['host'];
		}
		else
		{
			$Success = false;
			$Error .= "<br>Could not retrieve referal";
		}
		if($Success)
		{
			if(CheckIfIPIsLegit($ClickerIP))
			{
				if($ViewedAdType != 'Default')
				{
					$RedirectURL = getEntityURLByAdRunID($AdRunID);
					addClickToAdRunForMedia($AdRunID, $FromEntityID, $MediaID,$ClickerIP,$Referrer);
				}
				else
				{
					$RedirectURL = getComicadiaDefaultAdURL($AdRunID);
					addClickToDefaultMedia($AdRunID, $Referrer, $ViewerIP, $MediaID, $EntityID);
				}
				
				header("Location: ".$RedirectURL);
				exit();
			}
			else
			{
				header("Location: "."https://www.Comicadia.com");
			}
		}
		else
		{
			sendGenericEmailFromNoReply("Kiljax@gmail.com", "TestingAd System: Failure on Click", $Error);
		}
			
	}
	else
	{
		header("Location: "."https://www.Comicadia.com");
	}
}
else
{

	$MediaURL = $_GET['imgURL'];
	$ComicID = $_GET['ComicID'];

	if(checkComicID($ComicID))
	{
		$urlHolder = getWebcomicURLbyImgURL($MediaURL);
		addClickToMediaByComicID($MediaURL,$ComicID);
		header("Location: ".$urlHolder);
		exit();
	}
	elseif($MediaURL)
	{
		$urlHolder = getWebcomicURLbyImgURL($MediaURL);
		header("Location: ".$urlHolder);
		exit();
	}
	else
	{
		header("Location: "."https://www.Comicadia.com");
	}
}
?>