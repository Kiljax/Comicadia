<?php

include './php/GUI.php';
include './php/UserGUI.php';
include './php/GUI/GUI-Cpanel.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
{
	$alias = $_SESSION['Alias'];
	$email = $_SESSION['Email'];
	$type = getUserType($alias);
	if($type != 'Admin' && $type != 'Member' && $type != 'Subscriber')
	{
		header("Location: ./index.php");
	}
}
else 
{
	header("Location: ./index.php");
}
?>

<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Loading basic jquery -->
<script type="text/javascript\" src=\"https://code.jquery.com/jquery-latest.min.js"></script>
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


<!-- Javascript to load the date modifier so everyone will see their own date 
<script type="text/javascript" src="moment/moment.js"></script>
<script type="text/javascript" src="moment-timezone/moment-timezone.js"></script> -->

<!-- These two are the primary style sheets used for Comicadia-->
<link href="./css/cpanel.css" rel="stylesheet" type="text/css" />
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

		
function saveThemeEdits(Theme)
{
	var oldName = document.getElementById('Theme'+Theme).name;
	var newName = document.getElementById('Theme'+Theme).value;
	var oldRating = document.getElementById('Rating'+Theme).name;
	var newRating = document.getElementById('Rating'+Theme).value;
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
				document.getElementById('MSG'+Theme).innerHTML=xmlhttp.responseText;

		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=editTheme&OldThemeName="+oldName+"&OldThemeRating="+oldRating+"&NewThemeName="+newName+"&NewThemeRating="+newRating, true);
	xmlhttp.send();
}

function deleteTheme(Theme)
{
	var ThemeName = document.getElementById('Theme'+Theme).name;
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
					document.getElementById('EditTheme'+Theme).innerHTML='Deleted';
				}
				else
				{
					document.getElementById('MSG'+Theme).innerHTML=response;
				}
			}
		}
	xmlhttp.open("POST", "./php/actions.php?F=deleteTheme&ThemeName="+ThemeName, true);
	xmlhttp.send();
	}
}

function uploadNewProfileFromWeb(Alias)
{
	var URL = document.getElementById("uploadNewProfileFromWebURL").value;
	xmlhttp = getxml();
	if(URL)
	{
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				var response = xmlhttp.responseText;
				document.getElementById("uploadProfileMSG").innerHTML=response;
			}			
		}
		xmlhttp.open("POST", "php/actions.php?F=addProfilePicFromURL" + "&URL="+URL+"&Alias="+Alias, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("uploadProfileMSG").innerHTML='Please input a URL';
	}
}


function uploadNewProfileFromLocal(Alias)
{
	var filename= document.getElementById('uploadNewProfileFromLocalFile').value;
	
	var fd = new FormData();
	
	if(filename)
	{
		var file = document.getElementById('uploadNewProfileFromLocalFile').files[0];
		fd.append("uploadedFile", file);
		
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
				document.getElementById("uploadProfileMSG").innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
			};
		}
		xmlhttp.onreadystatechange = function(e) 
		{
			if ( 4 == this.readyState ) 
			{
				console.log(['xhr upload complete', e]);
				document.getElementById("uploadProfileMSG").innerHTML='Image upload successful';
			}
		};
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					
					document.getElementById("uploadProfileMSG").value="Succes";
					document.getElementById("uploadNewProfileFromLocalFile").value="";
				}
				else
				{
					document.getElementById("uploadProfileMSG").innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=addProfilePicFromLocal" + "&Alias="+Alias, true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("uploadProfileMSG").innerHTML="You must choose a file to upload";
	}
}

function uploadMediaFromURL(ComicID, UploadedBy, ComicNo)
{
	var Type = document.getElementById("addFileTypeSelect"+ComicNo).value;
	var URL = document.getElementById("addNewMediaURL"+ComicNo).value;
	var Artist = document.getElementById('addMediaArtist'+ComicNo).value;
	var Desc = document.getElementById('addMediaDescriptionText'+ComicNo).value;
	xmlhttp = getxml();
	   
	if(URL != '')
	{	 
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("uploadMSG"+ComicNo).innerHTML='Image upload successful';
					document.getElementById("addNewMediaURL"+ComicNo).value="";
				}
				else
				{
					document.getElementById("uploadMSG"+ComicNo).innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=addMediaFromURL" + "&Type="+Type+"&URL="+URL+"&Webcomic="+ComicID+"&Artist="+Artist+"&UploadedBy="+UploadedBy+"&Desc="+Desc, true);
		xmlhttp.send();
	}
	else
	{
		document.getElementById("uploadMSG"+ComicNo).innerHTML='Please input a URL';
	}
}

function uploadMediaFromLocal(ComicID, UploadedBy,ComicNo)
{
	var Type = document.getElementById("addFileFromLocalTypeSelect"+ComicNo).value;
	var Artist = document.getElementById('addMediaArtistForLocal'+ComicNo).value;
	var Desc = document.getElementById('addMediaDescriptionForLocalText'+ComicNo).value;
	var filename= document.getElementById('addNewMediaFileLocation'+ComicNo).value;
	
	if(filename)
	{
		var file = document.getElementById('addNewMediaFileLocation'+ComicNo).files[0];
		var fd = new FormData();
		
		fd.append("uploadedFile", file);
		
		fd.append("Type",Type);
		fd.append("Webcomic",ComicID);
		fd.append("Artist",Artist);
		fd.append("UploadedBy",UploadedBy);
		fd.append("Desc",Desc);
		
		
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
				document.getElementById("uploadMSG"+ComicNo).innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
			};
		}
		xmlhttp.onreadystatechange = function(e) 
		{
			if ( 4 == this.readyState ) 
			{
				console.log(['xhr upload complete', e]);
				document.getElementById("uploadMSG"+ComicNo).innerHTML='Image upload successful';
			}
		};
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					
					document.getElementById("addNewMediaFileLocation"+ComicNo).value="";
					document.getElementById("addMediaDescriptionForLocalText"+ComicNo).value="";
				}
				else
				{
					document.getElementById("uploadMSG"+ComicNo).innerHTML=response;
				}
		}
		xmlhttp.open("POST", "php/actions.php?F=addMediaFromLocal", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("uploadMSG"+ComicNo).innerHTML='A file must be selected';
	}
}

function postNews(Alias)
{
	var Title = document.getElementById('newsTitleText').value;
	var Details = document.getElementById('newsDetailsText').value;	
	var Category = document.getElementById('newsCategorySelect').value;
	var epoch = document.getElementById('newsDatepicker').value;
	xmlhttp = getxml();
	
	var fd = new FormData();
	
	fd.append("Alias", Alias);
	fd.append("Title", Title);
	fd.append("Details", Details);
	fd.append("Category", Category);
	fd.append("PubDate", epoch);
		
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('PostMSG').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=postNews", true);
	xmlhttp.send(fd);
	
}

function saveNewsEdits(NewsCount, DateWritten, Alias, Status)
{
	var Title = document.getElementById('newsTitleFor' + NewsCount).value;
	var Details = document.getElementById('newsDetailsFor'+NewsCount).value;
	var Category = document.getElementById('newsCategoryFor'+NewsCount).value;
	var PubDate = document.getElementById('editDatepickerFor'+NewsCount).value;
	
	var fd = new FormData();
	
	fd.append("Alias", Alias);
	fd.append("Title", Title);
	fd.append("Details", Details);
	fd.append("Category", Category);
	fd.append("PubDate", PubDate);
	fd.append("NewsID", DateWritten);
	fd.append("Status", Status);
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('newsErrMSG'+NewsCount).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=editNews", true);
	xmlhttp.send(fd);
}

function saveProfileChanges()
{
	var FirstName = document.getElementById("FirstNameText").value;
	var LastName = document.getElementById("LastNameText").value;
	var NewAlias = document.getElementById("AliasText").value;
	var OldAlias = document.getElementById("AliasText").name;
	var NewEmail = document.getElementById("EmailText").value;
	var OldEmail = document.getElementById("EmailText").name;
	
	var fd = new FormData();
	
	fd.append("FirstName", FirstName);
	fd.append("LastName", LastName);
	fd.append("NewAlias", NewAlias);
	fd.append("OldAlias", OldAlias);
	fd.append("NewEmail", NewEmail);
	fd.append("OldEmail", OldEmail);
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		response = xmlhttp.responseText;
		if(response == 'Save Successful.')
		{
			document.getElementById("AliasText").name = document.getElementById("AliasText").value;
			document.getElementById("EmailText").name = document.getElementById("EmailText").value;
		}
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('updateProfileMSG').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=saveUserProfileEdits", true);
	xmlhttp.send(fd);
}

function resetPassword()
{
	var Email = document.getElementById("EmailText").name;
	var currentPassword = document.getElementById("CurrentPasswordText").value;
	var confirmPassword = document.getElementById("ConfirmPasswordText").value;
	var newPassword = document.getElementById("NewPasswordText").value;
	
	if(currentPassword != '' && confirmPassword != '' && newPassword != '')
	{
		var fd = new FormData();
	
		fd.append("NewPassword" , newPassword); 
		fd.append("Email" , Email); 
		fd.append("OldPassword" , currentPassword); 
	
		if(confirmPassword == newPassword)
		{
			xmlhttp = getxml();
			xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					document.getElementById('changePasswordMSG').innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "./php/actions.php?F=resetUserPassByUser", true);
			xmlhttp.send(fd);
		}
		else
		{
			document.getElementById("changePasswordMSG").innerHTML="Please ensure you typed the new password correctly in both fields.";
		}
	}
	else
	{
		document.getElementById("changePasswordMSG").innerHTML="All fields must be filled to reset your password";
	}
}

function updateOwnRoles(ComicID, Alias, SelectNo)
{
	var Roles = document.getElementById("UserRolesText"+SelectNo).value;
	
	Roles = encordeURI(Roles);
	
	if(Roles =='')
	{
		document.getElementById('updateRolesMSG'+SelectNo).innerHTML='You must have one role associated to your comic';
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('updateRolesMSG'+SelectNo).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=saveUserRole&Alias="+Alias+"&Role="+Roles+"&ComicID="+ComicID, true);
		xmlhttp.send();
	}
}

function addCrewToWebcomic(ComicID, SelectNo)
{
	var NewCrewAlias = document.getElementById("addCrewSelect"+SelectNo).value;
	var NewCrewRoles = document.getElementById("addCrewRolesText"+SelectNo).value;
	
	NewCrewRoles = encordeURI(NewCrewRoles);
	
	if(NewCrewRoles == '')
	{
		document.getElementById('addCrewMSG'+SelectNo).innerHTML='A crewmate must have at least one role.';
	}
	else
	{
		xmlhttp = getxml();
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('addCrewMSG'+SelectNo).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=addCrew&Alias="+NewCrewAlias+"&Roles="+NewCrewRoles+"&ComicID="+ComicID, true);
		xmlhttp.send();
	}
}

function removeCrew(Alias, CrewCount, SelectNo, ComicID)
{
	var confirmDelete = confirm("Do you want to remove "+Alias+" from this comic?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'This person will be removed.')
				{
					document.getElementById("comicCrew"+SelectNo+CrewCount).innerHTML = 'Crew Removed';
				}
				else
				{
					document.getElementById('editCrewRoleMSG'+SelectNo+CrewCount).innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=removeCrew&Alias="+Alias+"&ComicID="+ComicID, true);
		xmlhttp.send();
	}
	
}

function updateCrewRoles(Alias,CrewCount,SelectNo,ComicID)
{
	var Roles = document.getElementById("editRoles"+SelectNo+CrewCount).value;
	
	if(Roles == '')
	{
		document.getElementById("editCrewRoleMSG"+SelectNo+CrewCount).innerHTML = "Roles cannot be blank";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById('editCrewRoleMSG'+SelectNo+CrewCount).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=saveUserRole&Alias="+Alias+"&Role="+Roles+"&ComicID="+ComicID, true);
		xmlhttp.send();
	}

}

function saveWebcomicProfile(ComicID, SelectNo)
{
	var oldComicName = document.getElementById("webcomicTitleFor"+SelectNo).name;
	var newComicName = document.getElementById("webcomicTitleFor"+SelectNo).value;
	var Synopsis = document.getElementById("webcomicSynopsisFor"+SelectNo).value;
	var OldURL = document.getElementById("webcomicURLFor"+SelectNo).name;
	var URL = document.getElementById("webcomicURLFor"+SelectNo).value;
	var OldRSS = document.getElementById("webcomicRSSFor"+SelectNo).value;
	var RSS = document.getElementById("webcomicRSSFor"+SelectNo).value;
	var Format = document.getElementById("webcomicFormatFor"+SelectNo).value;
	var Pitch = document.getElementById("webcomicPitchFor"+SelectNo).value;
		
	var Success = true;
	
	var Response = "Changes not saved.";
	if(newComicName =='')
	{
		Response = Response + "<br>Comics must have a name.";
		Success = false;
	}
	if(URL == '')
	{
		Response = Response + "<br>URL cannot be blank.";
		Success = false;
	}
	if(RSS == '')
	{
		Response = Response + "RSS cannot be blank";
		Success = false;
	}
	if(Success == true)
	{
		xmlhttp = getxml();
		var fd = new FormData();
		
		fd.append("ComicID", ComicID);
		fd.append("OldName", oldComicName);
		fd.append("NewName", newComicName);
		fd.append("Synopsis", Synopsis);
		fd.append("URL", URL);
		fd.append("RSS", RSS);
		fd.append("OldURL", OldURL);
		fd.append("OldRSS", OldRSS);
		fd.append("Format", Format);
		fd.append("Pitch", Pitch);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Changes saved.')
				{
					document.getElementById("webcomicURLFor"+SelectNo).name = URL;
					document.getElementById("webcomicRSSFor"+SelectNo).name = RSS;
					document.getElementById("webcomicTitleFor"+SelectNo).name = newComicName;
					document.getElementById('editWebcomicMSG'+SelectNo).innerHTML=response;
				}
				else
				{
					document.getElementById('editWebcomicMSG'+SelectNo).innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=saveWebcomicEditsFromUser", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("editWebcomicMSG"+SelectNo).innerHTML=Response;
	}
}

function deleteMedia(ImgURL,DivName,MediaCount)
{
	var confirmDelete = confirm("Are you sure you want to delete image completely?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Media deleted.')
				{
					document.getElementById("media"+DivName+MediaCount).innerHTML =  'Media Deleted';
				}
				else
				{
					document.getElementById("MSG"+DivName+MediaCount).innerHTML=response;
				}
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=removeMedia&ImgURL="+ImgURL, true);
		xmlhttp.send();
	}	
}

function activateMedia(ImgURL, DivName, MediaCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("MSG"+DivName+MediaCount).innerHTML='Activated';
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=activateMedia&ImgURL="+ImgURL, true);
		xmlhttp.send();
}

function deActivateMedia(ImgURL, DivName, MediaCount)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("MSG"+DivName+MediaCount).innerHTML='Deactivated';
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=deactivateMedia&ImgURL="+ImgURL, true);
		xmlhttp.send();
}

function saveGenres(ComicID, ComicNo)
{
	var Genre1 = document.getElementById("FirstGenre"+ComicNo).value;
	var Genre2 = document.getElementById("SecondGenre"+ComicNo).value;
	var Genre3 = document.getElementById("ThirdGenre"+ComicNo).value;
	
	if(Genre1 == '' && Genre2 == '' && Genre3 == '')
	{
		document.getElementById("saveGenreMSG"+ComicNo).innerHTML= "No Genres have been selected.";
	}
	else if(Genre1 == Genre2 || Genre2 == Genre3 || Genre1 == Genre3)
	{
		document.getElementById("saveGenreMSG"+ComicNo).innerHTML= "Genres cannot be duplicates.";
	}
	else
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("saveGenreMSG"+ComicNo).innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=saveGenres&ComicID="+ComicID+"&Genre1="+Genre1+"&Genre2="+Genre2+"&Genre3="+Genre3, true);
		xmlhttp.send();
	}
}

function saveThemesForWebcomic(ComicID, ComicNo)
{
	var ThemeArray = getCheckedBoxes("themeCheckbox"+ComicNo);
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
					document.getElementById("ThemeMSG"+ComicNo).innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "./php/actions.php?F=saveThemes&ComicID="+ComicID+"&ThemeList="+ThemeString, true);
			xmlhttp.send();
		}
		else
		{
			document.getElementById("ThemeMSG"+ComicNo).innerHTML= "Please only select up to 10 themes";
		}
	}
	else
	{
		document.getElementById("ThemeMSG"+ComicNo).innerHTML= "No Themes selected";
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

function scheduleUserEvent(Alias)
{
	var Title = document.getElementById("addEventTitleText").value;
	var PubDate = document.getElementById("addEventDateText").value;
	var Details = document.getElementById("addEventDetailsText").value;
	var Category = document.getElementById("addEventCategorySELECT").value;
	var Location = document.getElementById("addEventLocationText").value;
	var Type = document.getElementById("addEventTypeSELECT").value;
	
		
	var success = true;
	var error = "Event not created";
	if(Title =='')
	{
		success = false;
		error = error+"<br>Event must have a title";
	}		
	if(PubDate =='')
	{
		success = false;
		error = error+"<br>Event must have a Date";
	}		
	if(Details =='')
	{
		success = false;
		error = error+"<br>Event must have some details";
	}		
	if(Category =='')
	{
		success = false;
		error = error+"<br>Event must have a category";
	}		
	if(Type =='')
	{
		success = false;
		error = error+"<br>Event must have a Type";
	}		
	if(success == true)
	{
		var fd = new FormData();
		fd.append("Title",Title);
		fd.append("PubDate",PubDate);
		fd.append("Details",Details);
		fd.append("Location",Location);
		fd.append("Category",Category);
		fd.append("Type",Type);
		fd.append("Alias",Alias);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					document.getElementById("addEventMSG").innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "./php/actions.php?F=addEventFromUser", true);
			xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("addEventMSG").innerHTML = error;
	}
}


function saveEventEdits(DivID, Organizer)
{
	var DateWritten = document.getElementById("Location"+DivID).name;
	var Title = document.getElementById('Title'+DivID).value;
	var PubDate = document.getElementById('editEventDate'+DivID).value;
	var Location = document.getElementById('Location'+DivID).value;
	var Details = document.getElementById('Details'+DivID).value;
	var Category = document.getElementById('Category'+DivID).value;
	var Type = document.getElementById('Type'+DivID).value;
	var oldPubDate = document.getElementById("Details"+DivID).name;
	var success = true;
	var error = 'Edits not saved';
		
	if(Title =='')
	{
		success = false;
		error = error+"<br>An event must have a title";
	}
	if(PubDate =='')
	{
		success = false;
		error = error+"<br>An event must have a start time";
	}
	
	if(Details =='')
	{
		success = false;
		error = error+"<br>An event must have details";
	}
	if(success == true)
	{
		var fd = new FormData();
		fd.append("Title",Title);
		fd.append("Details",Details);
		fd.append("Location",Location);
		fd.append("Category",Category);
		fd.append("Alias",Organizer);
		fd.append("Type",Type);
		fd.append("DateWritten",DateWritten);
		fd.append("oldPubDate",oldPubDate);
		fd.append("PubDate",PubDate);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					document.getElementById("errMSGEvent"+DivID).innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.open("POST", "./php/actions.php?F=saveUserEditEvents", true);
			xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("errMSGEvent"+DivID).innerHTML=error;
	}
	
}

function deleteEvent(DateWritten,DivID)
{
	var confirmDelete = confirm("Are you sure you wish to delete this event?");
	if(confirmDelete)
	{
		var xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
			{
				if(xmlhttp.readyState == XMLHttpRequest.DONE)
				{
					response = xmlhttp.responseText;
					if(response == 'Deleted')
					{
						document.getElementById("Event"+DivID).innerHTML = 'Event Deleted';
					}
					else
					{
						document.getElementById("errMSGEvent"+DivID).innerHTML=xmlhttp.responseText;
					}
				}
			}
			xmlhttp.open("POST", "./php/actions.php?F=deleteUserEvent&DateCreated="+DateWritten, true);
			xmlhttp.send();
	}
}

function addWebcomic(Alias)
{
	var Genre1 = document.getElementById("FirstGenre").value;
	var Genre2 = document.getElementById("SecondGenre").value;
	var Genre3 = document.getElementById("ThirdGenre").value;
	var ThemeArray = getCheckedBoxes("themeCheckbox");
	var Title = document.getElementById("webcomicNameText").value;
	var URL = document.getElementById("webcomicURLText").value;
	var RSS = document.getElementById("webcomicRSSText").value;
	var Synopsis = document.getElementById("addWebcomicSynopsisText").value;
	var Pitch = document.getElementById("addWebcomicPitchText").value;
	var Format = document.getElementById("addWebcomicFormatSelect").value;
	var success = true;
	
	var error = 'Webcomic not added';
	
	if(Genre1 == '' && Genre2 == '' && Genre3 == '')
	{
		success = false;
		error = error +"<br>You must select at least one genre";
	}
	else
	{
		if(Genre1 != Genre2 && Genre2 != Genre3 && Genre3 != Genre1)
		{
		}
		else
		{
			success = false;
			error = error +"<br>You cannot choose the same genre twice";
		}
	}
	
	if(Title == '')
	{
		success = false;
		error = error+"<br>A webcomic requires a title";
	}
	if(URL == '')
	{
		success = false;
		error = error+"<br>A webcomic requires  URL";
	}
	if(RSS == '')
	{
		success = false;
		error = error+"<br>A webcomic requires an RSS link";
	}
	if(Synopsis == '')
	{
		success = false;
		error = error+"<br>A webcomic requires a synopsis";
	}
	
	if(ThemeArray.length > 0)
	{	
		if(ThemeArray.length < 11)
		{
			var ThemeString = '';
	
			for(i = 0; i< ThemeArray.length;++i)
			{
				ThemeString = ThemeString + ThemeArray[i] + ",";
			}
		}
		else
		{
			success = false;
			error = error+"<br>You can select up to 10 themes";
		}
	}
	else
	{
		success = false;
		error = error+"<br>You must select at least one theme for your comic";
	}
	
	if(success == true)
	{
		var fd = new FormData();
		
		fd.append("Title",Title);
		fd.append("URL",URL);
		fd.append("RSS",RSS);
		fd.append("Synopsis",Synopsis);
		fd.append("Genre1",Genre1);
		fd.append("Genre2",Genre2);
		fd.append("Genre3",Genre3);
		fd.append("ThemeArray",ThemeString);
		fd.append("Alias",Alias);
		fd.append("Format",Format);
		fd.append("Pitch",Pitch);
		
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("addWebcomicMSG").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=addUserWebcomic&Title=", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("addWebcomicMSG").innerHTML = error;
	}
}

function removeNews(NewsCount, DateWritten, Alias)
{
	var confirmDelete = confirm("Are you sure you want to delete this news item?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById("NewsItem"+NewsCount).innerHTML='Deleted';
				}
				else
				{
					document.getElementById('newsErrMSG'+NewsCount).innerHTML= response;
				}
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=deleteNewsPost&DateWritten="+DateWritten+"&Alias="+Alias, true);
		xmlhttp.send();
	}
}

function deleteWebcomic(ComicID,Alias)
{
	var confirmDelete = confirm("Are you sure you want to delete this webcomic? Once deleted, it cannot be recovered!")
	if(confirmDelete)
	{
		xmlhttp = getxml();
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				response = xmlhttp.responseText;
				if(response == 'Success')
				{
					document.getElementById(""+DivName+"Details").innerHTML='Deleted';
				}
				else
				{
					document.getElementById('editWebcomicMSG'+DivName).innerHTML= response;
				}
			}
		}
		xmlhttp.open("POST", "./php/actions.php?F=deleteWebcomicByCreator&ComicID="+ComicID+"&Alias="+Alias, true);
		xmlhttp.send();
	}
}

function updateUserSocialMedia(Alias,NameList)
{
	var Handles = [];
	for (var i = 0; i< NameList.length; i++)
	{
		Handles.push(document.getElementById('user'+NameList[i]+'Text').value);
	}
	var fd = new FormData();
	fd.append("Alias", Alias);
	fd.append("SocialMedias",JSON.stringify(NameList));
	fd.append("UserHandles",JSON.stringify(Handles));
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('userSocialMediaMSG').innerHTML= xmlhttp.responseText;
		
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=updateUserSocialMedia", true);
	xmlhttp.send(fd);
}

function updateComicSocialMedia(ComicID,DivAdd,NameList)
{
	var Handles = [];
	for (var i = 0; i< NameList.length; i++)
	{
		Handles.push(document.getElementById('comic'+NameList[i]+'Text'+DivAdd).value);
	}
	var fd = new FormData();
	fd.append("ComicID", ComicID);
	fd.append("SocialMedias",JSON.stringify(NameList));
	fd.append("UserHandles",JSON.stringify(Handles));
	
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('comicSocialMediaMSG'+DivAdd).innerHTML= xmlhttp.responseText;
		
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=updateComicSocialMedia", true);
	xmlhttp.send(fd);
}

  </script>
<meta name="description" content="The User Control Panel for Comicadia Subscribers. From here you can add webcomics that you work on or manage your profile. If the comic you add is a member of Comicadia, then you will be able to manage more in-depth details about your profile, as well as your comic's profile." />  
  <?php 
	loadGoogleAds();
?>


</head>
<title>Comicadia - User Control Panel</title>
<body>

<div id="UserPanel">
 <div id ="topBar"><div id='home' onclick='goHome()'><img src="media/ComicadiaHeader-low.png" title="Comicadia"></div>
  <?php
  loadLogin();
  ?>
 </div>
 <div id="AdminWrap">
 	<div id="leftPanel">
	<?php	
	buildControlPanel();
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
		  		buildUserWriteNews();
		  		buildUserEditNews();
		  	}
		  	elseif($call == 'Edit Profile')
		  	{
				buildEditProfile();			  	
		  	}
		  	elseif($call == 'Manage Webcomics')
			{
		  		buildUserAddWebcomic();
		  		buildUserManageWebcomics();
		  	}
		  	elseif($call == 'Manage Events')
		  	{
		  		buildUserAddEvent();
		  		buildUserEditEvent();
		  	}
			elseif($call == 'Manage Merch')
			{
				buildUserManageMerch();
			}
			else
			{
				print("This call is not supported.");
			}
		}
		else 
		{
			print("<div id='WelcomeDIV'>Welcome $alias. What would you like to do today?<div class='clear'></div> ");
			buildUserDashboard();
		}
		?>
		</div>
	</div>
	<div class='clear'></div></div>
</div>

</body>
</html>