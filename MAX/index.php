<?php

include '../php/GUI.php';
include './php/MAXGUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="https://www.comicadia.com/font-awesome/css/font-awesome.min.css">
<link href="../style.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<!-- Optional Bootstrap theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

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
	location.href = 'https://www.comicadia.com/index.php';	
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

function signUserUpForMAXRound(Alias, MAXID)
{
	confirmSignup = confirm("Signing up for a MAX round means you will be required to finish one drawing and submit it by the end of the round. If you do not, you will not receive your art and will be put denied entry into future rounds until your entry is submitted.");
	if(confirmSignup)
	{
		xmlhttp = getxml();
		var fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("MAXID",MAXID);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById(MAXID+"ERRMSG").innerHTML = xmlhttp.responseText;
				
			}
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=signUpForMAX", true);
		xmlhttp.send(fd);
	}
	
}

function withdrawUserFromMAXRound(Alias, MAXID)
{
	xmlhttp = getxml();
	var fd = new FormData();
	fd.append("Alias",Alias);
	fd.append("MAXID",MAXID);
	
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById(MAXID+"ERRMSG").innerHTML = xmlhttp.responseText;
			
		}
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=withdrawFromMAX", true);
	xmlhttp.send(fd);
}

function addCharacter(Alias)
{
	Name = document.getElementById("characterNameText").value;
	Age = document.getElementById("characterAgeText").value;
	Gender = document.getElementById("characterGenderText").value;
	Race = document.getElementById("characterRaceText").value;
	Hair = document.getElementById("characterHairText").value;
	Eyes = document.getElementById("characterEyesText").value;
	Height = document.getElementById("characterHeightText").value;
	Weight = document.getElementById("characterWeightText").value;
	Writeup = document.getElementById("characterWriteupText").value;
	Webcomic = document.getElementById("characterWebcomicSelect").value;
	if(Name.trim() == '')
	{
		document.getElementById("addCharacterMSG").innerHTML = "Character name cannot be blank";
	}
	else
	{
		xmlhttp = getxml();
		var fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("Name",Name);
		fd.append("Age",Age);
		fd.append("Gender",Gender);
		fd.append("Race",Race);
		fd.append("Hair",Hair);
		fd.append("Eyes",Eyes);
		fd.append("Height",Height);
		fd.append("Weight",Weight);
		fd.append("Writeup",Writeup);
		fd.append("Webcomic",Webcomic);
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("addCharacterMSG").innerHTML = xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=addCharacterToUser", true);
		xmlhttp.send(fd);
	}
}

function checkIfFileOver2Megs(filesize)
{
	if(filesize >= 2000000)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function uploadNewReferenceFromLocal(Alias,CharacterID)
{
	var filename= document.getElementById('uploadNewReferenceFromLocalFile'+CharacterID).value;
	
	var fd = new FormData();
	
	if(filename)
	{
		var file = document.getElementById('uploadNewReferenceFromLocalFile'+CharacterID).files[0];
		var filesize = file.size;
		
		if(checkIfFileOver2Megs(filesize))
		{
			document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML="File size cannot exceed 2 megabytes";
		}
		else
		{
			fd.append("uploadedFile", file);
			fd.append("Alias", Alias);
			fd.append("CharacterID",CharacterID);
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
					document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
				};
			}
			xmlhttp.onreadystatechange = function(e) 
			{
				if ( 4 == this.readyState ) 
				{
					console.log(['xhr upload complete', e]);
					document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML='Image upload successful';
				}
			};
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
					var response = xmlhttp.responseText;
					if(response == 'Success')
					{
						
						document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML="Success";
						document.getElementById("uploadNewReferenceFromLocalFile"+CharacterID).value="";
					}
					else
					{
						document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML=response;
					}
			}
			xmlhttp.open("POST", "php/MAXactions.php?F=addReferenceForCharacterFromLocal", true);
			xmlhttp.send(fd);
		}
	}
	else
	{
		document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML="No file was selected.";
	}
}

function uploadNewReferenceFromWeb(Alias, CharacterID)
{
	var URL = document.getElementById("uploadNewReferenceFromWebURL"+CharacterID).value;
	xmlhttp = getxml();
	if(URL)
	{
		var fd = new FormData();
		fd.append("URL", URL);
		fd.append("Alias", Alias);
		fd.append("CharacterID",CharacterID);
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML=response;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=addReferenceForCharacterFromURL", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("uploadReferenceMSG"+CharacterID).innerHTML='Please input a URL';
	}
}

function deleteReference(ReferenceURL,CharacterID,activeRef)
{
	var confirmDelete = confirm("Are you sure you wish to delete this reference?");
	if(confirmDelete)
	{
		fd = new FormData();
		fd.append("ImgURL",ReferenceURL);
		fd.append("CharacterID",CharacterID);
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				document.getElementById("character"+CharacterID+"Reference"+activeRef).innerHTML=response;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=deleteReferenceForCharacterByURL", true);
		xmlhttp.send(fd);
	}

}

function setThisCharacterAsPreferred(Alias,CharacterID)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("CharacterID",CharacterID);
	fd.append("Alias",Alias);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			var response = xmlhttp.responseText;
			document.getElementById("Character"+CharacterID+"PreferredMSG").innerHTML = xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=setCharacterAsPreferred", true);
	xmlhttp.send(fd);
}

function setReferenceAsThumbnail(ReferenceURL,CharacterID,activeRef)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("ImgURL",ReferenceURL);
	fd.append("CharacterID",CharacterID);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("character"+CharacterID+"Reference"+activeRef+"MSG").innerHTML=xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=setReferenceAsThumbnail", true);
	xmlhttp.send(fd);
}

function clearCharacterThumbnail(CharacterID,activeRef)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("CharacterID",CharacterID);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("character"+CharacterID+"Reference"+activeRef+"MSG").innerHTML=xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=removeReferenceAsThumbnail", true);
	xmlhttp.send(fd);
}

function removeCharacterAsPreferred(Alias, CharacterID)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("Alias",Alias);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("Character"+CharacterID+"PreferredMSG").innerHTML = xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=removeCharacterAsPreferred", true);
	xmlhttp.send(fd);
}

function uploadLateEntryFromLocal(Alias, RecipientAlias, MAXID)
{
	var filename= document.getElementById('submitEntryFromLocalFile'+MAXID).value;
	var CharacterID = document.getElementById("submitEntryCharacterSelect"+MAXID).value;
	var Comments = document.getElementById("submitEntryCommentText"+MAXID).value;
	var fd = new FormData();
	if(filename)
	{
		var file = document.getElementById('submitEntryFromLocalFile'+MAXID).files[0];
		var filesize = file.size;
		
		if(checkIfFileOver2Megs(filesize))
		{
			document.getElementById("uploadEntryMSG"+MAXID).innerHTML="File size cannot exceed 2 megabytes";
		}
		else
		{
			fd.append("uploadedFile", file);
			fd.append("Alias", Alias);
			fd.append("Recipient", RecipientAlias);
			fd.append("MAXID",MAXID);
			fd.append("CharacterID",CharacterID);
			fd.append("Comments",Comments);
			
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
					document.getElementById("uploadEntryMSG"+MAXID).innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
				};
			}
			xmlhttp.onreadystatechange = function(e) 
			{
				if ( 4 == this.readyState ) 
				{
					console.log(['xhr upload complete', e]);
					document.getElementById("uploadEntryMSG"+MAXID).innerHTML='Image upload successful';
				}
			};
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
					var response = xmlhttp.responseText;
					if(response == 'Success')
					{		
						document.getElementById("uploadEntryMSG"+MAXID).innerHTML="Success";
						document.getElementById("submitEntryFromLocalFile"+MAXID).value="";
						reloadUserEntry(Alias, MAXID);
					}
					else
					{
						document.getElementById("uploadEntryMSG"+MAXID).innerHTML=response;
					}
			}
			xmlhttp.open("POST", "php/MAXactions.php?F=addLateUserEntryForMAX", true);
			xmlhttp.send(fd);
		}
	}
	else
	{
		document.getElementById("uploadEntryMSG"+MAXID).innerHTML="No file was selected.";
	}
}

function uploadEntryFromLocal(Alias, RecipientAlias, MAXID)
{
	var filename= document.getElementById('submitEntryFromLocalFile').value;
	var CharacterID = document.getElementById("submitEntryCharacterSelect").value;
	var Comments = document.getElementById("submitEntryCommentText").value;
	var fd = new FormData();
	if(filename)
	{
		var file = document.getElementById('submitEntryFromLocalFile').files[0];
		var filesize = file.size;
		
		if(checkIfFileOver2Megs(filesize))
		{
			document.getElementById("uploadEntryMSG").innerHTML="File size cannot exceed 2 megabytes";
		}
		else
		{
			fd.append("uploadedFile", file);
			fd.append("Alias", Alias);
			fd.append("Recipient", RecipientAlias);
			fd.append("MAXID",MAXID);
			fd.append("CharacterID",CharacterID);
			fd.append("Comments",Comments);
			
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
					document.getElementById("uploadEntryMSG").innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
				};
			}
			xmlhttp.onreadystatechange = function(e) 
			{
				if ( 4 == this.readyState ) 
				{
					console.log(['xhr upload complete', e]);
					document.getElementById("uploadEntryMSG").innerHTML='Image upload successful';
				}
			};
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
					var response = xmlhttp.responseText;
					if(response == 'Success')
					{		
						document.getElementById("uploadEntryMSG").innerHTML="Success";
						document.getElementById("submitEntryFromLocalFile").value="";
						reloadUserEntry(Alias, MAXID);
					}
					else
					{
						document.getElementById("uploadEntryMSG").innerHTML=response;
					}
			}
			xmlhttp.open("POST", "php/MAXactions.php?F=addUserEntryForMAX", true);
			xmlhttp.send(fd);
		}
	}
	else
	{
		document.getElementById("uploadEntryMSG").innerHTML="No file was selected.";
	}
}

function reloadUserEntry(Alias, MAXID)
{
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("Alias",Alias);
	fd.append("MAXID", MAXID);
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			src = xmlhttp.responseText;
			img = document.createElement('img');

            img.src = src;
			document.getElementById("EntryPreview").remove();
            document.getElementById("EntryPreviewWrap").appendChild(img);
		}			
	}
	xmlhttp.open("POST", "php/MAXactions.php?F=loadUserEntry", true);
	xmlhttp.send(fd);
}

function reportMAXEntry(Alias,EntryID)
{
	Reason = document.getElementById("reportEntryText"+EntryID).value;
	
	if(Reason.trim() == '')
	{
		document.getElementById("reportEntryMSG"+EntryID).value = "You need to provide a reason as to why this entry should be reviewed by admins";
	}
	else
	{
		xmlhttp = getxml();
		fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("Reason",Reason);
		fd.append("EntryID", EntryID);
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
			document.getElementById("reportEntryMSG"+EntryID).innerHTML = xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=reportEntry", true);
		xmlhttp.send(fd);
	}
}
function searchMAXMembers()
{
	var Keyword = document.getElementById("searchMAXMembersText").value;
	if(Keyword.trim() == '')
	{
		window.location = "https://www.comicadia.com/MAX/?submit=Search+MAX+Members";
	}
	else
	{
		window.location = "https://www.comicadia.com/MAX/index.php" + "?Search="+Keyword+"&Fields=Users";
	}
}

function searchMAXCharacters()
{
	var Keyword = document.getElementById("searchMAXCharactersText").value;
	if(Keyword.trim() == '')
	{
		window.location = "https://www.comicadia.com/MAX/?submit=View+MAX+Characters";
	}
	else
	{
		window.location = "https://www.comicadia.com/MAX/index.php" + "?Search="+Keyword+"&Fields=Characters";
	}
}


function adoptEntry(Alias,OrphanID, EntryID)
{
	confirmAdoption = confirm("Are you sure you want to adopt this entry? You may only adopt ONE MAX Orphan at a time");
	if(confirmAdoption)
	{
		xmlhttp = getxml();
		fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("EntryID", EntryID);
		fd.append("OrphanID",OrphanID);
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("entry"+OrphanID).innerHTML = 'Claimed this adoption';
				}
				else
				{
					document.getElementById("adoptEntry"+OrphanID+"MSG").innerHTML = xmlhttp.responseText;
				}
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=adoptEntry", true);
		xmlhttp.send(fd);
	}
}
function uploadAdoptionEntryForReview(Alias,AdoptionID,EntryID)
{
	var filename= document.getElementById('adoptionUploadFile'+EntryID).value;
	var Comments = document.getElementById("adoptionCommentsText"+EntryID).value;
	
	if(filename)
	{
		var file = document.getElementById('adoptionUploadFile'+EntryID).files[0];
		var filesize = file.size;
		
		if(checkIfFileOver2Megs(filesize))
		{
			document.getElementById("uploadAdoptionEntryForReviewMSG"+EntryID).innerHTML="File size cannot exceed 2 megabytes";
		}
		else
		{
			var CharacterID = document.getElementById("adoptionCharacterSelect"+EntryID).value;
			var fd = new FormData();
			xmlhttp = getxml();
			fd.append("Alias",Alias);
			fd.append("uploadedFile", file);
			fd.append("AdoptionID",AdoptionID);
			fd.append("EntryID",EntryID);
			fd.append("CharacterID",CharacterID);
			fd.append("Comments",Comments);
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					response = xmlhttp.responseText;
					if(response == 'Success')
					{
						document.getElementById("adoptionEntry"+EntryID).innerHTML = 'Adoption Submitted';
					}
					else
					{
						document.getElementById("uploadAdoptionEntryForReviewMSG"+EntryID).innerHTML = xmlhttp.responseText;
					}				
				}			
			}
			xmlhttp.open("POST", "php/MAXactions.php?F=submitAdoptedEntryForReview", true);
			xmlhttp.send(fd);
		}
	}
	else
	{
		document.getElementById("uploadAdoptionEntryForReviewMSG"+EntryID).innerHTML = "A file is required to upload";
	}
}

function saveCharacterDetails(CharacterID)
{
	var Name = document.getElementById("character"+CharacterID+"NameText").value;
	var Age = document.getElementById("character"+CharacterID+"AgeText").value;
	var Gender = document.getElementById("character"+CharacterID+"GenderText").value;
	var Race = document.getElementById("character"+CharacterID+"RaceText").value;
	var Height = document.getElementById("character"+CharacterID+"HeightText").value;
	var Weight = document.getElementById("character"+CharacterID+"WeightText").value;
	var Hair = document.getElementById("character"+CharacterID+"HairText").value;
	var Eyes = document.getElementById("character"+CharacterID+"EyesText").value;
	var WriteUp = document.getElementById("character"+CharacterID+"WriteupText").value;
	var ComicID = document.getElementById("character"+CharacterID+"ComicSelect").value;
	if(Name.trim() == '')
	{
		document.getElementById("saveCharacterDetails"+CharacterID+"MSG").innerHTML = "Characters require at least a name";
	}
	else
	{
		fd = new FormData();
		xmlhttp = getxml();
		fd.append("CharacterID",CharacterID);
		fd.append("Name",Name);
		fd.append("Age", Age);
		fd.append("Gender",Gender);
		fd.append("Race",Race);
		fd.append("Height",Height);
		fd.append("Weight",Weight);
		fd.append("Hair",Hair);
		fd.append("Eyes",Eyes);
		fd.append("WriteUp",WriteUp);
		fd.append("ComicID",ComicID);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("saveCharacterDetails"+CharacterID+"MSG").innerHTML = xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=saveEditsToCharacterDetails", true);
		xmlhttp.send(fd);
	}
}

function retireCharacter(CharacterID)
{
	confirmRetire = confirm("Are you sure you want to retire this character? Retiring a character will make them not appear in your list of available characters");
	if(confirmRetire)
	{
		fd = new FormData();
		fd.append("CharacterID",CharacterID);
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("Character"+CharacterID).innerHTML = xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=retireCharacter", true);
		xmlhttp.send(fd);
	}
	
}

function reviveCharacter(CharacterID)
{
	fd = new FormData();
		fd.append("CharacterID",CharacterID);
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("Character"+CharacterID).innerHTML = xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=reviveCharacter", true);
		xmlhttp.send(fd);
}


function viewCharacterEntry(MAXID)
{
	var CharacterID = document.getElementById("submitEntryCharacterSelect"+MAXID).value;
	window.open("https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID&Fields=Characters");
}

function submitReferenceRequest(Alias)
{
	var Reason = document.getElementById("requestReferenceReasonText").value;
	var Character = document.getElementById("requestReferenceCharacterSelect").value;
	if(Reason.trim() == '')
	{
		document.getElementById("requestReferenceMSG").innerHTML = "You must provide a reason for why you are requesting an additional reference";
	}
	else
	{
		var fd = new FormData();
		fd.append("Reason",Reason);
		fd.append("CharacterID",Character);
		fd.append("Requester",Alias);
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("requestReferenceMSG").innerHTML = xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/MAXactions.php?F=submitRequestForReference", true);
		xmlhttp.send(fd);
	}
}
</script>

<meta name="description" content="The Multi Artist eXchange is a place where artists sign up every month to draw a random character from another artist and in return, receive a random drawing of one of their characters! Sign up today!" />

</head>
<title>Comicadia - Multi Artist eXchange</title>
<body>
<div id="MainContent">
<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
	<div id="IndexContent">
	<?php
	if (isset($_GET['submit']))
	{
		$function = $_GET['submit'];
		if($function =='Manage Profile')
		{
			if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
			{
				$alias = $_SESSION['Alias'];
				$email = $_SESSION['Email'];
				buildMAXParticipantProfile($alias);
				buildMAXManageUserReferences($alias);
				buildMAXUserPreviousRounds($alias);
			}
			else
			{
				header("Location: https://www.comicadia.com/MAX");
			}			
		}
		elseif($function =='Search MAX Members')
		{
			$articlesPerPage = 10;
			buildMAXMemberSearchDefault($articlesPerPage);
		}
		elseif($function =='View Previous MAX Rounds')
		{
			$articlesPerPage = 6;
			buildMAXPreviousRoundsWithPagination(0,$articlesPerPage);
		}
		elseif($function =='View MAX Characters')
		{
			$articlesPerPage = 20;
			buildMAXCharacterListWithPagination('',0, '', $articlesPerPage);
		}
		elseif($function == 'View Adoptions')
		{
			if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
			{
				$alias = $_SESSION['Alias'];
			}
			else
			{
				$alias = '';
			}
			$articlesPerPage = 10;
			buildMAXAdoptionListWithPagination(0,$articlesPerPage,$alias);
		}
		elseif($function == 'View MAX Blacklist')
		{
			if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
			{
				$alias = $_SESSION['Alias'];
				$email = $_SESSION['Email'];
			}
			else
			{
				$alias = '';
			}
				buildViewMAXBlacklist($alias);
		}
		else
		{
			header("Location: https://www.comicadia.com/MAX");
		}
	}
	elseif(isset($_REQUEST["Search"]) OR isset($_REQUEST['MemberAlias']) OR isset($_REQUEST['Fields']) OR isset($_REQUEST['CharacterID']))
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
		
		if(isset($_REQUEST['Fields']))
		{
			$Fields = $_REQUEST['Fields'];
		}
		// If the page number is less than 1, make it 1.
		
		if(isset($_GET["MemberAlias"]))
		{
			$MemberAlias = $_GET["MemberAlias"];
		}
		else
		{
			$MemberAlias = '';
		}
		
		if(isset($_GET["CharacterID"]))
		{
			$CharacterID = $_GET["CharacterID"];
		}
		else
		{
			$CharacterID = '';
		}
		
		if($Fields == 'Users')
		{
			$articlesPerPage = 10;
			buildMAXMemberSearchWithKeyword($Search, $MemberAlias,$pageNumber,$articlesPerPage);
		}
		elseif($Fields == 'Rounds')
		{
			$articlesPerPage = 6;
			buildMAXPreviousRoundsWithPagination($pageNumber,$articlesPerPage);
		}
		elseif($Fields == 'Characters')
		{
			$articlesPerPage = 20;
			buildMAXCharacterListWithPagination($Search,$pageNumber, $CharacterID, $articlesPerPage);
		}
		elseif($Fields == 'Adoptions')
		{
			if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
			{
				$alias = $_SESSION['Alias'];
			}
			else
			{
				$alias = '';
			}
			buildMAXAdoptionListWithPagination($pageNumber,$articlesPerPage,$alias);
		}
		else
		{
		}
	}
	elseif(isset($_REQUEST['EntryID']))
	{
		$EntryID = $_REQUEST['EntryID'];
		$Entry = getEntryOrOrphanByID($EntryID);
		if($Entry)
		{
			buildViewEntry($Entry);
		}
		else
			print("We're sorry, that image does not appear to exist.");
	}
	else
	{
		if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
		{
			$alias = $_SESSION['Alias'];
			$email = $_SESSION['Email'];
			buildMAXWelcome($alias);
		}
		else
		{
			buildMAXSplash();
			print("<div id='MAXsignup'>");
			print("<div id='MAXsignupHeader'><h2>Sign-Ups</h2></div>");
			print("Welcome to MAX. In order to sign up for a round, you must be logged in.");
			print("</div>");
		}
	}
	?>
	</div> <!-- End IndexContent-->
	<?php
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
	}
	else
	{
		$alias = '';
	}
	buildMAXSidebar($alias);
	?>		
	
	<div class="clear"></div>

	</div>  <!-- End BodyMain-->
	<?php 
	buildFooter();
	?>
	
</div>
</body>
</html>
