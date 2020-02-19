<?php

include "MAXfunctions.php";

function buildMAXAdminDashboard($Alias)
{
	$MAXSignUpActive = getMAXSignup();
	print("<div id='WelcomeDIV'>");
	print("<div class='currentMAXRound'>");
	if($MAXSignUpActive > 0)
	{
		$CurrentMAXRound = getCurrentMAXRound();
		$Start = $CurrentMAXRound->value("StartDate");
		$End = $CurrentMAXRound->value("EndDate");
		$FinalSignUp = $CurrentMAXRound->value("SignUpEndDate");
		$MAXID = $CurrentMAXRound->value("DateCreated");
		$Theme = $CurrentMAXRound->value("Theme");
		$Status = $CurrentMAXRound->value("Status");
		
		$SignUpCloseComparable = $FinalSignUp;
		$DeadlineComparable = $End;
		$StartDate = date('F jS, Y', $Start/1000);
		$EndDate = date('F jS, Y', $End/1000);
		$SignUpClose = date('F jS, Y', $FinalSignUp/1000);
		
		$now = time() *1000;
		
		print("<h3>MAX round currently underway:</h3>");
		print("<div id='MAXInfo'>");
		print("<div id='currentMAXRoundStart'><strong>Date Started:</strong> $StartDate</div>");
		print("<div id='currentMAXRoundSignUpsClose'><strong>Sign Ups close:</strong> $SignUpClose</div>");
		print("<div id='currentMAXRoundFinalSubmissionDate'><strong>Final date for submissions:</strong> $EndDate</div>");
		print("<div id='currentMAXRoundTheme'><strong>Theme:</strong> $Theme</div>");
		if($DeadlineComparable < $now)
		{
			print("<input type='button' value='Mark round as completed' onclick=\"markMAXRoundAsComplete('$MAXID')\">");
		}
		elseif($SignUpCloseComparable < $now)
		{
			if(isMAXRoundLocked($MAXID))
			{
				buildMAXMatchup($MAXID);
				buildSendReminder($MAXID,$Alias);
			}
			else
			{
				if(getMAXMAtchupStatus($MAXID) == 'Generated')
				{
					print("<input type='button' value='Reroll Matchups' onclick=\"GenerateMAXMatchup('$MAXID');\">");
					print("<div id='MatchUpList'>");
					print("<input type='button' value='Lock Matchups' onclick=\"lockMAXRound('$MAXID');\"><br>");
					buildMAXMatchup($MAXID);
					
					print("</div>");
				}
				else
				{
					print("<input type='button' value='Generate Matchups' onclick=\"GenerateMAXMatchup('$MAXID')\">");
					print("<div id='MatchUpList'></div>");
				}
				
			}
		}
		else
		{
			print("<strong>Sign-ups are still on-going.</strong><br>");
			$CurrenltList = getMembersSignedUpForMAX($MAXID);
			$signUpCount = getCountOfMembersSignedUpForMAX($MAXID);
			print("<strong>Currently signed up:</strong> $signUpCount<br>");
			buildMAXMemberPanelFromList($CurrenltList,'Admin');
				
		}
		
		print("</div>");
	}
	else
	{
		print("<h3>No round is currently going.</h3>");
		print("<div id='scheduleARound'></div>");
	}
	print("</div>");
	print("<div class='contentBox'>");
	print("<div class='dashboardItem'>");
	print("<h3>Late Entries</h3>");
	$LateCount = getCountOfAllPendingLateEntries();
	if($LateCount > 0)
	{
		print("There are $LateCount late entries that need to be moderated");
		print("<br>");
		print("<form action='?' method='GET'>");
		print("<input type='submit' id='ManageEntiresButton' value='MAX Entries' name='submit'> ");
		print("</form>");
	}
	else
	{
		print("There are no late entries to moderate");
	}
	print("</div>");
	print("<div class='dashboardItem'>");
	print("<h3>Reported Entries</h3>");
	$ReportedCount = getCountOfAllReportedEntries();
	if($ReportedCount >0)
	{
		print("There are $ReportedCount reported entries that need to be moderated");
		print("<br>");
		print("<form action='?' method='GET'>");
		print("<input type='submit' id='ManageEntiresButton' value='MAX Entries' name='submit'> ");
		print("</form>");
	}
	else
	{
		print("There are no reported entries that need to be moderated");
	}
	print("</div>");
	print("<div class='dashboardItem'>");
	print("<h3>Pending References</h3>");
	$PendingCount = countReferenceCountByStatus('Pending');
	if($PendingCount > 0)
	{
		print("There are $PendingCount references that need to be reviewed");
		print("<br>");
		print("<form action='?' method='GET'>");
		print("<input type='submit' id='ManageReferencesButton' value='MAX References' name='submit'> ");
		print("</form>");
	}
	else
	{
		print("There are no references that need to be reviewed.");
	}
	print("</div>");
	
	print("<div class='dashboardItem'>");
	print("<h3>Reference Requests</h3>");
	$RequestCount = getCountOfReferenceRequests();
	if($RequestCount > 0)
	{
		print("There are $RequestCount requests for additional references that need to be moderated");
		print("<br>");
		print("<form action='?' method='GET'>");
		print("<input type='submit' id='ManageReferencesButton' value='MAX References' name='submit'> ");
		print("</form>");
	}
	else
	{
		print("There are no reference requests that need to be moderated");
	}
	print("</div>");
	
	print("<div class='dashboardItem'>");
	print("<h3>Adoptions</h3>");
	$ReviewCount = getCountOfAdoptionsWaitingForReview();
	if($ReviewCount > 0)
	{
		print("There are $ReviewCount Adoptions that need to be reviewed");
		print("<form action='?' method='GET'>");
		print("<input type='submit' id='ManageAdoptionsButton' value='MAX Adoptions' name='submit'> ");
		print("</form>");
	}
	else
	{
		print("There are no adoptions for review at this time");
	}
	print("</div>");
}

function buildMAXMatchup($MAXID)
{
	print("<div id='MatchupTable'>");
	print("<div class='tableHeader'>");
	print("<div class='tableColumn'>Artist</div>");
	print("<div class='tableColumn'>Recipient</div>");
	print("<div class='tableColumn'>Times Sender has sent to Recipient</div>");
	print("<div class='tableColumn'>Has submitted</div>");
	print("</div> <! -- End tableHeader -->");
	$MatchupsList = getMAXMatchupForRound($MAXID);
	foreach($MatchupsList->getRecords() as $Matchup)
	{
		$Sender = $Matchup->value("Sender");
		$Receiver = $Matchup->value("Receiver");
		$subCount = getCountOfSubmissionFromUserToRecipientForMAXRound($Sender, $Receiver,$MAXID);
		print("<div class='tableRow'>");
		print("<div class='tableColumn'>$Sender</div>");
		print("<div class='tableColumn'>$Receiver</div>");
		print("<div class='tableColumn'>$subCount</div>");
		print("<div class='tableColumn'>");
		if(checkIfUserHasSubmittedArtForRound($Sender, $MAXID))
		{
			print("<input type='button' id='viewSubmissionButton$Sender' value='View'>");
			print("<div id='viewSubmittedEntryInternal$Sender' class='Internal'>");
			$UserEntry = getEntryDetailsByAliasAndMAXID($Sender, $MAXID);
			$EntryURL = $UserEntry->value("URL");
			$DateSubmitted = $UserEntry->value("EntryID");
			$DateSubmitted = date('F jS, Y', $DateSubmitted/1000);
			$SubmittedCharacterID = $UserEntry->value("CharacterID");
			$CharacterName = getCharacterNameByID($SubmittedCharacterID);
			$Comments = $UserEntry->value("Comments");
			print("<div class='adminViewMAXEntry'><a href='$EntryURL' target='_blank'><img src='$EntryURL'class='previewMAXEntry'></a><br><strong>Character Submitted:</strong> $CharacterName<br><strong>Date submitted:</strong> $DateSubmitted<br><strong>Comments: </strong><br>$Comments</div>");
			print("</div> <!-- end viewSubmittedEntryInternal -->");
			print("<script>");
			print("$('#viewSubmissionButton$Sender').click
			(
				function()
				{
					$('#viewSubmittedEntryInternal$Sender').slideToggle();
			});");
			print("</script>");
		}
		else
		{
			print("<strong>No</strong>");
		}
		print("</div><!-- end tableColumn -->");
		print("</div><!-- end tableRow -->"); //End tableRow
	}
	print("</div><!-- end MatchupTable -->"); //end MatchupTable
}

function buildMAXSplash()
{
	print("<center>");
	print("<div id='MAXSplash'>");
	print("<img src='https://www.comicadia.com/MAX/media/comicadia_max_splash.png' alt='MAX' />");
	print("</div>");
	print("</center>");
	buildMAXHelp();
}

function buildMAXHelp()
{
	print("<h2>Getting started</h2>");
	print("<div id='MAXHelpPanel'><a href='https://www.comicadia.com/MAX/about.php'><div id='MAXAboutButton'></div></a><a href='https://www.comicadia.com/MAX/guide.php'><div id='MAXGuideButton'></div></a><a href='https://www.comicadia.com/register.php'><div id='MAXRegisterButton'></div></a></div>");
}

function buildMAXWelcome($Alias)
{
	buildMAXSplash();
	$SignUpsOpen = getMAXSignup();
	if($SignUpsOpen > 0)
	{
		$Signup = getCurrentMAXRound();
		$SignupStart = $Signup->value("StartDate");
		$SignupDeadline = $Signup->value("EndDate");
		$SignupClose = $Signup->value("SignUpEndDate");
		$SignupTheme = $Signup->value("Theme");
		$SignupStatus = $Signup->value("Status");
		$MAXID = $Signup->value("DateCreated");
		
		$CloseDateComparable = $SignupClose;
		$DeadlineComparable = $SignupDeadline;
		$StartDate = date('F jS, Y', $SignupStart/1000);
		$EndDate = date('F jS, Y', $SignupDeadline/1000);
		$CloseDate = date('F jS, Y', $SignupClose/1000);
		$now = time()*1000;
		print("<div id='MAXsignup'>");
		print("<div id='MAXsignupHeader'><h2>Sign-Ups</h2></div>");
		print("<div id='currentMAXRound'>");
		print("<h3>Current MAX Round</h3>");
		print("<div class='MAXStart'>Start Date: $StartDate</div>");
		print("<div id='MAXSignUpCloses'>Sign-ups closed: $CloseDate</div>");
		print("<div id='MAXSignUpSubmissionDeadline'>Submission Deadline: $EndDate</div>");
		if($DeadlineComparable < $now)
		{
			print("<div id=MAXRoundClosed'>This MAX Round is now over.</div>");
			if(checkIfUserIsSignedUp($Alias, $MAXID))
			{
				if(checkIfUserHasSubmittedArtForRound($Alias, $MAXID))
				{
					if(checkIfUserReceivedArtForRound($Alias, $MAXID))
					{
						buildReviewMAXEntry($Alias, $MAXID);
					}
					else
					{
						print("The person who received your name did not submit their art. 
						They have until an administrator close the round to submit or else they will be 
						blacklisted from future rounds until such a time as they upload your entry");
					}
				}
				else				
				{
					print("You did not submit any art for this round, yet. If you do not upload art before 
					an admin closes the round, you will automatically be blacklisted 
					from participating in future MAX rounds until you upload an entry");
					buildMAXUploadLateSubmission($Alias,$MAXID);
					//Make an upload option here which removes blacklisted status
				}
				if(isMAXRoundLocked($MAXID))
				{
					buildMAXUploadSubmission($Alias, $MAXID);
					buildMAXRecipientForUser($Alias, $MAXID);
				}
			}
		}
		elseif($CloseDateComparable < $now)
		{
			print("<div id='signupsClosed'>Sign ups are currently closed.</div>");
			if(checkIfUserIsSignedUp($Alias, $MAXID))
			{
				if(isMAXRoundLocked($MAXID))
				{
					buildMAXUploadSubmission($Alias, $MAXID);
					buildMAXRecipientForUser($Alias, $MAXID);
					buildMAXAskForMoreReferences($Alias, $MAXID);
				}
				else
				{
					print("Pairings are underway. Please contact an administrator if pairings are not done within 48 hours.");
				}
				//Make a div entry for the reference sheets the user received for them to look at.
				///Make an upload option here if they haven't uploaded, yet
			}
			else
			{
				print("Participants have been matched up and the MAX Round is currently underway.");
			}
		}
		else
		{
			print("<div id='signupsOpen'>");
			if(checkIfUserHasACharacterWithReferences($Alias))
			{
				if(checkIfUserIsSignedUp($Alias, $MAXID))
				{
					print("<div id='signMeUp'>");
					print("<input type='button' value='Withdraw' id='withdrawFromRoundButton' onclick=\"withdrawUserFromMAXRound('$Alias','$MAXID')\">");
					print("<input type='button' value='Sign Up!' id='signUpForRoundButton' onclick=\"signUserUpForMAXRound('$Alias','$MAXID')\" style=\"display: none;\">");
					print("</div>");
				}
				else
				{
					print("<div id='signMeUp'>");
					print("<input type='button' value='Sign Up!' id='signUpForRoundButton' onclick=\"signUserUpForMAXRound('$Alias','$MAXID')\">");
					print("<input type='button' value='Withdraw' id='withdrawFromRoundButton' onclick=\"withdrawUserFromMAXRound('$Alias','$MAXID')\" style=\"display: none;\">");
					print("</div>");
				}
				print("<div id='SignUpERRMSG' class='ERRMSG'></div>");
				print("</div>");//end SignupsOpen
				print("<script>");
				print("$('#withdrawFromRoundButton').click
				(
					function()
					{
						$('#withdrawFromRoundButton').hide();
						$('#signUpForRoundButton').show();
				});");
				print("$('#signUpForRoundButton').click
				(
					function()
					{
						$('#withdrawFromRoundButton').show();
						$('#signUpForRoundButton').hide();
				});");
				print("</script>");
			}
			else
			{
				print("You don't have a character in the database or a reference for said character. Please upload at least one reference in order to partake in a MAX Round.");
				print("<form action='?' method='GET'>");
				print("<input type='submit' name='submit' id='ManageProfile' value='Manage Profile' class='MAXControlPanelButton'> ");
				print("</form>");
				print("</div>");
			}
		}
		print("</div></div>");
	}
	else
	{
		print("<div id='currentMAXRound'>");
		print("<h3>Current MAX Round</h3>");
		print("No round is currently open for sign-ups.");
		print("</div>");
	}
	if(doesUserHaveAnyRoundsIncomplete($Alias))
	{
		print("<div class='MAXHeader'>");
		print("<h3>You have incomplete rounds</h3>");
		$RoundList = getUserIncompleteRoundsEntryIDs($Alias);
		foreach($RoundList->getRecord() as $Round)
		{
			$LateMAXID = $Round->value("MAXID");
			buildMAXUploadLateSubmission($Alias,$LateMAXID);
		}
		print("</div> <!-- End lateMAXRounds -->");
	}
	if(isUserBlacklisted($Alias))
	{
		print("<div class='MAXHeader'>");
		print("<h3>You are currently on the Blacklist</h3>");
		buildParticipantBlacklistRecords($Alias);
		print("</div>");
	}
}

function buildMAXAdminPanel()
{
	print("<input type='submit' id='GoToDashboard' class='leftPanelItem' value='Dashboard' onclick=\"window.location.href = 'https://www.comicadia.com/MAX/admin.php'\">");
    print("<form action='?' method='GET'>");
	print("<input type='submit' id='ManageMAXRound' class='leftPanelItem' value='MAX Rounds' name='submit'>");
	print("<input type='submit' id='ManageReferencesButton' class='leftPanelItem' value='MAX References' name='submit'> ");
	print("<input type='submit' id='ManageAoptions' class='leftPanelItem' value='MAX Adoptions' name='submit'> ");
	print("<input type='submit' id='ManageUsersButton' class='leftPanelItem' value='MAX Users' name='submit'>");
	print("<input type='submit' id='ManageEntriesButton' class='leftPanelItem' value='MAX Entries' name='submit'> ");
	print("<input type='submit' id='ManageBlacklistButton' class='leftPanelItem' value='MAX Blacklist' name='submit'> ");
	print("</form>");
}

function buildMAXWelcomeControlPanel($Alias)
{
	print("<div id='Control Panel'>");
	print("<h3>Control Panel</h3>");
	print("<input type='submit' value='MAX Home' class='MAXControlPanelButton' onclick=\"window.location.href = 'https://www.comicadia.com/MAX/' \">");
	print("<input type='submit' value='About MAX' class='MAXControlPanelButton' onclick=\"window.location.href = 'https://www.comicadia.com/MAX/about.php' \">");
	print("<input type='submit' value='User Guide' class='MAXControlPanelButton' onclick=\"window.location.href = 'https://www.comicadia.com/MAX/guide.php' \">");
	print("<form action='https://www.comicadia.com/MAX/?' method='GET'>");
	if($Alias != '')
	{
		print("<input type='submit' name='submit' id='ManageProfile' value='Manage Profile' class='MAXControlPanelButton'> ");
	}
	if(getCountOfOrphanEntries() > 0)
	{
		print("<input type='submit' name='submit' id='viewPotentialAdoption' value='View Adoptions' class='MAXControlPanelButton'>");
	}
	print("<input type='submit' name='submit' id='SearchMembers' value='Search MAX Members' class='MAXControlPanelButton'>");
	print("<input type='submit' name='submit' id='ViewPreviousRounds' value='View Previous MAX Rounds' class='MAXControlPanelButton'>");
	print("<input type='submit' name='submit' id='ViewCharacters' value='View MAX Characters' class='MAXControlPanelButton'>");
	print("<input type='submit' name='submit' id='ViewBlacklist' value='View MAX Blacklist' class='MAXControlPanelButton'>");
	print("</form>");
	print("</div>");
}

function buildAdminManageMAXRounds()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Rounds</h2></div>");
	buildAddMAXRound();
	buildManageMAXRounds();
	print("</div>");
}

function buildMAXAdminManageReferences($Alias)
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX References</h2></div>");
	buildManagePendingReferences($Alias);
	buildManageSubmittedReferenceRequests($Alias);
	buildManageApprovedReferences($Alias);
	print("</div>");
}

function buildMAXAdminBlacklist($Alias)
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Blacklist</h2></div>");
	buildManageBlacklist($Alias);
	print("</div>");
}

function buildAdminManageAdoptions($Alias)
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Adoptions</h2></div>");
	buildAdoptionWaitingReview($Alias);
	buildAllApprovedAdoptions();
	buildAllRejectedAdoptions();
	print("</div>");
}

function buildManageMAXAdminEntries($Alias,$pageNumber,$articlesPerPage)
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Entries</h2></div>");
	buildManageReportedEntries($Alias);
	buildManageLateEntries($Alias);
	buildManageAllMAXEntries($Alias, $pageNumber, $articlesPerPage);
	print("</div>");
}

function buildMAXAdminMemberSearchDefault($articlesPerPage)
{
	$pageNumber = 0;
	$totalArticles = getMAXParticpantCount();
	$totalPages = ceil($totalArticles / $articlesPerPage);
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Users</h2></div>");
	buildMAXParticpantsSearch();
	buildMAXParticpantListAsPerPaginationByViewer($startArticle, $articlesPerPage,'Admin');
	buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Users');
	print("</div>");
}

function buildMAXMemberSearchDefault($articlesPerPage)
{
		$pageNumber = 0;
	$totalArticles = getMAXParticpantCount();
	$totalPages = ceil($totalArticles / $articlesPerPage);
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	
	print("<div id='MembersSearchWrap'>
	<div id='MAXHeader'><h2>Search MAX Participants</h2></div>");
	buildMAXParticpantsSearch();
	buildMAXParticpantListAsPerPaginationByViewer($startArticle, $articlesPerPage,'Subscriber');
	buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Users');
	print("</div>");
}

function buildMAXMemberSearchWithKeyword($Keyword, $MemberAlias,$pageNumber,$articlesPerPage)
{
	if($Keyword != '')
	{
		$totalArticles = getMAXParticpantCountByKeyword($Keyword);
	}
	else
	{
		$totalArticles = getMAXParticpantCount();
	}
	$totalPages = ceil($totalArticles / $articlesPerPage);
	
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	
	if($MemberAlias != '')
	{
		buildMAXParticipantProfile($MemberAlias);
		buildMAXCharactersOfRecipient($MemberAlias);
		buildMAXParticipantHistory($MemberAlias);
	}
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	print("<div id='MembersSearchWrap'>
	<div id='MAXHeader'><h2>Search MAX Participants</h2></div>");
	buildMAXParticpantsSearch();
	
	if($Keyword !='')
	{
		buildMAXParticpantListFromSearchAsPerPaginationByViewer($Keyword, $startArticle, $articlesPerPage,'Subscriber');
	}
	else
	{
		buildMAXParticpantListAsPerPaginationByViewer($startArticle, $articlesPerPage,'Subscriber');
	}
	buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Users');
	print("</div>");
}

function buildMAXAdminMemberSearchWithKeyword($Keyword, $MemberAlias,$pageNumber,$articlesPerPage,$Alias)
{
	if($Keyword != '')
	{
		$totalArticles = getMAXParticpantCountByKeyword($Keyword);
	}
	else
	{
		$totalArticles = getMAXParticpantCount();
	}
	$totalPages = ceil($totalArticles / $articlesPerPage);
	
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	
	if($MemberAlias != '')
	{
		print("<h2>".$MemberAlias."'s MAX Profile</h2>");
		buildMAXParticipantProfile($MemberAlias);
		buildMAXBlacklistParticipant($MemberAlias,$Alias);
		buildMAXParticipantHistory($MemberAlias);
	}
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage MAX Users</h2></div>");
	buildMAXParticpantsSearch();
	
	if($Keyword !='')
	{
		buildMAXParticpantListFromSearchAsPerPaginationByViewer($Keyword, $startArticle, $articlesPerPage,'Admin');
	}
	else
	{
		buildMAXParticpantListAsPerPaginationByViewer($startArticle, $articlesPerPage,'Admin');
	}
	buildMAXPagination($pageNumber, $totalPages, $totalArticles,'Users');
	print("</div>");
}

function buildMAXPagination($pageNumber, $totalPages, $totalArticles, $Fields)
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
			else
			{
				if(isset($_GET['Search']))
				{
					$Search = $_GET['Search'];
					print("<a href=\"?page=".$page."&Search=".$Search."&Fields=".$Fields."\">$page</a>");
				}
				else
				{
					print("<a href=\"?page=".$page."&Fields=$Fields\">$page</a>");
				}
			}
		}
		print("</div>");
	}
	else
	{
		print("No Users found with that particular keyword");
	}
}

function buildAddMAXRound()
{
	print("<div id='addMAXRound'>");
	print("<input type='button' id='addMAXRoundButton' value='Start a MAX round'>");
	print("<div id='addMAXRoundInternal' class='Internal'>");
	print("<div id='addMAXStartDate'>Start Date: <input type='text' id='addMAXStartDateText' name='addMAXStartDateText'></div>");
	print("<div id='addMAXCloseSignUp'>Sign Ups close: <input type='text' id='addMAXCloseSignUpText' name='addMAXCloseSignUpText'></div>");
	print("<div id='addMAXFinalDate'>Last day to submit: <input type='text' id='addMAXFinalDateText' name='addMAXFinalDateText'></div>");
	print("<div id='addMAXTheme'>Theme (Optional):<br> <textarea id='addMAXThemeText' class='MAXTextarea'></textarea></div>");
	print("<div id='addMAXStatus'>Status: <select id='addMAXStatusSelect'>");
	print("<option value='Pending'>Pending</option>");
	print("<option value='Active'>Active</option>");
	print("</select><br>");
	print("<div id='addMAXRoundButtons'><input type='button' value='Schedule a Round' id='addMAXRoundButton' onclick='ScheduleMAXRound()'></div>");
	print("<div id='addMAXerrMSG' class = 'ERRMSG'></div>");
	print("</div>"); //end addMAXRoundinternal
	print("</div>");//end addMAXRound
	
	print("<script>");
	print("$('#addMAXRoundButton').click
	(
		function()
		{
			$('#addMAXRoundInternal').slideToggle();
	});");
	print("$(function(){
	$('*[name=addMAXStartDateText]').appendDtpicker({
							\"dateFormat:\": \"DD-MM-YYYY\",
							\"futureOnly\": true,
							\"dateOnly\": true											
		});
	});");
	print("$(function(){
	$('*[name=addMAXCloseSignUpText]').appendDtpicker({
							\"dateFormat:\": \"DD-MM-YYYY\",
							\"futureOnly\": true,
							\"dateOnly\": true											
		});
	});");
	print("$(function(){
	$('*[name=addMAXFinalDateText]').appendDtpicker({
							\"dateFormat:\": \"DD-MM-YYYY\",
							\"futureOnly\": true,
							\"dateOnly\": true											
		});
	});");
	print("</script>");
}

function buildManageMAXRounds()
{
	if(getCountOfPendingMAXRounds() > 0)
	{
		print("<div id='editMAXPendingRoundsHeader' class='cpanelHeader'><h2>Pending MAX Rounds</h2></div>");
		print("<div id='editMAXPendingRoundsWrap'>");
		$MAXPendingList = getAllPendingMAXRounds();
		foreach($MAXPendingList->getRecords() as $MAXPendingEntry)
		{
			buildManageMAXRound($MAXPendingEntry);
		}
		print("</div>");
	}
	
	print("<div id='editMAXRoundsHeader' class='cpanelHeader'><h2>Previous MAX Rounds</h2></div>");
	print("<div id='editMAXRoundsWrap'>");
	if(getCountOfCompletedMAXRounds() > 0)
	{
		$MAXList = getAllCompletedMAXRounds();
		foreach($MAXList->getRecords() as $MAXEntry)
		{
			buildManageMAXRound($MAXEntry);
		}
		print("</div>");//End editMAXRoundsWrap
	}
	else
	{
		print("No rounds have been completed, yet");
	}
}

function buildManageMAXRound($MAXEntry)
{
	$MAXID = $MAXEntry->value("DateCreated");
	$StartDate = $MAXEntry->value("StartDate");
	$SignUp = $MAXEntry->value("SignUpEndDate");
	$Deadline = $MAXEntry->value("EndDate");
	$Theme = $MAXEntry->value("Theme");
	$Status = $MAXEntry->value("Status");
	
	$StartDateHuman = date('F jS, Y', $StartDate/1000);
	$DeadlineHuman = date('F jS, Y', $Deadline/1000);
	$SignUpHuman = date('F jS, Y', $SignUp/1000);
	
	$StartDate = date('Y-m-d', $StartDate/1000);
	$Deadline = date('Y-m-d', $Deadline/1000);
	$SignUp = date('Y-m-d', $SignUp/1000);
	
	print("<div id='MAXRound$MAXID' class='MAXEntry'>");
	print("<div id='MAXClickable$MAXID' class='dropLevel1'><h3>$StartDateHuman to $DeadlineHuman</h3></div>");
	print("<div id='MAXInternal$MAXID' class='Internal'>");
	print("<form><fieldsets>");
	print("<div class='MAXInfo'>Start Date: <input type='text' id='MAXStartText$MAXID' name='MAXStartText$MAXID' value='$StartDate' disabled></div>");
	print("<div class='MAXInfo'>Sign Up Closes: <input type='text' id='MAXCloseText$MAXID' name='MAXCloseText$MAXID' value='$SignUp' disabled></div>");
	print("<div class='MAXInfo'>Submission Deadline: <input type='text' id='MAXDeadlineText$MAXID' name='MAXDeadlineText$MAXID' value='$Deadline' disabled></div>");
	print("<div class='MAXInfo'>Theme: <input type='text' id='MAXThemeText$MAXID' name='$Status' value='$Theme' disabled></div>");
	$StatusList = getMAXStatusList();
	print("<div class='MAXInfo'>Status: <SELECT id='MAXStatusSelect$MAXID' disabled>");
	foreach($StatusList as $PotentialStatus)
	{
		$Selected = '';
		if($Status == $PotentialStatus)
		{
			$Selected = 'Selected';
		}
		print("<option value='$PotentialStatus' $Selected>$PotentialStatus</option>");
	}
	print("</select>");
	print("</div>");
	print("</fieldsets></form>");
	print("<div class='editMAXRoundButtons'><input type='button' value='Edit' id='editMAX$MAXID'> 
	<input type='button' value='Save' id='saveMAX$MAXID' onclick=\"UpdateMAX('$MAXID');\" disabled>
	<input type='reset' value='Reset' id='resetMAX$MAXID' disabled>
	<input type='button' value='Delete' onclick=\"deleteMAX('$MAXID');\"></div>");
	print("<div id='errMSG$MAXID' class='ERRMSG'></div>");
	print("</div>");// Endf MAXInternal
	print("</div>");//End MAXEntry
	print("<script>");
	print("$('#MAXClickable$MAXID').click		
	(
		function()
		{
			$('#MAXInternal$MAXID').slideToggle();
		});	");					
		
	print("$(function(){
	$('*[name=MAXStartText$MAXID]').appendDtpicker({
							\"dateFormat:\": \"YYYY-MM-DD\",
							\"dateOnly\": true											
		});
	});");
	print("$(function(){
	$('*[name=MAXCloseText$MAXID]').appendDtpicker({
							\"dateFormat:\": \"YYYY-MM-DD\",
							\"dateOnly\": true											
		});
	});");
	print("$(function(){
	$('*[name=MAXDeadlineText$MAXID]').appendDtpicker({
							\"dateFormat:\": \"YYYY-MM-DD\",
							\"dateOnly\": true											
		});
	});");
	print("	$('#editMAX$MAXID').click
			(
				function()
				{
					$('#saveMAX$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#resetMAX$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXStartText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXCloseText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXDeadlineText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXThemeText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXStatusSelect$MAXID').prop('disabled', function(i, v) { return !v; });
				});");
	print("	$('#saveMAX$MAXID').click
			(
				function()
				{
					$('#saveMAX$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#resetMAX$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXStartText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXCloseText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXDeadlineText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXThemeText$MAXID').prop('disabled', function(i, v) { return !v; });
					$('#MAXStatusSelect$MAXID').prop('disabled', function(i, v) { return !v; });
				});");	
	print("</script>");
}

function buildMAXAdoptionListWithPagination($pageNumber,$articlesPerPage,$Alias)
{
	$totalArticles = getCountOfOrphanEntries();
	if($pageNumber < 1)
	{
		$FirstView = true;
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	
	$totalPages = ceil($totalArticles / $articlesPerPage);
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	
	if(checkIfUserHasAnyClaimedAdoptions($Alias))
	{
		buildUserCurrentAdoptions($Alias);
	}
	
	print("<h2>MAX Adoptions</h2>");	
	print("<div id='PotentialAdoptions'>");
	print("</div>");
	if($totalArticles != 0)
	{
		$AdoptionList = getPotentialMAXEntriesForAdoption($startArticle, $totalArticles);
		
		print("<h3>Available Adoptions</h3>");
		print("<input type='button' value='View' id='availableAdoptionsClickable'>");
		print("<div id='availableAdoptionsInternal'");
		if($FirstView)
		{
			print("	class='Internal'");
		}
		print(">");
		buildMAXAdoptionPanel($AdoptionList, $Alias);
		buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Adoptions');
		print("</div>");
		print("<script>");
		print("$('#availableAdoptionsClickable').click		
		(
			function()
		{
			$('#availableAdoptionsInternal').slideToggle();
		});	");					
	print("</script>");
	}
	
	print("<div id='userCompletedAdoptions'>");
	print("<h3>Completed Adoptions</h3>");
	if(checkIfUserHasAnyCompletedAdoptions($Alias))
	{
		print("<input type='button' value='View' id='userCompletedAdoptionsClickable'>");
		print("<div id='userCompletedAdoptionsInternal' class='Internal'>");
		buildUserApprovedAdoptions($Alias);
		print("</div>");
		print("<script>");
		print("$('#userCompletedAdoptionsClickable').click		
		(
			function()
			{
				$('#userCompletedAdoptionsInternal').slideToggle();
			});	");					
		print("</script>");
	}
	else
	{
		print("None yet.");
	}
	print("</div>");
	
	print("<div id='userRejectedAdoptions'>");
	print("<h3>Rejected Adoptions</h3>");
	if(checkIFUserHasAnyRejectedAdoptions($Alias))
	{
		print("<input type='button' value='View' id='userRejectedAdoptionsClickable'>");
		print("<div id='userRejectedAdoptionsInternal' class='Internal'>");
		buildUserRejectedAdoptions($Alias);
		print("</div>");
		print("<script>");
		print("$('#userRejectedAdoptionsClickable').click		
		(
			function()
			{
				$('#userRejectedAdoptionsInternal').slideToggle();
			});	");					
		print("</script>");
	}
	else
	{
		print("None yet");
	}
	print("</div>");
}

function buildMAXPreviousRoundsWithPagination($pageNumber,$articlesPerPage)
{
	$totalArticles = getMAXCompletedRoundsCount();
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	
	$totalPages = ceil($totalArticles / $articlesPerPage);
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	
	print("<h2>Previous MAX Rounds</h2>");	
	print("<div id='PreviousMAXRounds'>");
	if($totalArticles != 0)
	{
		$MAXList = getAllCompletedMAXRoundsAndTheirEntriesByPagination($startArticle, $articlesPerPage);
		buildPreviousMAXRoundsPanel($MAXList);
		buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Rounds');
	}
	else
	{
		print("No Rounds have been completed, yet.");
	}
	print("</div>");
}

function buildPreviousMAXRoundsPanel($MAXList)
{
	foreach($MAXList->getRecords() as $MAX)
	{
		buildPreviousMAXRoundEntries($MAX);
	}
}

function buildMAXAdoptionPanel($AdoptionList,$Alias)
{
	
	print("<div id='potentialAdoptions'>");
	foreach($AdoptionList->getRecords() as $Entry)
	{
		$EntryID = $Entry->value("EntryID");
		$OrphanID = $Entry->value("OrphanID");
		$CharacterID = $Entry->value("CharacterID");
		$Receiver = $Entry->value("Receiver");
		
		print("<div id='entry$OrphanID'>");
		print("<div class='adoptEntryID'>OrphanID: $OrphanID</div>");
		print("<div class='adoptEntryReceiver'>Receiver: <a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$Receiver"."&Fields=Users' target='_blank'>$Receiver</a></div>");
		print("<div class='adoptEntryCharacter'>Character Originally Requested: ");
		if($CharacterID != '')
		{
			$CharacterName = getCharacterNameByID($CharacterID);
			print("<a href='https://www.comicadia.com/MAX/index.php?Search=$CharacterName&Fields='Characters''>$CharacterName</a>");
		}
		else
		{
			print("None");
		}
		print("</div>"); // end adoptEntryCharacter
		$Status = getMostRecentAdoptionStatusByOrphanID($OrphanID);
		if($Status != 'Completed' AND $Status != 'Claimed')
		{
			$OriginalSender = getOriginalSenderOfEntry($EntryID);
			if($Alias != '')
			{
				if($Alias != $OriginalSender AND $Alias != $Receiver)
				{
					print("<div class='adoptEntryButtons'><input type='button' value='Adopt me!'/ onclick=\"adoptEntry('$Alias','$OrphanID','$EntryID');\"></div>");
					print("<div id='adoptEntry$OrphanID"."MSG' class='ERRMSG'></div>");
				}
				else
				{
					if($Alias == $Receiver)
					{
						if($Status == '')
						{
							print("<div id='entryStatus'><strong>Waiting for Adoption</strong></div>");
						}
						else
						{
							print("<div id='entryStatus'><strong>$Status</strong></div>");
						}
					}
					elseif($Alias == $OriginalSender)
					{
						print("<div id='entryStatus'><strong>You were originally signed up to do this.</strong></div>");
					}
					else
					{
						print("<div id='entryStatus'><strong>Fix this</strong></div>");
					}
				}
			}
			else
			{
				print("<div id='entryStatus'><strong>Log In To Adopt me!</strong></div>");
			}
		}
		else
		{			
			print("<div id='entryStatus'>Current Status: <strong>$Status</strong></div>");
		}
		print("</div>"); //end Entry$EntryID
	}
	
	print("</div>"); // end potentialAdoptions
}

function buildMAXUserPreviousRounds($Alias)
{
	print("<h3>Your previous MAX Rounds</h3>");
	if(getCountOfUserCompletedRounds($Alias) > 0)
	{
		$SignUpsOpen = getMAXSignup();
		print("<div id='PreviousMAXRounds'>");
		if($SignUpsOpen > 0)
		{
			$Signup = getCurrentMAXRound();
			$GetOther = $Signup->value("DateCreated");
			$MAXList = getAllPreviousMAXRoundsUserEntered($GetOther, $Alias);
		}
		else
		{
			if(getCountOfMAXRoundsUserParticipatedIn($Alias) > 0)
			{
				$MAXList = getAllMAXRoundsUserEntered($Alias);
				foreach($MAXList->getRecords() as $MAX)
				{	
					buildUserPreviousMAXRoundEntries($Alias,$MAX);
				}
			}
			else
			{
				print("You have not participated in a round, yet");
			}
			
		}
		
		print("</div>");
	}
	else
	{
		print("You have not participated in a round, yet");
	}
}

function buildPreviousMAXRoundEntries($MAX)
{
	$MAXID = $MAX->value("MAXID");
	$StartDate  = $MAX->value("StartDate");
	$EndDate  = $MAX->value("EndDate");
	$StartDate = date('F jS, Y', $StartDate/1000);
	$EndDate = date('F jS, Y', $EndDate/1000);
	$Theme = $MAX->value("Theme");
	$EntryList = $MAX->value("EntryIDs");
	
	print("<div class='MAXPreviousRound'>");
	print("<div id='MAXPreviousRoundClickable$MAXID' class='mouseOver'><h3>$StartDate - $EndDate</h3></div>");
	print("<div id='MAXPreviousRoundInternal$MAXID'");
	if(checkIfMAXRoundIsMostRecentlyCompleted($MAXID))
	{
		print(">");
	}
	else
	{
		print(" class='Internal'>");
	}
	foreach($EntryList as $EntryID)
	{
		if(checkIfEntryHasReceivedSubmission($EntryID))
		{
			$EntryDetails = getEntryDetails($EntryID);
			$CharacterID = $EntryDetails->value("CharacterID");
			$Sender = $EntryDetails->value("Sender");
			$Receiver = $EntryDetails->value("Receiver");
			$URL = $EntryDetails->value("URL");
			$Comments = $EntryDetails->value("Comments");
			$Status = $EntryDetails->value("Status");
			if(checkIfUserHasSubmittedArtForRound($Receiver, $MAXID))
			{
				if($Status == 'Completed')
				{
					$CharacterName = getCharacterNameByID($CharacterID);
					print("<div class='previousMAXRoundEntry'>");
					print("<div class='EntryPreview'><a href='https://www.comicadia.com/MAX/index.php?EntryID=$EntryID' target='_blank'><img src = '$URL' class='previewMAXEntry' /></a></div>");
					print("<div class='EntryDetails'>");
					print("<strong><a href='https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID&Fields=Characters' target='_blank'>$CharacterName</a></strong><br>By $Sender For $Receiver");
					print("</div>"); //end EntryDetails
					if(trim($Comments) != '')
					{
						print("<div class='entryComments'><strong>Comments:</strong><br>$Comments</div>");
					}
					print("</div>");// End previousMAXRoundEntry
				}
				elseif($Status =='Pending')
				{
					print("The submission by $Sender for $Receiver is pending review.");
				}
				else
				{
					print("There is a status that is not recognized by the system,");
				}
			}
			else
			{
				print("This entry is not available until $Receiver uploads their entry for this round.");
			}
		}
		else
		{
			$EntryDetails = getUnsubmittedEntryDetails($EntryID);
			$Sender = $EntryDetails->value("Sender");
			$Receiver = $EntryDetails->value("Receiver");
			print("<div class='NoMAXEntry'>$Sender did not submit an entry for $Receiver, yet</div>");	
		}
	}	
	
	print("</div> <!-- End MAXPreviousRoundInternal -->"); // end MAXPreviousRoundInternal
	print("</div> <!-- End MAXPreviousRound -->"); // end MAXPreviousRound
	print("<script>");
	print("$('#MAXPreviousRoundClickable$MAXID').click		
	(
		function()
		{
			$('#MAXPreviousRoundInternal$MAXID').slideToggle();
	});	");					
	print("</script>");
	
}

function buildUserPreviousMAXRoundEntries($Alias, $MAX)
{
	$MAXID = $MAX->value("MAXID");
	$StartDate  = $MAX->value("StartDate");
	$EndDate  = $MAX->value("EndDate");
	$StartDate = date('F jS, Y', $StartDate/1000);
	$EndDate = date('F jS, Y', $EndDate/1000);
	$Theme = $MAX->value("Theme");
	print("<div class='MAXPreviousRound'>");
	print("<input type='button' value='$StartDate - $EndDate' id='MAXPreviousRoundClickable$MAXID'>");
	print("<div id='MAXPreviousRoundInternal$MAXID' class='Internal'>");
	if(checkIfUserHasSubmittedArtForRound($Alias, $MAXID))
	{
		$EntryDetails = getEntryDetailsByAliasAndMAXID($Alias, $MAXID);
		$Status = $EntryDetails->value("Status");
		if($Status == 'Completed')
		{
			if($Theme != 'None')
			{
				print("<div class='MAXTheme'>Theme: $Theme</div>");
			}
			print("<div class='yourMAXEntryText'><h4>Your Entry</h4></div>");
			$EntryURL = getEntryURLFromUserForRound($Alias, $MAXID);
			print("<div class='yourMAXEntry'>");
		
			print("<div class='EntryPreview'><a href='$EntryURL' target='_blank'><img src='$EntryURL' class='previewMAXEntry'/></a></div>");
			if(checkIfUserReceivedArtForRound($Alias, $MAXID))
			{
				buildReviewMAXEntry($Alias, $MAXID);
			}			
		}
		elseif($Status =='Pending')
		{
			print("You entry is pending review. Once it has been accepted, you will see your reward, if any was submitted.");
		}
		elseif($Status =='Rejected')
		{
			print("Your entry was rejected. Please submit a new entry or contact and admin in the discord chat to resolve this issue");
			buildMAXUploadLateSubmission($Alias,$MAXID);
		}
		else
		{
			print("An entry has been received, but its status is currently unknown. Contact Chippy!");
		}
		print("</div>");
	}
	else
	{
		print("No entry received, yet");
		buildMAXUploadLateSubmission($Alias,$MAXID);
	}
	
	print("</div>"); // end MAXPreviousRoundInternal
	print("</div>"); // end MAXPreviousRound
	print("<script>");
	print("$('#MAXPreviousRoundClickable$MAXID').click		
	(
		function()
		{
			$('#MAXPreviousRoundInternal$MAXID').slideToggle();
	});	");					
	print("</script>");
}

function buildMAXManageUserReferences($Alias)
{
	print("<h2>Your MAX Info</h2>");
	$characterCount = getCountofCharactersOfUser($Alias);
	print("<h3>Your Characters</h3>");
	
	if($characterCount > 0)
	{
		buildMAXAddCharacter($Alias);
		$characterList = getCharactersOfUser($Alias);
		buildMAXManageActiveCharacterByList($Alias,$characterList);
		$RetiredList = getRetiredCharactersForUser($Alias);
		buildMAXManageRetiredCharactersByList($Alias,$RetiredList);
	}
	else
	{
		print("You do not have any characters in the database, yet.");
		print("<br>");
		buildMAXAddCharacter($Alias);
	}
	
}

function buildMAXAddCharacter($Alias)
{
	print("<div id='addCharacter'>");
	print("<input type='button' id='addCharacterClickable' value='Add Character'>");
	print("<div id='addCharacterInternal' class='Internal'>");
	print("<div id='addCharacterDescription'>");
	print("<div id='addCharacterName'>Name: <input type='text' id='characterNameText' value='' ></div>");
	print("<div id='addCharacterAge'>Age: <input type='text' id='characterAgeText' value='' ></div>");
	print("<div id='addCharacterRace'>Race/Species: <input type='text' id='characterRaceText' value='' ></div>");
	print("<div id='addCharacterGender'>Gender: <input type='text' id='characterGenderText' value='' ></div>");
	print("<div id='addCharacterHair'>Hair: <input type='text' id='characterHairText' value='' ></div>");
	print("<div id='addCharacterEyes'>Eyes: <input type='text' id='characterEyesText' value='' ></div>");
	print("<div id='addCharacterHeight'>Height: <input type='text' id='characterHeightText' value='' ></div>");
	print("<div id='addCharacterWeight'>Weight: <input type='text' id='characterWeightText' value='' ></div>");
	print("<div id='addCharacterWriteup'>Bio: <br> <textarea class='characterBio' id='characterWriteupText' ></textarea></div>");
	print("<div id='addCharacterWebcomic'>Webcomic: ");
	print("<select id='characterWebcomicSelect'>");
	print("<option value='None'>None</option>");
	$UserComics = getUsersWebcomicNames($Alias);
	foreach($UserComics->getRecords() as $Comic)
	{
		$ComicID = $Comic->value("ComicID");
		$ComicName = $Comic->value("Name");
		print("<option value='$ComicID'>$ComicName</option>");
	}
	print("</select>");
	print("</div>");
	print("<div id='addCharacterBottons'><input type='button' value='Save' id='addCharacter' onclick=\"addCharacter('$Alias');\"></div>");
	print("<div id='addCharacterMSG' class='errMSG'></div>");
	print("</div>"); //end CharacterDescription
	print("</div>");//end Internal Add Character
	print("</div>"); // end addCharacter
	print("<script>");
	print("$('#addCharacterClickable').click		
		(
			function()
			{
				$('#addCharacterInternal').slideToggle();
			});	");	
	print("</script>");
}

function buildMAXCharactersOfRecipient($Alias)
{
	print("<div id='RecipientCharacters'>");
	print("<h3>Characters</h3>");
	if(getCharactersOfUserCount($Alias) > 0)
	{
		$CharacterList = getCharactersOfUser($Alias);
		foreach($CharacterList->getRecords() as $Character)
		{
			$CharacterName = $Character->value("Name");
			$CharacterAge = $Character->value("Age");
			$CharacterRace = $Character->value("Race");
			$CharacterGender = $Character->value("Gender");
			$CharacterHair = $Character->value("Hair");
			$CharacterEyes = $Character->value("Eyes");
			$CharacterHeight = $Character->value("Height");
			$CharacterWeight = $Character->value("Weight");
			$CharacterWriteUp = $Character->value("WriteUp");
			$CharacterID = $Character->value("CharacterID");
			$Comic = getCharacterWebcomic($CharacterID);
			
			
			print("<div id='Character$CharacterID' class='characterWrap'>");
			$preferredURL = getCharacterThumbnail($CharacterID);
			if($preferredURL != '')
			{
				print("<div class='currentCharacterThumbnail'>Current Thumbnail: <br><a href='$preferredURL' target='_blank'><img src='$preferredURL' class='previewMAXEntry' /></a></div>");
			}
			print("<input type='button' id='Character".$CharacterID."Clickable' value='$CharacterName'>");
			if(isCharacterCurrentlyPreferred($CharacterID))
			{
				print("<div id='prefferedCharacter' class='preferredCharacterIndicator'>PREFERRED</div>");
			}
			print("<div id='Character".$CharacterID."Internal' class='Internal'>");
			print("<input type='button' id='DetailsForCharacter$CharacterID' value='Character Bio'> ");
			print("<div id='Character".$CharacterID."DetailsInternal' class='Internal'>");
			print("<div id='Character".$CharacterID."Name'><strong>Name:</strong> $CharacterName</div>");
			print("<div id='Character".$CharacterID."Age'><strong>Age: </strong>$CharacterAge</div>");
			print("<div id='Character".$CharacterID."Race'><strong>Race/Species: </strong>$CharacterRace</div>");
			print("<div id='Character".$CharacterID."Gender'><strong>Gender: </strong>$CharacterGender</div>");
			print("<div id='Character".$CharacterID."Hair'><strong>Hair: </strong>$CharacterHair</div>");
			print("<div id='Character".$CharacterID."Eyes'><strong>Eyes: </strong>$CharacterEyes</div>");
			print("<div id='Character".$CharacterID."Height'><strong>Height: </strong>$CharacterHeight</div>");
			print("<div id='Character".$CharacterID."Weight'><strong>Weight: </strong>$CharacterWeight</div>");
			$OwnerAlias = getCharacterOwnerAliasByID($CharacterID);
			print("<div id='Chracter".$CharacterID."Owner'><strong>Owned by: </strong><a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$OwnerAlias&Fields=Users'>$OwnerAlias</a></div>");
			$ComicID = $Comic->value("ComicID");
			print("<div id='Character".$CharacterID."Comic'><strong>Comic: </strong>");
			if($ComicID != '')
			{
				$ComicName = $Comic->value("Name");
				$ComicURL = $Comic->value("URL");
				print("<a href='$ComicURL' target='_blank'>$ComicName</a>");
			}
			else
			{
				print("None");
			}
			print("</div>");
			
			print("<div id='Character".$CharacterID."Writeup'><strong>Bio: </strong><br> <div class='characterBio'>$CharacterWriteUp</div></div>");
			print("</div>"); //end DetailsForCharacterInternal
			
			print("<input type='button' value='References' id='character".$CharacterID."ReferencesClickable'>");
			print("<div id='Character".$CharacterID."ReferencesInternal' class='Internal'>");
			print("<h3>References for $CharacterName</h3>");
			if(getCharacterOfUserReferencesCount($Alias, $CharacterID) >0)
			{
				print("<div class='ReferencesWrap'>");
				$referenceList = getCharacterOfUserReferences($Alias, $CharacterID);
				foreach($referenceList->getRecords() as $Reference)
				{
				
					$ReferenceURL = $Reference->value("URL");
					$ReferenceStatus = $Reference->value("Status");
					if($ReferenceStatus == 'Approved')
					{
						print("<div class='referencePreview'>");
						print("<a href='$ReferenceURL' target='_blank'><img src='$ReferenceURL' class='previewMAXEntry' /></a>");
						print("</div>");
						
					}
				}
				print("</div  <!-- End Reference Wrap -->");// End ReferencesWrap
			}
			else
			{
				print("No references for this character have been uploaded");
			}
			print("</div> <!-- End ReferencesInternal -->"); //End Character".$CharacterID."ReferencesInternal
			print("</div> <!-- End CharacterIDInternal -->"); //End Character".$CharacterID."Internal
			print("</div> <!-- End CharacterWrap -->"); //End Character$CharacterID			
			
			
			print("<script>");
			print("$('#Character".$CharacterID."Clickable').click		
			(
				function()
			{
				$('#Character".$CharacterID."Internal').slideToggle();
			});	");	
			print("$('#DetailsForCharacter$CharacterID').click		
			(
				function()
			{
				$('#Character".$CharacterID."DetailsInternal').slideToggle();
			});	");	
			print("$('#character".$CharacterID."ReferencesClickable').click		
			(
				function()
			{
				$('#Character".$CharacterID."ReferencesInternal').slideToggle();
			});	");	
			print("</script>");
			
		}
	}
	else
	{
		print("This user has not uploaded any characters.");
	}
	print("</div> <!-- End RecipientCharacters -->"); //end RecipientCharacters
}

function buildMAXManageActiveCharacterByList($Alias,$characterList)
{
	print("<h3>Active Characters</h3>");
	if(getCharactersOfUserCount($Alias) > 0 )
	{	
		foreach($characterList->getRecords() as $Character)
		{
			buildManageCharacterEntry($Alias, $Character);

		}	
	}
	else
	{
		print("You currently have no active characters");
	}
}

function buildMAXManageRetiredCharactersByList($Alias,$characterList)
{
	print("<h3>Retired Characters</h3>");
	if(getCountOfRetiredCharactersForUser($Alias) > 0 )
	{	
		foreach($characterList->getRecords() as $Character)
		{
			buildManageCharacterEntry($Alias, $Character);

		}	
	}
	else
	{
		print("You currently have no retired characters");
	}
}

function buildManageCharacterEntry($Alias, $Character)
{
	
	$CharacterName = $Character->value("Name");
	$CharacterAge = $Character->value("Age");
	$CharacterRace = $Character->value("Race");
	$CharacterGender = $Character->value("Gender");
	$CharacterHair = $Character->value("Hair");
	$CharacterEyes = $Character->value("Eyes");
	$CharacterHeight = $Character->value("Height");
	$CharacterWeight = $Character->value("Weight");
	$CharacterWriteUp = $Character->value("WriteUp");
	$CharacterID = $Character->value("CharacterID");
	$CharacterStatus = $Character->value("Status");
	print("<div id='Character$CharacterID' class='characterWrap'>");
	print("<input type='button' id='Character".$CharacterID."Clickable' value='$CharacterName'>");
	print("<div id='Character".$CharacterID."Internal' class='Internal'>");
	if(!isCharacterCurrentlyPreferred($CharacterID))
	{
		print("<input type='button' id='setCharacterAsPreferredButton$CharacterID' value='Make this Character your preferred character' onclick=\"setThisCharacterAsPreferred('$Alias','$CharacterID');\">");
		print("<input type='button' id='removeCharacterAsPreferredButton$CharacterID' value='Remove this character as your preferred character' onclick=\"removeCharacterAsPreferred('$Alias','$CharacterID');\"\ style='display: none;'>");
	}
	else
	{
		print("<input type='button' id='setCharacterAsPreferredButton$CharacterID' value='Make this Character your preferred character' onclick=\"setThisCharacterAsPreferred('$Alias','$CharacterID');\" style='display: none;'> ");
		print("<input type='button' id='removeCharacterAsPreferredButton$CharacterID' value='Remove this character as your preferred character' onclick=\"removeCharacterAsPreferred('$Alias','$CharacterID');\" >");
	}
	print("<script>");
	print("$('#setCharacterAsPreferredButton$CharacterID').click		
	(
		function()
		{
			$('#setCharacterAsPreferredButton$CharacterID').hide();
			$('#removeCharacterAsPreferredButton$CharacterID').show();
		});	");	
	print("$('#removeCharacterAsPreferredButton$CharacterID').click		
	(
		function()
		{
			$('#setCharacterAsPreferredButton$CharacterID').show();
			$('#removeCharacterAsPreferredButton$CharacterID').hide();
		});	");	
		
	print("</script>");
	print("<div id='Character".$CharacterID."PreferredMSG' class='errMSG'></div>");
	print("<div id='Character".$CharacterID."Description'>");
	print("<form>");
	print("<fieldsets>");
	print("<div class='CharacterName'>Name: <input type='text' id='character".$CharacterID."NameText' value=\"$CharacterName\" disabled></div>");
	print("<div class='CharacterAge'>Age: <input type='text' id='character".$CharacterID."AgeText' value=\"$CharacterAge\" disabled></div>");
	print("<div class='CharacterRace'>Race/Species: <input type='text' id='character".$CharacterID."RaceText' value=\"$CharacterRace\" disabled></div>");
	print("<div class='CharacterGender'>Gender: <input type='text' id='character".$CharacterID."GenderText' value=\"$CharacterGender\" disabled></div>");
	print("<div class='CharacterHair'>Hair: <input type='text' id='character".$CharacterID."HairText' value=\"$CharacterHair\" disabled></div>");
	print("<div class='CharacterEyes'>Eyes: <input type='text' id='character".$CharacterID."EyesText' value=\"$CharacterEyes\" disabled></div>");
	print("<div class='CharacterHeight'>Height: <input type='text' id='character".$CharacterID."HeightText' value=\"$CharacterHeight\" disabled></div>");
	print("<div class='CharacterWeight'>Weight: <input type='text' id='character".$CharacterID."WeightText' value=\"$CharacterWeight\" disabled></div>");
	print("<div class='CharacterComic'>Comic: <Select id='character".$CharacterID."ComicSelect' disabled>");
	print("<option value='None'>None</option>");
	$UserComics = getUsersWebcomicNames($Alias);
	$CharacterComic = getCharacterWebcomic($CharacterID);
	{
		foreach($UserComics->getRecords() as $UserComic)
		{
			$ComicID = $UserComic->value("ComicID");
			$ComicName = $UserComic->value("Name");
			$CharacterComicID = $CharacterComic->value("ComicID");
			$Selected = '';
			if($ComicID == $CharacterComicID)
			{
				$Selected = 'Selected';
			}
			print("<option value='$ComicID' $Selected>$ComicName</option>");
		}
	}
	print("</select>");
	print("</div>");
	print("<div class='CharacterWriteup'>Bio: <br> <textarea class='characterBio' id='character".$CharacterID."WriteupText' disabled>$CharacterWriteUp</textarea></div>");
	print("<div class='editCharacterProfileButtons'>");
	print("<input type='button' id='editDetailsForCharacter$CharacterID' value='Edit'>");
	print("<input type='button' id='saveDetailsForCharacter$CharacterID' value='Save' onclick=\"saveCharacterDetails('$CharacterID');\" disabled>");
	print("<input type='Reset' id='resetDetailsForCharacter$CharacterID' value='Reset' disabled>");
	print("</fieldsets>");
	print("</form>");
	print("<div id='saveCharacterDetails$CharacterID"."MSG' class='ERRMSG'></div>");
	if($CharacterStatus == 'Active')
	{
		print("<input type='button' value='Retire Character' id='retireChracter$CharacterID' onclick=\"retireCharacter('$CharacterID');\" >");
	}
	else
	{
		print("<input type='button' value='Revive Character' id='reviveChracter$CharacterID' onclick=\"reviveCharacter('$CharacterID');\" >");
	}
	print("</div>");
	$preferredURL = getCharacterThumbnail($CharacterID);
	if($preferredURL != '')
	{
		print("<div class='currentCharacterThumbnail'>Current Thumbnail: <br><a href='$preferredURL' target='_blank'><img src='$preferredURL' class='previewMAXEntry' /></a></div>");
	}
	print("</div>"); // End character text description
	print("<script>");
	print("$('#Character".$CharacterID."Clickable').click		
	(
		function()
		{
			$('#Character".$CharacterID."Internal').slideToggle();
		});	");	
	print("$('#editDetailsForCharacter$CharacterID').click		
	(
		function()
		{
			$('#saveDetailsForCharacter$CharacterID').prop('disabled', function(i, v) { return !v; });
			$('#resetDetailsForCharacter$CharacterID').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."NameText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."AgeText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."RaceText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."GenderText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."HairText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."EyesText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."HeightText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."WeightText').prop('disabled', function(i, v) { return !v; });				
			$('#character".$CharacterID."WriteupText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."ComicSelect').prop('disabled', function(i, v) { return !v; });
		});	");	
	print("$('#saveDetailsForCharacter$CharacterID').click		
	(
		function()
		{
			$('#saveDetailsForCharacter$CharacterID').prop('disabled', function(i, v) { return !v; });
			$('#resetDetailsForCharacter$CharacterID').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."NameText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."AgeText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."RaceText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."GenderText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."HairText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."EyesText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."HeightText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."WeightText').prop('disabled', function(i, v) { return !v; });				
			$('#character".$CharacterID."WriteupText').prop('disabled', function(i, v) { return !v; });
			$('#character".$CharacterID."ComicSelect').prop('disabled', function(i, v) { return !v; });
		});	");	
	print("</script>");
	$referenceCount = getCharacterOfUserReferencesCount($Alias, $CharacterID);
	$referenceList = getCharacterOfUserReferences($Alias, $CharacterID);
	print("<input type='button' value='References' id='character".$CharacterID."ReferencesClickable'>");
	print("<div id='Character".$CharacterID."ReferencesInternal' class='Internal'>");
	print("<script>");
	print("$('#character".$CharacterID."ReferencesClickable').click		
	(
		function()
		{
			$('#Character".$CharacterID."ReferencesInternal').slideToggle();
		});	");	
	print("</script>");
	if($referenceCount > 0)
	{
		if($referenceCount < 5)
		{
			buildMAXAddReference($Alias, $CharacterID);
		}
		$activeRef = 0;
		foreach($referenceList->getRecords() as $Reference)
		{
			print("<div id='character".$CharacterID."Reference$activeRef'>");
			$ReferenceURL = $Reference->value("URL");
			$ReferenceStatus = $Reference->value("Status");
			print("<div id='character".$CharacterID."Reference".$activeRef."Preview' class='referencePreview'>");
			print("<a href='$ReferenceURL' target='_blank'><img src='$ReferenceURL' class='previewMAXEntry' /></a>");
			print("</div>");
			print("<div class='referenceStatus'>Status: $ReferenceStatus</div>");
			print("<div class='ReferenceButtons'>");
			if($ReferenceStatus == 'Approved')
			{
				$ApprovedURL = getCharacterThumbnail($CharacterID);
				if($ApprovedURL != $ReferenceURL)
				{
					print("<input type='button' value='Set as Thumbnail' id='setReferenceasThumbnailButton$CharacterID' onclick=\"setReferenceAsThumbnail('$ReferenceURL','$CharacterID','$activeRef');\">");
					print("<input type='button' style='display: none' value='Remove as Thumbnail' id='clearCharacterThumbnailButton$CharacterID' onclick=\"clearCharacterThumbnail('$CharacterID','$activeRef');\">");
				}
				else
				{
					print("<input type='button' style='display: none' value='Set as Thumbnail' id='setReferenceasThumbnailButton$CharacterID' onclick=\"setReferenceAsThumbnail('$ReferenceURL','$CharacterID','$activeRef');\">");
					print("<input type='button' value='Remove as Thumbnail' id='clearCharacterThumbnailButton$CharacterID' onclick=\"clearCharacterThumbnail('$CharacterID','$activeRef');\">");
				}
				print("<script>");
				print("$('#setReferenceasThumbnailButton$CharacterID').click		
				(
					function()
					{
						$('#setReferenceasThumbnailButton$CharacterID').hide();
						$('#clearCharacterThumbnailButton$CharacterID').show();
					});	");	
				print("$('#clearCharacterThumbnailButton$CharacterID').click		
				(
				function()
				{
					$('#setReferenceasThumbnailButton$CharacterID').show();
					$('#clearCharacterThumbnailButton$CharacterID').hide();
				});	");	
				print("</script>");
			}
			print("<input type='button' value='Delete' onclick=\"deleteReference('$ReferenceURL','$CharacterID','$activeRef')\">");
			print("<div id='character".$CharacterID."Reference".$activeRef."MSG' class='errMSG'></div>");
			print("</div>");
			print("</div>");
			//Show reference and give the option to remove it.
			$activeRef++;
		}
	}
	else
	{
		print("No references for this character have been uploaded, yet.");
		buildMAXAddReference($Alias, $CharacterID);
	}
	print("</div>");
	print("</div>"); //End Current Character Internal
	print("</div>");//End Current Character
}


function buildMAXAddReference($Alias, $CharacterID)
{
	print("<div class='addReferenceDIV'>");
	print("<input type='button' id='uploadReference$CharacterID' class='uploadReferenceButton' value='Upload Reference'>");
	print("<div id='uploadReferenceInternal$CharacterID' class='Internal'>");
	print("<input type='button' id='uploadFromLocalClickable$CharacterID' value='Upload from machine' class='uploadFromMachineButton'>");
	print("<input type='button' id='uploadFromWebClickable$CharacterID' value='Upload from the web' class='uploadFromWebButton'>");
	print("<div id='uploadFromLocalInternal$CharacterID' class='Internal'>");
	print("File Location: <input type='file' id='uploadNewReferenceFromLocalFile$CharacterID' class='addFileText'><br>");
	print("<input type='button' id='uploadNewReferenceLocal$CharacterID' value='Upload' onclick=\"uploadNewReferenceFromLocal('$Alias','$CharacterID');\">");
	print("</div>");
	print("<div id='uploadFromWebInternal$CharacterID' class='Internal'>");
	print("URL: <input type='url' id='uploadNewReferenceFromWebURL$CharacterID'><br>");
	print("<input type='button' id='uploadNewReferenceWeb$CharacterID' value='Upload' onclick=\"uploadNewReferenceFromWeb('$Alias','$CharacterID');\">");
	print("</div>");
	print("<div id='uploadReferenceMSG$CharacterID'></div>");
	print("</div>");
	print("<script>");
	print("$('#uploadReference$CharacterID').click
	(
	function()
	{
		$('#uploadReferenceInternal$CharacterID').slideToggle();
	});");
	print("$('#uploadFromLocalClickable$CharacterID').click
	(
	function()
	{
		$('#uploadFromLocalInternal$CharacterID').show();
		$('#uploadFromWebInternal$CharacterID').hide();
		
	});");
	print("$('#uploadFromWebClickable$CharacterID').click
	(
	function()
	{
		$('#uploadFromLocalInternal$CharacterID').hide();
		$('#uploadFromWebInternal$CharacterID').show();
		
	});");
	print("</script>");
	print("</div>");
}

function buildManagePendingReferences($Alias)
{
	print("<div id='pendingReferencesWrap'>");
	print("<input type='button' value='Pending References' id='PendingReferences'>");
	print("<div id='PendingReferencesInternal' class='Internal'>");
	$PendingReferencesCount = countReferenceCountByStatus('Pending');
	if($PendingReferencesCount > 0)
	{
		$ReferenceList = getAllPendingReferences();
		buildManageReferencesPanel($Alias,$ReferenceList,'Pending');
	}
	else
	{
		print("There are no references waiting for approval");
	}
	print("</div>");
	print("</div>"); // end pendingReferencesWrap
	print("<script>");
	print("$('#PendingReferences').click
	(
	function()
	{
		$('#PendingReferencesInternal').slideToggle();
	});");
	print("</script>");
}

function buildManageApprovedReferences($Alias)
{
	print("<div id='approvedReferencesWrap'>");
	print("<input type='button' value='Approved References' id='ApprovedReferences'>");
	print("<div id='ApprovedReferencesInternal' class='Internal'>");
	$ApprovedReferencesCount = countReferenceCountByStatus('Approved');
	if($ApprovedReferencesCount > 0)
	{
		$ReferenceList = getAllApprovedReferences();
		buildManageReferencesPanel($Alias,$ReferenceList,'Approved');
	}
	else
	{
		print("No references have been approved, yet");
	}
	print("</div>"); //end Internal
	print("</div>"); // end approvedReferencesWrap
	print("<script>");
	print("$('#ApprovedReferences').click
	(
	function()
	{
		$('#ApprovedReferencesInternal').slideToggle();
	});");
	print("</script>");
}

function buildManageReferencesPanel($Alias,$ReferenceList,$PanelType)
{
	print("<div id='ReferencePanel'>");
	foreach($ReferenceList->getRecords() as $Reference)
	{
		$UserAlias = $Reference->value("Alias");
		$Name = $Reference->value("CharacterName");
		$URLs = $Reference->value("URLs");
		$CharacterID = $Reference->value("CharacterID");
		if($PanelType == 'Pending')
		{
			if(checkIfReferencesAlreadyRequestedForCharacter($CharacterID,"Requested"))
			{
				print("<div class='referenceRequestBlock'>There has been a request for additional references for this character.<br>");
				print("Reason for request:<br>");
				$RequestList = getAllSentRequestsForReferencesForCharacterByID($CharacterID);
				foreach($RequestList->getrecords() as $Request)
				{
					$Reason =  $Request->value("Reason");
					$Requester = $Request->value("Requester");
					print("<div class='reasonBlock'>");
					print("Requested By: $Requester<br>");
					print("Reason given: <br>$Reason");
					print("</div>");
				}
				print("</div>");
				print("If none of the references uploaded meet the request, please contact $UserAlias and ask for a new reference to be uploaded that matches the request.");
			}
		}
		print("<div id='referencesFor$CharacterID'>");
		print("<div id='characterHeader'><h3>$Name</h3> ($UserAlias)</div>");
		$activeRef = 0;
		foreach($URLs as $URL)
		{
			print("<div id='character".$CharacterID."ReferencePreviewForAdmin".$activeRef."' class='adminPendingPreview'>");
			print("<a href='$URL' target='_blank'><img src='$URL' class='previewMAXEntry'/></a>");
			print("<div id='adminPendingButtons'>");
			if($PanelType == 'Pending')
			{
				print("<input type='button' class='approveCharacterButton' value='Approve' onclick=\"approveReference('$CharacterID','$URL','$Alias','$activeRef');\">");
			}
			print("<input type='button' class='rejectCharacterButton' value='Reject' onclick=\"rejectReference('$CharacterID','$URL','$Alias','$activeRef');\">");
			print("</div>");
			print("</div>");
			$activeRef++;
		}
		print("</div>");
	}
	print("</div>");
}

function buildManageBlacklist($Alias)
{
	print("<div id='manageBlacklist'>");
	$BlackListCount = getCountOfActivelyBlacklistedMembers();
	if($BlackListCount > 0)
	{
		$activeCount = 0;
		$Blacklist = getActivelyBlacklistedMembers();
		foreach($Blacklist->getRecords() as $User)
		{
			$UserAlias = $User->value("Alias");
			$Reason = $User->value("Reason");
			$DateBlacklisted = $User->value("DateCreated");
			$DateBlacklistedForHuman = date('F jS, Y', $DateBlacklisted/1000);
			
			print("<div id='blacklistRecord".$DateBlacklisted."$activeCount' class='blacklistRecord'>");
			print("<div class='userAlias'>User: $UserAlias</div>");
			print("<div class='blacklistDate'>Date Blacklisted: $DateBlacklistedForHuman</div>");
			print("<div class='reasonForBlacklist'>Reason for blacklist:</div>");
			print("<div class='blacklistReasonDIV'>$Reason</div>");
			print("<input type='button' id='resolveBlacklist".$DateBlacklisted."$activeCount' value='Resolve'>");
			print("<div id='resolveBlacklistDetails".$DateBlacklisted.$activeCount."Internal' class='Internal'>");
			print("<div class='blacklistReasonDIV'>");
			print("<textarea id='resolveBlacklistDetails".$DateBlacklisted."".$activeCount."Text'></textarea>");
			print("</div>");
			print("<div class='blacklistButtons".$DateBlacklisted."$activeCount'>");
			print("<input type='button' value='Save' id='liftBlacklist".$DateBlacklisted."$activeCount' onclick=\"liftBlacklist('$UserAlias','$Alias','$DateBlacklisted','$activeCount');\">");
			print("<div id='liftBlacklist".$DateBlacklisted."".$activeCount."MSG' class='ERRMSG'></div>");
			print("</div>");// end blacklistbuttons
			print("</div>"); // end resolveBlacklistDetailsInternal
			print("</div>");
			print("<script>");
			print("$('#resolveBlacklist".$DateBlacklisted."$activeCount').click
			(
				function()
			{
				$('#resolveBlacklistDetails".$DateBlacklisted.$activeCount."Internal').slideToggle();
			});");
			print("</script>");
			$activeCount++;
		}
	}
	else
	{
		print("No one is currently on the blacklist");
	}
	print("</div>"); // end Manage Blacklist
}


function buildMAXParticipantProfile($Alias)
{
	$ProfilePic = getUserProfilePicForMAX($Alias);
	print("<div id='MAXParticipantHeader'>");
	print("<h2>$Alias</h2>");
	print("<img src='$ProfilePic' />");
	print("</div>");
}

function buildMAXCharacterProfilWithReferences($CharacterID)
{
	$CharacterName = getCharacterNameByID($CharacterID);
	$CharacterBio = getCharacterBio($CharacterID);
	print("<div id='characterBio'>");
	print("<h2>$CharacterName</h2>");
	$ThumbnailURL = getCharacterThumbnail($CharacterID);
	if($ThumbnailURL != '')
	{
		print("<a href='$ThumbnailURL' target='_blank'><img src='$ThumbnailURL' class='previewMAXEntry' /></a>");
	}
	$Age = $CharacterBio->value("Age");
	$Eyes = $CharacterBio->value("Eyes");
	$Gender = $CharacterBio->value("Gender");
	$Hair = $CharacterBio->value("Hair");
	$Height = $CharacterBio->value("Height");
	$Race = $CharacterBio->value("Race");
	$Weight = $CharacterBio->value("Weight");
	$WriteUp = $CharacterBio->value("WriteUp");
	$Webcomic = getCharacterWebcomic($CharacterID);
	
	print("<div class='characterDetailsWrap'>");
	print("<h3>Character Details:</h3>");
	print("<div class='characterDetails'><strong>Race/Species:</strong> $Race</div>");
	print("<div class='characterDetails'><strong>Age:</strong> $Age</div>");
	print("<div class='characterDetails'><strong>Hair:</strong> $Hair</div>");
	print("<div class='characterDetails'><strong>Eye Colour:</strong> $Eyes</div>");
	print("<div class='characterDetails'><strong>Height:</strong> $Height</div>");
	print("<div class='characterDetails'><strong>Weight:</strong> $Weight</div>");
	$OwnerAlias = getCharacterOwnerAliasByID($CharacterID);
	print("<div id='Chracter".$CharacterID."Owner'><strong>Owned by: </strong><a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$OwnerAlias&Fields=Users'>$OwnerAlias</a></div>");
	$WebcomicID = $Webcomic->value("ComicID");
	if($WebcomicID != '')
	{
		$WebcomicURL = $Webcomic->value("URL");
		$WebcomicName = $Webcomic->value("Name");
		print("<div class='characterDetails'><strong>Webcomic:</strong> <a href='$WebcomicURL' target='_blank'>$WebcomicName</a></div>");
	}
	else
	{
		print("<div class='characterDetails'><strong>Webcomic:</strong> None</div>");
	}
	print("<div class='characterBioDiv'><strong>Bio:</strong> <br><div class='characterBio'>$WriteUp</div></div>");
	print("</div>");
	print("<div id='characterReferencesWrap'>");
	print("<h3>References</h3>");
	if(getCountOfCharacterReferencesByID($CharacterID) > 0)
	{
		$ReferenceList = getReferencesFromCharacterID($CharacterID);
		foreach($ReferenceList->getRecords() as $Reference)
		{
			$ReferenceURL = $Reference->value("URL");
			print("<div class='referencePreview'><a href='$ReferenceURL' target='_blank'><img src='$ReferenceURL' class='previewMAXEntry' /></a></div>");
		}
	}
	else
	{
		print("This character has no references, yet");
	}
	print("</div>");
	print("<h3>Other art</h3>");
	print("<div id='otherChracterArt'>");
	if(checkIfCharacterHasAnySubmittedArt($CharacterID))
	{
		$CharacterArtList = getAllSubmittedCharacterArt($CharacterID);
		foreach($CharacterArtList->getRecords() as $Character)
		{
			$EntryID = $Character->value("OrphanID");
			if($EntryID == '')
			{
				$EntryID = $Character->value("DateCreated");
			}		
			$URL = $Character->value("URL");
			$Sender = $Character->value("Sender");
			$Comments = $Character->value("Comments");
			$DateCreated = $Character->value("DateCreated");
			print("<div id='characterArt$DateCreated' class='characterAdditionalArt'>");
			print("<a href='https://www.comicadia.com/MAX/index.php?EntryID=$EntryID' target='_blank'><img src='$URL' class='previewMAXEntry' /></a>");
			print("<div id='imgFooter'>By: <a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$Sender"."&Fields=Users' target='_blank'>$Sender</a><br>");
			if($Comments != '')
			{
				print("Comments: <br>");
				print("<div class='submittedComments'>$Comments</div>");
			}
			print("</div>");
			print("</div>");
		}
	}
	else
	{
		print("There are no other artworks received for this character");
	}
	print("</div>");// end otherCharacterArt
	print("</div>");
}

function buildMAXParticipantHistory($Alias)
{
	print("<div id='MaxParticipantRounds'>");
	print("<h3>Previous Entries</h3>");
	if(getCountOfMAXRoundsUserParticipatedIn($Alias) > 0)
	{
		$UserParticipatedRounds = getMAXRoundsUserParticipatedIn($Alias);
		foreach($UserParticipatedRounds->getRecords() as $Round)
		{
			$EntryID = $Round->value("EntryID");
			$Receiver = $Round->value("Receiver");
			if(checkIfReceiverSubmittedTheirEntryForMAXRoundBasedOnEntryID($EntryID, $Receiver))
			{
				$imgURL = $Round->value("URL");	
				$CharacterID = $Round->value("CharacterID");
				$MaxStartDate = $Round->value("MAXStartDate");
				$MAXEndDate = $Round->value("MAXEndDate");
				$MaxStartDate = date('F jS, Y', $MaxStartDate/1000);
				$MAXEndDate = date('F jS, Y', $MAXEndDate/1000);
				$CharName = getCharacterNameByID($CharacterID);
				print("<div class='previousRoundPreview'>");
				print("<a href='$imgURL' target='_blank'><img src='$imgURL' class='previewMAXEntry'/></a>");
				print("<div id='MAXRoundFooter'><h4>$MaxStartDate - $MAXEndDate<br>$CharName</h4></div>");
				print("</div>"); // end PreviousRoundPreview
			}
			else
				print("$Receiver must submit their MAX round for this image to be unlocked.");
		}
	}
	else
	{
		print("This user has not participated in any MAX rounds, yet");
	}
	print("</div>");
}

function buildParticipantBlacklistRecords($Alias)
{
	$BlacklistRecords = getUserBlacklistRecords($Alias);
		foreach($BlacklistRecords->getRecords() as $record)
		{
			$Date = $record->value("DateCreated");
			$Date = date('F jS, Y', $Date/1000);
			$Reason = $record->value("Reason");
			$Blacklister = $record->value("Blacklister");
			print("<div class='blacklistRecord'>");
			print("<div class=blacklistDate'>Date Blacklisted: $Date</div>");
			print("<div class='blacklistedBy'>Blacklisted By: $Blacklister</div>");
			print("<div id='blacklistReason' class='blacklistReason'>$Reason</div>");
			print("</div>");
			
			if($Blacklister == 'System')
			{
				print("This blacklist entry was generated automatically due to a missed submission.");
			}
			print("</div>");
		}
}

function buildMAXBlacklistParticipant($MemberAlias,$AdminAlias)
{
	print("<div id='blacklistOptions'>");
	if(isUserBlacklisted($MemberAlias))
	{
		print("User is currently blacklisted.");
		buildParticipantBlacklistRecords($Alias);
	}
	else
	{
		print("User is currently not on the blacklist.");
	}
	print("<div id='addBlacklistToUser'>");
	print("<input type='button' id='addUserToBlacklist' value='Add User to MAX Blacklist'>");
	print("<div id='blacklistDetailsInternal'>");
	print("Reason for Blacklisting:<br>");
	print("<div id='addBlacklistReasonWrap'>");
	print("<textarea id='blacklistReasonText'></textarea>");
	print("</div>");
	print("<input type='button' value='Save' onclick=\"addUserToBlacklist('$MemberAlias','$AdminAlias');\">");
	print("<div id='addUserToBlackListMSG' class='ERRMSG'></div>");
	print("</div>");//end BlacklistInternal
	print("</div>");//end addblacklistToUser
	print("</div>");
	print("<script>");
	print("$('#addUserToBlacklist').click
	(
	function()
	{
		$('#blacklistDetailsInternal').slideToggle();
	});");
	print("</script>");
}

function buildMAXParticpantsSearch()
{
	print("<div id='searchMAXMembers'>");
	print("<span class='searchBar'>Alias contains: <input type='text' id='searchMAXMembersText'> <input type='button' id='searchMembersButton' value='Search' onclick='searchMAXMembers();'></span>");
	print("<div id='searchMSG' class='ErrMSG'></div>");
	print("</div>");
}

function buildMAXCharacterSearch()
{
	
	print("<div id='searchMAXCharacters'>");
	print("<h2>Character Search</h2>");
	print("<span class='searchBar'>Name contains: <input type='text' id='searchMAXCharactersText'> <input type='button' id='searchCharactersButton' value='Search' onclick='searchMAXCharacters();'></span>");
	print("<div id='searchMSG' class='ErrMSG'></div>");
	print("</div>");
}

function buildMAXCharacterListWithPagination($Search,$pageNumber, $CharacterID, $articlesPerPage)
{
	if($Search != '')
	{
		$totalArticles = getMAXCharacterCountByKeyword($Search);
		$totalPages = ceil($totalArticles / $articlesPerPage);
	}
	else
	{
	$totalArticles = getMAXCharacterCount();
	$totalPages = ceil($totalArticles / $articlesPerPage);
	}
	
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	
	if($CharacterID !='')
	{
		buildMAXCharacterProfilWithReferences($CharacterID);
	}
	
	
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	$CharacterList = getMAXCharacterListAndReferencesByPagination($Search,$startArticle, $articlesPerPage);
	buildMAXCharacterSearch();
	buildMAXCharactersPanelFromList($CharacterList);
	buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Characters');
}

function buildMAXParticpantListFromSearchAsPerPaginationByViewer($Search, $startArticle, $articlesPerPage,$ViewerType)
{
	$MemberList = getMAXMembersListFromSearchByPagination($Search,$startArticle,$articlesPerPage,$ViewerType);
	buildMAXMemberPanelFromList($MemberList,$ViewerType);
}

function buildMAXParticpantListAsPerPaginationByViewer($startArticle, $articlesPerPage,$ViewerType)
{
	$MemberList = getMAXMembersListByPagination($startArticle,$articlesPerPage,$ViewerType);
	buildMAXMemberPanelFromList($MemberList, $ViewerType);
}

function buildMAXMemberPanelFromList($MemberList, $ViewerType)
{
	if($ViewerType == 'Admin')
	{
		$Linkme = "admin.php";
	}
	else
	{
		$Linkme = "index.php";
	}
	print("<div id='searchResults' class='membersPagination'>");
	foreach($MemberList->getRecords() as $Member)
	{
	
		$MemberAlias = $Member->value("Alias");
		$MemberType = $Member->value("UserType");
		$MemberProfile = $Member->value("ProfilePic");
		$Rounds = getCountOfMAXRoundsUserParticipatedIn($MemberAlias);
		print("<div class='membersSearchResult $MemberType'><a href=\"https://www.comicadia.com/MAX/".$Linkme."?MemberAlias=$MemberAlias"."&Fields=Users\">");
		if($MemberProfile != "")
		{
			print("<img src='$MemberProfile' />");
		}
		else
		{
			print("<img src='https://www.comicadia.com/media/user.png' />");
		}
		print("$MemberAlias</a>");
		print("<div class='RoundsParticipatedIn'>Rounds completed: $Rounds</div>");
		print("<div class='clear'></div></div>");
			
	}
	print("</div>");
}


/**************************************

This is where the changes must be made for the cards to be changed.
This will build the character panels ONLY for the search results
We will streamline the function to be utilized by both the character list
and other pages. For now, let's see if we can get the character list 
porperly configured.

**************************************/

function buildMAXCharactersPanelFromList($CharacterList)
{
	print("<h2>Characters</h2>");
	foreach($CharacterList->getRecords() as $Character)
	{
		
		$Name = $Character->value("Name");
		$CharacterID = $Character->value("CharacterID");
		$RefList = $Character->value("RefIDs");
		print("<div class='charactercard col-xs-6 col-sm-4'>");
		print("<div class='card'>");
		print("<a class='pic' href='https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID"."&Fields=Characters'>");
		$PreferredThumbnail = getCharacterThumbnail($CharacterID);
		if($PreferredThumbnail != '')
		{
			print("<img class='card-img-top' src='$PreferredThumbnail' />");
		}
		else
		{
			print("<img src='https://www.comicadia.com/media/user.png' />");
		}
		print("<div class='card-body text-center'");
		print("<div class='card-title'>$Name");
		print("</a><br><a class='btn btn-info' href='https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID"."&Fields=Characters'>References</a></div><!-- end card-title classed div -->");
		print("<div class='card-buttons'>");
		print("</div> <!-- end card-buttons classed div -->");
		
		print("</div <!-- end card-body text-center classed div -->");
		
		print("<div id='character$CharacterID"."Internal' class='Internal'>");
		foreach($RefList as $ReferenceID)
		{
			$RefURL = getReferenceURLFromID($ReferenceID);
			print("<div class='previewMAXCharacter'><a href='$RefURL' target='_blank'><img src='$RefURL' class='previewMAXEntry'/></a></div>");
		}
		print("</div> <!-- end card classed div -->");
		print("</div> <!-- end charactercard classed div -->"); 
	}
}


function buildMAXRecipientForUser($Alias, $MAXID)
{
	print("<div id='MAXRecipient'>");
	print("<div id='MAxRecipientHeader'><h3>MAX Round match-up</h3></div>");
	$Recipient = getMAXRecipientForUserForMAXRound($MAXID, $Alias);
	$RecipientAlias = $Recipient->value("Alias");
	$RecipientProfile = $Recipient->value("ProfilePic");
	$RecipientEmail = $Recipient->value("Email");
	print("<div id='RecipientProfile'>");
	print("<div class='profilePic'>");
	if($RecipientProfile != '')
	{
		print("<img src='$RecipientProfile' />");
	}
	else
	{
		print("<img src='https://www.comicadia.com/media/user.png' />");
	}
	print("</div>"); //end profilePic
	print("<h4>$RecipientAlias</h4>");
	print("</div>");//End RecipientProfile
	buildMAXCharactersOfRecipient($RecipientAlias);
	
	print("</div>");// End MAXRecipient
}

function buildMAXUploadSubmission($Alias,$MAXID)
{
	print("<h3>Your Entry</h3>");
	if(checkIfUserHasSubmittedArtForRound($Alias, $MAXID))
	{
		$EntryURL = getEntryURLFromUserForRound($Alias, $MAXID);
		print("<div id='EntryPreviewWrap'>");
		print("<div id='EntryPreview'><a href='$EntryURL' target='_blank'><img src='$EntryURL' id='EntryPreviewIMG'/></a></div>");
		print("</div>");
		print("<strong>Current Entry</strong>");
		print("<br>");
		print("<input type='button' id='submitYourEntryClickable' value='Submit a new entry'> ");
	}
	else
	{
		print("<div id='EntryPreviewWrap'>");
		print("<div id='EntryPreview'>None, yet</div>");
		print("</div>");
		print("<strong>Current Preview</strong>");
		print("<br>");
		print("<input type='button' id='submitYourEntryClickable' value='Submit your entry'> ");
	}
	print("<div id='submitEntryInternal' class='Internal'>");
	$Recipient = getMAXRecipientForUserForMAXRound($MAXID, $Alias);
	$RecipientAlias = $Recipient->value("Alias");
	print("<div id=submitEntryForMAX'>");
	print("<h3>Submit your entry</h3>");
	print("Character: ");
	print("<select id='submitEntryCharacterSelect'>");
	$PossibleCharacterList = getCharactersOfUser($RecipientAlias);
	foreach($PossibleCharacterList->getRecords() as $Character)
	{
		$CharacterID = $Character->value("CharacterID");
		$CharacterName = $Character->Value("Name");
		$Selected = '';
		if(isCharacterCurrentlyPreferred($CharacterID))
		{
			$Selected = 'Selected';
		}
		
		print("<option value='$CharacterID'>$CharacterName</option>");
	}
	print("</select><br>");
	print("<div id='submitEntryComments'>Comment:<br><textarea id='submitEntryCommentText' class='entryCommentText'></textarea></div>");
	print("<div id='submitEntryFromLocal'>");
	print("File Location: <input type='file' id='submitEntryFromLocalFile'>");
	print("<br>");
	print("<input type='button' value='Upload' id='uploadFromLocalButton' onclick=\"uploadEntryFromLocal('$Alias', '$RecipientAlias', '$MAXID');\"> ");
	print("</div>"); //end submitEntryFromLocalInternal
	print("<div id='uploadEntryMSG' class='ERRMSG'></div>");
	print("</div>");//end submitEntryInternal
	print("</div>");//end submitEntryForMAX
	
	print("<script>");
	print("$('#submitYourEntryClickable').click
	(
	function()
	{
		$('#submitEntryInternal').slideToggle();
	});");
	print("</script>");
}

function buildMAXUploadLateSubmission($Alias,$MAXID)
{
	print("<input type='button' id='submitYourEntryClickable$MAXID' value='Submit your entry'> ");
	print("<div id='submitEntryInternal$MAXID' class='Internal'>");
	$Recipient = getMAXRecipientForUserForMAXRound($MAXID, $Alias);
	$RecipientAlias = $Recipient->value("Alias");
	print("<div id=submitEntryForMAX$MAXID'>");
	print("<h3>Submit your entry</h3>");
	print("Character: ");
	print("<select id='submitEntryCharacterSelect$MAXID'>");
	$PossibleCharacterList = getCharactersOfUser($RecipientAlias);
	foreach($PossibleCharacterList->getRecords() as $Character)
	{
		$CharacterID = $Character->value("CharacterID");
		$CharacterName = $Character->Value("Name");
		$Selected = '';
		if(isCharacterCurrentlyPreferred($CharacterID))
		{
			$Selected = 'Selected';
		}
		
		print("<option value='$CharacterID'>$CharacterName</option>");
	}
	print("</select><input type='button' onclick=\"viewCharacterEntry('$MAXID');\" value='View Character'><br>");
	print("<div id='submitEntryComments$MAXID'>Comment:<br><textarea id='submitEntryCommentText$MAXID' class='entryCommentText'></textarea></div>");
	print("<div id='submitEntryFromLocal$MAXID'>");
	print("File Location: <input type='file' id='submitEntryFromLocalFile$MAXID'>");
	print("<br>");
	print("<input type='button' value='Upload' id='uploadFromLocalButton$MAXID' onclick=\"uploadLateEntryFromLocal('$Alias', '$RecipientAlias', '$MAXID');\"> ");
	print("</div>"); //end submitEntryFromLocalInternal
	print("<div id='uploadEntryMSG$MAXID' class='ERRMSG'></div>");
	print("</div>");//end submitEntryInternal
	print("</div>");//end submitEntryForMAX
	
	print("<script>");
	print("$('#submitYourEntryClickable$MAXID').click
	(
	function()
	{
		$('#submitEntryInternal$MAXID').slideToggle();
	});");
	print("</script>");
}

function buildReviewMAXEntry($Alias, $MAXID)
{
	$ReceivedEntry = getUserReceivedArtForRound($Alias, $MAXID);
	$EntryURL = $ReceivedEntry->value("URL");
	$EntryArtist = $ReceivedEntry->value("Alias");
	$EntryID = $ReceivedEntry->value("EntryID");
	$Comments = $ReceivedEntry->value("Comments");
	$CharacterID = $ReceivedEntry->value("CharacterID");
	$CharName = getCharacterName($CharacterID);
	
	print("<div id='receivedEntryWrap$EntryID'>");
	print("<div id='receivedEntry$EntryID'>");
	print("<div class='receivedEntryHeader'><h4>Your reward:</h4></div>");
	print("<div id='entryReceivedPreview$EntryID'>");
	print("<a href='$EntryURL' target='_blank'><img src='$EntryURL' /></a> <br> $CharName by $EntryArtist");
	print("</div>"); // end entryReceivedPreview
	print("<div class='comments'>Comments: <br><div id='entrycomments' class='entryComments'>$Comments </div></div>");
	print("</div>");//end receivedEntry
	if(hasUserAlreadyReportedEntry($EntryID, $Alias))
	{
		print("<div id='entryReported'>This entry has been reported.</div>");
	}
	else
	{
		print("<div class='receivedEntryActions'><input type='button' id='reportEntryClickable$EntryID' value='Report'></div>");
		print("<div id='reportEntryInternal$EntryID' class='Internal'>");
		print("<div class='reportEntryTextDIV'><textarea id='reportEntryText$EntryID'></textarea></div>");
		print("<div class='reportEntryButtonsDIV'><input type='button' class='reportEntryButton' value='Report' onclick=\"reportMAXEntry('$Alias','$EntryID');\"></div>");
		print("<div id='reportEntryMSG$EntryID' class='ERRMSG'></div>");
		print("</div>"); //end reportEntryInternal
	}
	print("</div>");//end receivedEntryWrap
	print("<script>");
	print("$('#reportEntryClickable$EntryID').click
	(
		function()
		{
			$('#reportEntryInternal$EntryID').slideToggle();
	});");
	print("</script>");
}


function buildManageReportedEntries($Alias)
{
	print("<h3>Reported Entries</h3>");
	if(getCountOfAllReportedEntries() > 0)
	{
		$ReportedList = getAllActiveReportedEntries();
		foreach($ReportedList->getRecords() as $Report)
		{
			$EntryID = $Report->value("EntryID");
			$ReportDate = $Report->value("ReportedOn");
			$Reason = $Report->value("Reason");
			$Sender = $Report->value("Sender");
			$Receiver = $Report->value("Receiver");
			$URL = $Report->value("URL");
			$Comments = $Report->value("Comments");
			$ReportDateHuman = date('F jS, Y @ H:i', $ReportDate/1000);
			
			print("<div id='reportedEntry$EntryID'>");
			print("<div id='entryPreview$EntryID' class='entryPreview'>");
			print("<a href='$URL' target='_blank'><img src='$URL' /></a>");
			print("</div>");
			print("<div class='reportSubmittedBy'>Artist: $Sender</div>");
			print("<div class='reportBy'>Reported by: $Receiver on $ReportDateHuman</div>");
			print("<div class='reportedComments'>Comments From Artist: <br>$Comments</div>");
			print("<div class='reportReason'>Reason given: <br>$Reason</div>");
			print("<div class='reportAdminInput'>Decision text: <br><textarea id='reportAdminInputText$EntryID' class='adminInputText'></textarea></div>");
			print("</div>");
			print("<div class='reportAdminButtons'><input type='button' id='blacklistUser$EntryID' value='Validate complaint' onclick=\"addUserToBlacklistViaEntryID('$Sender','$Alias','$EntryID','$ReportDate');\">");
			print("<input type='button' id='markReportResolved$EntryID' value='Mark as resolved' onclick=\"markReportAsResolved('$EntryID','$Alias','$Receiver','$ReportDate');\">");
			print("<div id='reportMSG$EntryID' class='ERRMSG'></div>");
			print("</div>");
			print("</div>");
		}
	}
	else
	{
		print("No Entries have been reported.");
	}
}

function buildManageLateEntries($Alias)
{
	$countOfLateEntries = getCountOfAllPendingLateEntries();
	print("<h3>Late Entries</h3>");
	if($countOfLateEntries >0)
	{
		$LateList = getAllPendingLateEntries();
		foreach($LateList->getRecords() as $Report)
		{
			$EntryID = $Report->value("EntryID");
			$Sender = $Report->value("Sender");
			$Receiver = $Report->value("Receiver");
			$URL = $Report->value("URL");
			$Comments = $Report->value("Comments");
			
			print("<div id='lateEntry$EntryID'>");
			print("<div id='entryPreview$EntryID' class='entryPreview'>");
			print("<a href='$URL' target='_blank'><img src='$URL' /></a>");
			print("</div>");
			print("<div class='lateSubmittedBy'>Artist: $Sender</div>");
			print("<div class='lateComments'>Comments From Artist: <br>$Comments</div>");
			print("<div class='lateAdminInput'>Decision text: <br><textarea id='lateEntryAdminInputText$EntryID' class='adminInputText'></textarea></div>");
			print("</div>");
			print("<div class='lateAdminButtons'><input type='button' id='rejectLatEntry$EntryID' value='Reject Late Entry' onclick=\"rejectLateEntry('$Alias','$EntryID');\">");
			print("<input type='button' id='acceptLateEntry$EntryID' value='Accept Late Entry' onclick=\"acceptLateEntry('$EntryID','$Alias');\">");
			print("<div id='lateEntry$EntryID"."MSG' class='ERRMSG'></div>");
			print("</div>");//end lateAdminButtons
			print("</div>");
		}
	}
	else
	{
		print("No late entries at this time.");
	}
}
function buildManageAllMAXEntries($Alias, $pageNumber, $articlesPerPage)
{
	$totalArticles = getCountOfCompletedMAXRounds();
	
	$totalPages = ceil($totalArticles / $articlesPerPage);
	
	if($pageNumber < 1)
	{
		$pageNumber = 1;
		// Check that the page is below the last page
	}
	else if($pageNumber > $totalPages)
	{
		$pageNumber = $totalPages;
	}
	$startArticle = ($pageNumber - 1) * $articlesPerPage;
	
	print("<h3>All entries by Round</h3>");
	print("<input type='button' id='allMAXRoundsClickable' value='View'>");
	print("<div id='allMAXRoundsInternal'>");
	$MAXList = getAllCompletedMAXRoundsAndTheirEntriesByPagination($startArticle, $articlesPerPage);
	buildPreviousMAXRoundsPanel($MAXList);
	buildMAXPagination($pageNumber, $totalPages, $totalArticles, 'Entries');
	print("</div>");//end allMAXRoundsInternal
	print("<script>");
	print("$('#allMAXRoundsClickable').click
	(
		function()
		{
			$('#allMAXRoundsInternal').slideToggle();
	});");
	print("</script>");
}

function buildMAXSidebar($Alias)
{
	
	print("<div id='Sidebar'>");
	print("<center>");
	print("<div id='SideBarHeader'>");
	print("</div>");
	print("</code>");
	buildMAXWelcomeControlPanel($Alias);
	//Sidebar tower ad
	print("<div id='googleTower' class='adTower'>");
	loadGoogleTower();
	print("	</div>");
	print("<div class='clear'>");
	print("</div>");
	print("</div>");
}


function buildUserCurrentAdoptions($Alias)
{
	$Orphan = getActiveAdoptionForUser($Alias);
	
	$URL = $Orphan->value("URL");
	$EntryID = $Orphan->value("EntryID");
	$AdoptionID = $Orphan->value("AdoptionID");
	$OrphanID = $Orphan->value("OrphanID");
	$Receiver = $Orphan->value("Receiver");
	$ReceiverProfile = getUserProfilePicForMAX($Receiver);
	$CharacterID = $Orphan->value("CharacterID");
	$Status = $Orphan->value("Status");
	print("<h2>Your current Adoption</h2>");
	print("<div id='adoptionEntry$EntryID'>");
	if($ReceiverProfile != '')
	{
		print("<div class='profilePic'><img src='$ReceiverProfile' /></div>");
	}
	else
	{
		print("<div class='profilePic'><img src='https://www.comicadia.com/media/user.png' /></div>");
	}
	print("<div class='adoptionDetail'>Receiver: <a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$Receiver"."&Fields='Users'' target='_blank'>$Receiver</a></div>");
	if($CharacterID !='')
	{
		$CharacterName = getCharacterNameByID($CharacterID);
		print("<div class='adoptionDetail'>Character Requested: <a href='https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID"."&Fields='Characters' target='_blank'>$CharacterName</a></div>");
	}
	else
	{
		print("<div class='adoptionDetail'>Character Requested: None</div>");
	}
	if($URL != '')
	{
		print("<div class='MAXEntrytWrap'><div class='previewAdoptionEntry'><img src='$URL' class='previewMAXEntry' /></div><div class='MAXEntryDetails'><strong>Your entry</strong></div></div>");
	}
	if($Status == 'Claimed')
	{
		print("<h3>Submit Entry</h3>");
		print("<div class='characterSelect'>");
		print("Character Drawn: <br><select id='adoptionCharacterSelect$EntryID'>");
		$CharacterList = getCharactersOfUser($Receiver);
		foreach($CharacterList->getRecords() as $Character)
		{
			$Selected = '';
			$CharacterID = $Character->value("CharacterID");
			$CharacterName = $Character->value("Name");
			
			if(isCharacterCurrentlyPreferred($CharacterID))
			{
				$Selected = 'Selected';
			}
			print("<option value='$CharacterID' $Selected>$CharacterName</option>");
		}
		print("</select>");
		print("</div>");
		print("<div id='adoptionComments>Comments: <br><textarea id='adoptionCommentsText$EntryID' class='submitEntryCommentText'></textarea></div>");
		print("<div class='adoptionButtons'><input type='File' id='adoptionUploadFile$EntryID' ></div>");
		print("<input type='button' id='submitAdoptionForReview$EntryID' value='Upload' onclick=\"uploadAdoptionEntryForReview('$Alias','$AdoptionID','$EntryID');\">");
		print("<div id='uploadAdoptionEntryForReviewMSG$EntryID' class='ERRMSG'></div>");
	}
	elseif($Status == 'Submitted')
	{
		print("<div class='adoptionStatus'>You submission is under review.</div>");
	}
	elseif($Status == 'Rejected')
	{
		$RejectedEntry = getRejectedAdoptionDetails($AdoptionID);
		$RejectedBy = $RejectedEntry->value("AdminAlias");
		$Reason = $RejectedEntry->value("Reason");
		print("<div class='adoptionStatus'>You submission was rejected by $RejectedBy.</div>");
		print("<div class='adoptionRejectedReason'>Reason:<br>$Reason</div>");
	}
	else
	{
		print("What have you done!?");
	}
	print("</div>");
}

function buildAdoptionWaitingReview($Alias)
{
	print("<h3>Adoptions Under Review</h3>");
	print("<div class='adoptionsForReviewWrap'>");
	$AdoptionsForReview = getAdoptionsSubmittedForReview();
	foreach($AdoptionsForReview->getRecords() as $Orphan)
	{
		$ImgURL = $Orphan->value("URL");
		$RequestedCharacterID = $Orphan->value("PreferredCharacter");
		$SubmittedCharacterID = $Orphan->value("SubmittedCharacter");
		$Sender = $Orphan->value("Sender");
		$Receiver = $Orphan->value("Receiver");
		$DateSubmitted = $Orphan->value("DateSubmitted");
		$AdoptionID = $Orphan->value("DateCreated");
		$DateSubmitted = date('F jS, Y', $DateSubmitted/1000);
		$Comments = $Oprhan->value("Comments");
		print("<div id='reviewAdoptionEntryDetails$AdoptionID'> ");
		print("<div class='reviewAdoptionEntryDetail'>Foster Artist: $Sender</div>");
		print("<div class='reviewAdoptionEntryDetail'>Orphaned Artist: $Receiver</div>");
		if($RequestedCharacterID != '')
		{
			$RequestedCharacterName = getCharacterNameByID($RequestedCharacterID);
			print("<div class='reviewAdoptionEntryDetail'>Requested Character: $RequestedCharacterName</div>");
			$RequestedCharThumbnail = getCharacterThumbnail($RequestedCharacterID);
			if($RequestedCharThumbnail != '')
			{
				print("<div class='reviewAdoptionEntryDetail'>Thumbnail: <br><a href='$RequestedCharThumbnail' target='_blank' ><img src='$RequestedCharThumbnail' class='previewMAXEntry' /></a></div>");
			}
		}
		else
		{
			print("<div class='reviewAdoptionEntryDetail'>Requested Character: None</div>");
		}		
		print("<div class='reviewAdoptionEntryDetail'>Date Submitted: $DateSubmitted</div>");
		print("<div class='reviewAdoptionEntryDetail'>Submission:<br>");
		print("<a href='$ImgURL' target='_blank'><img src='$ImgURL' class='previewMAXEntry' /></a>");
		print("</div>");
		print("<div class='adoptionReviewButtons'><input type='button' value='Accept' id='acceptAdoptionSubmission$AdoptionID' onclick=\"acceptAdoptionSubmission('$Alias','$AdoptionID');\"><input type='button' value='Reject' id='rejectAdoptionSubmission$AdoptionID'></div>");
		print("<div id='rejectAdoptionSubmission$AdoptionID"."Internal'>");
		print("Reason: <br><textarea id='rejectAdoptionSubmissionReason$AdoptionID'></textarea><br>");
		print("<input type='button' value='Submit Rejection' id='submitRejectionOfAdoption' onclick=\"rejectSubmission('$Alias','$AdoptionID');\"> ");
		print("<div id='rejection$AdoptionID"."MSG' class='ERRMSG'></div>");
		print("</div>");//End rejectionIntenral
		print("<div id='accept$AdoptionID"."MSG' class='ERRMSG'></div>");
		print("</div>");
		
		print("<script>");
		print("$('#rejectAdoptionSubmission$AdoptionID').click
		(
			function()
			{
				$('#rejectAdoptionSubmission$AdoptionID"."Internal').slideToggle();
		});");
		print("</script>");
	}
	print("</div>");
}

function buildAllApprovedAdoptions()
{
	print("<h3>Approved Adoptions</h3>");
	print("<input type='button' value='View' id='approveAdoptionsClickable'>");
	print("<div id='approvedAdoptionsInternal' class='Internal'>");
	$ApprovedList = getAllApprovedAdoptions();
	print("<div class='approvedAdoptionsList'>");
	foreach($ApprovedList->getRecords() as $Adoption)
	{
		$ApprovedBy = $Adoption->value("ApprovedBy");
		$URL = $Adoption->value("URL");
		$DateApproved = $Adoption->value("DateApproved");
		$DateCreated = $Adoption->value("AdoptionID");
		$Alias = $Adoption->value("Alias");
		$CharacterSubmitted = $Adoption->value("SubmittedCharacter");
		$PreferredCharacter = $Adoption->value("PreferredCharacter");
		$Receiver = $Adoption->value("Receiver");
		$Comments = $Adoption->value("Comments");
		$DateApproved = date('F jS, Y', $DateApproved/1000);
		print("<div id='acceptedEntry$DateCreated'>");
		print("<div class='adoptionDetail'>By: $Alias</div>");
		print("<div class='adoptionDetail'>For: $Receiver</div>");
		$SubmittedCharacterName = getCharacterNameByID($CharacterSubmitted);
		print("<div class='adoptionDetail'>Character submitted: $SubmittedCharacterName</div>");
		print("<div class='adoptionDetail'>Date Approved: $DateApproved</div>");
		print("<div class='adoptionDetail'>Approved by: $ApprovedBy</div>");
		print("<div class='adoptionDetail'>Submission:<br><a href='$URL' target=_blank'><img src='$URL' class='previewMAXEntry' /></a></div>");
		if($Comments != '')
		{
			print("<div class='adoptionDetail'>Comments: <br>$Comments</div>");
		}
		print("</div>");
	}
	print("</div>");// End approvedAdoptionsInternal
	print("</div>");
	print("<script>");
	print("$('#approveAdoptionsClickable').click
	(
		function()
		{
			$('#approvedAdoptionsInternal').slideToggle();
	});");
	print("</script>");
}
function buildAllRejectedAdoptions()
{
	print("<h3>Reject Adoptions</h3>");
	print("<input type='button' value='View' id='rejectedAdoptionsClickable'>");
	print("<div id='rejectedAdoptionsInternal' class='Internal'>");
	$RejectedList = getAllRejectedAdoptions();
	print("<div class='rejectAdoptionsList'>");
	foreach($RejectedList->getRecords() as $Adoption)
	{
		buildRejectedEntry($Adoption);
	}
	print("</div>");//end rejectedAdoptionsInternal
	print("</div>");
	print("<script>");
	print("$('#rejectedAdoptionsClickable').click
	(
		function()
		{
			$('#rejectedAdoptionsInternal').slideToggle();
	});");
	print("</script>");
}

function buildUserApprovedAdoptions($Alias)
{
	print("<div id='userApprovedAdoptions'>");
	$AcceptedList = getAllUserApprovedAdoptions($Alias);
	foreach($AcceptedList->getRecords() as $Adoption)
	{
		buildApprovedAdoptionEntry($Adoption);
	}
	print("</div>");
}

function buildUserRejectedAdoptions($Alias)
{
	print("<div id='userRejectedAdoptions'>");
	$RejectedList = getAllUserRejectedAdoptions($Alias);
	foreach($RejectedList->getRecords() as $Adoption)
	{
		buildRejectedEntry($Adoption);
	}
	print("</div>");
}

function buildApprovedAdoptionEntry($Adoption)
{
	$ApprovedBy = $Adoption->value("ApprovedBy");
	$URL = $Adoption->value("URL");
	$DateApproved = $Adoption->value("DateApproved");
	$DateCreated = $Adoption->value("AdoptionID");
	$Alias = $Adoption->value("Alias");
	$CharacterSubmitted = $Adoption->value("SubmittedCharacter");
	$PreferredCharacter = $Adoption->value("PreferredCharacter");
	$Receiver = $Adoption->value("Receiver");
	$Comments = $Adoption->value("Comments");
	$DateApproved = date('F jS, Y', $DateApproved/1000);
	
	print("<div id='acceptedEntry$DateCreated'>");
	print("<div class='adoptionDetail'>By: $Alias</div>");
	print("<div class='adoptionDetail'>For: $Receiver</div>");
	$SubmittedCharacterName = getCharacterNameByID($CharacterSubmitted);
	print("<div class='adoptionDetail'>Character submitted: $SubmittedCharacterName</div>");
	print("<div class='adoptionDetail'>Date Approved: $DateApproved</div>");
	print("<div class='adoptionDetail'>Approved by: $ApprovedBy</div>");
	print("<div class='adoptionDetail'>Submission:<br><a href='$URL' target=_blank'><img src='$URL' class='previewMAXEntry' /></a></div>");
	if($Comments != '')
		{
			print("<div class='adoptionDetail'>Comments: <br>$Comments</div>");
		}
	print("</div>");
}

function buildRejectedEntry($Adoption)
{
	$RejectBy = $Adoption->value("RejectedAdmin");
	$URL = $Adoption->value("URL");
	$Reason = $Adoption->value("Reason");
	$DateRejected = $Adoption->value("DateRejected");
	$DateCreated = $Adoption->value("AdoptionID");
	$Alias = $Adoption->value("Alias");
	$CharacterSubmitted = $Adoption->value("SubmittedCharacter");
	$PreferredCharacter = $Adoption->value("PreferredCharacter");
	$Receiver = $Adoption->value("Receiver");
	$DateRejected = date('F jS, Y', $DateRejected/1000);
	
	print("<div id='rejectedEntry$DateCreated'>");
	print("<div class='adoptionDetail'>By: $Alias</div>");
	print("<div class='adoptionDetail'>For: $Receiver</div>");
	$SubmittedCharacterName = getCharacterNameByID($CharacterSubmitted);
	print("<div class='adoptionDetail'>Character submitted: $SubmittedCharacterName</div>");
	print("<div class='adoptionDetail'>Date rejected: $DateRejected</div>");
	print("<div class='adoptionDetail'>Rejected by: $RejectBy</div>");
	print("<div class='adoptionDetail'>Reason:<br><div class='rejectionReasonText'>$Reason</div></div>");
	print("<div class='adoptionDetail'>Submission:<br><a href='$URL' target=_blank'><img src='$URL' class='previewMAXEntry' /></a></div>");
	print("</div>");

}	
function buildViewMAXEntry($EntryID)
{
	$Entry = getEntryDetails($EntryID);
	buildViewEntry($Entry);
}

function  buildViewMAXOrphanEntry($OrphanID)
{
	$Entry = getOrphanEntryDetails($OrphanID);
	buildViewEntry($Entry);
}

function buildViewEntry($Entry)
{
	$EntryID = $Entry->value("EntryID");
	$CharacterID = $Entry->value("CharacterID");
	$URL = $Entry->value("URL");
	$Sender = $Entry->value("Sender");
	$Receiver = $Entry->value("Receiver");
	$Comments = $Entry->value("Comments");
	$CharacterName = getCharacterNameByID($CharacterID);
	print("<div class='viewEntry'>");
	print("<h2>$CharacterName by $Sender</h2>");
	print("<a href='$URL' target='_blank'><img src='$URL' class='fullsizePreviewEntry' /></a>");
	print("<div class='commentsWrap'>Artist's Comments:<br>");
	print("<div class='artistComments'>$Comments</div>");
	print("</div>");
	print("<h3><i class='fa fa-comments'></i>  User Comments </h3><div id='disqus_thread'></div>");
		print("<script>
			var disqus_config = function () 
			{
				this.page.identifier = 'MAXEntry-".$EntryID."-By-$Sender';
			};
			
			(function() { // DON'T EDIT BELOW THIS LINE
			var d = document, s = d.createElement('script');
			s.src = 'https://comicadia.disqus.com/embed.js';
			s.setAttribute('data-timestamp', +new Date());
			(d.head || d.body).appendChild(s);
		})();
		</script>
		<noscript>Please enable JavaScript to view the <a href='https://disqus.com/?ref_noscript'>comments powered by Disqus.</a></noscript>");
	print("</div>");
	
}

function buildViewMAXBlacklist($Alias)
{
	print("<h2>MAX Blacklist</h2>");
	if($Alias != '')
	{
		if(isUserBlacklisted($Alias))
		{
			print("<h4>You are currently on the blacklist</h4>");
		}
		else
		{
			print("<h4>You are currently not on the blacklist</h4>");
		}
	}
	$BlackListCount = getCountOfActivelyBlacklistedMembers();
	
	if($BlackListCount > 0)
	{
		print("<h3>Currently Blacklisted</h3>");
		$BlacklistString = "";
		$activeCount = 0;
		$Blacklist = getActivelyBlacklistedMembers();
		foreach($Blacklist->getRecords() as $User)
		{
			$UserAlias = $User->value("Alias");
			$Reason = $User->value("Reason");
			$DateBlacklisted = $User->value("DateCreated");
			$DateBlacklistedForHuman = date('F jS, Y', $DateBlacklisted/1000);
			$BlacklistString .= "<a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$UserAlias&Fields=Users' target='_blank'>$UserAlias</a>, ";
		}
		$BlacklistString = rtrim(trim($BlacklistString),',');
		print($BlacklistString);
	}
	else
	{
		print("No one is currently on the blacklist");
	}
}


function buildMAXAskForMoreReferences($Alias, $MAXID)
{
	print("<div id='requestMoreReferences'>");
	print("<h3>Reference Request</h3>");
	print("<div id='referencemsg'>Having a problem with trying to get down a detail of the character you want to draw or you think that there needs to be some more references for a character? 
	Then please, select from the character list below, fill out what you would like and hit submit.<br>
	This message will be forwarded to the MAX staff and they will validate the request, which will then be sent to the owner of the character anonymously. 
	You can make one request for each character. If a character already has 5 references, a request will still be forwarded to the admins and the owner will be asked to replace on of their references, if the reasoning behind the request is accepted.</div>");
	$Recipient = getMAXRecipientForUserForMAXRound($MAXID, $Alias);
	$RecipientAlias = $Recipient->value("Alias");
	$CharacterList = getCharactersOfUser($RecipientAlias);
	print("<select id='requestReferenceCharacterSelect'>");
	foreach($CharacterList->getRecords() as $Character)
	{
		$CharacterName = $Character->value("Name");
		$CharacterID = $Character->value("CharacterID");
		print("<option value='$CharacterID'>$CharacterName</option>");
	}
	print("</select><br>");
	print("Reason for request:<br>");
	print("<textarea id='requestReferenceReasonText'></textarea><br>");
	print("<input type='button' id='requestReferenceSubmitButton' value='Submit' onclick=\"submitReferenceRequest('$Alias');\"><br>");
	print("<div id='requestReferenceMSG' class='errMSG'></div>");
	print("</div>");
	
}
function buildManageSubmittedReferenceRequests($Alias)
{
	print("<div id='pendingReferencesRequestsWrap'>");
	print("<input type='button' value='References Requests' id='PendingReferenceRequestsButton'>");
	print("<div id='PendingReferencesRequestsInternal' class='Internal'>");
	$requestReferenceCount = getCountOfReferenceRequests();
	if($requestReferenceCount > 0)
	{
		$RequestList = getAllPendingRequestedReferences();
		buildManageReferenceRequestPanel($Alias,$RequestList);
	}
	else
	{
		print("There are no reference requests waiting for approval");
	}
	print("</div>");
	print("</div>"); // end pendingReferencesWrap
	print("<script>");
	print("$('#PendingReferenceRequestsButton').click
	(
	function()
	{
		$('#PendingReferencesRequestsInternal').slideToggle();
	});");
	print("</script>");
}

function buildManageReferenceRequestPanel($Alias,$RequestList)
{
	print("<div id='manageReferenceRequestPanel'>");
	foreach($RequestList->getRecords() as $Request)
	{
		$Reason = $Request->value("Reason");
		$CharacterID = $Request->value("CharacterID");
		$CharacterName = getCharacterNameByID($CharacterID);
		$Owner = $Request->value("Owner");
		$Requester = $Request->value("Requester");
		$RequestID = $Request->value("RequestID");
		print("<div id='request$RequestID'>");
		print("<input type='button' value='Requested for: $CharacterName' id='request$RequestID'> <br>");
		print("Owner of character: $Owner <br>");
		print("Requested by: $Requester <br>");
		print("Reason: <br><textarea id='submittedReasonText$RequestID' disabled>$Reason</textarea>");
		print("<div id='requestReferenceControlPanel$RequestID'>");
		print("<input type='button' id='editReason$RequestID' value='Edit Reason'><input type='button' id='acceptRequest$RequestID' value='Accept' onclick=\"acceptReferenceRequest('$Alias','$CharacterID','$RequestID','$Requester');\"><input type='button' id='rejectRequest$RequestID' value='Reject request' onclick=\"rejecttReferenceRequest('$Alias','$CharacterID','$RequestID','$Requester');\">");
		print("<div id='requestReferenceMSG$RequestID' class='errMSG'></div>");
		print("</div>");
		print("<script>");
		print("$('#editReason$RequestID').click
		(	
		function()
		{
			$('#submittedReasonText$RequestID').prop('disabled', function(i, v) { return !v; });
		});");
		
		print("</script>");
		print("<div class='ReferencesWrap'>");
		print("<h3>Current References for $CharacterName</h3>");
		$referenceList = getCharacterReferences($CharacterID);
		foreach($referenceList->getRecords() as $Reference)
		{
			$ReferenceURL = $Reference->value("URL");
			$ReferenceStatus = $Reference->value("Status");
			if($ReferenceStatus == 'Approved')
			{
				print("<div class='referencePreview'>");
				print("<a href='$ReferenceURL' target='_blank'><img src='$ReferenceURL' class='previewMAXEntry' /></a>");
				print("</div>");
				
			}
		}
		print("</div  <!-- End Reference Wrap -->");// End ReferencesWrap
		print("</div>");
	}
	print("</div> <!-- End manageReferenceRequestPanel -->");
}

function buildSendReminder($MAXID,$Alias)
{
	if(checkIfReminderHasBeenSent($MAXID))
	{
		print("<div class='reminderBox'>A reminder has already been sent</div>");
	}
	else
	{
		print("<div class='reminderBox'><input type='button' value='Send Reminder' id='sendMAXRoundReminder' onclick=\"sendReminderToMAXMembers('$MAXID','$Alias');\"></div>");
		print("<div id='MAXReminderMSG' class='errMSG'></div>");
	}
}
?>