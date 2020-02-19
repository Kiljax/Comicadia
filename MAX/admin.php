<?php
include '../php/GUI.php';
include './php/MAXGUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
{
	$alias = $_SESSION['Alias'];
	$email = $_SESSION['Email'];
	$type = getUserType($alias);
	if($type == 'Admin' || $type =='MAX' )
	{
		
	}
	else
	{
		header("Location: https://www.comicadia.com/index.php");
	}
}	
else 
{
	header("Location: https://www.comicadia.com/index.php");
}

?>

<html>
<head>
<link rel="stylesheet" href="https://www.comicadia.com/font-awesome/css/font-awesome.min.css">
<!-- These two are the primary style sheets used for Comicadia-->
<link href="../css/cpanel.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script type="text/javascript" src="../simpleTimePicker/jquery.simple-dtpicker.js"></script>
<link type="text/css" href="../simpleTimePicker/jquery.simple-dtpicker.css" rel="stylesheet" />

<script type="text/javascript">
$(document).ready(function(){
  $('#login-trigger').click(function(){
    $(this).next('#login-content').slideToggle();
    $(this).toggleClass('active');          
    
    if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
      else $(this).find('span').html('&#x25BC;')
    })
});

function goHome()
{
	location.href = 'https://www.comicadia.com/MAX/';	
}

function AttemptLogin()
{	
	var Email = document.getElementById('username').value;
	var Pass = document.getElementById('password').value;
	
	var xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			if(xmlhttp.responseText != 'Success')
			{
				document.getElementById('PassMSG').innerHTML=xmlhttp.responseText;
			}
			else 
			{
				location.reload();				
			}
		}
	}
	xmlhttp.open("POST", "../php/actions.php?F=Login&Email="+Email+"&Password="+Pass, true);
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

function parseDate(dateAsString) 
{
    ConvertedDate =  new Date(dateAsString.replace(/-/g, '/'));
	epoch = new Date(ConvertedDate).getTime() / 1000;
	return epoch;
}

function ScheduleMAXRound()
{
	var StartDate = document.getElementById('addMAXStartDateText').value;
	var CloseSignUpDate = document.getElementById('addMAXCloseSignUpText').value;
	var FinalDate = document.getElementById('addMAXFinalDateText').value;
	var Theme = document.getElementById("addMAXThemeText").value;
	var Status = document.getElementById("addMAXStatusSelect").value;
	StartDate = parseDate(StartDate);
	CloseSignUpDate = parseDate(CloseSignUpDate);
	FinalDate = parseDate(FinalDate);
	
	
	Success = true;
	Error = "MAX Round not scheduled";
	if(StartDate >= CloseSignUpDate)
	{
		Success = false;
		Error = Error +"<br>Sign ups can only close after the start date";
	}
	if(StartDate >= FinalDate)
	{
		Success = false;
		Error = Error +"<br>The Start Date must be before the End Date for a MAX Round. We don't have time travel, yet.";
	}
	if(CloseSignUpDate >= FinalDate)
	{
		Success = false;
		Error = Error +"<br>Sign ups must be closed before the submission deadlines";
	}
	if(Success)
	{
		fd = new FormData();
		fd.append("Start",StartDate);
		fd.append("SignUp",CloseSignUpDate);
		fd.append("Final",FinalDate);
		fd.append("Status",Status);
		fd.append("Theme",Theme);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('addMAXerrMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=scheduleMAXRound", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("addMAXerrMSG").innerHTML = Error;
	}	
}

function UpdateMAX(MAXID)
{
	NewStart = parseDate(document.getElementById("MAXStartText"+MAXID).value);
	NewClose = parseDate(document.getElementById("MAXCloseText"+MAXID).value);
	NewDeadline = parseDate(document.getElementById("MAXDeadlineText"+MAXID).value);
	NewTheme = document.getElementById("MAXThemeText"+MAXID).value;
	NewStatus = document.getElementById("MAXStatusSelect"+MAXID).value;
	OldStatus = document.getElementById("MAXThemeText"+MAXID).name;
	
	Success = true;
	Error = "MAX Round not modified";
	
	if(NewStart > NewClose)
	{
		Success =false;
		Error = Error + "<br>Start date cannot be after the sign-ups close date";
	}
	if(NewClose > NewDeadline)
	{
		Success =false;
		Error = Error + "<br>Sign-ups must close on a date before the submission deadline";
	}
	if(NewStart > NewDeadline)
	{
		Success =false;
		Error = Error + "<br>Start date cannot be after the submission deadline";
	}
	if(OldStatus == 'Completed')
	{
		Success =false;
		Error = Error + "<br>Completed MAX rounds cannot be modified. $OldStatus";
	}
	
	
	if(Success)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("MAXID",MAXID);
			fd.append("NewStart",NewStart);
			fd.append("NewClose",NewClose);
			fd.append("NewDeadline",NewDeadline);
			fd.append("NewTheme",NewTheme);
			fd.append("NewStatus",NewStatus);
			fd.append("OldStatus",OldStatus);
			
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('errMSG'+MAXID).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=updateMAXRound", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("errMSG"+MAXID).innerHTML = Error;
	}
}

function deleteMAX(MAXID)
{
	confirmDelete = confirm("Are you sure that you want to delete this MAX Round?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("MAXID",MAXID);
			
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('MAXRound'+MAXID).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=deleteMAXRound", true);
		xmlhttp.send(fd);
	}
}

function markMAXRoundAsComplete(MAXID)
{
	confirmComplete = confirm("Are you sure you want to mark this MAX round as completed?");
	if(confirmComplete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("MAXID",MAXID);
			
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('MAXInfo').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=markMAXRoundCompleted", true);
		xmlhttp.send(fd);
	}
}

function GenerateMAXMatchup(MAXID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("MAXID",MAXID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('MatchUpList'+MAXID).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=generateMAXMatchups", true);
	xmlhttp.send(fd);

}

function lockMAXRound(MAXID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("MAXID",MAXID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('MatchupStatus').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=lockMAXRound", true);
	xmlhttp.send(fd);
}

function approveReference(CharacterID,URL,Alias,refNo)
{
	xmlhttp = getxml();
	fd = new FormData();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("CharacterID",CharacterID);
		fd.append("URL",URL);
		fd.append("Alias",Alias);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("character"+CharacterID+"ReferencePreviewForAdmin"+refNo).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=approveReference", true);
	xmlhttp.send(fd);
}

function rejectReference(CharacterID,URL,Alias,refNo)
{
	xmlhttp = getxml();
	fd = new FormData();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("CharacterID",CharacterID);
		fd.append("URL",URL);
		fd.append("Alias",Alias);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("character"+CharacterID+"ReferencePreviewForAdmin"+refNo).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=rejectReference", true);
	xmlhttp.send(fd);
}

function addUserToBlacklist(MemberAlias,AdminAlias)
{
	Reason = document.getElementById("blacklistReasonText").value;
	if(Reason != '')
	{		
		var confirmAdd = confirm("Are you sure you want to add "+MemberAlias+" to the blacklist?");
		if(confirmAdd)
		{
			xmlhttp = getxml();
			xmlhttp.onreadystatechange = function()
			{
				fd = new FormData();
				fd.append("MemberAlias",MemberAlias);
				fd.append("AdminAlias",AdminAlias);
				fd.append("Reason",Reason);
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					document.getElementById("addUserToBlackListMSG").innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "./php/MAXactions.php?F=addUserToBlacklist", true);
			xmlhttp.send(fd);
		}
	}
	else
	{
		document.getElementById("addUserToBlackListMSG").innerHTML= "To add someone to the blacklist, you must input a reason";
	}
}

function addUserToBlacklistViaEntryID(MemberAlias, AdminAlias, EntryID,ReportedReason,ReportedOn)
{
	Reason = document.getElementById("reportAdminInputText"+EntryID).value;
	if(Reason == '')
	{		
		Reason = ReportedReason;
	}
	var confirmAdd = confirm("Are you sure you want to add "+MemberAlias+" to the blacklist?");
	if(confirmAdd)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("MemberAlias",MemberAlias);
			fd.append("AdminAlias",AdminAlias);
			fd.append("Reason",Reason);
			fd.append("ReportedOn",ReportedOn);
			fd.append("EntryID",EntryID);
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Success.')
				{
					document.getElementById("reportedEntry"+EntryID).innerHTML="Incident has been marked as resolved and " + MemberAlias + " has been added to the blacklist";
				}
				else
				{
					document.getElementById("reportMSG"+EntryID).innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=addUserToBlacklistAndResolveIncident", true);
		xmlhttp.send(fd);
	}
}

function liftBlacklist(UserAlias,Alias,DateBlacklisted,activeCount)
{
	Reason = document.getElementById("resolveBlacklistDetails"+DateBlacklisted+""+activeCount+"Text").value;
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("MemberAlias",UserAlias);
		fd.append("AdminAlias",Alias);
		fd.append("BlacklistID",DateBlacklisted);
		fd.append("Reason", Reason);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("liftBlacklist"+DateBlacklisted+""+activeCount+"MSG").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=removeUserFromBlacklist", true);
	xmlhttp.send(fd);
}

function searchMAXMembers()
{
	var Keyword = document.getElementById("searchMAXMembersText").value;
	if(Keyword.trim() == '')
	{
		document.getElementById("searchMSG").innerHTML = 'Search requires anything but empty spaces';
	}
	else
	{
		window.location = "https://www.comicadia.com/MAX/admin.php" + "?Search="+Keyword;
	}
}


function markReportAsResolved(EntryID,Alias,Receiver,ReportedOn)
{
	Reason = document.getElementById("reportAdminInputText"+EntryID).value;
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("MemberAlias",Receiver);
		fd.append("AdminAlias",Alias);
		fd.append("Reason",Reason);
		fd.append("EntryID", EntryID);
		fd.append("ReportedOn", ReportedOn);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			if(response == 'Success')
			{
				document.getElementById("reportedEntry"+EntryID).innerHTML="Incident marked as resolved";
			}
			else
			{
				document.getElementById("reportMSG"+EntryID).innerHTML=xmlhttp.responseText;
			}
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=resolveReportedEntry", true);
	xmlhttp.send(fd);
}

function rejectSubmission(Alias,AdoptionID)
{
	Reason = document.getElementById("rejectAdoptionSubmissionReason"+AdoptionID).value;
	if(Reason == '')
	{		
		document.getElementById(""+AdoptionID).innerHTML = "A reason is required to reject a submission";
	}
	var confirmAdd = confirm("Are you sure you wish to reject this submission?");
	if(confirmAdd)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("AdminAlias",Alias);
			fd.append("Reason",Reason);
			fd.append("AdoptionID",AdoptionID);
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Rejected.')
				{
					document.getElementById("reviewAdoptionEntryDetails"+AdoptionID).innerHTML="Submission Rejected";
				}
				else
				{
					document.getElementById("rejection"+AdoptionID+"MSG").innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=rejectSubmittedEntry", true);
		xmlhttp.send(fd);
	}
}

function acceptAdoptionSubmission(Alias, AdoptionID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("AdminAlias",Alias);
		fd.append("AdoptionID",AdoptionID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			if(response == 'Accepted')
			{
				document.getElementById("reviewAdoptionEntryDetails"+AdoptionID).innerHTML="Submission Accepted";
			}
			else
			{
				document.getElementById("accept"+AdoptionID+"MSG").innerHTML=xmlhttp.responseText;
			}
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=acceptSubmittedEntry", true);
	xmlhttp.send(fd);
}

function acceptLateEntry(EntryID,Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("AdminAlias",Alias);
		fd.append("EntryID",EntryID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			if(response == 'Accepted')
			{
				document.getElementById("lateEntry"+EntryID).innerHTML="Submission Accepted";
			}
			else
			{
				document.getElementById("lateEntry"+EntryID+"MSG").innerHTML=xmlhttp.responseText;
			}
		}
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=acceptLateEntry", true);
	xmlhttp.send(fd);
}

function rejectLateEntry(Alias,EntryID)
{
	Reason = document.getElementById("lateEntryAdminInputText"+EntryID).value;
	
	if(Reason.trim() == '')
	{
		document.getElementById("lateEntry"+EntryID+"MSG").innerHTML= "You must provide a reason for rejecting a late entry";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("AdminAlias",Alias);
			fd.append("EntryID",EntryID);
			fd.append("Reason",Reason);
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Accepted')
				{
					document.getElementById("lateEntry"+EntryID).innerHTML="Submission Accepted";
				}
				else
				{
					document.getElementById("lateEntry"+EntryID+"MSG").innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=rejectLateEntry", true);
		xmlhttp.send(fd);
	}
}

function testEmail()
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("testemailmsg").innerHTML = xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=testEmail", true);
		xmlhttp.send(fd);
	
}

function acceptReferenceRequest(AdminAlias, CharacterID, RequestID,Requester)
{
	Reason = document.getElementById("submittedReasonText"+RequestID).value;
	if(Reason.trim() == '')
	{
		document.getElementById("requestReferenceMSG"+RequestID).innerHTML = "A reason must be provided for a reference to be requested.";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("AdminAlias",AdminAlias);
			fd.append("CharacterID",CharacterID);
			fd.append("Reason",Reason);
			fd.append("Sender",Requester);
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				if(xmlhttp.responseText.trim() == 'Email Sent')
				{
					document.getElementById("request"+RequestID).innerHTML = "Email Sent";
				}
				else
				{
					document.getElementById("requestReferenceMSG"+RequestID).innerHTML = xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=adminSubmitRequestForReference", true);
		xmlhttp.send(fd);
	}
}

function rejecttReferenceRequest(AdminAlias, CharacterID, RequestID,Requester)
{
	Reason = document.getElementById("submittedReasonText"+RequestID).value;
	if(Reason.trim() == '')
	{
		document.getElementById("requestReferenceMSG"+RequestID).innerHTML = "A reason must be provided for a reference to be requested.";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			fd = new FormData();
			fd.append("AdminAlias",AdminAlias);
			fd.append("CharacterID",CharacterID);
			fd.append("Reason",Reason);
			fd.append("Sender",Requester);
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				if(xmlhttp.responseText.trim() == 'Email Sent')
				{
					document.getElementById("request"+RequestID).innerHTML = "Email Sent";
				}
				else
				{
					document.getElementById("requestReferenceMSG"+RequestID).innerHTML = xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "./php/MAXactions.php?F=adminRejectRequestForReference", true);
		xmlhttp.send(fd);
	}
}

function sendReminderToMAXMembers(MAXID,Alias)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("MAXID",MAXID);
	fd.append("Alias",Alias);
	xmlhttp.onreadystatechange = function()
	{
		document.getElementById("MAXReminderMSG").innerHTML = xmlhttp.responseText;
	}
	xmlhttp.open("POST", "./php/MAXactions.php?F=adminSendMAXReminder", true);
	xmlhttp.send(fd);
}
</script>

<meta name="description" content="MAX Control Panel" />

</head>
<title>Comicadia - MAX Admin Panel</title>
<body>
<div id="AdminPanel">
 <div id ="topBar"><div id='home' onclick='goHome()'><img src="../media/ComicadiaHeader-low.png" title="Comicadia"></div>
  <?php
  loadLogin();
  ?>
 </div>
 <div id="AdminWrap">
 	<div id="leftPanel">
	<?php	
	buildMAXAdminPanel();
	?>
 	</div>
 	<div id="contentWrap">
 		<div id='contentPanel'>
		<?php
		$articlesPerPage = 10;
		if (isset($_GET['submit']))
		{
		  	$call = $_GET['submit'];
			if($call == 'MAX Rounds')
			{
				buildAdminManageMAXRounds();
			}
			elseif($call == 'MAX Users')
			{
				buildMAXAdminMemberSearchDefault($articlesPerPage, 'Admin');	
			}
			elseif($call == 'MAX References')
			{
				buildMAXAdminManageReferences($alias);
				
			}
			elseif($call == 'MAX Entries')
			{
				$articlesPerPage = 10;
				if(!isset($_GET['page']))
				{
					$pageNumber = 0;
				}
				else
				{
					$pageNumber = (int)$_GET['page'];
					// Convert the page number to an integer
				}
				buildManageMAXAdminEntries($alias,$pageNumber,$articlesPerPage);
			}
			elseif($call == 'MAX Blacklist')
			{
				buildMAXAdminBlacklist($alias);
			}
			elseif($call == 'MAX Adoptions')
			{
				buildAdminManageAdoptions($alias);
			}
			else
			{
				buildMAXAdminDashboard($alias);
			}
		}
		elseif(isset($_REQUEST["Search"]) OR isset($_REQUEST['MemberAlias']))
		{
			if(isset($_REQUEST["Search"]))
			{
				$Search = $_REQUEST['Search'];
			}
			else
			{
				$Search = '';
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
			
			if(isset($_GET["MemberAlias"]))
			{
				$MemberAlias = $_GET["MemberAlias"];
			}
			else
			{
				$MemberAlias = '';
			}
			buildMAXAdminMemberSearchWithKeyword($Search, $MemberAlias,$pageNumber,$articlesPerPage,$alias);
		}
		elseif(isset($_REQUEST['Fields']))
		{
			$articlesPerPage = 10;
			$Fields = $_REQUEST['Fields'];
			if(isset($_GET['page']))
			{
				$pageNumber = (int)$_GET['page'];
			}
			else
			{
				$pageNumber = 0;
			}
			if($Fields == 'Entries')
			{
				buildManageMAXAdminEntries($alias,$pageNumber,$articlesPerPage);
			}
			elseif($Fields == 'Users')
			{
				if(isset($_REQUEST["Search"]))
				{
					$Search = $_REQUEST['Search'];
				}
				else
				{
					$Search = '';
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
				
				if(isset($_GET["MemberAlias"]))
				{
					$MemberAlias = $_GET["MemberAlias"];
				}
				else
				{
					$MemberAlias = '';
				}
				
				buildMAXAdminMemberSearchWithKeyword($Search, $MemberAlias,$pageNumber,$articlesPerPage,$alias);
			}
			else
			{
				buildMAXAdminDashboard($alias);
			}
		}
		else 
		{
			buildMAXAdminDashboard($alias);
		}
		?>
		</div>
	</div>
	</div>
</div>

</body>
</html>