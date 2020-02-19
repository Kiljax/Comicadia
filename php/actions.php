<?php
include 'functions.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

$function = $_REQUEST["F"];

if($function == 'Login')
{
	
	$Email = strtolower($_REQUEST['Email']);
	$Password = $_REQUEST['Password'];
	if($Email == '' || $Password == '')
	{
		print('Both Fields Required');
	}
	else 
	{
		if(Login($Email, $Password))
		{
			print("Success");
		}
		else
		{
			print('Invalid login');
		}
	}
}

if($function =='loadUser')
{
	$searchEmail = strtolower($_REQUEST['Alias']);
	$UserTypes = getUserTypes();
	$User = getUserDetails($searchEmail);
	$UserAlias = $User->value('Alias');
	$FirstName = $User->value('FirstName');
	$LastName = $User->value('LastName');
	$Type = $User->value('Type');
	$UserEmail = $User->value('Email');
	print("<form>
	<div id='editUser'>
	<div id='PersonalInfo'></div>
	<div id='editAlist' class='Personal'><strong>Alias:</strong><input type='text' name='$UserAlias' id='editAliasText' value='$UserAlias'></div>
	<div id='editFirstName' class='Personal'><strong>First Name:</strong><input type='text' id='editFirstNameText' value='$FirstName'></div>
	<div id='editLastName' class='Personal'><strong>Last Name</strong><input type='text' id='editLastNameText' value='$LastName'></div>	
	<div id='editEmail' class='Personal'><strong>Email:</strong><input type='email' name='$UserEmail' id='editEmailText' value='$UserEmail'></div>
	<div id='editUserType' class='Administration'><strong>Member type:</strong>
	<br><select id='UserTypeSELECT'>");
	foreach($UserTypes as $record)
	{
		if($record == $Type)
		{
			$Selected = 'Selected';
		}
		else 
		{
			$Selected = '';
		}
		print("<option value='$record' $Selected>$record</option>");
	}
	print("</select>
	<div id='PassDiv'><strong>Password: </strong><input type='button' id='editPasswordBTN' onclick='adminResetPass()' value='Reset Password'></div>
	<input type='button' id='saveEdits 'name='submit' value='Save' onclick='saveUserEdits()'>
	</form>
	<div id='saveERR' class='ErrMSG'></div>
	</div>");
}

if($function == 'resetUserPassByUser')
{
	$OldPassword = $_REQUEST['OldPassword'];
	$NewPassword = $_REQUEST['NewPassword'];
	$Email = $_REQUEST['Email'];
	$Success = TRUE;
	
	$Error = "Password not changed.";
	
	if($Email =='')
	{
		$Success = FALSE;
		$Error = $Error."<br>No user exists with that email";
	}
	if($NewPassword == '')
	{
		$Success = FALSE;
		$Error = $Error."<br>New password cannot be blank";
	}
	if($OldPassword == '')
	{
		$Success = FALSE;
		$Error = $Error."<br>Old password cannot be blank";
	}
	if($Success)
	{
		if(Login($Email, $OldPassword))
		{
			$HashedPass = password_hash($NewPassword, PASSWORD_DEFAULT);
			resetUserPass($Email, $HashedPass);
			print("Password has been changed");
		}
		else
		{
			print("That is not the current password.");
		}
	}
}

if($function == 'resetUserPass')
{
	$Alias = $_REQUEST['Alias'];
	$UserDetails =getUserDetails($Alias);
	$Email = $UserDetails->value("Email");
	$Password = $_REQUEST['Password'];
	$Success = TRUE;
	$Error = 'Password not changed';
	if($Email == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>No user selected';
	}
	if($Password == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>New password cannot be blank';
	}
	if($Success)
	{
	resetUserPass($Email, password_hash($Password, PASSWORD_DEFAULT));
	print('Password updated');
	}
	else 
	{
		print($Error);
	}
}

if($function =='saveUser')
{	
	$Alias = $_REQUEST['Alias'];
	$Email = $_REQUEST['Email'];
	$FirstName = $_REQUEST['FirstName'];
	$LastName = $_REQUEST['LastName'];
	$Type = $_REQUEST['UserType'];
	$Password = $_REQUEST["Password"];
	$Success = TRUE;
	$Error = "User not created";
	
	if($FirstName == '')
	{
		$Error = $Error. '<br>First Name is required';
		$Success = FALSE;
	}

	if($LastName == '' )
	{
		$Error = $Error. '<br>Last Name is required';
		$Success = FALSE;
	}
	
	if($Alias == '')
	{
		$Error = $Error . '<br>An Alias is required';
		$Success = FALSE;
	}
	
	if($Password == '')
	{
		$Error = $Error . '<br>A password is required';
		$Success = FALSE;
	}
	
	if($Email == '')
	{
		$Error = $Error . '<br>Email cannot be blank';
		$Success = FALSE;
	}
	else 
	{	
		if (!filter_var($Email, FILTER_VALIDATE_EMAIL) === false) 
		{
  		} 
  		else 
  		{
  			$Success = FALSE;
			$Error = $Error . "<br>$Email is not a valid email address";
		}
	}	
	
	if(checkAlias($Alias) > 0)
	{
		$Success = FALSE;
		$Error = $Error . '<br>Alias already exists';
	}
	
	if(checkEmail($Email) > 0 )
	{
		$Success = FALSE;
		$Error = $Error . '<br> Email Address already in use';
	}
	
	if($Success)
	{
		print('Save Successful.');
		createUser($FirstName, $LastName,$Alias,$Email,$Type,$Password);
	}
	else 
	{
		print("Save unsuccessful. $Error");
	}
}

if($function =='addWebcomic')
{
	$ComicName = $_REQUEST['ComicName'];
	$ComicURL = $_REQUEST['ComicURL'];
	$ComicRSS = $_REQUEST['ComicRSS'];
	$Email = $_REQUEST['Creator'];
	$Membership = $_REQUEST['Membership'];
	$Status = $_REQUEST['Status'];
	$Success = TRUE;
	$Error = 'Webcomic not created';
	if($ComicName != '')
	{
		if(checkDuplicateWebcomicName($ComicName) > 0)
		{
			$Error = $Error .'<br>A Webcomic with that name already exists';
			$Success = FALSE;
		}
	}
	else 
	{
		$Error = $Error . '<br>The Webcomic name cannot be blank';
		$Success = FALSE;
	}
	if($ComicURL != '')
	{
		if(checkDuplicateWebcomicURL($ComicURL) > 0)
		{
			$Error = $Error .'<br>That URL is already being used by another webcomic';
			$Success = FALSE;
		}
	}
	else 
	{
		$Error = $Error .'<br>The Webcomic URL cannot be blank.';
	}
	
	if($ComicRSS != '')
	{
		if(checkDuplicateWebcomicRSS($ComicRSS) > 0)
		{
			$Error = $Error .'<br>That RSS feed is already being used by another webcomic';
			$Success = FALSE;
		}
	}
	else 
	{
		$Error = $Error . '<br>The Webcomic RSS cannot be blank.';
	}
	if($Success)
	{
		createWebcomic($ComicName, $ComicURL, $Membership,$ComicRSS, $Status);
		linkUserToComic($Email, $ComicID);
		print("Webcomic $ComicName added");
	}
	else 
	{
		print("$Error");
	}
}

if($function == 'editNews')
{
	$Alias = $_REQUEST['Alias'];
	$PubDate = $_REQUEST['PubDate'];
	$Category = $_REQUEST['Category'];
	$Details = $_REQUEST['Details'];
	$Title = $_REQUEST['Title'];
	$DateWritten = $_REQUEST['NewsID'];
	$Status = $_REQUEST['Status'];
	$Success = TRUE;
	$Error = 'News post not modified.';
	
	$PubDate = DateTime::createFromFormat('Y-m-d', $PubDate);
	$PubDate = $PubDate->format('U');
	$PubDate = $PubDate * 1000;
	
	if($PubDate == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Date to publish cannot be empty';
	}
	if($Title == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Title cannot be empty';
	}
	if($Details == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Details cannot be empty';
	}
	if($Success)
	{
		editNews($DateWritten,$Alias,$PubDate, $Category,$Details,$Title, $Status);
		print("News post $Title, has been edited.");
	}
	else 
	{
		print($Error);
	}
}

if($function == 'postNews')
{
	$Alias = $_REQUEST['Alias'];
	$PubDate = $_REQUEST['PubDate'];
	$Category = $_REQUEST['Category'];
	$Details = $_REQUEST['Details'];
	$Title = $_REQUEST['Title'];
	
	
	$PubDate = DateTime::createFromFormat('Y-m-d', $PubDate);
	$PubDate = $PubDate->format('U');
	$PubDate = $PubDate * 1000;
	
	$Success = TRUE;
	$Error = 'News not scheduled';
	if($PubDate == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Date to publish cannot be empty';
	}
	if($Title == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Title cannot be empty';
	}
	if($Details == '')
	{
		$Success = FALSE;
		$Error = $Error . '<br>Details cannot be empty';
	}
	if($Success)
	{
		createNewNews($Alias,$PubDate, $Category,$Details,$Title);
		print("News post for $Category scheduled");
	}
	else 
	{
		print($Error);
	}
}

if($function =='previewNews')
{
	$DateWritten = $_REQUEST['DateWritten'];
	$FirstName = $_REQUEST['FirstName'];
	$LastName = $_REQUEST['LastName'];
	$NewsPost = getSpecificNews($DateWritten);
	$Details = $NewsPost->value('Details');
	$Title = $NewsPost->value('Title');
	$_SESSION['PostID'] = $DateWritten;
	//action="' . htmlspecialchars($_SERVER["PHP_SELF"]) 
	print("<form method='GET' action='https://www.comicadia.com/admin.php'>");
	print("
	<strong>ID:</strong><br>
	<input type='text' class='editText' name='NewsPostID' disabled value='$DateWritten'>
	<br><strong>Title:</strong><br>
	<input type='text' class='editText' name='EditNewsPost' id='editNewsPost' disabled value='$Title'>
	<br><strong>Written by:</strong><br>
	<input type='text' class='editText' name='PosterFirst' id='PosterFirstName' value='$FirstName' disabled> <input type='text' class='editText' name='PosterLast' id='PosterLastName' value='$LastName' disabled>
	<br><strong>Details:</strong><br>
	<textarea class= 'editTextarea' name='NewsPostDetails' id='NewsDetails' disabled>$Details</textarea>");
	print("<br><input type='submit' name='submit' class='editBTN' value='Edit News'>");
	print("</form>");	
}

if($function == 'saveUserProfileEdits')
{
	$FirstName = $_REQUEST['FirstName'];
	$LastName = $_REQUEST['LastName'];
	$Alias = $_REQUEST['NewAlias'];
	$Email = $_REQUEST['NewEmail'];
	$CurrentEmail = $_REQUEST['OldEmail'];
	$CurrentAlias = $_REQUEST['OldAlias'];
	$Success = TRUE;
	$Error = '';
	$Type = getUserType($CurrentAlias);
	
	if($FirstName == '')
	{
		$Error = $Error. '<br>First Name is required';
		$Success = FALSE;
	}

	if($LastName == '' )
	{
		$Error = $Error. '<br>Last Name is required';
		$Success = FALSE;
	}
	
	if($Alias == '')
	{
		$Error = $Error . '<br>An Alias is required';
		$Success = FALSE;
	}
	
	if (!filter_var($Email, FILTER_VALIDATE_EMAIL) === false) 
	{
  	} 
  	else 
  	{
  		$Success = FALSE;
		$Error = $Error . "<br>$Email is not a valid email address";
	}	
	
	if(checkDuplicateAlias($Alias, $CurrentEmail) > 0)
	{
		$Success = FALSE;
		$Error = $Error . '<br>Alias already exists';
	}
	
	if(checkDuplicateEmail($Email, $CurrentAlias) > 0 )
	{
		$Success = FALSE;
		$Error = $Error . '<br> Email Address already in use';
	}
	
	if($Success)
	{
		print('Save Successful.');
		updateUser($CurrentEmail,$CurrentAlias,$FirstName, $LastName,$Alias,$Email,$Type);
	}
	else 
	{
		print("Save unsuccessful. $Error");
	}
}

if($function =='saveUserEdits')
{
	$FirstName = $_REQUEST['FirstName'];
	$LastName = $_REQUEST['LastName'];
	$Alias = $_REQUEST['Alias'];
	$Email = $_REQUEST['Email'];
	$Type = $_REQUEST['UserType'];
	$CurrentEmail = $_REQUEST['OldEmail'];
	$CurrentAlias = $_REQUEST['OldAlias'];
	$Success = TRUE;
	$Error = '';
	
	if($FirstName == '')
	{
		$Error = $Error. '<br>First Name is required';
		$Success = FALSE;
	}

	if($LastName == '' )
	{
		$Error = $Error. '<br>Last Name is required';
		$Success = FALSE;
	}
	
	if($Alias == '')
	{
		$Error = $Error . '<br>An Alias is required';
		$Success = FALSE;
	}
	
	if (!filter_var($Email, FILTER_VALIDATE_EMAIL) === false) 
	{
  	} 
  	else 
  	{
  		$Success = FALSE;
		$Error = $Error . "<br>$Email is not a valid email address";
	}	
	
	if(checkDuplicateAlias($Alias, $CurrentEmail) > 0)
	{
		$Success = FALSE;
		$Error = $Error . '<br>Alias already exists';
	}
	
	if(checkDuplicateEmail($Email, $CurrentAlias) > 0 )
	{
		$Success = FALSE;
		$Error = $Error . '<br> Email Address already in use';
	}
	
	if($Success)
	{
		print('Save Successful.');
		updateUser($CurrentEmail,$CurrentAlias,$FirstName, $LastName,$Alias,$Email,$Type);
	}
	else 
	{
		print("Save unsuccessful. $Error");
	}
}

if($function =='saveUserRole')
{
	$Alias = $_REQUEST['Alias'];
	$BeginRole = $_REQUEST['Role'];
	$ComicID = $_REQUEST['ComicID'];
	$RoleArray = explode(',',$BeginRole);
	$Error = "Changes to team not saved.";	
	$Success = TRUE;
	if(count($RoleArray) > 1)
	{
		$Role = array();
		foreach($RoleArray as $item)
		{
			if($item != '')
			{
			array_push($Role, $item);
			}
		}
	}
	else 
	{ 
		$Role = $BeginRole;
	}
	if($Alias =='')
	{
		$Error = $Error.'<br>Person is not valid.';
		$Success = FALSE;
	}
	if($Role == '')
	{
		$Error = $Error.'<br>Role cannot be blank - Did you mean to remove this person?';
		$Success = FALSE;
	}
	if($Success)
	{
		print('Roles updated for this team member');
		saveUserRoles($ComicID, $Alias, $Role);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'removeCrew')
{
	$Alias = $_REQUEST['Alias'];
	$ComicID = $_REQUEST['ComicID'];
	$Success = TRUE;
	$Error = 'Teammate note removed.';
	
	if($Alias == '')
	{
		$Error = $Error.'<br>Email cannot be blank';
		$Success = FALSE;
	}
	if($ComicName == '')
	{
		$Error = $Error.'<br>Comic name cannot be blank';
		$Success = FALSE;
	}
	if($Success)
	{
		print("This person will be removed.");
		removeCrewFromWebcomic($ComicID, $Alias);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'addCrew')
{
	$Alias = $_REQUEST['Alias'];
	$ComicID = $_REQUEST['ComicID'];
	$BeginRole = $_REQUEST['Roles'];
	$RoleArray = explode(',',$BeginRole);
	$Error = "Changes to team not saved.";	
	$Success = TRUE;
	if(count($RoleArray) > 1)
	{
		$Role = array();
		foreach($RoleArray as $item)
		{
			if($item != '')
			{
			array_push($Role, $item);
			}
		}
	}
	else 
	{ 
		$Role = $BeginRole;
	}
	if($Alias =='')
	{
		$Error = $Error.'<br>Person is not valid.';
		$Success = FALSE;
	}
	if($Role == '')
	{
		$Error = $Error.'<br>Role cannot be blank - Did you mean to remove this person?';
		$Success = FALSE;
	}
	if($Success)
	{
		print('Roles updated for this team member');
		createUserRoles($ComicID, $Alias, $Role);
	}
	else 
	{
		print($Error);
	}
	
}

if($function == 'saveWebcomicEditsFromUser')
{
	$ComicID = $_REQUEST['ComicID'];
	$CurrentComicName = $_REQUEST["OldName"];
	$NewComicName = $_REQUEST["NewName"];
	$OldURL = $_REQUEST["OldURL"];
	$OldRSS = $_REQUEST["OldRSS"];
	$URL = $_REQUEST["URL"];
	$RSS = $_REQUEST["RSS"];
	$Format = $_REQUEST['Format'];
	$Synopsis = $_REQUEST["Synopsis"];
	$Pitch = $_REQUEST["Pitch"];
	$Success = TRUE;
	$Error = "Changes to Webcomic not saved";
	
	if($CurrentComicName != $NewComicName)
	{
		if(checkDuplicateWebcomicName($NewComicName) > 0)
		{
			$Error = $Error."<br>A comic with this name already exists";
			$Success = FALSE;
		}
	}
	if($OldURL != $URL)
	{
		if(checkDuplicateWebcomicURL($URL) > 0)
		{
			$Error = $Error.'<br>A comic has already claimed this URL';
			$Success = FALSE;
		}
	}
	if($OldRSS != $RSS)
	{
		if(checkDuplicateWebcomicRSS($RSS) > 0)
		{
			$Error = $Error.'<br>A webcomic has already claimed that RSS feed';
			$Success = FALSE;
		}
	}
	if($Success)
	{
		print("Changes saved.");
		updateWebcomicFromUser($ComicID,$NewComicName,$URL,$RSS,$Synopsis,$Format,$Pitch);
	}
	else
	{
		print($Error);
	}
	
}

if($function == 'saveWebcomicEdits')
{
	$ComicID = $_REQUEST['ComicID'];
	$CurrentComic = getComicDetailsByID($ComicID);
	$CurrentComicName = $CurrentComic->value("Name");
	$CurrentComicRSS = $CurrentComic->value("RSS");
	$CurrentComicURL = $CurrentComic->value("URL");
	$ComicName = $_REQUEST['ComicName'];
	$URL = $_REQUEST['URL'];
	$RSS = $_REQUEST['RSS'];
	$Status = $_REQUEST['Status'];
	$Membership = $_REQUEST['Membership'];
	$Synopsis = $_REQUEST['Synopsis'];
	$Pitch = $_REQUEST["Pitch"];
	$Success = TRUE;
	$Error = 'Changes to Webcomic not saved';
	
	if($ComicName == '')
	{
		$Error = $Error.'<br>Comic Name cannot be blank.';
		$Success = FALSE;
	}
	if($RSS == '')
	{
		$Error = $Error.'<br>RSS cannot be blank';
		$Success = FALSE;
	}
	if($URL == '')
	{
		$Error = $Error.'<br>URL cannot be blank';
		$Success = FALSE;
	}
	if($CurrentComicName != $ComicName)
	{
		if(checkDuplicateWebcomicName($ComicName) > 0)
		{
			$Error = $Error.'<br>A comic with this name already exists';
			$Success = FALSE;
		}
	}
	if($CurrentComicURL != $URL)
	{
		if(checkDuplicateWebcomicURL($URL) > 0)
		{
			$Error = $Error.'<br>A comic has already claimed this URL';
			$Success = FALSE;
		}
	}
	if($CurrentComicRSS != $RSS)
	{
		if(checkDuplicateWebcomicRSS($RSS) > 0)
		{
			$Error = $Error.'<br>A webcomic has already claimed that RSS feed';
			$Success = FALSE;
		}
	}
	if($Success)
	{
		print("Webcomic details updated.");
		updateWebcomic($ComicID,$ComicName,$URL,$RSS,$Status,$Membership,$Synopsis,$Pitch);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'addEvent')
{
	$EventDate = $_REQUEST['EventDate'];
	$EventDate = $EventDate * 1000;
	$Title = $_REQUEST['Title'];
	$Organizer = $_REQUEST['Organizer'];
	$Details = $_REQUEST['Details'];
	$Type = $_REQUEST['Type'];
	$Location = $_REQUEST['Location'];
	$Category = $_REQUEST['Category'];
	
	$Success = TRUE;
	$Error = 'Event not scheduled.';	
	
	if($EventDate == '')
	{
		$Success = FALSE;
		$Error = $Error. '<br>A date is required.';
	}	
	if($Title == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>An event title is required';
	}
	if($Organizer == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>An organizer must be selected';
	}
	if($Details =='')
	{
		$Success = FALSE;
		$Error = $Error.'<br>You must provide details on this event';
	}
	if($Type =='')
	{
		$Success = FALSE;
		$Error = $Error.'<br>An event type is required.';
	}
	if($Success)
	{
		print("Event created. $EventDate");
		
		createEvent($EventDate,$Title,$Location,$Organizer,$Details,$Type,$Category);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'deleteEvent')
{
	$Title = $_REQUEST['Title'];
	$Alias= $_REQUEST['Organizer'];
	$EventDate = $_REQUEST['EventDate'];
	$Type = $_REQUEST['Type'];
	$Success = TRUE;
	if($Success) 
	{
		print("Deleted");
		deleteEvent($Title, $EventDate, $Type,$Alias);
	}
	else
	{
		print("Event not deleted.");
	}
}

if($function == 'editEvent')
{
	$NewTitle = $_REQUEST['NewTitle'];
	$NewOrg = $_REQUEST['NewOrganizer'];
	$NewDate = $_REQUEST['NewEventDate'];
	$NewType = $_REQUEST['NewType'];
	$NewCat = $_REQUEST['NewCategory'];
	$NewLoc = $_REQUEST['NewLocation'];
	$NewDetails = $_REQUEST['NewDetails'];
	$OldTitle = $_REQUEST['OldTitle'];
	$OldOrg = $_REQUEST['OldOrganizer'];
	$OldStart = $_REQUEST['OldStartTime'];
	$OldType = $_REQUEST['OldType'];
	$NewStatus = $_REQUEST['NewStatus'];
	$Timezone = $_REQUEST['timezone'];
	
	//$NewDate = $NewDate * 1000;
	$NewDate = DateTime::createFromFormat('Y-m-d H:i', $NewDate);
	$NewDate = $NewDate->format('U');
	$NewDate = $NewDate *1000;
	$Now = time();
	$Now = $Now * 1000;

	$Success = TRUE;
	$Error = 'Event not modified';	
	
	if($NewTitle == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>Title cannot be blank';
	}
	if($NewDate == '')
	{
		$Success = FALSE;
		$Error = $Error.'<br>Event Date cannot be blank';
	}
	
	if($NewDetails == '')
	{
		$Success = FALSE;
		$Error=$Error.'<br>Details cannot be blank.';
	}
	if($NewDate > time())
	{
		$Sucess = FALSE;
		$Error = $Error.'<br>Event time cannot be in the past.';
	}
	if($NewDate != $OldStart || $NewOrg != $OldOrg)
	{
		if(checkEventDuplicates($NewDate,$NewType,$NewOrg) > 0)
		{
			$Error = $Error.'There is another event running at this time, of this type, by this organizer.';
			$Success = FALSE;
		}
	}
	if($Success)
	{
		
		print('Event has been modified');
		editEvent($OldTitle,$OldOrg,$OldType,$OldStart,$NewTitle,$NewDate,$NewOrg,$NewLoc,$NewOrg,$NewDetails,$NewCat,$NewType,$NewStatus);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'registerNewUser')
{
	$FirstName = $_REQUEST["FirstName"];
	$LastName = $_REQUEST["LastName"];
	$Alias = $_REQUEST["Alias"];
	$Email = $_REQUEST["Email"];
	$Confirm = $_REQUEST["Confirm"];
	$Password = $_REQUEST["Password"];
 	$Email = strtolower($Email);
	$Confirm = strtolower($Confirm);

	$Success = TRUE;
 	$Error = 'User not Registered';
 
 	if($FirstName == '')
 	{
  		$Success = FALSE;
  		$Error = $Error."<br>First Name is required";
 	}
 	if($LastName == '')
 	{
  		$Success = FALSE;
  		$Error = $Error."<br>Last Name is required";
 	}
 
 	if($Alias == '')
 	{
  		$Success = FALSE;
  		$Error = $Error."<br>Alias is required";
 	}
 	else
 	{
  		if(isAliasAvailable($Alias) > 0)
  		{
			$Success = FALSE;
   		$Error = $Error."<br>Sorry, that Alias is already in use";
  		}
 	}
 	if($Email != '' and $Confirm != '')
 	{
  		if($Email != $Confirm)
  		{
   		$Error = $Error."<br>Both emails must be identical.";
   		$Success = FALSE;
  		}
  		else
  		{
   		if(isEmailAvailable($Email) > 0)
   		{ 
    			$Success = FALSE;
    			$Error = $Error."Sorry, that email is already in use";
   		}
  		}
 	}
 	else
 	{
  		$Error = $Error."You must enter both an email and confirm the address.";
 	}
 	if($Success)
 	{
  		print("User Created");
  		registerNewUser($Email, $Password, $Alias, $FirstName, $LastName);
 	}
 	else
 	{
  	print($Error);
 	}
}

if($function == 'createTheme')
{
	$Name = $_REQUEST['ThemeName'];
	$Rating = $_REQUEST['ThemeRating'];
	$Error = 'Theme not added.';
	$Success = TRUE;
	if($Name == '')
	{
		$Success = FALSE;
		$Error=$Error."<br>A Theme requires a name";
	}
	else 
	{
		if(checkDuplicateTheme($Name) > 0)
		{
			$Error = $Error."<br>A theme with that name already exists";
			$Success = FALSE;
		}
	}
	if($Rating == '')
	{
		$Rating = 0;
	}
	else 
	{
		if(!is_numeric($Rating))
		{
			$Success = FALSE;
			$Error = $Error."<br>Rating must be a number";
		}
	}
	if($Success)
	{
		print("Added");
		createTheme($Name,$Rating);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'editGenre')
{
	$OldName = $_REQUEST['OldGenreName'];
	$NewName = $_REQUEST['NewGenreName'];
	$Error = 'Changes not saved';
	$Success = TRUE;
	
	if($NewName == '')
	{
		$Error = $Error."<br>Genres require a name";
		$Success = FALSE; 
	}
	if($Success)
	{
		print("Changes saved");
		editGenre($OldName,$NewName);
	}
	else
	{
		print($Error);
	}
}

if($function == 'editTheme')
{
	$OldName = $_REQUEST['OldThemeName'];
	$OldRating = $_REQUEST['OldThemeRating'];
	$NewName = $_REQUEST['NewThemeName'];
	$NewRating = $_REQUEST['NewThemeRating'];
	$Error = "Changes not saved";
	$Success = TRUE;
	if($NewName == '')
	{
		$Error = $Error."<br>Themes require a name";
		$Success = FALSE; 
	}
	else
	{
		if(strtolower($OldName) != strtolower($NewName))
		{
			if(checkDuplicateTheme($NewName) >0)
			{
				$Error = $Error."<br>A Theme with that name already exists";
				$Success = FALSE;
			}
		}
	}
	if($NewRating == '')
	{
		$NewRating = 0;
	}
	else 
	{
		if(!is_numeric($NewRating))
		{
			$Error = $Error."<br>Ratings must be numerical";
			$Success = FALSE;
		}
	}
	if($Success)
	{
		print("Changes saved");
		editTheme($OldName, $NewName, $NewRating);
	}
	else 
	{
		print($Error);
	}
}


if($function == 'deleteTheme')
{
	$Name = $_REQUEST['ThemeName'];
	if(checkDuplicateTheme($Name) >0)
	{
		print("Deleted");
		deleteTheme($Name);
	}
	else 
	{
		print("Theme not deleted.");
	}
}

if($function == 'deleteGenre')
{
	$Name = $_REQUEST['GenreName'];
	print("Genre Deleted");
	deleteGenre($Name);		
	
}

if($function == 'createGenre')
{
	$Name = $_REQUEST['GenreName'];
	
	$Error = 'Genre not added.';
	$Success = TRUE;
	
	if($Name == '')
	{
		$Success = FALSE;
		$Error=$Error."<br>A Genre requires a name";
	}
	else 
	{
		if(checkDuplicateGenre($Name) > 0)
		{
			$Error = $Error."<br>A Genre with that name already exists";
			$Success = FALSE;
		}
	}
	if($Success)
	{
		print("Genre added");
		createGenre($Name);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'addMediaFromURL')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$WebcomicID = $_REQUEST["Webcomic"];
	$MediaType = $_REQUEST["Type"];
	$ImgURL = $_REQUEST["URL"];
	$Artist = $_REQUEST['Artist'];
	$OriginalLocation = $_REQUEST["URL"];
	$Uploader = $_REQUEST['UploadedBy'];
	$Desc = $_REQUEST['Desc'];
	
	$checkURL = checkURL($ImgURL);
	$name = basename($ImgURL);
	$name = str_replace(" ","",$name);
	list($txt, $ext) = explode(".", $name);
	$name = $txt.time();
	$name = $name.".".$ext;
	
	//check if the files are only image / document
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($ImgURL);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		$AcceptableDimensions = getMediaDimensionsByType($MediaType);
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
	
	if($Artist == '')
	{
		$AddError= $AddError.'<br>An artist must be associated with media';
		$AddSuccess = FALSE;
	}
	if($Type =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A media type must be selected.';
	}
	if($WebcomicID =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A webcomic must be associated to the file.';
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
	if(trim($Desc) == '')
	{
		$Desc = $MediaType.' for '.$WebcomicID;
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
			addMediaForWebcomic($MediaType, $ImgURL, $WebcomicID, $Artist,$Uploader, $Desc);
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

if($function == 'addMediaFromLocal')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$WebcomicID = $_REQUEST["Webcomic"];
	$Artist = $_REQUEST['Artist'];
	$Uploader = $_REQUEST['UploadedBy'];
	$Desc = $_REQUEST['Desc'];
	$MediaType = $_REQUEST['Type'];
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	
	if($FileName == '')
	{
		$AddError = $AddError."<br>A file must have a name.";
		$AddSuccess = FALSE;
		
	}
	else
	{
		$FileName = str_replace(" ","",$FileName);
		list($txt, $ext) = explode(".", $FileName);
		$name = $txt.time();
		$name = $name.".".$ext;
		
	}
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$AcceptableDimensions = getMediaDimensionsByType($MediaType);
		$AcceptableHeight = $AcceptableDimensions->value("Height");
		$AcceptableWidth = $AcceptableDimensions->value("Width");
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
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
	
	if($Artist == '')
	{
		$AddError= $AddError.'<br>An artist must be associated with media';
		$AddSuccess = FALSE;
	}
	if($MediaType =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A media type must be selected.';
	}
	if($WebcomicID =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A webcomic must be associated to the file.';
	}
	if(trim($Desc) == '')
	{
		$Desc = $MediaType.' for '.$WebcomicID;
	}
	if($AddSuccess) 
	{
		//check success
		if(file_put_contents("../media/$name",$FileContent) )
		{
			$ImgURL = 'https://www.comicadia.com/media/'.$name;
			addMediaForWebcomic($MediaType, $ImgURL, $WebcomicID, $Artist,$Uploader, $Desc);
			print("Succes");
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

if($function == 'addProfilePicFromLocal')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$Alias = $_REQUEST['Alias'];
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileName = str_replace(" ","",$FileName);
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	
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
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		if((int)$Width != 150 && (int)$Height != 150)
		{
			$AddError = $AddError."<br>Profile pics can only be 150x150";
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
			addProfilePic($ImgURL,$Alias);
			print("Succes");
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

if($function == 'addProfilePicFromURL')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	
	$Alias = $_REQUEST['Alias'];
	$ImgURL = $_REQUEST["URL"];
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
				$Dimensions = getimagesize($ImgURL);
				$Width = $Dimensions[0];
				$Height = $Dimensions[1];
				if((int)$Width != 150 && (int)$Height != 150)
				{
					$AddError = $AddError."<br>Profile pics can only be 150x150";
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
		
		//here is the actual code to get the file from the url and save it to the uploads folder
		//get the file from the url using file_get_contents and put it into the folder using file_put_contents
		$upload = file_put_contents("../media/$name",file_get_contents($OriginalLocation));
		//check success
		if($upload)  
		{
			$ImgURL = 'https://www.comicadia.com/media/'.$name;
			addProfilePic($ImgURL,$Alias);
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

if($function == 'removeMedia')
{
	$ImgURL = $_REQUEST["ImgURL"];
	deleteMedia($ImgURL);
	print("Media deleted.");
}

if($function == 'activateMedia')
{
	$ImgURL = $_REQUEST["ImgURL"];
	activateMedia($ImgURL);
}

if($function == 'deactivateMedia')
{
	$ImgURL = $_REQUEST["ImgURL"];
	deactivateMedia($ImgURL);
}

if($function == 'filteredThemeList')
{	
	$Term = $_REQUEST['term'];
	$ThemeList = getFilteredThemes($Term);

	foreach($ThemeList->getRecords() as $Theme)
	{
		$data[] = $Theme->value("Name");
	} 
    //return json data
    echo json_encode($data);
}

if($function == 'saveGenres')
{
	$Genre1 = $_REQUEST["Genre1"];
	$Genre2 = $_REQUEST["Genre2"];
	$Genre3 = $_REQUEST["Genre3"];
	$ComicID = $_REQUEST['ComicID'];
	$GenreList = array();
	if($Genre1 != '')
	{
		array_push($GenreList,$Genre1);
	}
	if($Genre2 != '')
	{
		array_push($GenreList,$Genre2);
	}
	if($Genre3 != '')
	{
		array_push($GenreList,$Genre3);
	}
	
	clearGenresOfWebcomic($ComicID);
	foreach($GenreList as $Genre)
	{
		addGenreToWebcomic($Genre,$ComicID);
	}
	print("Genres changed");
}

if($function == 'saveThemes')
{
	$ThemeList = $_REQUEST["ThemeList"];
	$ThemeList = rtrim(trim($ThemeList),',');
	$ThemeList = explode(',',$ThemeList);
	$ComicID = $_REQUEST['ComicID'];
	if(count($ThemeList) < 11 && count($ThemeList) > 0)
	{
		$printString = '';
		clearAllThemesFromComic($ComicID);
		foreach($ThemeList as $Theme)
		{
			
			addThemeToComic($Theme, $ComicID);
		}
		print("Themes updated");
	}
	else
	{
		print("You may select between 1 and 10 themes. No more.");
	}
}

if($function == 'addEventFromUser')
{
	$EventDate = $_REQUEST['PubDate'];
	$Title = trim($_REQUEST['Title']);
	$Organizer = $_REQUEST['Alias'];
	$Details = trim($_REQUEST['Details']);
	$Type = $_REQUEST['Type'];
	$Location = trim($_REQUEST['Location']);
	$Category = $_REQUEST['Category'];
	
	$PubDate = DateTime::createFromFormat('Y-m-d H:i', $EventDate);
	$PubDate = $PubDate->format('U');
	$PubDate = $PubDate * 1000;
	
	if($Category == 'Public')
	{
		$Status = 'Pending';
	}
	else
	{
		$Status = 'Approved';
	}
	
	$Success = TRUE;
	$Error = "Event not created";
	if($Title =='')
	{
		$Success = FALSE;
		$Error = $Error."<br>Title cannot be blank";
	}
	if($Details == '')
	{
		$Success = FALSE;
		$Error = $Error."<br>Details cannot be blank";
	}
	if($Success)
	{
		if(checkDuplicateEvent($PubDate, $Organizer) == 0)
		{
			createUserEvent($PubDate,$Title,$Organizer,$Details,$Type,$Location,$Category,$Status);
			print("Event created");
		}
		else
		{
			print("You already have an event at this time.");
		}
	}
	else
	{
		print($Error);
	}

}

if($function =='saveUserEditEvents')
{
	$DateWritten = $_REQUEST['DateWritten'];
	$OriginalPubDate = $_REQUEST['oldPubDate'];
	$EventDate = $_REQUEST['PubDate'];
	$Title = trim($_REQUEST['Title']);
	$Organizer = $_REQUEST['Alias'];
	$Details = trim($_REQUEST['Details']);
	$Type = $_REQUEST['Type'];
	$Location = trim($_REQUEST['Location']);
	$Category = $_REQUEST['Category'];
	
	$PubDate = DateTime::createFromFormat('Y-m-d H:i', $EventDate);
	$PubDate = $PubDate->format('U');
	$PubDate = $PubDate * 1000;
	
	if($Category == 'Public')
	{
		$Status = 'Pending';
	}
	else
	{
		$Status = 'Approved';
	}
	
	if($PubDate != $OriginalPubDate)
	{
		if(checkDuplicateEvent($PubDate, $Organizer) != 0)
		{
			$Success = FALSE;
			$Error = $Error.'<br>You already have an event scheduled for this time';
		}
	}
	
	$Success = TRUE;
	$Error = "Event not created";
	if($Title =='')
	{
		$Success = FALSE;
		$Error = $Error."<br>Title cannot be blank";
	}
	if($Details == '')
	{
		$Success = FALSE;
		$Error = $Error."<br>Details cannot be blank";
	}
	if($Success)
	{
		editUserEvent($DateWritten,$PubDate,$Title,$Organizer,$Details,$Type,$Location,$Category);
		print("Event modified");
		
	}
	else
	{
		print($Error);
	}
}

if($function == 'deleteUserEvent')
{
	$DateCreated = $_REQUEST['DateCreated'];
	if($DateCreated != '')
	{
		deleteUserEvent($DateCreated);	
		print("Deleted");
	}
	else
	{
		print("The date created cannot be blank");
	}
}

if($function == 'addUserWebcomic')
{
	$Alias = trim($_REQUEST['Alias']);
	$Title = test_input($_REQUEST['Title']);
	$URL = trim($_REQUEST['URL']);
	$RSS = trim($_REQUEST['RSS']);
	$Synopsis = test_input($_REQUEST['Synopsis']);
	$Pitch = test_input($_REQUEST["Pitch"]);
	$Format = $_REQUEST['Format'];
	$Genre1 = $_REQUEST['Genre1'];
	$Genre2 = $_REQUEST['Genre2'];
	$Genre3 = $_REQUEST['Genre3'];
	$ThemeArray = $_REQUEST['ThemeArray'];
	$GenreList = array();

	$Success = TRUE;
	$Error = 'Webcomic not added';
	
	$ThemeArray = rtrim(trim($ThemeArray),',');
	$ThemeArray = explode(',',$ThemeArray);
	
	if($Title != '')
	{
		if(checkDuplicateWebcomicName($Title) > 0)
		{
			$Error = $Error .'<br>A Webcomic with that name already exists';
			$Success = FALSE;
		}
	}
	else
	{
		$Success = FALSE;
		$Error = $Error."<br>A webcomic requires a title";
	}

	if($URL != '')
	{
		if(isValidUrl($URL))
		{
			if(checkDuplicateWebcomicURL($URL) > 0)
			{
				$Error = $Error .'<br>A Webcomic with that URL already exists';
				$Success = FALSE;
			}
		}
		else
		{
			$Error = $Error."<br>The URL provided was not a valid URL";
			$Success = FALSE;
		}
	}
	else
	{
		$Success = FALSE;
		$Error = $Error."<br>A webcomic requires a URL";
	}
	
	if($RSS != '')
	{
		if(isValidUrl($RSS))
		{
			if(checkDuplicateWebcomicRSS($RSS) > 0)
			{
				$Error = $Error .'<br>A Webcomic with that RSS feed already exists';
				$Success = FALSE;
			}
		}
		else
		{
			$Error = $Error."<br>The RSS feed provided was not a valid URL";
			$Success = FALSE;
		}
	}
	else
	{
		$Success = FALSE;
		$Error = $Error."<br>A webcomic requires a RSS feed";
	}
	
	if($Synopsis == '')
	{
		$Success = FALSE;
		$Error = $Error."<br>A webcomic requires a synopsis";
	}
	
	if($Genre1 != '')
	{
		array_push($GenreList,$Genre1);
	}
	if($Genre2 != '')
	{
		array_push($GenreList,$Genre2);
	}
	if($Genre3 != '')
	{
		array_push($GenreList,$Genre3);
	}
	
	if($Success)
	{
		print("Success");
		createUserWebcomic($Title, $Alias, $URL, $RSS, $Synopsis, $Format, $Pitch);
		$ComicID = getMostRecentlyAddedWebcomic();
		foreach($GenreList as $Genre)
		{
			addGenreToWebcomic($Genre,$ComicID);
		}
		foreach($ThemeArray as $Theme)
		{
			addThemeToComic($Theme, $ComicID);
		}
	}
	else
	{
		print($Error);
	}
}

/*
if($function == 'searchUsersToEdit')
{
	$SearchBy = $_REQUEST['SearchBy'];
	$Keyword = $_REQUEST['Keyword'];
	if($Keyword == '')
	{
		$FilteredUserList = searchAllUsers($SearchBy);
	}
	else
	{
		$FilteredUserList = searchUsersBySpecifics($SearchBy, $Keyword);
	}
	foreach($UserList->getRecords() as $FoundUser)
	{
		print('found');
	}
*/

if($function == 'confirmAttendance')
{
	$Alias = $_REQUEST['Alias'];
	$DateCreated = $_REQUEST['DateCreated'];
	confirmMemberAttendanceToEvent($Alias,$DateCreated);
	print("You are now signed up for this event.");
}

if($function == 'cancelAttendance')
{
	$Alias = $_REQUEST['Alias'];
	$DateCreated = $_REQUEST['DateCreated'];
	cancelMemberAttendanceToEvent($Alias,$DateCreated);
	print("You are no longer signed up for this event.");
}

if($function == 'uploadFile')
{
	$FileType = $_FILES['image']['type'];
	$FileName = $_FILES['image']['name'];
	$FileName = str_replace(" ","",$FileName);
	
	list($txt, $ext) = explode(".", $FileName);
	$name = $txt.time();
	$name = $name.".".$ext;
	
	$FileContent = file_get_contents($_FILES['image']['tmp_name']);
	
	$image = $_FILES['image']['name'];
	
	if(file_put_contents("../media/$name",$FileContent) ) 
	{
		echo 'https://www.comicadia.com/media/'.$name;
	} 
	else 
	{
		echo "Unable to Upload";
	}
}


if($function == 'deleteNewsPost')
{
	$DateWritten = $_REQUEST['DateWritten'];
	$Alias = $_REQUEST['Alias'];
	deleteNewsPost($DateWritten, $Alias);
	print("Success");
}

if($function == 'deleteWebcomicByCreator')
{
	$ComicID = $_REQUEST['ComicID'];
	$Alias = $_REQUEST['Alias'];
	deleteWebcomic($ComicID, $Alias);
	print("Success");
}

if($function == 'loadRotator')
{
	$Count = $_REQUEST["Count"];
	$Type = $_REQUEST["Type"];
	$ComicID = $_REQUEST["ComicID"];
	if(checkComicID($ComicID))
	{
		$Ads = rotateHorizontalAdsByType($Type,$Count,$ComicID);
		$AdCount = 0;
		print("<link href=\"https://www.comicadia.com/css/rotator.css\" rel=\"stylesheet\" type=\"text/css\" />");
		print("<div id='ComicadiaHorizontalBox'>");
		print("<div class='comicadiaBlock'><a href='https://www.comicadia.com'><img src='https://www.comicadia.com/media/comicadiaRotator.png' /></div></a>");
		foreach($Ads->getRecords() as $row)
		{
			$imgURL = $row->value("ImgURL");
			print("<a href=\"https://www.comicadia.com/php/redirector.php?imgURL=$imgURL&ComicID=$ComicID\" target='_blank'>
			<div id='Comicadia$AdCount' style='background-image: url(\"$imgURL\"); background-size:cover' class='Comicadia$Type'></div></a>");
			addViewToMediaByComicID($imgURL,$ComicID);
			$AdCount++;
		}
		
		print("<div style='clear:both'></div></div>");
	}
	else
	{
		print("Sorry, that comic ID is invalid");
	}
}

if($function == 'loadMobileRotator')
{
	$Count = $_REQUEST["Count"];
	$Type = $_REQUEST["Type"];
	$ComicID = $_REQUEST["ComicID"];
	if(checkComicID($ComicID))
	{
		$Ads = rotateHorizontalAdsByType($Type,$Count,$ComicID);
		$AdCount = 0;
		print("<div id='ComicadiaMobileHorizontalBox'>");
		print("<div class='comicadiaBlock'><a href='https://www.comicadia.com'><img src='https://www.comicadia.com/media/comicadiaRotator.png' /></div></a>");
		foreach($Ads->getRecords() as $row)
		{
			$imgURL = $row->value("ImgURL");
			print("<a href=\"https://www.comicadia.com/php/redirector.php?imgURL=$imgURL&ComicID=$ComicID\" target='_blank'>
			<div id='Comicadia$AdCount' style='background: transparent url(\"$imgURL\") 
			top left no-repeat; width:150px; height:50px' class='Comicadia$Type'></div></a>");
			addViewToMediaByComicID($imgURL,$ComicID);
			$AdCount++;
		}
		print("<div style='clear:both'></div></div>");
	}
	else
	{
		print("Sorry, that comic ID is invalid");
	}
}

if($function == 'deleteUser')
{
	$Alias = $_REQUEST['Alias'];
	$Deleter = $_REQUEST['Deleter'];
	$DeleterType = getUserType($Deleter);
	if($DeleterType == 'Admin')
	{
		print("Success");
		deleteUser($Alias);
	}
	else
	{
		print("You do not have permissions to delete users");
	}
}


if($function == 'deleteProfilePic')
{
	$Alias = $_REQUEST['Alias'];
	print("Deleted");
	removeProfilePic($Alias);
}

if($function == 'adminAddCrew')
{
	$Alias= $_REQUEST['Alias'];
	$ComicID = $_REQUEST['ComicID'];
	$ComicDetails = getComicDetailsByID($ComicID);
	$ComicName = $ComicDetails->value("Name");
	
	$Roles = $_REQUEST['Roles'];
	$RoleArray = explode(',',$Roles);
	$Error = "Changes to team not saved.";	
	$Success = TRUE;
	if(count($RoleArray) > 1)
	{
		$Role = array();
		foreach($RoleArray as $item)
		{
			if($item != '')
			{
			array_push($Role, $item);
			}
		}
	}
	else 
	{ 
		$Role = $Roles;
	}
	if($Alias =='')
	{
		$Error = $Error.'<br>Person is not valid.';
		$Success = FALSE;
	}
	if($Role == '')
	{
		$Error = $Error.'<br>Role cannot be blank - Did you mean to remove this person?';
		$Success = FALSE;
	}
	
	if($Success)
	{
		print('Team member added.');
		createUserRoles($ComicID, $Alias, $Role);
	}
	else 
	{
		print($Error);
	}
	
}

if($function == 'adminUpdateCrew')
{
	$Alias= $_REQUEST['Alias'];
	$ComicID = $_REQUEST['ComicID'];
	$ComicDetails = getComicDetailsByID($ComicID);
	$ComicName = $ComicDetails->value("Name");
	
	$Roles = $_REQUEST['Roles'];
	$RoleArray = explode(',',$Roles);
	$Error = "Changes to team not saved.";	
	$Success = TRUE;
	if(count($RoleArray) > 1)
	{
		$Role = array();
		foreach($RoleArray as $item)
		{
			if($item != '')
			{
			array_push($Role, $item);
			}
		}
	}
	else 
	{ 
		$Role = $Roles;
	}
	if($Alias =='')
	{
		$Error = $Error.'<br>Person is not valid.';
		$Success = FALSE;
	}
	if($Role == '')
	{
		$Error = $Error.'<br>Role cannot be blank - Did you mean to remove this person?';
		$Success = FALSE;
	}
	
	if($Success)
	{
		print('Roles updated for this team member');
		saveUserRoles($ComicID, $Alias, $Role);
	}
	else 
	{
		print($Error);
	}
}

if($function == 'adminRemoveCrew')
{
	$ComicID = $_REQUEST['ComicID'];
	$Alias = $_REQUEST['Alias'];
	$ComicDetails = getComicDetailsByID($ComicID);
	$ComicName = $ComicDetails->value("Name");
	removeCrewFromWebcomic($ComicID, $Alias);
	print("Deleted");
}

if($function == 'adminSaveGenres')
{
	$ComicID = $_REQUEST['ComicID'];
	$ComicDetails = getComicDetailsByID($ComicID);
	$ComicName = $ComicDetails->value("Name");
	$Genre1 = $_REQUEST["Genre1"];
	$Genre2 = $_REQUEST["Genre2"];
	$Genre3 = $_REQUEST["Genre3"];
	$GenreList = array();
	if($Genre1 != '')
	{
		array_push($GenreList,$Genre1);
	}
	if($Genre2 != '')
	{
		array_push($GenreList,$Genre2);
	}
	if($Genre3 != '')
	{
		array_push($GenreList,$Genre3);
	}
	
	clearGenresOfWebcomic($ComicID);
	foreach($GenreList as $Genre)
	{
		addGenreToWebcomic($Genre,$ComicID);
	}
	print("Genres changed");
}

if($function == 'adminSaveThemes')
{
	$ComicID = $_REQUEST['ComicID'];
	$ComicDetails = getComicDetailsByID($ComicID);
	$ComicName = $ComicDetails->value("Name");
	$ThemeList = $_REQUEST["ThemeList"];
	$ThemeList = rtrim(trim($ThemeList),',');
	$ThemeList = explode(',',$ThemeList);
	
	if(count($ThemeList) < 11 && count($ThemeList) > 0)
	{
		$printString = '';
		clearAllThemesFromComic($ComicID);
		foreach($ThemeList as $Theme)
		{
			
			addThemeToComic($Theme, $ComicID);
		}
		print("Themes updated");
	}
	else
	{
		print("You may select between 1 and 10 themes. No more.");
	}	
}

if($function == 'adminDeleteComic')
{
	$Alias = $_REQUEST['Alias'];
	$ComicID = $_REQUEST['ComicID'];
	if(getUserType($Alias) == 'Admin')
	{
		print("Deleted");
		deleteComicByID($ComicID);
	}
	else
	{
		print("You do not have permission to delete webcomics!");
	}
}

if($function == 'approveEvent')
{
	$EventID = $_REQUEST['EventID'];
	$Alias = $_REQUEST['Alias'];
	if(getUserType($Alias) == 'Admin')
	{
		if(wasEventAlreadyApproved($EventID) == false)
		{			
			print("Approved");
			approveEvent($EventID,$Alias);
		}
		else
		{
			print("This event was already approved by someone else.");
		}
	}
	else
	{
		print("You do not have permission to approve events.");
	}
	
}

if($function == "adminDeleteEvent")
{
	$EventID = $_REQUEST['EventID'];
	$Alias = $_REQUEST['Alias'];
	
	if(getUserType($Alias) == 'Admin')
	{
		if(wasEventAlreadyApproved($EventID) == false)
		{			
			print("Deleted");
			adminDeleteEvent($EventID);
		}
		else
		{
			print("This event was already approved by someone else.");
		}
	}
	else
	{
		print("You do not have permission to delete events.");
	}
}

if($function == 'approveNews')
{
	$NewsID = $_REQUEST['NewsID'];
	$Alias = $_REQUEST['Alias'];
	if(getUserType($Alias) == 'Admin')
	{
		if(wasNewsAlreadyApproved($NewsID) == false)
		{			
			print("Approved");
			approveNews($NewsID,$Alias);
		}
		else
		{
			print("This post was already approved by someone else.");
		}
	}
	else
	{
		print("You do not have permission to approve News Posts.");
	}
	
}

if($function == "adminDeleteNews")
{
	$NewsID = $_REQUEST['NewsID'];
	$Alias = $_REQUEST['Alias'];
	
	if(getUserType($Alias) == 'Admin')
	{
		if(wasNewsAlreadyApproved($NewsID) == false)
		{			
			print("Deleted");
			adminDeleteNews($NewsID);
		}
		else
		{
			print("This event was already approved by someone else.");
		}
	}
	else
	{
		print("You do not have permission to delete events.");
	}
}

if($function == 'adminAddMediaFromURL')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$MediaType = $_REQUEST["Type"];
	$ImgURL = $_REQUEST["URL"];
	$Artist = $_REQUEST['Artist'];
	$OriginalLocation = $_REQUEST["URL"];
	$Uploader = $_REQUEST['UploadedBy'];
	$Desc = $_REQUEST['Desc'];
	
	$checkURL = checkURL($ImgURL);
	$name = basename($ImgURL);
	$name = str_replace(" ","",$name);
	list($txt, $ext) = explode(".", $name);
	$name = $txt.time();
	$name = $name.".".$ext;
	
	//check if the files are only image / document
	
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$Dimensions = getimagesize($ImgURL);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
		$AcceptableDimensions = getMediaDimensionsByType($MediaType);
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
	
	if($Artist == '')
	{
		$AddError= $AddError.'<br>An artist must be associated with media';
		$AddSuccess = FALSE;
	}
	if($Type =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A media type must be selected.';
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
	if(trim($Desc) == '')
	{
		$Desc = $MediaType.' uploaded by '.$Uploader;
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
			addMediaByAdmin($MediaType, $ImgURL, $Artist,$Uploader, $Desc);
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

if($function == 'adminAddMediaFromLocal')
{
	$AddSuccess = true;
	$AddError ='File not uploaded.';
	$Artist = $_REQUEST['Artist'];
	$Uploader = $_REQUEST['UploadedBy'];
	$Desc = $_REQUEST['Desc'];
	$MediaType = $_REQUEST['Type'];
	
	$FileType = $_FILES['uploadedFile']['type'];
	$FileName = $_FILES['uploadedFile']['name'];
	$FileName = str_replace(" ","",$FileName);
	$FileContent = file_get_contents($_FILES['uploadedFile']['tmp_name']);
	
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
	if($ext == "jpg" or $ext == "png" or $ext == "gif" or $ext =='jpeg')
	{
		$AcceptableDimensions = getMediaDimensionsByType($MediaType);
		$AcceptableHeight = $AcceptableDimensions->value("Height");
		$AcceptableWidth = $AcceptableDimensions->value("Width");
		$Dimensions = getimagesize($_FILES['uploadedFile']['tmp_name']);
		$Width = $Dimensions[0];
		$Height = $Dimensions[1];
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
	
	if($Artist == '')
	{
		$AddError= $AddError.'<br>An artist must be associated with media';
		$AddSuccess = FALSE;
	}
	if($MediaType =='')
	{
		$AddSuccess = false;
		$AddError = $AddError.'<br>A media type must be selected.';
	}
	if(trim($Desc) == '')
	{
		$Desc = $MediaType.' uploaded by '.$Uploader;
	}
	if($AddSuccess) 
	{
		//check success
		if(file_put_contents("../media/$name",$FileContent) )
		{
			$ImgURL = 'https://www.comicadia.com/media/'.$name;
			addMediaByAdmin($MediaType, $ImgURL, $Artist,$Uploader, $Desc);
			print("Succes");
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

if($function == 'updateCadenceSplash')
{
	$URL = $_REQUEST['URL'];
	print("Updated");
	updateSplashArt($URL);
}

if($function == 'reloadPreview')
{
	$Splash = getSplash();
	if($Splash)
	{
		$SplashTitle = $Splash->value("Title");
		$CadenceArt = $Splash->value("URL");
		$SplashText = $Splash->value("Text");
		print("<div id='FrontPageSplash'>
		<div id='<CadenceAnnounces' style='background-image: url($CadenceArt); Height: 200px; Width: 150px'></div>
		<div id='splashMessage'><span id='splashHeader'>$SplashTitle</span><span id='splashMessage'>$SplashText</span></div>
		</div>");
	}
	else
	{
		print("No preview available at this time");
	}
}

if($function == 'updateSplashMessage')
{
	$Text = trim($_REQUEST["Text"]);
	$Title = trim($_REQUEST['Title']);
	$URL = $_REQUEST['URL'];
	$Alias= $_REQUEST["Alias"];
	$Success = TRUE;
	$Error = 'Spalsh message not updated';
	if($URL == '')
	{
		$Success = FALSE;
		$Error = $Error. "<br>Splash messages require an announcement picture";
	}
	if($Text == '')
	{
		$Success = FALSE;
		$Error = $Error . "<br>Splash messages require text.";
	}
	if($Title == '')
	{
		$Success = FALSE;
		$Error = $Error . "<br>Splash messages require a Title.";
	}
	if($Success)
	{
		if(checkIfSplashMessageExists())
		{
			print("Splash Message updated");
			updateSplashMessage($Title,$Text,$Alias,$URL);
		}
		else
		{
			print("Splash Message Created");
			createSplashMessage($Title,$Text,$Alias,$URL);
		}
	}
	else
	{
		print($Error);
	}
}

if($function == 'submitContactMessage')
{
	$Email = $_REQUEST['Email'];
	$Name = $_REQUEST['Name'];
	$Message = $_REQUEST['Message'];
	$Type = $_REQUEST['Type'];
	$Success = true;
	$error = 'Message not sent';
	if(trim($Name) == '')
	{
		$Success = false;
		$error = $error . 'Name required.<br>';
	}
	if(trim($Email) == '') 
	{
		$Success = false;
		$error = $error . 'Email required.<br>';
	}
	if(trim($Message) == '')
	{
		$Success = false;
		$error = $error . 'Message cannot be blank<br>';
	}
	if($Success)
	{
		saveContactMessage($Name, $Email, $Message,$Type);
		print("Message sent");
	}
	else
	{
		print($error);
	}

}

if($function == 'markMessageAsRead')
{
	$MessageID = $_REQUEST['MessageID'];
	$Alias = $_REQUEST['Alias'];
	if(getUserType($Alias) != 'Admin')
	{
		print("You do not have permission to do this");
	}
	else
	{
		print("Marked as read");
		markMessageAsRead($MessageID, $Alias);
	}
}

if($function == 'updateUserSocialMedia')
{
	$Alias = $_REQUEST['Alias'];
	$MediaList = json_decode($_REQUEST['SocialMedias']);
	$HandleList = json_decode($_REQUEST['UserHandles']);
	$Error = "An error occured. The following fields were not saved:";
	$FailCount = 0;
	$blankCount = 0;
	for($i =0; $i < count($MediaList); $i++)
	{
		$SocMedia = $MediaList[$i];
		$URL = $HandleList[$i];
		removeSocialMediaURL($Alias, $SocMedia);
		if($URL)
		{			
			addSocialMediaURL($Alias, $SocMedia, $URL);
		}
		else
		{
			$blankCount +=1;
		}
	}
	
	if($blankCount == count($MediaList))
	{
		print("All social media links erased.");
	}
	elseif($FailCount == 0)
	{
		print("All social media accounts regsitered with no errors");
	}
	else
	{
		print($Error);
	}
}

if($function =='updateComicSocialMedia')
{
	$ComicID = $_REQUEST['ComicID'];
	$MediaList = json_decode($_REQUEST['SocialMedias']);
	$HandleList = json_decode($_REQUEST['UserHandles']);
	$Error = "An error occured. The following fields were not saved:";
	$FailCount = 0;
	$blankCount = 0;
	for($i =0; $i < count($MediaList); $i++)
	{
		$SocMedia = $MediaList[$i];
		$URL = $HandleList[$i];
		removeSocialMediaURLFromComic($ComicID, $SocMedia);
		if($URL)
		{
			if(isValidUrl($URL))
			{
				addSocialMediaURLtoComic($ComicID, $SocMedia, $URL);
			}
			else
			{
				$Error = $Error."<br>$SocMedia did not have a valid URL.";
				$FailCount += 1;
			}
		}
		else
		{
			$blankCount +=1;
		}
	}
	
	if($blankCount == count($MediaList))
	{
		print("All social media links erased.");
	}
	elseif($FailCount == 0)
	{
		print("All social media accounts regsitered with no errors");
	}
	else
	{
		print($Error);
	}
}

if($function == 'addNewSocialMediatype')
{
	$Name = trim($_REQUEST['Name']);
	$Class = $_REQUEST['Class'];
	$Color = $_REQUEST['Color'];
	$success = true;
	$error = 'Social Media Type not saved';
	if(!$Color)
	{
		$Color = '#ffffff';
	}
	if($Class == '')
	{
		$Class = 'GenericSocMediaBack';
	}
	if($Name == '')
	{
		$success = false;
		$error = $error . "<br>A social media type name cannot be blank";
	}
	if($success)
	{
		if(checkDuplicateSocialMediaName(trim($Name)) > 0)
		{
			print("There is already a Social Media Type with that name.");
		}
		else
		{
			saveNewSocialMediaType($Name, $Color, $Class);
			print("Social Media Type Added");
		}
	}
	else
	{
		print($error);
	}
}

if($function == 'adminModifySocialMedia')
{
	$Name = trim($_REQUEST['Name']);
	$Class = $_REQUEST['Class'];
	$Color = $_REQUEST['Color'];
	$success = true;
	$error = 'Social Media Type not saved';
	if(!$Color)
	{
		$Color = '#ffffff';
	}
	if($Class == '')
	{
		$Class = 'GenericSocMediaBack';
	}
	if($Name == '')
	{
		$success = false;
		$error = $error . "<br>A social media type name cannot be blank";
	}
	if($success)
	{
		{
			editSocialMediaType($Name, $Color, $Class);
			print("Social Media Type modified");
		}
	}
	else
	{
		print($error);
	}
}


if($function == 'adminDeleteSocialMedia')
{
	$Name = trim($_REQUEST['Name']);
	print("Deleted");
	removeSocialMediaType($Name);
}

if($function == 'adminSendMessageFromNoReply')
{
	$Alias = $_REQUEST['Alias'];
	$type = getUserType($Alias);
	if($type != 'Admin')
	{
		print("You do not have permission to send an email from the no-reply account");
	}
	else
	{
		$Message = $_REQUEST['Message'];
		$Recipient = $_REQUEST['Recipient'];
		$Subject = $_REQUEST['Subject'];
		if(trim($Message) == '' || trim($Subject) == '')
		{
			print("An email requires something in the message and the subject text fields.");
		}
		else
		{
			adminSendMessageFromNoReply($Alias, $Recipient, $Subject, $Message);
		}
	}
}

if($function == 'adminRejectAd')
{
	$Alias = $_REQUEST['Alias'];
	$AdID = $_REQUEST['AdID'];
	$Reason = test_input($_REQUEST['Reason']);
	if(trim($Reason) == '')
	{
		print("Reason cannot be blank for rejecting an ad");
	}
	else
	{
		adminRejectAd($AdID, $Alias, $Reason);
		print("Success");
	}
}

if($function == 'adminApproveAd')
{
	$Alias = $_REQUEST['Alias'];
	$AdID = $_REQUEST['AdID'];
	adminApproveAd($AdID, $Alias);
	print("Success");
}
?>