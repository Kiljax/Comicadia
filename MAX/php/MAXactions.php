<?php
include 'MAXfunctions.php';
include "../../php/functions.php";
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

$function = $_REQUEST["F"];

if($function =='scheduleMAXRound')
{
	$Start = $_REQUEST['Start'];
	$SignUpClose = $_REQUEST['SignUp'];
	$Deadline = $_REQUEST['Final'];
	$Theme = $_REQUEST['Theme'];
	$Status = $_REQUEST['Status'];
	
	$Earlier = strtotime("-1 week", $Deadline) * 1000;
	$Later = strtotime("+1 week", $Deadline) * 1000;
	
	$Start = $Start * 1000;
	$SignUpClose = $SignUpClose * 1000;
	$Deadline = $Deadline * 1000;
	
	$Success = true;
	$Error = "MAX Round not scheduled";
	
	if(!$Theme)
	{
		$Theme = 'None';
	}
	if($Start >= $SignUpClose)
	{
		$Success = false;
		$Error .= "<br>Start date must be before end of sign-ups";
	}
	if($SignUpClose >= $Deadline)
	{
		$Success = false;
		$Error .= "<br>Sign ups must close before the deadline";
	}
	if($Start >= $Deadline)
	{
		$Success = false;
		$Error .= "<br>The Start date must be before the deadline";
	}
	if($Success)
	{
		
		if(checkIfMAXAlreadyScheduled($Start,$Later,$Earlier))
		{
			print("There is already a MAX round scheduled within a week of the chosen start date.");
		}
		else
		{
			
			createMAXRound($Start, $Deadline, $SignUpClose, $Theme, $Status);
			print("MAX Round created");
		}
	}
	else
	{
		print($Error);
	}		
}

if($function == 'updateMAXRound')
{
	$MAXID = $_REQUEST["MAXID"];
	$NewStart = $_REQUEST["NewStart"];
	$NewClose = $_REQUEST["NewClose"];
	$NewDeadline = $_REQUEST["NewDeadline"];
	$NewTheme = $_REQUEST["NewTheme"];
	$NewStatus = $_REQUEST["NewStatus"];
	$OldStatus = $_REQUEST["OldStatus"];
	
	$Earlier = strtotime("-1 week", $NewStart) * 1000;
	$Later = strtotime("+1 week", $NewStart) * 1000;
	
	$NewStart = $NewStart * 1000;
	$NewClose = $NewClose * 1000;
	$NewDeadline = $NewDeadline * 1000;
	
	$Success = true;
	$Error = "MAX Round not modified";
	
	if($OldStatus == 'Completed')
	{
		$Success =false;
		$Error .= "<br>This round was closed and cannot be modified.";
	}
	if(!$NewTheme)
	{
		$NewTheme = 'None';
	}
	if($NewStart >= $NewClose)
	{
		$Success = false;
		$Error .= "<br>Start date must be before end of sign-ups";
	}
	if($NewClose >= $NewDeadline)
	{
		$Success = false;
		$Error .= "<br>Sign ups must close before the deadline";
	}
	if($NewStart >= $NewDeadline)
	{
		$Success = false;
		$Error .= "<br>The Start date must be before the deadline";
	}
	if($Success)
	{
		
		if(checkIfUpdatedMAXAlreadyScheduled($NewStart,$Later,$Earlier, $MAXID))
		{
			print("There is already a MAX round scheduled within a week of the chosen start date.");
		}
		else
		{
			if($NewStatus  == 'Active')
			{
				setMAXRoundAsActive($MAXID);
			}
				updateMAXRound($MAXID, $NewStart, $NewDeadline, $NewClose, $NewTheme, $NewStatus);
				print("MAX Round updated");
		}
	}
	else
	{
		print($Error);
	}		
}

if($function == 'deleteMAX')
{
	$MAXID = $_REQUEST["MAXID"];
	deleteMAX($MAXID);
	print("Deleted");
}

if($function == 'markMAXRoundCompleted')
{
	$MAXID = $_REQUEST["MAXID"];
	markMAXRoundCompleted($MAXID);
	print("MAX Round now marked as completed");
}

if($function == 'signUpForMAX')
{
	$Alias = $_REQUEST['Alias'];
	$MAXID = $_REQUEST['MAXID'];
	if(isUserBlacklisted($Alias))
	{
		print("You cannot apply to a MAX round until you have submitted any/all previous round entries.");
	}
	else
	{
		if(checkIfUserIsSignedUp($Alias, $MAXID))
		{
			print("You are already registered for this round.");
		}
		else
		{
			signUpForMax($Alias, $MAXID);
			print("You are now signed up for this MAX round");
		}
	}
}

if($function =='withdrawFromMAX')
{
	$Alias = $_REQUEST['Alias'];
	$MAXID = $_REQUEST['MAXID'];
	if(isMAXSignupClosed($MAXID))
	{
		print("Sorry. The MAX round Signups are closed. You can no longer withdraw.");
	}
	else
	{
		print("You have withdrawn your application for this round");
		withdrawUserFromMAX($Alias, $MAXID);
	}
}

	
if($function == 'generateMAXMatchups')
{
	$MAXID = $_REQUEST['MAXID'];	
	if(isMAXRoundLocked($MAXID))
	{
		print("The MAX Round is locked and cannot be generated");
	}
	else
	{
		clearMAXMatchups($MAXID);
		$MatchupArray = buildMAXMatchupArray($MAXID);
		
		if($MatchupArray)
		{
			print("<input type='button' value='Lock Matchups' onclick=\"lockMAXRound('$MAXID');\">");
			print("<div id='MatchupStatus' class='errMSG'></div>");
			print("<div id='MatchupStatus' class='errMSG'></div>");
			foreach($MatchupArray as $Matchup)
			{
				$User = $Matchup["User"];
				$Recipient = $Matchup["Recipient"];
				$PreviousSubs = $Matchup["SubCount"];
				print("$User -> $Recipient -> $PreviousSubs<br>");
			}
		}
		else
		{
			print("There was a matching error");
			setMatchupStatusAsError($MAXID);
		}
	}
}


if($function == 'lockMAXRound')
{
	$MAXID = $_REQUEST['MAXID'];
	lockMAXRound($MAXID);
	$MAX = getCurrentMAXRound();
	$MAXID = $MAX->value("DateCreated");
	$RecipientList = getEmailAddressesForMAXRoundParticipants($MAXID);
	$StartDate = $MAX->value("StartDate");
	$Theme = $MAX->value("Theme");
	$SignUpCloseDate = $MAX->value("SignUpEndDate");
	$Deadline = $MAX->value("EndDate");
	
	$SignUpCloseDate = date('F jS, Y', $SignUpCloseDate/1000);
	$StartDate = date('F jS, Y', $StartDate/1000);
	$Deadline = date('F jS, Y', $Deadline/1000);
	
	$Subject = "MAX Round Details";
	$Text = "The Max Round has started! \r\nTheme: $Theme\r\nDeadline for submissions: $Deadline ".
	"\r\nPlease log in to <a href='https://wwww.comicadia.com/MAX'>MAX</a> in order to see who you ".
	"will be drawing art for!\r\n\r\nThank you for participating in this round!".
	"\r\nThe Comicadia/MAX Team";
	foreach($RecipientList as $Recipient)
	{
		$Email = $Recipient->value("Email");
		$Alias = $Recipient->value("Alias");
		$Text = "Hello $Alias, \r\n".$Text;
		sendEmailFromNoReply($Email, $Subject, $Text);
	}
	print("Success.");
}

if($function == 'addCharacterToUser')
{
	$Alias = $_REQUEST['Alias'];
	$Name = $_REQUEST['Name'];
	$Age = $_REQUEST['Age'];
	$Gender = $_REQUEST['Gender'];
	$Race = $_REQUEST['Race'];
	$Hair = $_REQUEST['Hair'];
	$Eyes = $_REQUEST['Eyes'];
	$Height = $_REQUEST['Height'];
	$Weight = $_REQUEST['Weight'];
	$Writeup = $_REQUEST['Writeup'];
	$Webcomic = $_REQUEST['Webcomic'];
	
	if(trim($Name) == '')
	{
		print("Name cannot be blank");
	}
	else
	{
		if(checkIfUserAlreadyHasCharacterRegistered($Alias, $Name))
		{
			print("You already have a character with this name registered.");
		}
		else
		{
			addCharacterToUser($Alias, $Name, $Age, $Gender, $Race, $Hair, $Eyes, $Height, $Weight, $Writeup, $Webcomic);
			print("Character Registered");
		}
	}
}

if($function == 'addReferenceForCharacterFromLocal')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$CharacterID = $_REQUEST['CharacterID'];
	$Alias = $_REQUEST['Alias'];
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	$FileName = str_replace(' ', '', $FileName);
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
	}
	
	if($_FILES['uploadedFile']['size'] > 2000000) 
	{ //2 MB (size is also in bytes)
	
        $AddError = $AddErorr +  "<br>Media cannot exceed 2 megabytes in size";
		$AddSuccess = FALSE;
    }
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		
		if((int)$Width > 2000 || (int)$Height > 2000)
		{
			$AddError = $AddError."<br>Media cannot exceed 1200 pixels in width or height.";
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
		if(getCharacterOfUserReferencesCount($Alias, $CharacterID) >= 5)
		{
			print("You already have the maximum allowed references for that character. Please delete one");
		}
		else
		{
			if(file_put_contents("../media/$name",$FileContent) )
			{
				$ImgURL = 'https://www.comicadia.com/MAX/media/'.$name;
				addCharacterReference($ImgURL,$CharacterID);
				print("Success");
			}
			else 
			{
				print($AddError. "Image upload unsuccessful, please check your folder permission<br>");
			}
		}
	}
	else
	{
		print($AddError);
	}	
}

if($function == 'addReferenceForCharacterFromURL')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$CharacterID = $_REQUEST['CharacterID'];
	$ImgURL = $_REQUEST["URL"];
	$Alias = $_REQUEST['Alias'];
	$OriginalLocation = $_REQUEST["URL"];
	
	if($ImgURL != '')
	{
		if(isValidUrl($ImgURL))
		{
			$name = basename($ImgURL);
			list($txt, $ext) = explode(".", $name);
			$name = $txt.time();
			$name = $name.".".$ext;
					
			if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
			{
				$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
				$Width = $Dimensions[0];
				$Height = $Dimensions[1];
				
				if((int)$Width > 2000 || (int)$Height > 2000)
				{
					$AddError = $AddError."<br>Media cannot exceed 1200 pixels in width or height.";
					$AddSuccess = FALSE;
				}
			}
			else
			{
				$AddError = $AddError.'<br>Please upload only jpgs, gifs or pngs.';
				$AddSuccess = FALSE;
			}
		}
		else
		{
			$AddError = $AddError."<br>The URL entered is invalid.";
			$AddSuccess = FALSE;
		}
	}
	else
	{
		$AddError = $AddError."<br>File cannot be blank";
		$AddSuccess = FALSE;
	}
	
	//check if the files are only image / document
	
	if($AddSuccess == true) 
	{
		if(getCharacterOfUserReferencesCount($Alias, $CharacterID) >= 5)
		{
			print("You already have the maximum allowed references for that character. Please delete one");
		}
		else
		{
			//here is the actual code to get the file from the url and save it to the uploads folder
			//get the file from the url using file_get_contents and put it into the folder using file_put_contents
			$upload = file_put_contents("../media/$name",file_get_contents($OriginalLocation));
			//check success
			if($upload)  
			{
				$ImgURL = 'https://www.comicadia.com/MAX/media/'.$name;
				addCharacterReference($ImgURL,$CharacterID);
				print("Success");
			}
			else 
			{
				print($AddError. "Image upload unsuccessful, please check your folder permission<br>");
			}
		}
	}
	else 
	{
		print $AddError;
	}		
}

if($function == 'deleteReferenceForCharacterByURL')
{
	$ImgURL = $_REQUEST['ImgURL'];
	$CharacterID = $_REQUEST['CharacterID'];
	deleteCharacterReference($ImgURL, $CharacterID);
	print("Deleted");
	
}

if($function == 'setReferenceAsThumbnail')
{
	$ImgURL = $_REQUEST['ImgURL'];
	$CharacterID = $_REQUEST['CharacterID'];
	clearThumbnailForCharacter($CharacterID);
	setReferenceAsThumbnailForCharacter($CharacterID,$ImgURL);
	print("Thumbnail updated");
}

if($function == 'removeReferenceAsThumbnail')
{
	$CharacterID = $_REQUEST['CharacterID'];
	clearThumbnailForCharacter($CharacterID);
	print("Thumbnail cleared");
}

if($function =='setCharacterAsPreferred')
{
	$CharacterID = $_REQUEST['CharacterID'];
	$Alias = $_REQUEST['Alias'];
	clearCharacterPreferencesForUser($Alias);
	setCharacterAsPreferred($Alias,$CharacterID);
	print("Character set as preferred");
}

if($function == 'removeCharacterAsPreferred')
{
	$Alias = $_REQUEST['Alias'];
	clearCharacterPreferencesForUser($Alias);
	print("Character preference has been cleared");
}

if($function == 'approveReference')
{
	$URL = $_REQUEST['URL'];
	$Alias = $_REQUEST['Alias'];
	adminApproveReference($URL, $Alias);
	print("Approved");
}

if($function == 'rejectReference')
{
	$URL = $_REQUEST['URL'];
	$Alias = $_REQUEST['Alias'];
	adminRejectReference($URL, $Alias);
	print("Approved");
}

if($function == 'addUserToBlacklist')
{
	$MemberAlias = $_REQUEST['MemberAlias'];
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	
	if(trim($Reason) == '')
	{
		print("To add a user to the blacklist, you must input a reason");
	}
	else
	{
		addUserToBlacklist($MemberAlias, $AdminAlias, $Reason);
		print("User has been added to the blacklist");
	}
}

if($function == 'addUserToBlacklistAndResolveIncident')
{
	$MemberAlias = $_REQUEST['MemberAlias'];
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	$EntryID = $_REQUEST['EntryID'];
	$ReportedOn = $_REQUEST['ReportedOn'];
	
	if(trim($Reason) == '')
	{
		print("To add a user to the blacklist, you must input a reason");
	}
	else
	{		
		if($Reason == '')
		{
			print("You need to provide your reasoning for the user.");
		}
		else
		{
			if(hasReportAlreadyBeenHandled($MemberAlias, $ReportedOn, $EntryID))
			{
				print("This MAX Entry issue has already been marked as resolved.");
			}
			else
			{
				addUserToBlacklist($MemberAlias, $AdminAlias, $Reason);
				createOprhanEntry($EntryID);
				resolveReportedEntry($EntryID, $MemberAlias, $ReportedOn, $AdminAlias, $Reason, 'Reported');
				print("Success.");
			}
		}
	}
}

if($function == 'removeUserFromBlacklist')
{
	$MemberAlias = $_REQUEST['MemberAlias'];
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	$BlacklistID = $_REQUEST['BlacklistID'];
	
	if(trim($Reason) == '')
	{
		print("To resolve a blacklist incident, you must input a reason");
	}
	else
	{
		removeUserFromBlacklist($MemberAlias, $AdminAlias, $BlacklistID, $Reason);
		print("This Blacklist record has been marked as resolved");
	}
}

if($function =='addLateUserEntryForMAX')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	
	$CharacterID = $_REQUEST['CharacterID'];
	$Alias = $_REQUEST['Alias'];
	$MAXID = $_REQUEST['MAXID'];
	$Recipient = $_REQUEST['Recipient'];
	$Comments = $_REQUEST['Comments'];
	
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	$FileName = str_replace(' ', '', $FileName);
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
	}

    if($_FILES['uploadedFile']['size'] > 2000000)
	{ //2 MB (size is also in bytes)
	
        $AddError = $AddErorr +  "<br>Media cannot exceed 2 megabytes in size";
		$AddSuccess = FALSE;
    }
    
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		
		if((int)$Width > 2000 || (int)$Height > 2000)
		{
			$AddError = $AddError."<br>Media cannot exceed 1200 pixels in width or height.";
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
			$ImgURL = 'https://www.comicadia.com/MAX/media/'.$name;
			addLateUserEntryForMAX($ImgURL,$MAXID,$Alias,$Recipient,$CharacterID,$Comments);
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

if($function == 'addUserEntryForMAX')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	
	$CharacterID = $_REQUEST['CharacterID'];
	$Alias = $_REQUEST['Alias'];
	$MAXID = $_REQUEST['MAXID'];
	$Recipient = $_REQUEST['Recipient'];
	$Comments = $_REQUEST['Comments'];
	
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	$FileName = str_replace(' ', '', $FileName);
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
	}
	
	if($_FILES['uploadedFile']['size'] > 2000000) 
	{ //2 MB (size is also in bytes)
	
        $AddError = $AddErorr +  "<br>Media cannot exceed 2 megabytes in size";
		$AddSuccess = FALSE;
    }
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		
		if((int)$Width > 2000 || (int)$Height > 2000)
		{
			$AddError = $AddError."<br>Media cannot exceed 1200 pixels in width or height.";
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
			$ImgURL = 'https://www.comicadia.com/MAX/media/'.$name;
			addUserEntryForMAX($ImgURL,$MAXID,$Alias,$Recipient,$CharacterID,$Comments);
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

if($function == 'loadUserEntry')
{
	$Alias = $_REQUEST['Alias'];
	$MAXID = $_REQUEST['MAXID'];
	$IMGURL = getEntryURLFromUserForRound($Alias, $MAXID);
	print("$IMGURL");
	
}

if($function == 'reportEntry')
{	
	$Alias = $_REQUEST['Alias'];
	$EntryID = $_REQUEST['EntryID'];
	$Reason  = trim($_REQUEST['Reason']);
	
	if($Reason == '')
	{
		print("You need to provide a reason as to why this entry should be reviewed by admins");
	}
	
	else
	{
		if(hasUserAlreadyReportedEntry($EntryID, $Alias))
		{
			print("You have already reported this entry.");
		}
		else
		{
			reportEntry($EntryID, $Alias, $Reason);
			print("Entry has been submitted for admin review.");
		}
	}
}

if ($function == 'resolveReportedEntry')
{
	$Alias = $_REQUEST['MemberAlias'];
	$Admin = $_REQUEST['AdminAlias'];
	$EntryID = $_REQUEST['EntryID'];
	$Reason  = trim($_REQUEST['Reason']);
	$ReportedOn = $_REQUEST['ReportedOn'];
	if($Reason == '')
	{
		print("You need to provide your reasoning for the user.");
	}
	else
	{
		if(hasReportAlreadyBeenHandled($Alias, $ReportedOn, $EntryID))
		{
			print("This MAX Entry issue has already been marked as resolved.");
		}
		else
		{
			resolveReportedEntry($EntryID, $Alias, $ReportedOn, $Admin, $Reason, 'Completed');
			print("Resolved.");
		}
	}
}

if($function == 'adoptEntry')
{
	$Alias = $_REQUEST['Alias'];
	$EntryID = $_REQUEST['EntryID'];
	$OrphanID = $_REQUEST['OrphanID'];
	$Success = true;
	$Error = "Entry not adopted:";
	if(checkIfOrphanWasAdopted($OrphanID))
	{
		$Error = $Error."<br>This entry has been adopted already";
		$Success = false;
	}
	if(checkIfUserHasAlreadyClaimedAnAdoption($Alias))
	{
		$Error = $Error."<br>You are already signed up for an adoption.";
		$Success = false;
	}
	if($Success)
	{
		print("Success");
		adoptEntry($OrphanID,$EntryID,$Alias);
	}
	else
	{
		print("$Error");
	}
}

if($function == "submitAdoptedEntryForReview")
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$Alias = $_REQUEST['Alias'];
	$AdoptionID = $_REQUEST['AdoptionID'];
	$EntryID = $_REQUEST['EntryID'];
	$CharacterID = $_REQUEST['CharacterID'];
	$Comments = $_REQUEST['Comments'];
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	$FileName = str_replace(' ', '', $FileName);
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
	}
	
	if($_FILES['uploadedFile']['size'] > 2000000) 
	{ //2 MB (size is also in bytes)
	
        $AddError = $AddErorr +  "<br>Media cannot exceed 2 megabytes in size";
		$AddSuccess = FALSE;
    }
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		
		if((int)$Width > 2000 || (int)$Height > 2000)
		{
			$AddError = $AddError."<br>Media cannot exceed 1200 pixels in width or height.";
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
			$ImgURL = 'https://www.comicadia.com/MAX/media/'.$name;
			submitAdoptionArtForReview($ImgURL,$Alias,$AdoptionID,$EntryID,$CharacterID,$Comments);
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

if($function == 'rejectSubmittedEntry')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	$AdoptionID = $_REQUEST['AdoptionID'];
	if(trim($Reason) == '')
	{
		print("A reason is required to reject a submission");
	}
	elseif(!checkIfAdoptionIsStillPending($AdoptionID))
	{
		print("This adoption has already been moderated.");
	}
	else
	{
		print("Rejected.");
		rejectAdoptionSubmission($AdminAlias, $Reason, $AdoptionID);
	}
}

if($function == 'acceptSubmittedEntry')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$AdoptionID = $_REQUEST['AdoptionID'];
	if(!checkIfAdoptionIsStillPending($AdoptionID))
	{
		print("This adoption has already been moderated.");
	}
	else
	{
		acceptAdoptionSubmission($AdminAlias, $AdoptionID);
		print("Accepted");
	}
	
}

if($function == 'saveEditsToCharacterDetails')
{
	$CharacterID = $_REQUEST['CharacterID'];
	$Name = $_REQUEST['Name'];
	$Age = $_REQUEST['Age'];
	$Gender = $_REQUEST['Gender'];
	$Race = $_REQUEST['Race'];
	$Hair = $_REQUEST['Hair'];
	$Eyes = $_REQUEST['Eyes'];
	$Height = $_REQUEST['Height'];
	$Weight = $_REQUEST['Weight'];
	$WriteUp = $_REQUEST['WriteUp'];
	$ComicID = $_REQUEST['ComicID'];
	
	if(trim($Name) == '')
	{
		print("All characters require at least a name");
	}
	else
	{
		saveCharacterEdits($CharacterID, $Name, $Age, $Gender, $Race, $Hair, $Eyes, $Height, $Weight, $WriteUp, $ComicID);
		print("Changes saved");
	}
}

if($function == 'retireCharacter')
{
	$CharacterID = $_REQUEST['CharacterID'];
	retireCharacter($CharacterID);
	print("Character retired.");
}

if($function == 'reviveCharacter')
{
	$CharacterID = $_REQUEST['CharacterID'];
	reviveCharacter($CharacterID);
	print("Character revived.");
}

if($function == 'acceptLateEntry')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$EntryID = $_REQUEST['EntryID'];
	if(checkIfLateEntryIsStillPending($EntryID))
	{
		print("Success");
		approveLateEntry($EntryID, $AdminAlias);
		clearUserFromBlacklistForLateSubmission($EntryID,$AdminAlias);
	}
	else
	{
		print("That Entry has already been moderated.");
	}
}

if($function == 'rejectLateEntry')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$EntryID = $_REQUEST['EntryID'];
	$Reason = $_REQUEST['Reason'];
	if(checkIfLateEntryIsStillPending($EntryID))
	{
		print("Success");
		rejectLateEntry($EntryID, $AdminAlias, $Reason);
	}
	else
	{
		print("That Entry has already been moderated.");
	}
}


if($function == 'submitRequestForReference')
{
	$CharacterID = $_REQUEST['CharacterID'];
	$Requester = $_REQUEST['Requester'];
	$Reason = $_REQUEST['Reason'];
	if(checkIfReferencesAlreadyRequestedForCharacter($CharacterID, ["Requested","Sent"]))
	{
		print("You have already requested for additional references for this character.");
	}
	else
	{
		userSubmitRequestForMoreReferences($Requester, $CharacterID, $Reason);
		print("Request submitted for review by the MAX admins. You will receive an email once a decision has been made.");
	}	
}

if($function == 'adminSubmitRequestForReference')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	$CharacterID = $_REQUEST['CharacterID'];
	$Sender = $_REQUEST['Sender'];
	$Receiver = getCharacterOwnerAliasByID($CharacterID);
	$CharacterName = getCharacterNameByID($CharacterID);
	
	if(checkIfReferencesAlreadyRequestedForCharacter($CharacterID, ["Sent"]))
	{
		return "This user has already been emailed the request for them to provide additional references for $CharacterName.";
	}
	else
	{
		adminRequestForMoreReferences($Receiver, $AdminAlias, $CharacterID, $Reason, $Sender);
	}
}

if($function == 'adminRejectRequestForReference')
{
	$AdminAlias = $_REQUEST['AdminAlias'];
	$Reason = $_REQUEST['Reason'];
	$CharacterID = $_REQUEST['CharacterID'];
	$Receiver = $_REQUEST['Sender'];
	$CharacterName = getCharacterNameByID($CharacterID);
	
	if(checkIfReferencesAlreadyRequestedForCharacter($CharacterID, ["Sent"]))
	{
		return "This request has already been handled by another Admin.";
	}
	else
	{
		adminRejectRequestForMoreReferences($Receiver, $AdminAlias, $CharacterID, $Reason);
	}
}

if($function =='adminSendMAXReminder')
{
	$MAXID = $_REQUEST['MAXID'];
	$Alias = $_REQUEST['Alias'];
	if(checkIfReminderHasBeenSent($MAXID))
	{
		print("A reminder has already been sent");
	}
	else
	{
		adminSendMAXReminder($MAXID, $Alias);
		print("A reminder has been sent to those who have not submitted any art yet.");
	}
}
?>