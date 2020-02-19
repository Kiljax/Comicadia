<?php

include 'php/GUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
{
	$alias = $_SESSION['Alias'];
	$email = $_SESSION['Email'];
	$type = getUserType($alias);
	if($type != 'Admin')
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
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>

<!-- include libraries(jQuery, bootstrap) -->
<!-- removing bootstrap to see if WYSIWYG editor is boned. If bootstrap is on, the calendar is boned -->
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 

<script type="text/javascript" src="simpleTimePicker/jquery.simple-dtpicker.js"></script>
<link type="text/css" href="simpleTimePicker/jquery.simple-dtpicker.css" rel="stylesheet" />

<!-- include summernote WYSIWYG text editor -->
<link href="js/summernote/summernote.css" rel="stylesheet">
<script src="js/summernote/summernote.js"></script>

<!-- Javascript to load the date modifier so everyone will see their own date -->
<!--
<script type="text/javascript" src="moment/moment.js"></script>
<script type="text/javascript" src="moment-timezone/moment-timezone.js"></script>
-->

<!-- These two are the primary style sheets used for Comicadia-->
<link href="https://www.comicadia.com/css/cpanel.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
<script>
	$(document).ready(function() 
{
	$('.summernote').summernote({
    height: 200,
	toolbar: 
	[
		//[groupName, [list of button]]
		['style', ['bold', 'italic', 'underline']],
		//['misc', ['undo','redo']],
		['font', ['strikethrough']],
		['fontsize', ['fontsize']],
		['color', ['color']],
		['para', ['ul', 'ol', 'paragraph']],
		['insert', ['link','picture']],
		['misc', ['codeview']]
	],
    callbacks : 
	{
        onImageUpload: function(image) 
		{
            uploadImage(image[0]);
        }
    }
});

	function uploadImage(image) 
	{
		var data = new FormData();
		data.append("image",image);
		$.ajax 
		({
			data: data,
			type: "POST",
			url: "php/actions.php?F=uploadFile",
			cache: false,
			contentType: false,
			processData: false,
			success: function(url) 
			{
				var image = url;
				$('.summernote').summernote('insertImage', image);
			},
			error: function(data) 
			{
				console.log(data);
			}
		});
	}
});

function disableProfilePic(Alias, Counter)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			document.getElementById('ProfilePic'+Counter).innerHTML=response;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteProfilePic"+"&Alias="+Alias, true);
	xmlhttp.send(); 
}

function saveUserEdits(Counter)
{
	var NewFirstName = document.getElementById('UserFirstName'+Counter).value;
	var NewLastName = document.getElementById('UserLastName'+Counter).value;
	var NewEmail = document.getElementById('UserEmail'+Counter).value;
	var NewAlias =document.getElementById('UserAlias'+Counter).value;
	var NewUserType = document.getElementById('UserType'+Counter).value;
	var oldEmail = document.getElementById('UserEmail'+Counter).name;
	var oldAlias = document.getElementById('UserAlias'+Counter).name;
	
	var fd = new FormData();
	
	fd.append("FirstName",NewFirstName);
	fd.append("LastName",NewLastName);
	fd.append("Email",NewEmail);
	fd.append("Alias",NewAlias);
	fd.append("UserType",NewUserType);
	fd.append("OldEmail",oldEmail);
	fd.append("OldAlias",oldAlias);
		
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Save Successful.')
			{
				document.getElementById("UserAlias"+Counter).name = NewAlias;
				document.getElementById("UserEmail"+Counter).name = NewEmail;
			}
			document.getElementById('userMSG'+Counter).innerHTML=response;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=saveUserEdits", true);
	xmlhttp.send(fd); 
}

function AttemptLogin()
{	

	var Email = document.getElementById('username').value;
	var Pass = document.getElementById('password').value;
	
	var fd = new FormData();
	
	fd.append("Email",Email);
	fd.append("Password",Pass);
	
	xmlhttp = getxml();
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
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=Login", true);
	xmlhttp.send(fd);
}

function loadUser(Alias) 
{
	var fd = new FormData();
	
	fd.append("Alias",Alias);
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('EditUserContent').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=loadUser" + Alias, true);
	xmlhttp.send(fd);
}

function saveNewUser()
{
	
	var NewFirstName = document.getElementById('newFirstNameText').value;
	var NewLastName = document.getElementById('newLastNameText').value;
	var NewPassword = document.getElementById('newPasswordText').value;
	var NewEmail = document.getElementById('newEmailText').value;
	var NewAlias =document.getElementById('newAliasText').value;
	var NewUserType = document.getElementById('UserTypeSELECT').value;
	
	var fd = new FormData();
	
	fd.append("FirstName",NewFirstName);
	fd.append("LastName",NewLastName);
	fd.append("Password",NewPassword);
	fd.append("Email",NewEmail);
	fd.append("Alias",NewAlias);
	fd.append("UserType",NewUserType);
	
	xmlhttp = getxml();
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('saveERR').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=saveUser", true);
	xmlhttp.send(fd);
}

function previewNews(DateWritten, First, Last)
{
	var fd = new FormData();
	fd.append("DateWritten",DateWritten);
	fd.append("FirstName",First);
	fd.append("LastName",Last);
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('newsPreview').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=previewNews", true);
	xmlhttp.send(fd);
}


function postNews(Alias)
{	
	var Title = document.getElementById('newsTitleText').value;
	var Details = document.getElementById('newsDetailsText').value;	
	var Category = document.getElementById('newsCategorySelect').value;
	var epoch = document.getElementById('newsDatepicker').value;
	
	var success = true;
	var error = 'Message not posted';
	
	if(Title.trim() == '')
	{
		success = false;
		error = error + '<br>News posts require a title';
	}
	if(Details.trim() == '')
	{
		success = false;
		error = error + '<br>News posts require some details';
	}
	
	if(success)
	{
		xmlhttp = getxml();
		
		var fd = new FormData();
		
		fd.append("Alias",Alias);
		fd.append("Title",Title);
		fd.append("Details",Details);
		fd.append("PubDate",epoch);
		fd.append("Category",Category);
		fd.append("Alias", Alias);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('PostMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=postNews", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('PostMSG').innerHTML= error;
	}
}

function ConfirmEditNews()
{
	var altFormat = $( "#editDatepicker" ).datepicker( "option", "altFormat" );
	var PubDate = $( "#editDatepicker" ).datepicker( "getDate" );
	//var PubDate = document.getElementById('newsDatepicker').value;
	var Email = document.getElementById('newsDetailsText').name;
	var Title = document.getElementById('newsTitleText').value;
	var Details = document.getElementById('newsDetailsText').value;	
	var Category = document.getElementById('newsCategorySelect').value;
	var DateWritten = document.getElementById('EditNewsButton').name;
	var Status = document.getElementById('newsStatusSELECT').value;
	
	var epoch = PubDate.getTime();
	
	var success = true;
	var error = 'Edits not saved';
	
	if(Title.trim() =='')
	{
		error = error + '<br>News posts need a title';
		success = false;
	}
	if(Details.trim() =='')
	{
		error = error + '<br>News posts need Details';
		success = false;
	}
			
	if(success)
	{
		var fd = new FormData();
	
		fd.append("NewsID",DateWritten);
		fd.append("Email",Email);
		fd.append("Title",Title);
		fd.append("Details",Details);
		fd.append("PubDate",epoch);
		fd.append("Category",Category);
		fd.append("Status",Status);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('PostMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=editNews&NewsID="+DateWritten+"&Email="+Email+"&Title="+Title+"&Details="+Details+"&PubDate="+epoch+"&Category="+Category+"&Status="+Status, true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('PostMSG').innerHTML=error;
	}
}

function loadWebcomic(ComicID)
{
	xmlhttp = getxml();
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('editWebcomicMSG').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=previewWebcomic&ComicID="+ComicID, true);
	xmlhttp.send();
}

function addWebcomic()
{
	var ComicName = document.getElementById('webcomicNameText').value;
	var ComicURL = document.getElementById('webcomicURLText').value;
	var ComicRSS = document.getElementById('webcomicRSSText').value;
	var Creator = document.getElementById('webcomicCreatorSelect').value;
	var Membership =document.getElementById('webcomicMembershipSelect').value;
	var Status = document.getElementById('webcomicStatusSelect').value;
	
	var error = 'Comic not added';
	var success = true;
	
	
	
	if(ComicName.trim() == '')
	{
		success = false;
		error = error + '<br>A comic needs a name';
	}
	if(ComicURL.trim() == '')
	{
		success = false;
		error = error + '<br>A comic needs a URL';
	}
	if(ComicRSS.trim() == '')
	{
		success = false;
		error = error + '<br>A comic needs an RSS feed';
	}
	
	if(success)
	{
		var fd = new FormData();
		fd.append("ComicName",ComicName);
		fd.append("ComicURL",ComicURL);
		fd.append("ComicRSS",ComicRSS);
		fd.append("Creator",Creator);
		fd.append("Membership",Membership);
		fd.append("Status",Status);
		
		xmlhttp = getxml();
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('addWebcomicMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=addWebcomic", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('addWebcomicMSG').innerHTML=error;
	}
}

function adminResetPass(Counter,Alias)
{
	var Password = prompt("Please enter a password:", "Password");

	xmlhttp = getxml();
	if(Password.trim() == '')
	{
		document.getElementById('resetPasswordMSG'+Counter).innerHTML= 'The new password cannot be blank';
	}
	else
	{
		var fd = new FormData();
		fd.append("Password", Password);
		fd.append("Alias",Alias);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('resetPasswordMSG'+Counter).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=resetUserPass", true);
		xmlhttp.send(fd);
	}
}

function saveUserRoles(Alias,CrewCount,ComicID)
{
	var Role = document.getElementById('Role'+CrewCount+'').value;
	
	xmlhttp = getxml();
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('crewMSG'+CrewCount+'').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=saveUserRole&ComicID="+ComicID+"&Alias="+Alias+"&Role="+Role, true);
	xmlhttp.send();
}

function removeCrew(Alias,CrewCount,ComicID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('crewMSG'+CrewCount+'').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=removeCrew&ComicID="+ComicID+"&Alias="+Alias, true);
	xmlhttp.send();
}

function saveWebcomicDetails(ComicID, Counter)
{
	var Name = document.getElementById("comicName"+Counter).value;
	var RSS = document.getElementById("comicRSS"+Counter).value;
	var URL = document.getElementById("comicURL"+Counter).value;
	var Synopsis = document.getElementById("comicSynopsis"+Counter).value;
	var Membership = document.getElementById("comicMembership"+Counter).value;
	var Status = document.getElementById("comicStatus"+Counter).value;
	var Pitch = document.getElementById("comicPitch"+Counter).value;
		
	var Success = true;
	var Error = 'Changes not saved';
	
	if(Name.trim() == '')
	{
		Success = false;
		Error = Error + '<br>Comics require a name';
	}
	
	if(RSS.trim() == '')
	{
		Success = false;
		Error = Error + '<br>Comics require an RSS';
	}
	
	if(URL.trim() == '')
	{
		Success = false;
		Error = Error + '<br>Comics require a URL';
	}
	
	if(Success)
	{
		var fd = new FormData();
		
		fd.append("ComicID",ComicID);
		fd.append("ComicName",Name);
		fd.append("RSS",RSS);
		fd.append("URL",URL);
		fd.append("Synopsis",Synopsis);
		fd.append("Membership",Membership);
		fd.append("Status",Status);
		fd.append("Pitch",Pitch);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('comicMSG'+Counter).innerHTML=xmlhttp.responseText;
			}
		}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=saveWebcomicEdits", true);
	xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("comicMSG"+Counter).innerHTML = Error;
	}
}

function saveComicChanges()
{
	var CurrentName = document.getElementById('editComicNameText').name;
	var CurrentURL = document.getElementById('editComicURLText').name;
	var CurrentRSS = document.getElementById('editComicRSSText').name;
	  
	var ComicName = document.getElementById('editComicNameText').value;
	var ComicURL = document.getElementById('editComicURLText').value;
	var ComicRSS = document.getElementById('editComicRSSText').value;
	var ComicStatus = document.getElementById('editWebcomicStatusSelect').value;
	var Membership = document.getElementById('editWebcomicMembershipSelect').value;
	
	
	var success = true;
	var error = 'Changes to comic not saved';
	
	xmlhttp = getxml();
	
	if(ComicName.trim() == '')
	{
		success = false;
		error = error + 'A Comic needs a name';
	}
	
	if(ComicURL.trim() == '')
	{
		success = false;
		error = error + 'A Comic needs a URL';
	}
	
	if(ComicRSS.trim() == '')
	{
		success = false;
		error = error + 'A Comic needs an RSS feed';
	}
	if(success)
	{
		var fd = new FormData();
		fd.append("CurrentName",CurrentName);
		fd.append("CurrentURL",CurrentURL);
		fd.append("CurrentRSS",CurrentRSS);
		fd.append("ComicName",ComicName);
		fd.append("URL",ComicURL);
		fd.append("RSS",ComicRSS);
		fd.append("Status",ComicStatus);
		fd.append("Membership",Membership);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('saveWebcomicMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=saveWebcomicEdits", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('saveWebcomicMSG').innerHTML=error;
	}
}

function addCrew(ComicID)
{
	var Alias = document.getElementById('addNewCrewSelect').value;
	var Roles = document.getElementById('addCrewRoleText').value;
	
		xmlhttp = getxml();
		
	var fd = new FormData();
	fd.append("ComicID",ComicID);
	fd.append("Alias",Alias);
	fd.append("Roles",Roles);
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('addCrewMSG').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=addCrew", true);
	xmlhttp.send(fd);
}

function parseDate(dateAsString) 
{
    return new Date(dateAsString.replace(/-/g, '/'))
}

function scheduleEvent()
{
	var EventDate = document.getElementById('addEventDateText').value;
	var EventTitle = document.getElementById('addEventTitleText').value;
	var EventLocation = document.getElementById('addEventLocationText').value;
	var EventOrganizer = document.getElementById('addEventSELECT').value;
	var EventDetails = document.getElementById('addEventDetailsText').value;
	var EventType = document.getElementById('addEventTypeSELECT').value;	
	var EventCategory = document.getElementById('addEventCategorySELECT').value;
	EventDate = parseDate(EventDate);
	epoch = new Date(EventDate).getTime() / 1000
	
	
	var success = true;
	var error = 'Event not scheduled';
	
	
	if(EventTitle.trim() == '')
	{
		error = error + '<br>Events require a title';
		success = false;
	}
	if(EventLocation.trim() == '')
	{
		error = error + '<br>Events require a location';
		success = false;
	}
	if(EventDetails.trim() == '')
	{
		error = error + '<br>Events require details';
		success = false;
	}
	//var b = EventDate.split(/\D+/);
	//var epoch = new Date(b[2], --b[1], b[0], b[3], b[4], b[5]) / 1000;
	
	if(success)
	{
		var fd = new FormData();
		fd.append("Title",EventTitle);
		fd.append("Organizer",EventOrganizer);
		fd.append("EventDate",epoch);
		fd.append("Details",EventDetails);
		fd.append("Type",EventType);
		fd.append("Location",EventLocation);
		fd.append("Category",EventCategory);
		
		xmlhttp = getxml();
			   
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('addEventMSG').innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=addEvent", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('addEventMSG').innerHTML=error;
	}
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

function formatDate(date) 
{            // function for reusability
	var d = date.getUTCDate().toString(),           // getUTCDate() returns 1 - 31
   m = (date.getUTCMonth() + 1).toString(),    // getUTCMonth() returns 0 - 11
   y = date.getUTCFullYear().toString(),       // getUTCFullYear() returns a 4-digit year
   h = date.getUTCHours().toString(),
   minutes = date.getUTCMinutes().toString(),
   s = date.getUTCSeconds().toString()
   formatted = '';
   if (d.length === 1) 
   {                           // pad to two digits if needed
   	d = '0' + d;
   }
   if (m.length === 1) 
   {                           // pad to two digits if needed
  		m = '0' + m;
   }
   if(h.length === 1)
   {
   	h = '0' + h;
   }
   if(minutes.length === 1)
   {
   	minutes = '0' + minutes;
   }
   if(s.length === 1 )
   {
   	s = '0' + s;
   }
   formatted = d + '-' + m + '-' + y + ' ' + h+':'+minutes+':'+s;              // concatenate for output
   return formatted;
}


function deleteNews(NewsID,NewsCount,Poster)
{
	var confirmDelete = confirm("Are you sure that you want to delete this news entry?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
	
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById('newsItem'+NewsCount+'').innerHTML="Deleted";
				}
				else 
				{
					document.getElementById('newsItemMSG'+NewsCount+'').innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteNewsPost&DateWritten="+NewsID+"&Alias="+Poster, true);
		xmlhttp.send();
	}
}

function deleteEvent(EventID,EventType,EventOrganizer, DivID)
{
	EventTitle = document.getElementById('Title'+DivID).name;
	var confirmDelete = confirm("Do you want to delete this event?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
	
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Deleted')
				{
					document.getElementById('Event'+DivID).innerHTML=response;
				}
				else 
				{
					document.getElementById('err'+DivID).innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteEvent&Title="+EventTitle+"&Organizer="+EventOrganizer+"&EventDate="+EventID+"&Type="+EventType, true);
		xmlhttp.send();
	}
}

function deleteUser(Alias, Counter,Deleter)
{
	var confirmDelete = confirm("Are you sure that you want to delete "+ Alias + "?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
	
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById('UserDetails'+Counter+'').innerHTML="Deleted";
				}
				else 
				{
					document.getElementById('userMSG'+Counter+'').innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteUser&Alias="+Alias+"&Deleter="+Deleter, true);
		xmlhttp.send();
	}
}

function saveEventEdits(DivID)
{
	var OriginalEventTitle = document.getElementById('Title'+DivID).name;
	var OriginalEventOrganizer = document.getElementById('Organizer'+DivID).name;
	var OriginalEventStartTime = document.getElementById('Location'+DivID).name;
	var OriginalEventType = document.getElementById('Type'+DivID).name;
	var NewTitle = document.getElementById('Title'+DivID).value;
	var NewStart = document.getElementById('editEventDate'+DivID).value;
	var NewOrganizer = document.getElementById('Organizer'+DivID).value;
	var NewLocation = document.getElementById('Location'+DivID).value;
	var NewDetails = document.getElementById('Details'+DivID).value;
	var NewCategory = document.getElementById('Category'+DivID).value;
	var NewType = document.getElementById('Type'+DivID).value;
	var NewStatus = document.getElementById('Status'+DivID).value;
	
	epoch = NewStart;
	
	var success = true;
	var error = 'Changes to event not saved';
	
	if(NewTitle.trim() =='')
	{
		success = false;
		error = error + '<br>Events require a title';
	}
	if(NewLocation.trim() =='')
	{
		success = false;
		error = error + '<br>Events require a location';
	}
	if(NewDetails.trim() =='')
	{
		success = false;
		error = error + '<br>Events require some details';
	}
	
	//var tz = moment.tz.guess();
	if(success)
	{
		var fd = new FormData();
		fd.append("NewTitle",NewTitle);
		fd.append("NewOrganizer",NewOrganizer);
		fd.append("NewEventDate",epoch);
		fd.append("NewType",NewType);
		fd.append("NewCategory",NewCategory);
		fd.append("NewLocation",NewLocation);
		fd.append("NewDetails",NewDetails);
		fd.append("OldTitle",OriginalEventTitle);
		fd.append("OldStartTime",OriginalEventStartTime);
		fd.append("OldType",OriginalEventType);
		fd.append("NewStatus",NewStatus);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				document.getElementById('err'+DivID+'').innerHTML=response;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=editEvent", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('err'+DivID+'').innerHTML=error;
	}
}

function searchUsersByKeywords()
{
	var SearchBy = document.getElementById('SearchUserSELECT').value;
	var Keyword = document.getElementById('searchKeywordsTEXT').value;
	
	if(Keyword.trim() == '')
	{
		document.getElementById('addThemeERR').innerHTML='You must enter at least a single character to search by';					
	}
	else
	{
		var fd =new FormData();
		fd.append("SearchBy",SearchBy);
		fd.append("Keyword",Keyword);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					
					document.getElementById('addThemeERR').innerHTML=response;					
				}
			}
			xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=searchUsersToEdit", true);
			xmlhttp.send(fd);
	}
}

function createTheme()
{
	var ThemeName = document.getElementById('AddThemeNameText').value;
	var ThemeRating = document.getElementById('AddThemeValueText').value;
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Added')
			{
				document.getElementById('addThemeERR').innerHTML='Added';
				reloadThemes();
			}
			else 
			{
				document.getElementById('addThemeERR').innerHTML=response;
			}
		}
	}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=createTheme&ThemeName="+ThemeName+"&ThemeRating="+ThemeRating, true);
	xmlhttp.send();
}

function createGenre()
{
	var GenreName = document.getElementById("AddGenreNameText").value;
	if(GenreName.trim() != "")
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Genre added')
				{
					document.getElementById('addGenreERR').innerHTML='Added';
					reloadGenres();
				}
				else 
				{
					document.getElementById('addGenreERR').innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=createGenre&GenreName="+GenreName, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("addGenreERR").innerHTML = "Genre name cannot be blank";
	}
	
}

function reloadThemes()
{
	location.href = 'https://www.comicadia.com/admin.php?submit=Manage+Themes';	
}

function reloadGenres()
{
	location.href = 'https://www.comicadia.com/admin.php?submit=Manage+Genres';	
}

function saveThemeEdits(ThemeCount)
{
	var Name = document.getElementById("Theme"+ThemeCount).value;
	var oldName = document.getElementById('Theme'+ThemeCount).name;
	var oldRating = document.getElementById('Rating'+ThemeCount).name;
	var newRating = document.getElementById('Rating'+ThemeCount).value;
	var Success = true;
	var Error = 'Changes not saved';
	
	if(Name.trim() == '')
	{	
		Success = false;
		Errror = error + "<br>Theme name cannot be blank";
	}
	if(newRating.trim() != '')
	{	
		if(!IsNumeric(newRating))
		{
			Success = false;
			Errror = error + "<br>Rating must be a number";
		}
	}
	else
	{
		Success = false;
		Errror = error + "<br>Rating cannot be blank";
	}
	
	if(Success)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Changes saved')
				{
					document.getElementById("Theme"+ThemeCount).name = Name;
					document.getElementById('MSG'+ThemeCount).innerHTML=xmlhttp.responseText;
				}
				else
				{
					document.getElementById('MSG'+ThemeCount).innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=editTheme&OldThemeName="+oldName+"&OldThemeRating="+oldRating+"&NewThemeName="+Name+"&NewThemeRating="+newRating, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById('MSG'+ThemeCount).innerHTML= Error;
	}
}

function saveGenreEdits(GenreCount)
{
	var Name = document.getElementById("Genre"+GenreCount).value;
	var oldGenreName = document.getElementById("Genre"+GenreCount).name;
	if(Name.trim() != '')
	{
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Changes saved')
				{
					document.getElementById("Genre"+GenreCount).name = Name;
					document.getElementById("MSG"+GenreCount).innerHTML = response;
				}
				else
				{
					document.getElementById("MSG"+GenreCount).innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=editGenre&OldGenreName="+oldGenreName+"&NewGenreName="+Name, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("MSG"+GenreCount).innerHTML = 'Gnere names cannot be blank';
	}
}

function deleteTheme(ThemeCount)
{
	var ThemeName = document.getElementById('Theme'+ThemeCount).name;
	var confirmDelete = confirm("Do you want to delete this Theme? ("+ThemeName+")");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Deleted')
				{
					document.getElementById('EditTheme'+ThemeCount).innerHTML='Deleted';
				}
				else
				{
					document.getElementById('MSG'+ThemeCount).innerHTML=response;
				}
			}
		}
	xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteTheme&ThemeName="+ThemeName, true);
	xmlhttp.send();
	}
}

function deleteGenre(GenreCount)
{
	var GenreName = document.getElementById("Genre"+GenreCount).name;
	var confirmDelete = confirm("Do you want to delete this Genre? ("+GenreName+")");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Genre Deleted')
				{
					document.getElementById('editGenre'+GenreCount).innerHTML='Deleted';
				}
				else
				{
					document.getElementById('MSG'+GenreCount).innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=deleteGenre&GenreName="+GenreName, true);
		xmlhttp.send();
	}
}


function adminAddCrew(ComicID, Counter)
{
	NewCrew = document.getElementById("addCrew"+Counter).value;
	Roles = document.getElementById("addCrewRoles"+Counter).value;
	
	
	if(Roles.trim() == '')
	{
		document.getElementById("addCrewMSG"+Counter).innerHTML = "You cannot add a crewmater who has no role";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
					document.getElementById('addCrewMSG'+Counter).innerHTML=xmlhttp.responseText;

			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminAddCrew&ComicID="+ComicID+"&Alias="+NewCrew+"&Roles="+Roles, true);
		xmlhttp.send();
	}
}

function adminUpdateCrew(ComicID,CrewCount,Counter,CrewAlias)
{
	Roles = document.getElementById("crewRoles"+Counter+""+CrewCount).value;
	
	
	if(Roles.trim() == '')
	{
		document.getElementById("manageCrew"+Counter+""+CrewCount).innerHTML = 'You cannot have a crewmate with no roles.';
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
					document.getElementById('manageCrewMSG'+Counter+""+CrewCount).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminUpdateCrew&ComicID="+ComicID+"&Alias="+CrewAlias+"&Roles="+Roles, true);
		xmlhttp.send();
	}
}

function adminRemoveCrew(ComicID,CrewCount,Counter,CrewAlias)
{
	var confirmDelete = confirm("Are you sure you want to remove "+CrewAlias+"?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Deleted')
				{
					document.getElementById("manageCrew"+Counter+""+CrewCount).innerHTML = 'Deleted';
				}
				else
				{
					document.getElementById("manageCrewMSG"+Counter+""+CrewCount).innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminRemoveCrew&ComicID="+ComicID+"&Alias="+CrewAlias, true);
		xmlhttp.send();
	}
	
}

function adminSaveGenres(ComicID, Counter)
{
	var Genre1 = document.getElementById("FirstGenre"+Counter).value;
	var Genre2 = document.getElementById("SecondGenre"+Counter).value;
	var Genre3 = document.getElementById("ThirdGenre"+Counter).value;
	
	if(Genre1 == '' && Genre2 == '' && Genre3 == '')
	{
		document.getElementById("saveComicGenreMSG"+Counter).innerHTML= "No Genres have been selected.";
	}
	else if(Genre1 == Genre2 || Genre2 == Genre3 || Genre1 == Genre3)
	{
		document.getElementById("saveComicGenreMSG"+Counter).innerHTML= "Genres cannot be duplicates.";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("saveComicGenreMSG"+Counter).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminSaveGenres&ComicID="+ComicID+"&Genre1="+Genre1+"&Genre2="+Genre2+"&Genre3="+Genre3, true);
		xmlhttp.send();
	}
}

function adminDeleteComic(ComicID,Counter,Alias)
{
	var confirmDelete = confirm("Are you sure you want to delete this comic? Once this is done, it cannot be recovered!");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				if(response == 'Deleted')
				{
					document.getElementById("comicSearchResult"+Counter).innerHTML= 'Deleted';
				}
				else
				{
					document.getElementById("deleteComicMSG"+Counter).innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminDeleteComic&ComicID="+ComicID+"&Alias="+Alias, true);
		xmlhttp.send();
	}
}

function adminSaveThemesForWebcomic(ComicID, Counter)
{
	var ThemeArray = getCheckedBoxes("themeCheckbox"+Counter);
//	var ThemeList[];
	//for(i=0 ;i<ThemArray.length;++i)
	//{
		//ThemeList.push(ThemeArray[i].value);
	//}
	if(ThemeArray.length > 0)
	{	
		if(ThemeArray.length < 11)
		{
			var ThemeString = '';
	
			for(i = 0; i< ThemeArray.length;++i)
			{
				ThemeString = ThemeString + ThemeArray[i] + ",";
			}
			xmlhttp = getxml();
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					document.getElementById("ThemeMSG"+Counter).innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=adminSaveThemes&ComicID="+ComicID+"&ThemeList="+ThemeString, true);
			xmlhttp.send();
		}
		else
		{
			document.getElementById("ThemeMSG"+Counter).innerHTML= "Please only select up to 10 themes";
		}
	}
	else
	{
		document.getElementById("ThemeMSG"+Counter).innerHTML= "No Themes selected";
	}
}

function getCheckedBoxes(chkboxName) 
{
  var checkboxes = document.getElementsByName(chkboxName);
  var checkboxesChecked = [];
  // loop over them all
  for (var i=0; i<checkboxes.length; i++) {
     // And stick the checked ones onto an array...
     if (checkboxes[i].checked) {
        checkboxesChecked.push(checkboxes[i].value);
     }
  }
  // Return the array if it is non-empty, or null
  return checkboxesChecked;
}

function saveNewsEdits(alias,NewsCount,Poster,epochDateWritten)
{
	NewDetails = document.getElementById("newsDetails"+NewsCount).value; 
	NewStatus = document.getElementById("newsStatusSelect"+NewsCount).value; 
	NewCategory = document.getElementById("newsCategorySelect"+NewsCount).value; 
	NewTitle = document.getElementById("newsTitleText"+NewsCount).value;
	NewPubDate = document.getElementById("newsPubDate"+NewsCount).value; 
	
	Error = 'Changes not saved';
	Success = true;
	
	if(NewDetails.trim() == '')
	{
		Sucess = false;
		Error = Error + '<br>News requires details';
	}
	if(NewTitle.trim() == '')
	{
			Sucess = false;
		Error = Error + '<br>News requires a Title';
	}
	
	if(Success)
	{
		var fd = new FormData();
	
		fd.append("NewsID",epochDateWritten);
		fd.append("Details",NewDetails);
		fd.append("Status",NewStatus);
		fd.append("Category",NewCategory);
		fd.append("Title",NewTitle);
		fd.append("PubDate",NewPubDate);
		fd.append("Alias",alias);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				{
					document.getElementById('newsItemMSG'+NewsCount).innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.open("POST", "https://www.comicadia.com/php/actions.php?F=editNews", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById('newsItemMSG'+NewsCount).innerHTML=Error;
	}
}

function uploadMediaFromURL(Alias)
{
	var Type = document.getElementById("addFileFromWebTypeSelect").value;
	var URL = document.getElementById("addNewMediaURL").value;
	var Artist = document.getElementById('addMediaArtistFromURL').value;
	var Desc = document.getElementById('addMediaFromURLDescriptionText').value;
	
	xmlhttp = getxml();
	   
	if(URL != '')
	{	 
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("adminAddMediaERRMSG").innerHTML='Image upload successful';
					document.getElementById("addNewMediaURL"+ComicNo).value="";
				}
				else
				{
					document.getElementById("adminAddMediaERRMSG").innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=adminAddMediaFromURL" + "&Type="+Type+"&URL="+URL+"&Artist="+Artist+"&UploadedBy="+Alias+"&Desc="+Desc, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("adminAddMediaERRMSG").innerHTML='Please input a URL';
	}
}

function uploadMediaFromLocal(Alias)
{
	var Type = document.getElementById("addFileFromLocalTypeSelect").value;
	var Artist = document.getElementById('addMediaArtistForLocal').value;
	var Desc = document.getElementById('addMediaDescriptionForLocalText').value;
	var filename= document.getElementById('addNewMediaFileLocation').value;
	
	
	if(filename)
	{
		var file = document.getElementById('addNewMediaFileLocation').files[0];
		var fd = new FormData();
		
		fd.append("uploadedFile", file);
		fd.append("Desc", Desc);
		fd.append("UploadedBy", Alias);
		fd.append("Artist", Artist);
		fd.append("Type", Type);
		
		xmlhttp = getxml();

		xmlhttp.addEventListener('progress', function(e) 
		{
			var done = e.position || e.loaded, total = e.totalSize || e.total;
			console.log('xhr progress: ' + (Math.floor(done/total*1000)/10) + '%');
		}, false);
		if ( xmlhttp.upload ) 
		{
			xmlhttp.upload.onprogress = function(e) 
			{
				var done = e.position || e.loaded, total = e.totalSize || e.total;
				console.log('xhr.upload progress: ' + done + ' / ' + total + ' = ' + (Math.floor(done/total*1000)/10) + '%');
				document.getElementById("adminAddMediaERRMSG").innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
			};
		}
		xmlhttp.onreadystatechange = function(e) 
		{
			if ( 4 == this.readyState ) 
			{
				console.log(['xhr upload complete', e]);
				document.getElementById("adminAddMediaERRMSG").innerHTML='Image upload successful';
			}
		};
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					
					document.getElementById("addNewMediaFileLocation").value="";
					document.getElementById("addMediaDescriptionForLocalText").value="";
				}
				else
				{
					document.getElementById("adminAddMediaERRMSG").innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=adminAddMediaFromLocal", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("adminAddMediaERRMSG").innerHTML='A file must be selected';
	}
}

function activateMedia(CurrentMediaURL,MediaTypeCount,MediaCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("ERRMSG"+MediaTypeCount+""+MediaCount).innerHTML = 'Activated';
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=activateMedia" + "&ImgURL="+CurrentMediaURL, true);
	xmlhttp.send();
}

function deactivateMedia(CurrentMediaURL,MediaTypeCount,MediaCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("ERRMSG"+MediaTypeCount+""+MediaCount).innerHTML = 'Deactivated';
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=deactivateMedia" + "&ImgURL="+CurrentMediaURL, true);
	xmlhttp.send();
}

function deleteMedia(CurrentMediaURL,MediaTypeCount,MediaCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Media deleted.')
			{
				document.getElementById("mediaType"+MediaTypeCount+"Media"+MediaCount).innerHTML = 'Deleted';
			}
			else
			{
				document.getElementById("ERRMSG"+MediaTypeCount+""+MediaCount).innerHTML = response;
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=removeMedia" + "&ImgURL="+CurrentMediaURL, true);
	xmlhttp.send();
}

function approveEvent(EventID,alias,UnapprovedCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Approved')
			{
				document.getElementById("unapprovedEvent"+UnapprovedCount).innerHTML = 'Approved';
			}
			else
			{
				document.getElementById("unapprovedEventMSG"+UnapprovedCount).innerHTML = response;
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=approveEvent" + "&EventID="+EventID+"&Alias="+alias, true);
	xmlhttp.send();
}

function approveNews(NewsID,alias,UnapprovedCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Approved')
			{
				document.getElementById("unapprovedNews"+UnapprovedCount).innerHTML = 'Approved';
			}
			else
			{
				document.getElementById("unapprovedNewsMSG"+UnapprovedCount).innerHTML = response;
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=approveNews" + "&NewsID="+NewsID+"&Alias="+alias, true);
	xmlhttp.send();
}

function adminDeleteComicNews(NewsID,alias,UnapprovedCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Approved')
			{
				document.getElementById("unapprovedNews"+UnapprovedCount).innerHTML = 'Approved';
			}
			else
			{
				document.getElementById("unapprovedNewsMSG"+UnapprovedCount).innerHTML = response;
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=adminDeleteNews" + "&NewsID="+EventID+"&Alias="+alias, true);
	xmlhttp.send();
}

function adminDeleteEvent(EventID,alias,UnapprovedCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			if(response == 'Deleted')
			{
				document.getElementById("unapprovedEvent"+UnapprovedCount).innerHTML = 'Deleted';
			}
			else
			{
				document.getElementById("unapprovedEventMSG"+UnapprovedCount).innerHTML = response;
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=adminDeleteEvent" + "&EventID="+EventID+"&Alias="+alias, true);
	xmlhttp.send();
}


function uploadCadenceSplashFromURL(Alias)
{
	var URL = document.getElementById("addNewMediaURL").value;
	var Artist = document.getElementById("").value;
	xmlhttp = getxml();
	   
	if(URL != '')
	{	 
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("").innerHTML='Image upload successful';
					document.getElementById(""+ComicNo).value="";
				}
				else
				{
					document.getElementById("").innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=adminUploadCadenceSplash" + "&URL="+Type+"&URL="+URL+"&Artist="+Artist+"&UploadedBy="+Alias+"&Desc="+Desc, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("adminAddMediaERRMSG").innerHTML='Please input a URL';
	}

}

function updateCadenceSplashArt()
{
	if(document.querySelector('input[name="CadenceSelector"]:checked'))
	{
		var URL = document.querySelector('input[name="CadenceSelector"]:checked').value;
	}
	else
	{
		error = error + "<br>Splash Message requires an image";
		success = false;
	}
	if(success)
	{
		
		xmlhhtp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("updateCadenceSplashMSG").value = xmlhhtp.responseText;
				var loadImage = "<img src='" + URL + "'>";
				document.getElementById("cadencePreview").innerHTML = loadImage;
				updateSplashPreview();
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=updateCadenceSplash" + "&URL="+URL, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("updateCadenceSplashMSG").value = error;
	}
}

function updateSplashPreview()
{
	xmlhhtp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("splashPreview").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=reloadPreview", true);
	xmlhttp.send();
}

function updateSplash(alias)
{
	
	var NewTitle = document.getElementById('SplashTitle').value;
	var NewMessage = document.getElementById('splashMessageTEXT').value;
		
	success = true;
	error = 'Splash not updated';
	if(document.querySelector('input[name="CadenceSelector"]:checked'))
	{
		var URL = document.querySelector('input[name="CadenceSelector"]:checked').value;
	}
	else
	{
		error = error + "<br>Splash Message requires an image";
		success = false;
	}
	if(NewTitle.trim() == '')
	{
		success = false;
		error = error + '<br>Splash Messages require a title.';
	}
	if(NewMessage.trim() == '')
	{
		error = error + '<br>Splash Messages require text.';
		success = false;
	}
	if(success)
	{
		var fd = new FormData();
		
		fd.append("Text",NewMessage);
		fd.append("Title",NewTitle);
		fd.append("Alias",alias);
		fd.append("URL",URL);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("updateSplashMSG").value = xmlhttp.responseText;
				updateSplashPreview();
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=updateSplashMessage", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("updateSplashMSG").innerHTML = error;
	}
}

function markMessageRead(MessageID,alias)
{
	xmlhttp = getxml();
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Marked as read')
				{
					document.getElementById('message'+MessageID).innerHTML = 'Marked as read';
				}
				else
				{
					document.getElementById('messageMSG'+MessageID).innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=markMessageAsRead"+"&MessageID="+MessageID + "&Alias="+alias, true);
		xmlhttp.send();			
}

function saveNewSocMedia()
{
	Class = document.getElementById('addSocialMediaIcon').value;
	Color = document.getElementById('addSocialMediaColor').value;
	Name = document.getElementById('addSocialMediaName').value;
	xmlhttp = getxml();
	success = true;
	error = "Social Media Type not uploaded";
	if(Name.trim() =='' )
	{
		success = false;
		error = error + "<br>Social Media Type needs a name";
	}
	if(success)
	{
		var fd = new FormData();
		fd.append("Color",Color);
		fd.append("Name",Name);
		fd.append("Class", Class);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Social Media Type Added')
				{
					location.reload();
				}
				else
				{
					document.getElementById('addSocMediaMSG').innerHTML = response;
				}
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=addNewSocialMediatype", true);
		xmlhttp.send(fd);			
	}
	else
	{
		document.getElementById('addSocMediaMSG').innerHTML = error;
	}
}

function adminUpdateSocialMedia(SocMediaCount)
{
	Color = document.getElementById("adminModifySocColor"+SocMediaCount).value;
	Class = document.getElementById("adminModifySocIcon"+SocMediaCount).value;
	Name = document.getElementById("adminModifySocIcon"+SocMediaCount).name;
	
	var fd = new FormData();
	
	fd.append("Color", Color);
	fd.append("Class", Class);
	fd.append("Name", Name);
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('adminSocialMediaMSG'+SocMediaCount).innerHTML = xmlhttp.responseText;	
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=adminModifySocialMedia", true);
	xmlhttp.send(fd);			
}

function adminDeleteSocialMedia(SocMediaCount)
{
	Name = document.getElementById("adminModifySocIcon"+SocMediaCount).name;
	var fd= new FormData();
	fd.append("Name",Name);
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			if(response == 'Deleted')
			{
				document.getElementById('ModSocMedia'+SocMediaCount).innerHTML = xmlhttp.responseText;	
			}
			else
			{
				document.getElementById('adminSocialMediaMSG'+SocMediaCount).innerHTML = response;	
			}
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=adminDeleteSocialMedia", true);
	xmlhttp.send(fd);			
}

function sendEmailAsNoReply(Alias)
{
	Subject = document.getElementById("messageSubjectText").value;
	Email = document.getElementById("messagerecipientSELECT").value;
	Message = document.getElementById("messageBodyText").value;
	
	if(Email.trim() == '' || Subject.trim() == '' || Message.trim() != '')
	{
		document.getElementById('sendMessageMSG').innerHTML = "All fields must be filled to send an email";	
	}
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Subject",Subject);
		fd.append("Recipient",Email);
		fd.append("Message",Message);
		fd.append("Alias",Alias);
		
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			document.getElementById('sendMessageMSG').innerHTML = response;	
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=adminSendMessageFromNoReply", true);
	xmlhttp.send(fd);			
}

function setAdStatus(AdID, Status, Alias)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("Alias", Alias);
	fd.append("AdID", AdID);
	
	if(Status == 'Rejected')
	{
		Reason = prompt("Please give a brief explanation for the rejection", "Deemed inappropriate");
		
		if(Reason.trim() != '')
		{
			xmlhttp.onreadystatechange = function()
			{
				fd.append("Reason", Reason);
				
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					response = xmlhttp.responseText;
					if(response == 'Success')
					{
						document.getElementById("managePendingAd"+AdID).innerHTML = 'Ad Rejected.';	
					}
					else
					{
						document.getElementById("managePendingAdMSG"+AdID).innerHTML = response;
					}
				}
			}
			xmlhttp.open("POST", "php/actions.php?F=adminRejectAd", true);
			xmlhttp.send(fd);			

		}
		else
		{
			document.getElementById("managePendingAdMSG"+AdID).innerHTML = 'A reason is required to reject an ad.';
		}
	}
	else if(Status == 'Approved')
	{
		xmlhttp.onreadystatechange = function()
			{
				response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("managePendingAd"+AdID).innerHTML = 'Ad Approved to begin running campaigns.';	
				}
				else
				{
					document.getElementById("managePendingAdMSG"+AdID).innerHTML = response;
				}
			}
			xmlhttp.open("POST", "php/actions.php?F=adminApproveAd", true);
			xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("managePendingAdMSG"+AdID).innerHTML = 'An invalid status was provided';
	}
}
 </script>
  <meta name="description" content="Comicadia Adminstrative Control Panel. This site is used for the administering of Comicadia news, events, users and comics. It is the back-end of the system and should only be accessed by those considered Staff to Comicadia." />
  
</head>
<title>Comicadia - Admin Panel</title>
<body>

<div id="AdminPanel">
 <div id ="topBar"><div id='home' onclick='goHome()'>Comicadia</div>
  <?php
  loadLogin();
  ?>
 </div>
 <div id="AdminWrap">
 	<div id="leftPanel">
	<?php	
	buildAdminPanel();
	?>
 	</div>
 	<div id="contentWrap">
 		<div id='contentPanel'>
		<?php
		if (isset($_GET['submit']))
		{
		  	$call = $_GET['submit'];
		  	if($call == 'Manage News')
		 	{
				buildAdminNewsPanel();
		  	}
		  	elseif($call == 'Manage Users')
		  	{
				buildAdminManageUsers();
		  	}
			elseif($call == 'Manage Webcomics')
			{
				buildAdminManageWebcomics();
			}
			elseif($call == 'Manage Events')
			{
				buildAdminManageEvents();
			}
		  	elseif($call == 'Manage Themes')
		  	{
				buildAdminManageThemes();
		  	}
			elseif($call == 'Manage Genres')
			{
				buildAdminManageGenres();
			}
			elseif($call == 'Manage Media')
			{
				buildAdminManageMedia();
			}
			elseif($call == 'Manage Splash')
			{
				buildAdminManageSplash();
			}
			elseif($call == 'Manage Messages')
			{
				buildAdminManageMessages();
			}
			elseif($call == 'Social Media Types')
			{
				buildAdminManageSocialMediaTypes();
			}
			elseif($call == 'Manage Ads')
			{
				buildAdminManageAds($alias);
			}
			else
			{
				buildAdminDashboard();
			}
		
		}
		else 
		{
			print("<div id='WelcomeDIV'>Welcome $alias. What would you like to do today?");
			buildAdminDashboard();
		}
		?>
		</div>
	</div>
	</div>
</div>

</body>
</html>