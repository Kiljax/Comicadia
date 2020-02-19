<?php
include "UserFunctions.php";

function buildControlPanel()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Subscriber')
		{
			print("
				<input type='button' id='ControlPanelHome' class='leftPanelItem' value='Dashboard' onclick=\"window.location.href = 'http://www.comicadia.com/cpanel.php'\">
			<form action='?' method='GET'>
				<input type='submit' id='editUser' class='leftPanelItem' value='Edit Profile' name='submit'>
				<input type='submit' id='editUserWebcomic' class='leftPanelItem' value='Manage Webcomics' name='submit'>				
				</form>
				");
		}
		elseif($type == 'Member' || $type == 'Admin')
		{
			print("
				<input type='submit' id='ControlPanelHome' class='leftPanelItem' value='Dashboard' onclick=\"window.location.href = 'http://www.comicadia.com/cpanel.php'\">
			<form action='?' method='GET'>
				<input type='submit' id='editUser' class='leftPanelItem' value='Edit Profile' name='submit'>
				<input type='submit' id='editUserWebcomic' class='leftPanelItem' value='Manage Webcomics' name='submit'>
				<input type='submit' id='writeUserNews' class='leftPanelItem' value='Manage News' name='submit'>
				<input type='submit' id='addEvent' class='leftPanelItem' value='Manage Events' name='submit'>
				<input type='submit' id='addEvent' class='leftPanelItem' value='Manage Merch' name='submit'>
				</form>
				");
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}
	
}

function buildUserWriteNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Member' || $type == 'Admin')
		{
			$categories = getAllNewsCategories();
			print("
			<div id='MangeNews' class='cpanelHeader'><h2>Manage News</h2></div>
			<input type='button' id='addNewsClickable' value='Write News'>
			<div id='addNewsInternal' class='Internal'>
			<div id='newsCategory'><strong>Category:</strong><select class='cpanelSelect'  id='newsCategorySelect' name='$type'>");
			foreach($categories as $record)
			{
				print("<option value='$record'>$record</option>");
			}
			print("</select>
			<div id='newsTitle'><strong>Title:</strong><input type='text' id='newsTitleText' name='$alias' value=''></div>
			<div id='newsDetails'><strong>Details<br></strong><textarea id='newsDetailsText'  class='summernote' name='$email'></textarea></div>
			<div id='newsPubDate'><strong>Date to Publish:</strong><input type='text' id='newsDatepicker' name='newsDatepicker'></p></div> 
			<div id='newsSubmit'><input type='button' id='submitNewsButton' name='CreateNewsButton' value='Create' onclick='postNews(\"$alias\")' class='submitBTN'></div>
			<div id='PostMSG'></div>
			</div></div>");
			
			print("<script>
				$(function(){
					$('*[name=newsDatepicker]').appendDtpicker({
											\"dateformat:\": \"DD-MM-YYYY hh:mm\",
											\"minuteInterval\": 30,
											\"futureOnly\": true,
											\"dateOnly\": true											
					});
				});
			$('#addNewsClickable').click
					(
						function()
						{
							$('#addNewsInternal').slideToggle();
					});
			</script>");
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}
}

function buildUserEditNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Member' || $type == 'Admin')
		{
			$NewsList = getUserEditNews($alias);
			/*
			print("<div id='editNewsWrap'><div id='editNewsHeadClickable' class='dropLevel1'>Edit Your News</div>");
			print("<div id='editNewsHeadInternal' class='Internal'>");
			print("
			<script>
			$('#editNewsHeadClickable').click
					(
						function()
						{
							$('#editNewsHeadInternal').slideToggle();
					});
			</script>");
			*/
			$NewsCount =0;
			foreach($NewsList->getRecords() as $record)
			{
				$PubDate = $record->value('DatePublished');
				$Title = $record->value('Title');
				$Details = $record->value('Details');
				$DateWritten = $record->value('DateWritten');
				$PubDate = date('Y-m-d', $PubDate / 1000);
				
				$Category = $record->value('Category');
				$Status = $record->value('Status');
				print("<div id='NewsItem$NewsCount' class='NewsItem'>
				<div id='EditNewsClickable$NewsCount' class='dropLevel0'><span class='NewsHeader'>$Title</span></div>
				<div id='EditNewsInternal$NewsCount' class='Internal'>
				<form>
				<div class='newsTitleDIV'>Title: <input type='text' id='newsTitleFor$NewsCount' value='$Title' class='newsTitle' disabled></div>
				<div class='newsCategoryDIV'>Category: <select class='cpanelSelect'  id='newsCategoryFor$NewsCount' disabled>");
				$newsCategories = getCategories();
				foreach($newsCategories as $newsCategory)
				{
					if($newsCategory = $Category)
					{
						$Selected = 'Selected';
					}
					else
					{
						$Selected = '';
					}
					
					print("<option value='$newsCategory' $Selected>$newsCategory</option>");
				}
				print("</select></div>
				<div class='newStatusDIV'>Status: $Status</div>
				<div class='newsPubDateDIV'>Date to Publish: <input type='text' id='editDatepickerFor$NewsCount' name='editDatepickerFor$NewsCount' value='$PubDate' disabled></div>
				<div class='newsDetailsDIV'>Details:<br><textarea id='newsDetailsFor$NewsCount' class='newsDetails' disabled>$Details</textarea>
				</form></div>
				<input type='button' id='editNews$NewsCount' value='Edit' class='editNewsbutton'>
				<input type='button' id='saveNews$NewsCount' value='Save' class='editNewsbutton up' disabled onclick=\"saveNewsEdits('$NewsCount', '$DateWritten','$alias','$Status');\"> 
				<input type='reset' id='resetNews$NewsCount' value='Reset' class='editNewsbutton rem' disabled>
				<input type='button' id='DeleteNews$NewsCount' value='Delete' class = editNewsButton del' onclick='removeNews(\"$NewsCount\",\"$DateWritten\",\"$alias\")'>
				<div id='newsErrMSG$NewsCount' class='errMSG'></div>
				</div>");
				print("</div>");
				print("<script>
				
				$(function(){
					$('*[name=editDatepickerFor$NewsCount]').appendDtpicker({
											\"dateformat:\": \"DD-MM-YYYY\",
											\"dateOnly\": true,
											\"current\": \"$PubDate\"
					});
				});
					
				$('#editNews$NewsCount').click
					(
						function()
						{
							$('#editNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#resetNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsTitleFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsCategoryFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#editDatepickerFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsDetailsFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#saveNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsDetailsFor$NewsCount').summernote(
							{focus:true, height: 200,
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
							}});
					});
				$('#saveNews$NewsCount').click
					(
						function()
						{
							saveNewsEdits('$NewsCount');
							$('#resetNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsTitleFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsCategoryFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#editDatepickerFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsDetailsFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#saveNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							var markup = $('#newsDetailsFor$NewsCount').summernote('code');
							$('#newsDetailsFor$NewsCount').summernote('destroy');
					});
				$('#resetNews$NewsCount').click
					(
						function()
						{
							$('#editNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#resetNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsTitleFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsCategoryFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#editDatepickerFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#newsDetailsFor$NewsCount').prop('disabled', function(i, v) { return !v; });
							$('#saveNews$NewsCount').prop('disabled', function(i, v) { return !v; });
							var markup = $('#newsDetailsFor$NewsCount').summernote('code');
							$('#newsDetailsFor$NewsCount').summernote('destroy');
					});	
					
					$('#EditNewsClickable$NewsCount').click
					(
						function()
						{
							$('#EditNewsInternal$NewsCount').slideToggle();
					});
					
					</script>");
				$NewsCount += 1;
			}
			
			//print("</div>"); //end Internal
			print("</div>");
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}	
}

function returnHome()
{
	header("Location: http://www.comicadia.com/index.php");
}

function buildEditProfile()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		$User = getUserDetails($alias);
		
		if($type == 'Member' || $type == 'Admin' || $type == 'Subscriber')
		{
			$FirstName = $User->value("FirstName");
			$LastName = $User->value("LastName");
			$Alias = $User->value("Alias");
			$Email = $User->value("Email");
			$Pic = $User->value("Pic");
			print("<div id='EditProfile'>
			<div id='EditProfileHeader' class='cpanelHeader'>
			<h2>Edit Profile:</h2></div>
			<div id='PersonalInfo'>
						<h2>Personal Info:</h2>
						<div id='profilePic' class='profilePic content-box'><img src='$Pic'><br>
						<input type='button' id='changeProfileClickable' class='changeProfilePicButton' value='Change Profile Picture'>
							<div id='changeProfilePicInternal' class='Internal'>
								<input type='button' id='uploadFromLocalClickable' value='Upload from machine' class='uploadFromMachineButton'> <input type='button' id='uploadFromWebClickable' value='Upload from the web' class='uploadFromWebButton'>
								<div id='uploadFromLocalInternal' class='Internal'>
									File Location: <input type='file' id='uploadNewProfileFromLocalFile' name='uploadNewProfileFromLocalFile' class='addFileText'><br>
									<input type='button' id='uploadNewProfilePicLocal' name='$Pic' value='Upload' onclick=\"uploadNewProfileFromLocal('$alias');\">
								</div>
								<div id='uploadFromWebInternal' class='Internal'>
									URL: <input type='url' id='uploadNewProfileFromWebURL'><br>
									<input type='button' id='uploadNewProfilePicWeb' value='Upload' onclick=\"uploadNewProfileFromWeb('$alias');\">
								</div>
							<div id='uploadProfileMSG'></div>
							</div></div>");
							print("<script>
							$('#uploadFromLocalClickable').click
							(
							function()
							{
								$('#uploadFromLocalInternal').show();
								$('#uploadFromWebInternal').hide();
								
							});
							$('#uploadFromWebClickable').click
							(
							function()
							{
								$('#uploadFromLocalInternal').hide();
								$('#uploadFromWebInternal').show();
								
							});
							</script>");
							
			print("<div class='content-box'><form>
				First Name: <input type='text' id='FirstNameText' value='$FirstName' disabled><br>
				Last Name: <input type='text' id='LastNameText' value ='$LastName' disabled><br>
				Alias: <input type='text' id='AliasText' name='$Alias' value='$Alias' disabled><br>
				Email: <input type='text' id='EmailText' name='$Email'value='$Email' disabled><br>
				<input type='button' id='editProfileChangesButton' class='profileButton' value='Edit'>
				<input type='button' id='saveProfileChangesButton' class='profileButton up' value='Save Changes' onclick='saveProfileChanges()' disabled>
				<input type='reset' id='resetProfileChangesButton' class='profileButton rem' value='Cancel' disabled><br>
				<div id='updateProfileMSG'></div></form>
				</div>
				<div>
				<input type='button' id='PasswordResetButton' class='resetPasswordButton' value='Reset Password'>  
				<div id='ResetPasswordInternal' class='Internal'>
					Current Password: <input type='text' id='CurrentPasswordText'><br>
					New Password: <input type='text' id='NewPasswordText'><br>
					Confirm Password: <input type='text' id='ConfirmPasswordText'><br>
					<input type='button' id='ResetPasswordButton' class='profileButton' onclick=\"resetPassword();\" value='Confirm Password Reset'>
					<div id='changePasswordMSG' class='errMSG'></div>
				</div>
				
			</div>");
			print("<div id='userSocialMedia'>");
			print("<div id='userSocialMediaClickable' class='dropLevel1'>Social Media</div>");
			print("<div id='userSocialMediaInternal' class='Internal'>");
			$NameList = "[";
			$SocialMediaList = getAllSocialMediaTypes();
			foreach($SocialMediaList->getRecords() as $SocMedia)
			{
				$Class = $SocMedia->value("Class");
				$SocName = $SocMedia->value("Name");
				$BGColor = $SocMedia->value("BGColor");
				$NameList = $NameList ."'$SocName',";
				$YourSocMediaURL = getSpecificSocialMediaURLByName($SocName,$alias);
				if($YourSocMediaURL)
				{
					
				}
				else
				{
					$YourSocMediaURL = '';
				}
				print("<div id='userSocialMedia$SocName'><span class='socialMediaInput'><span class='profilelMediaIcon' style='background-color: $BGColor;'><i class='$Class'></i></span> URL: </span><input id='user".$SocName."Text' type='text' value='$YourSocMediaURL'></span></div>");
			}
			$NameList = rtrim(trim($NameList),',');
			$NameList = $NameList ."]";
			print("<div id='socialMediaSaveDIV'><input type='button' id='userUpdateSocialMedia' value='Save Changes' onclick=\"updateUserSocialMedia('$alias',$NameList);\"></div>");
			print("<div id='userSocialMediaMSG' class='errMSG'></div>");
			print("</div>"); // End userSociaMediaInternal
			print("</div>"); //End userSocialMedia	
		print("</div>");
		print("<div class='clear'></div>");
			print("<script>
					$('#userSocialMediaClickable').click
					(
						function()
						{
							$('#userSocialMediaInternal').slideToggle();
					});
					$('#PasswordResetButton').click
					(
						function()
						{
							$('#ResetPasswordInternal').slideToggle();
					});
					$('#changeProfileClickable').click
					(
						function()
						{
							$('#changeProfilePicInternal').slideToggle();
					});
					
					$('#editProfileChangesButton').click
					(
						function()
						{
							$('#editProfileChangesButton').prop('disabled', function(i, v) { return !v; });
							$('#resetProfileChangesButton').prop('disabled', function(i, v) { return !v; });
							$('#FirstNameText').prop('disabled', function(i, v) { return !v; });
							$('#LastNameText').prop('disabled', function(i, v) { return !v; });
							$('#AliasText').prop('disabled', function(i, v) { return !v; });
							$('#EmailText').prop('disabled', function(i, v) { return !v; });
							$('#saveProfileChangesButton').prop('disabled', function(i, v) { return !v; });
					});
					$('#saveProfileChangesButton').click
					(
						function()
						{
							$('#FirstNameText').prop('disabled', function(i, v) { return !v; });
							$('#resetProfileChangesButton').prop('disabled', function(i, v) { return !v; });
							$('#LastNameText').prop('disabled', function(i, v) { return !v; });
							$('#AliasText').prop('disabled', function(i, v) { return !v; });
							$('#EmailText').prop('disabled', function(i, v) { return !v; });
							$('#saveProfileChangesButton').prop('disabled', function(i, v) { return !v; });
					});
					$('#resetProfileChangesButton').click
					(
						function()
						{
							$('#editProfileChangesButton').prop('disabled', function(i, v) { return !v; });
							$('#FirstNameText').prop('disabled', function(i, v) { return !v; });
							$('#resetProfileChangesButton').prop('disabled', function(i, v) { return !v; });
							$('#LastNameText').prop('disabled', function(i, v) { return !v; });
							$('#AliasText').prop('disabled', function(i, v) { return !v; });
							$('#EmailText').prop('disabled', function(i, v) { return !v; });
							$('#saveProfileChangesButton').prop('disabled', function(i, v) { return !v; });
					});
					</script>");
			$WebcomicList = getUsersWebcomics($Alias);
			$WebcomicCount = getUserWebcomicsCount($Alias);
			if($WebcomicCount > 0)
			{
				print("<div id='UsersWebcomicsWrap'>
				<h2>Your Comics</h2>
				<div id='YourComicsInternal'>");
				$ComicCount = 0;
				foreach($WebcomicList->getrecords() as $Webcomic)
				{
					$ComicName = $Webcomic->value('Name');
					$DivName = $ComicCount;
					$RoleList = $Webcomic->value('Role');
					$RoleString = '';
					$IsCreator = FALSE;
					if(is_array($RoleList))
					{
						foreach($RoleList as $Role)
						{
							if($Role == 'Creator' || $Role == 'Co-Creator')
							{
								$IsCreator = TRUE;
							}
							$RoleString = $RoleString .$Role.",";
						}
						$RoleString = rtrim(trim($RoleString),',');
					}
					else
					{
						$RoleString = $RoleList;
						if($RoleString == 'Creator' || $RoleString =='Co-Creator')
						{
							$IsCreator = true;
						}
					}	
					print("<div id='".$DivName."Details'>
								<div id='Clickable$DivName' class='dropLevel0 ComicLista'><span>$ComicName</span></div>
								<div id='Internal$DivName' class='Internal'>");
					print("Roles: $RoleString</div> ");
								
					
					print("</div>");
					print("<script>
					$('#Clickable$DivName').click
					(
						function()
						{
							$('#Internal$DivName').slideToggle();
					});
					</script>");
					$ComicCount +=1;
				}
				print("</div></div></div>");
				print("
				<script>
					$('#YourComics').click
					(
						function()
						{
							$('#YourComicsInternal').slideToggle();
					});
				</script>");
			}
			else
			{
				print("<div id='addYourOwnComic'>You are not a part of any webcomic. Would you like to <a href='http://www.comicadia.com/cpanel?submit=Add+Webcomic'>add your own?</a></div>");
			}
		}
		else
		{
			returnHome();
		}	
	}
	else
	{
		returnHome();
	}
			
}
function buildUserAddWebcomic()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		$User = getUserDetails($alias);
		
		if($type == 'Member' || $type == 'Admin' || $type == 'Subscriber')
		{
			$MembershipList = getComicMemberships();
			$StatusList = getWebcomicStatusList();
			print("<div id='addWebomic'>
				<div id='manageWebcomicsHeader' class='cpanelHeader'><h2>Manage Webcomics</h2></div>			
				<input type='button' id='addWebomicHeadClickable' class='addComicButton' value='Add a Webcomic'>
				<div id='addWebcomicHeadInternal' class='Internal'>");
			print("<script>
			$('#addWebomicHeadClickable').click
					(
						function()
						{
							$('#addWebcomicHeadInternal').slideToggle();
					});
			</script>");
			print("<div id='webcomicTitle' class='addWebcomicDIV'><strong>Webcomic Name:</strong>
			<br><input type='text' class='webcomicText' id='webcomicNameText'></div>
			<div id='webcomicURL' class='addWebcomicDIV'><strong>URL:</strong>
			<br><input type='text' class='webcomicText' id='webcomicURLText'></div>
			<div id='webcomicRSS' class='addWebcomicDIV'><strong>RSS feed:</strong>
			<br><input type='text' class='webcomicText' id='webcomicRSSText'></div>
			<div id='webcomicFormat' class='addWebcomicDIV'><strong>Format:</strong>
			<br><select class='cpanelSelect'  id='addWebcomicFormatSelect'>");
			$FormatList = getWebcomicFormats();
			foreach($FormatList as $Format)
			{
				print("<option value='$Format'>$Format</option>");
			}
			print("</select></div>");
			print("<div id='webcomicPitch' class='addWebcomicPitchDIV'><strong>Pitch:</strong><br>
			<input type='text' id='addWebcomicPitchText' class=addWebcomicPitchText'></div>");
			print("<div id='webcomicSynopsis' class='addWebcomicDIV'><strong>Synopsis:</strong><br>
			<textarea id='addWebcomicSynopsisText' class='summernote'></textarea></div>");
			print("<div id='addWebcomicGenres' class='addWebcomicDIV'><strong>Genres</strong>");
					$GenresList = getAllWebcomicGenres();
												
					print("<select class='cpanelSelect'  id='FirstGenre' >");
					print("<option value=''>None</option>");
					foreach($GenresList->getRecords() as $FirstGenre)
					{
						$GenreName = $FirstGenre->value("Name");
						print("<option value='$GenreName'>$GenreName</option>");
					}
					print("</select>");
					
					
					print("<select class='cpanelSelect'  id='SecondGenre'>");
					print("<option value=''>None</option>");
					
					foreach($GenresList->getRecords() as $SecondGenre)
					{
						$GenreName = $SecondGenre->value("Name");
						print("<option value='$GenreName'>$GenreName</option>");
					}
					print("</select>");
					
					print("<select class='cpanelSelect'  id='ThirdGenre'>");
					print("<option value=''>None</option>");
					
					foreach($GenresList->getRecords() as $ThirdGenre)
					{
						$GenreName = $ThirdGenre->value("Name");
						print("<option value='$GenreName'>$GenreName</option>");
					}
					print("</select>");
			print("</div>");
			
			print("<div id='addWebcomicThemes' class='addWebcomicDIV'><strong>Themes</strong><br>");
			
			$ActiveThemeList = getActiveThemes();
			foreach($ActiveThemeList->getRecords() as $activeThemes)
			{
				$activeTheme = $activeThemes->value("Name");
				print("<div class='checkboxWrap'><input type='checkbox' name='themeCheckbox' class='themeSelectCheckboxes' value='$activeTheme'>$activeTheme</div>");
			}
			print("</div>");
			print("<br><input type='button' value='Add Webcomic' id='addWebcomicBTN' onclick='addWebcomic(\"$alias\");'>
			<div id='addWebcomicMSG' class='errMSG'></div>
			</div></div>");
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}
}

function buildUserAddEvent()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		$User = getUserDetails($alias);
		
		if($type == 'Member' || $type == 'Admin')
		{
			print("<div id='addEventHeader' class='cpanelHeader'><h2>Manage Events</h2></div>
			<input type='button' id='addEventHeadClickable' value='Schedule an Event'>
			<div id='addEventHeadInternal' class='Internal'>");
			print("
			<script>
			$('#addEventHeadClickable').click
					(
						function()
						{
							$('#addEventHeadInternal').slideToggle();
					});
			</script>			
			");
			print("<div id='addEventDIV'>
				<div id='addEventTitle' class='cpanelItem'><strong>Event Title:</strong><br>
					<input type='text' id='addEventTitleText' value=''></div>
					<div id='addEventLocation' class=cpanelItem'><strong>Event Location:</strong><br>
					<input type='text' id='addEventLocationText'></div>
				<div id='eventDatepickerwrap'>	
				<strong>Event Date:</strong><br>");
				print("<input type='text' id='addEventDateText' value='' name='EventDatePicker'>
					<script type='text/javascript'>
						$(function(){
							$('*[name=EventDatePicker]').appendDtpicker({
													\"dateformat:\": \"DD-MM-YYYY hh:mm\",
													\"minuteInterval\": 30,
													\"futureOnly\": true			
							});
						});
					</script>
					</div>
					<div id='addEventCategory' class='cpanelItem'>
					<strong>Category</strong><br>
					<select class='cpanelSelect'  id='addEventCategorySELECT'>");
						$CategoryList = getCategories();
						foreach($CategoryList as $Category)
						{
							print("<option value='$Category'>$Category</option>");
						}
					print("</select></div>
					<div id='addEventType' class='cpanelItem'>
						<strong>Event type:</strong><br>
						<select class='cpanelSelect'  id='addEventTypeSELECT'>");
						$TypeList = getEventTypes();
						foreach($TypeList as $Type)
						{
							print("<option value='$Type'>$Type</option>");
						}
					print("</select></div>
					<div id='addEventDetails' class='cpanelItem'>
						<strong>Details:</strong><br>
						<textarea id='addEventDetailsText' class='summernote'></textarea>		
					</div>
					<input type='button' name='addEventButton' id='addEventBTN' onclick='scheduleUserEvent(\"$alias\")' value='Schedule Event'>
					<div id='addEventMSG' class='errMSG'></div>
				</div></div>");
		}
		else
		{
			returnHome();
		}
	}
	else	
	{
		returnHome();
	}
} 

function buildUserEditEvent()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Member' || $type == 'Admin')	
		{/*
			print("<div id='editEventHeadClickable' class='dropLevel1'>Edit Your Events</div>
			<div id='editEventHeadInternal' class='Internal'>");
			print("
			<script>
				$('#editEventHeadClickable').click
					(
						function()
						{
							$('#editEventHeadInternal').slideToggle();
					});
			</script>");
			*/
			print("<div id='editEventWrap'>");
			$EventList = getAllUserEvents($alias);
			
			foreach($EventList->getRecords() as $Event)
			{
				$DateWritten= $Event->value("DateCreated");
				$Start = $Event->value("Start_Time");
				$Type = $Event->value('Type');
				$DivAdd = str_replace(' ', '', $Type);
				$Title = $Event->value('Title');
				$Organizer = $Event->value('Alias');
				$Location = $Event->value('Location');
				$Email = $Event->value('Email');
				$Category = $Event->value('Category');
				$DisplayDate =date('d-m-Y H:i', $Start/1000);
				$CalendarDefault = date('Y-m-d H:i', $Start / 1000);
				$Details = $Event->value('Details');
				$Status = $Event->value('Status');
				
				$DivID = $DivAdd . 'Event' . $Start;
				
				print("<div class='EventEntry' id='Event$DivID'>
							<div id='EditEventClickable$DivID' class='dropLevel0'><span class='EventHeader'>$Title</span></div>");
				print("<div id='EditEventInternal$DivID' class='Internal'>");
				print("<form>");
				print("<div class='EventItem'><strong>Title:</strong> <input type='text' id='Title$DivID' value=\"$Title\" name=\"$Title\" disabled class='edit$DivID'></div>");
				
				print("<div class='EventItem'><strong>Event Date/Time</strong>
						<input type='text' id='editEventDate$DivID' value='' disabled name='EventDatePicker$DivID'>
						<script>
							$(function(){
								$('*[name=EventDatePicker$DivID]').appendDtpicker({
												\"dateformat:\": \"DD-MM-YYYY hh:mm\",
												\"minuteInterval\": 30,
												\"current\": \"$CalendarDefault\",
												\"futureOnly\": true							
								});
							});
						</script>
						</div>");
				print("<div class='EventItem'><strong>Location:</strong> <input type='text' id='Location$DivID' name='$DateWritten' value=\"$Location\" disabled class='edit$DivID'></div>
						<div class='EventItem'>
							<strong>Type:</strong><select class='cpanelSelect'  id='Type$DivID' name='$Type' disabled>");
				$TypeList = getEventTypes();
				foreach($TypeList as $PType)
				{
					if($PType == $Type)
					{
						$Selected = 'Selected';
					}
					else
					{
						$Selected = '';
					}
					print("<option value='$PType' $Selected>$PType</option>");
				}
				print("	</select></div>
				<div class='EventItem'>
				<strong>Category:</strong><select class='cpanelSelect'  id='Category$DivID' disabled>");
				$CategoryList = getCategories();
				foreach($CategoryList as $Cat)
				{
					if($Cat == $Category)
					{
						$Selected = 'Selected';
					}
					else
					{
						$Selected = '';
					}
					
					print("<option value='$Cat'>$Cat</option>");
				}
				print("</select></div>");
				print("	<div class='EventItem'>");
				print("<div class='EventItem'>
				<strong>Status: </strong> $Status<br></div>");
				
				print("<div class='EventDetails'>
				<strong>Details:</strong><br><textarea id='Details$DivID' name='$Start' disabled>$Details</textarea>
				</div>
				<div id='Options$DivID'>
					<input type='button' id='Edit$DivID' class='EnableEdit' value='Edit'> 
					<input type='button' id='Save$DivID' class='SaveEdits up' value='Save' disabled onclick='saveEventEdits(\"$DivID\",\"$Organizer\")'> 
					<input type='reset' id='Cancel$DivID' class= CancelEdits' value='Cancel' disabled>
					<input type='button' id='Delete$DivID' class='DeleteEvent rem' onclick=\"deleteEvent('$DateWritten','$DivID')\" value='Remove'> 
					<div id='errMSGEvent$DivID'></div>
					</form>
				</div>
				</div>
				</div>
				</div>
				<script>
				
				$('#EditEventClickable$DivID').click
				(
					
					function()
					{
						$('#EditEventInternal$DivID').slideToggle();
					});
				
				$('#Edit$DivID').click
				(
					function()
					{
						$('#Save$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Title$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Location$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Organizer$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Category$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Type$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Details$DivID').prop('disabled', function(i, v) { return !v; });
						$('#editEventDate$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Status$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Edit$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Cancel$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Details$DivID').summernote({focus: true, height: 200,
						toolbar: 
						[
							//[groupName, [list of button]]
							['style', ['bold', 'italic', 'underline']],
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
					}});
				});
				
				$('#Cancel$DivID').click
				(
					function()
					{
						$('#Save$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Title$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Location$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Organizer$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Category$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Type$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Details$DivID').prop('disabled', function(i, v) { return !v; });
						$('#editEventDate$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Status$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Edit$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Cancel$DivID').prop('disabled',function(i,v) {return !v; });
						var markup = $('#Details$DivID').summernote('code');
						$('#Details$DivID').summernote('destroy');
				});
				
				$('#Save$DivID').click
				(
					function()
					{
						$('#Save$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Title$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Location$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Organizer$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Category$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Type$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Details$DivID').prop('disabled', function(i, v) { return !v; });
						$('#editEventDate$DivID').prop('disabled', function(i, v) { return !v; });
						$('#Status$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Edit$DivID').prop('disabled',function(i,v) {return !v; });
						$('#Cancel$DivID').prop('disabled',function(i,v) {return !v; });
						var markup = $('#Details$DivID').summernote('code');
						$('#Details$DivID').summernote('destroy');
				});
			</script>
			<div id='err$DivID' class='errMSG'></div>");
			}
			//print("</div>"); //end Internal
			print("</div>");
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}
}

/*
deleteEvent('$Title','$DisplayDate','$Type','$Organizer','$DivID')
					onclick="script(' . htmlspecialchars(json_encode($row['City'])) . ')"
$('#Delete$DivID').click(function()
					{
						if(confirm('Are you sure you want to delete this event?'))
						{
							deleteEvent('$Title','$DisplayDate','$Type','$Organizer','$DivID');
						}
					}
					);
					*/

					
function buildUserDashboard()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		$Allowed = FALSE;
		
		if($type == 'Subscriber')
		{
			$EventList = getAllPublicEvents();
			$NewsList =getAllPublicNews();
			buildTheDashboard($EventList, $NewsList);
		}
		elseif($type == 'Member' || $type == 'Admin')
		{
			$EventList = getAllMemberEvents();
			$NewsList = getAllMemberNews();
			buildTheDashboard($EventList, $NewsList);
		}
		else
		{
			header("Location: http://www.comicadia.com/index.php");
		}
	}
	else
	{
			header("Location: http://www.comicadia.com/index.php");
	}
}

function buildTheDashboard($EventList, $NewsList)
{
	$NewsCount = 0;
	print("<div id='DashboardNewsWrap' class='content-box'><h2>News:</h2>");
	foreach($NewsList->getRecords() as $NewsItem)
	{
		$NewsTitle = $NewsItem->value('Title');
		$NewsDetails = $NewsItem->value('Details');
		$NewsPoster = $NewsItem->value('Alias');
		$NewsStatus = $NewsItem->value('Status');
		$NewsID = $NewsItem->value("DateWritten");
		$NewsPubDate = $NewsItem->value('DatePublished');
		$NewsPubDate = date('F jS, Y', $NewsPubDate/1000);
		if($NewsStatus != 'Approved')
		{
			$Identifier = 'Unapproved';
		}
		else 
		{
			$Identifier = 'Approved';
		}				
		print("<div class='DashboardNews$Identifier'>
			<div class='DashboardNewsTitle'>Title: $NewsTitle</div>
			<div class='DashboardNewsPoster'>Poster: $NewsPoster</div>
			<div class='DashboardNewsPubDate'>Date to Publish: $NewsPubDate</div>
			<div class='DashboardReadThis'><a href='http://www.comicadia.com/news.php?NewsID=$NewsID' target='_blank'>Check it out</a></div>
		</div>"); 
	}
	print("</div>");
	print("
	
	<div id='middleDash' class='content-box'>
		I am the middle bar.
	</div>
	<div id='rightDash' class='content-box'>");
	print("<div id='DashboardEventWrap'><h2>Events:</h2>");
	$EventCount = 0;
	foreach($EventList->getRecords() as $Event)
	{
		$EventTitle = $Event->value('Title');
		$EventStart = $Event->value('Start_Time');
		$EventStart = date('F jS, Y @ H:i:s', $EventStart/1000);
		$EventID = $Event->value("DateCreated");
		$EventOrganizer = $Event->value('Alias');
		print("<div class='DashboardEvent'>
			<div class='DashboardEventTitle'>$EventTitle</div>
			<div class='DashboardEventDate'>$EventStart</div>
			<div class='DashboardEventOrg'>$EventOrganizer</div>
			<div class='DashboardEventRead'><a href='http://www.comicadia.com/events.php?EventID=$EventID'>Check it out!</a></div>
		</div>");
		$EventCount++;
	}
	if($EventCount == 0)
	{
		print("No Events to display");
	}
	print("</div>");
	print("
			</div>
			</div>
			</div>");
	
}
function buildUserManageWebcomics()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Member' || $type == 'Admin' || $type == 'Subscriber')
		{
			/*
			print("<div id='manageUserWebcomicsWrap'><div id='manageUserWebcomicsClickable' class='dropLevel0'>Manage Your Webcomics</div>");
			*/
			//print("<div id='manageUserWebcomicsInternal' class='Internal'>");
			/*
			print("<script>
			$('#manageUserWebcomicsClickable').click
				(
					
					function()
					{
						$('#manageUserWebcomicsInternal').slideToggle();
					});
			</script>");
			*/
			//Begin Your Comics Wrap
			$UserWebcomicList = getUsersWebcomics($alias);
			$DivAdd = 0;
			foreach($UserWebcomicList->getRecords() as $Webcomic)
			{
				$ComicID = $Webcomic->value("ComicID");
				$ComicName = $Webcomic->value("Name");
				$ComicMembership = $Webcomic->value("Membership");
				$ComicURL = $Webcomic->value("URL");
				$ComicRSS = $Webcomic->value("RSS");
				$ComicSynopsis = $Webcomic->value("Synopsis");
				$ComicPitch = $Webcomic->value("Pitch");
				$UserRoles = $Webcomic->value("Role");
				$ComicFormat = $Webcomic->value("Format");
				$RoleString ='';
				
				$IsCreator = FALSE;
				if(is_array($UserRoles))
				{
					foreach($UserRoles as $UserRole)
					{
						if(trim($UserRole) == 'Creator' || trim($UserRole) =='Co-Creator')
						{
							$IsCreator = TRUE;
						}
					}
				}
				else
				{
					$RoleString = $UserRoles;
					if(trim($RoleString) == 'Creator' || trim($RoleString) == 'Co-Creator')
					{
						$IsCreator = true;
					}
				}
				
				print("<div id='".$DivAdd."Details' class='ComicLista'>
								<div id='Clickable$DivAdd' class='dropLevel0'>
								<span class='WebcomicProfileHeader'>$ComicName</span></div>
								<div id='Internal$DivAdd' class='Internal'>");
				//Begin Comic Details Wrap
				print("<script>
				$('#Clickable$DivAdd').click
				(
					function()
					{
						$('#Internal$DivAdd').slideToggle();
					});
				</script>");
				if($IsCreator)
				{
					print("<div id='webcomicDetailsFor$DivAdd' class='EditWebcomicProfile'>"); //Beginning DIV to wrap current comic, if you're the creator
					
					print("<div id='webcomicProfileClickable$DivAdd' class='dropLevel1'><span>Webcomic Profile</span></div>");
					print("<div id='webcomicProfileInternal$DivAdd' class='Internal'>"); //Beginning of Internal Div for the current comic'
					
					//
					//print("<div id='webcomicLong$DivAdd' class='webcomicLongHeader'></div>"); // Thinking of using a banner image to identify the comic, commenting out for now
					print("<form>
					Title: <input type='text' name='$ComicName' id='webcomicTitleFor$DivAdd' value='$ComicName' disabled class='editWebcomicText'><br>
					URL: <input type='text' name='$ComicURL' id='webcomicURLFor$DivAdd' value='$ComicURL' disabled class='editWebcomicText'><br>
					RSS: <input type='text' name='$ComicRSS' id='webcomicRSSFor$DivAdd' value='$ComicRSS' disabled class='editWebcomicText'><br>
					Format: <select class='cpanelSelect'  id='webcomicFormatFor$DivAdd' disabled>");
					$WebcomicFormatList = getWebcomicFormats();
					foreach($WebcomicFormatList as $WebcomicFormatChoice)
					{
						if($WebcomicFormat == $WebcomicFormatChoice)
						{
							$Selected = 'Selected';
						}
						else
						{
							$Selected = '';
						}
						print("<option value='$WebcomicFormatChoice'>$WebcomicFormatChoice</option>");
					}
					print("</select><br>");
					print("Pitch (\"Elevator Pitch\"):<br>
					<input type='text' id='webcomicPitchFor$DivAdd' class='editWebcomicPitch' value='$ComicPitch' disabled><br>");
					print("Synopsis: <br>
					<textarea id='webcomicSynopsisFor$DivAdd' class='editWebcomicTextArea' disabled>$ComicSynopsis</textarea><br>
					<div id='editWebcomicMSG$DivAdd' class='errMSG'></div>"); // Message indicator for any modifictions made to the webcomic Profile
					print("<input type='button' class='editWebcomicProfilebutton' id='editWebcomicProfile$DivAdd' value='Edit'>
					<input type='button' class='editWebcomicProfilebutton up' id='saveWebcomicProfile$DivAdd' value='Save' disabled onclick=\"saveWebcomicProfile('$ComicID','$DivAdd')\">
					<input type='reset' class='editWebcomicProfilebutton rem' id='resetWebcomicProfile$DivAdd' value='Reset' disabled>
					</form>");
					
					//Scripts to disable/enable buttons based on which button is pressed
					print("
					<script>
					$('#webcomicProfileClickable$DivAdd').click
							(
								function()
								{
									$('#webcomicProfileInternal$DivAdd').slideToggle();
								});
				
					$('#editWebcomicProfile$DivAdd').click
					(
						function()
						{
							$('#webcomicSynopsisFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#saveWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicTitleFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicURLFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicRSSFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#resetWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#editWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicFormatFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicPitchFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicSynopsisFor$DivAdd').summernote({focus: true, height: 300,
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
							}});
							
					});
					$('#saveWebcomicProfile$DivAdd').click
					(
						function()
						{
							$('#webcomicSynopsisFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#saveWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicTitleFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicURLFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicRSSFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#resetWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#editWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicPitchFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicFormatFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							var markup = $('#webcomicSynopsisFor$DivAdd').summernote('code');
							$('#webcomicSynopsisFor$DivAdd').summernote('destroy');
					});
					$('#resetWebcomicProfile$DivAdd').click
					(
						function()
						{
							$('#webcomicSynopsisFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#saveWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicTitleFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicURLFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicRSSFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#resetWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#editWebcomicProfile$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicPitchFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							$('#webcomicFormatFor$DivAdd').prop('disabled', function(i, v) { return !v; });
							var markup = $('#webcomicSynopsisFor$DivAdd').summernote('code');
							$('#webcomicSynopsisFor$DivAdd').summernote('destroy');
					});
					</script>");
					
					
					$DivName = $DivAdd;
					$RoleList = $Webcomic->value('Role');
					$RoleString = '';
					
					//Check to see if the person has multiple roles, if so, break it down into a string and remove the last comma that is added in the loop - makes it look nice.
					if(is_array($RoleList))
					{
						foreach($RoleList as $Role)
						{
							$RoleString = $RoleString .$Role.",";
						}
						$RoleString = rtrim(trim($RoleString),',');
					}
					else
					{
						$RoleString = $RoleList;
					}
					
					
					print("Your role(s): <input type='text' id='UserRolesText$DivName' value='$RoleString'><br>
							<input type='button' id='updateRolesButton' class='up' value='Update' onclick=\"updateOwnRoles('$ComicID','$alias','$DivName');\">
							<div id='updateRolesMSG$DivName'></div>");
					print("<input type='button' id='deleteComic$DivName' class='deleteComic' value='Delete This Webcomic' onclick='deleteWebcomic(\"$ComicID\",\"$alias\");'></div>"); //Thus ends the webcomic profile DIV 
					
					//Begin CrewWrap Div 
					print("<div id='crewWrap'>
							<div id='manageCrew$DivName' class='dropLevel1'><span class='WebcomicProfileHeader'>Manage Crew</span></div>
							<div id='manageCrewInternal$DivName' class='Internal'>");
							//Begin InternalCrewWrap
					print("<div id='addCrew$DivName' class='dropLevel2'>
								<span class='WebcomicProfileHeader'>Add Crewmate</span></div>
									<div id='addCrewInternal$DivName' class='Internal'>");
									//Begin Add Crew Internal
					print("Name: <select class='cpanelSelect'  id='addCrewSelect$DivAdd' >");
					$UserList = getAllUsersNotWorkingOnWebcomic($ComicID);
					foreach($UserList->getRecords() as $CrewUser)
					{
						$UserAlias = $CrewUser->value('Alias');
						if($UserAlias != $alias)
						{
							print("<option value='$UserAlias'>$UserAlias</option>");
						}
					}
					print("</select>
									Roles: <input type='text' id='addCrewRolesText$DivName'>
									<input type='button' id='addCrewButton' class='profileButton' value='Add' onclick=\"addCrewToWebcomic('$ComicID','$DivAdd');\">
									<div id='addCrewMSG$DivName'></div>
								</div>");//End Add Crew Internal
								
					//Scripts to show/hide every Manage Crew internal div when clickable clicked
					print("<script>
					$('#addCrew$DivName').click
					(
						function()
						{
							$('#addCrewInternal$DivName').slideToggle();
						});
						
					$('#manageCrew$DivName').click
					(
						function()
						{
							$('#manageCrewInternal$DivName').slideToggle();
						});
					</script>");
					$yourCrewCount = getWebcomicCrewCount($ComicID);
					//If you have more than one person already working on the comic and you're a creator/co-creator, you may edit their access
					if($yourCrewCount > 1)
					{
						print("<div id='currentCrewWrap$DivName'>
									<div id='currentCrew$DivName' class='dropLevel2''><span class='WebcomicProfileHeader'>Current Crew</span></div>");
									//Begin Current Crew Internal
						$CrewList = getWebcomicCrew($ComicID);
						$CrewCount = 0;
						foreach($CrewList->getRecords() as $Crew)
						{
							$CrewAlias = $Crew->value('Alias');
							$CrewRoles = $Crew->value('Role');
							$CrewRoleString = '';
							if($CrewAlias != $alias)
							{
								if(is_array($CrewRoles))
								{	
									foreach($CrewRoles as $CrewRole)
									{
										$CrewRoleString = $CrewRoleString .$CrewRole.",";
									}
									$CrewRoleString = rtrim(trim($CrewRoleString),',');
								}
								else
								{
									$CrewRoleString = $CrewRoles;
								}
								//Begin Crew Details Div
								print("<div id=\"comicCrew".$DivName."$CrewCount\" class='dropLevel2'>
											<b>Crewmate:</b> $CrewAlias || <b>Role(s):</b> <input type='text' id='editRoles".$DivName."$CrewCount' value='$CrewRoleString'>
											<input type='button' class='profileButton up' id='updateCrewRole".$DivName."$CrewCount' value='Update' onclick=\"updateCrewRoles('$CrewAlias','$CrewCount','$DivName','$ComicID');\"> 
											<input type='button' class='profileButton rem' id='removeCrew".$DivName."$CrewCount' value='Remove' onclick=\"removeCrew('$CrewAlias','$CrewCount','$DivName','$ComicID');\">
											<div id='editCrewRoleMSG".$DivName."$CrewCount'></div>
										</div>"); 
								//End Crew Details Div
								$CrewCount +=1;
							}
						}
						print("</div></div>");
						//End Manage Crew Internal
						//End Current Crew Internal
						print("
						<script>$('#currentCrew$DivName').click
						(
							function()
							{");
							for($i = 0;$i<=$CrewCount;$i++)
							{
								print("$('#comicCrew".$DivName.$i."').slideToggle();");
							}
							print("});
						</script>");
						
					}
					else
					{
						print("</div>");
					}
					print("<div id='comicSocialMedia$DivName' class='dropLevel1'><span class='WebcomicProfileHeader'>Social Media Links</span></div>");
					print("<div id='comicSocialMediaInternal$DivName' class='Internal'>");
					$SocialMediaList = getAllSocialMediaTypes();
					$NameList = "[";
					foreach($SocialMediaList->getRecords() as $SocMedia)
					{
						$Class = $SocMedia->value("Class");
						$SocName = $SocMedia->value("Name");
						$BGColor = $SocMedia->value("BGColor");
						
						$NameList = $NameList ."'$SocName',";
						$YourSocMediaURL = getSpecificSocialMediaURLByNameForComic($SocName,$ComicID);
						if($YourSocMediaURL)
						{
							
						}
						else
						{
							$YourSocMediaURL = '';
						}
						print("<div id='comicSocialMedia$SocName".$DivName."'><span class='socialMediaInput'><span class='profileMediaIcon' style='background-color: $BGColor;'><i class='$Class'></i></span> URL: </span><input id='comic".$SocName."Text$DivName' type='text' value='$YourSocMediaURL'></span></div>");
					}
					$NameList = rtrim(trim($NameList),',');
					$NameList = $NameList ."]";
					print("<div id='socialMediaSaveDIV$DivName'><input type='button' id='comicUpdateSocialMedia$DivName' value='Save Changes' onclick=\"updateComicSocialMedia('$ComicID','$DivName',$NameList);\"></div>");
					print("<div id='comicSocialMediaMSG$DivName' class='errMSG'></div>");
					print("</div>"); //End ComicSocialMediaInternal Div
					print("<script>");
					print("$('#webcomicSocialMediaClickable$DivName').click
							(
								function()
								{
									$('#webcomicSocialMediaInternal$DivName').slideToggle();
							});");
					print("</script>");
					if($ComicMembership == 'Comicadia')
					{
						/*
						$webcomicMediaList = getAllMediaForWebcomic($ComicName);
						*/
						print("<div id='webcomicMediaWrap'>");
						//Begin Media Wrap
						print("<div id='webcomicMediaClickable$DivAdd' class='dropLevel1'><span class='WebcomicProfileHeader'>Manage Media</span></div>
						<div id='webcomicMediaInternal$DivAdd' class='Internal'>");
						//Begin Manage Media Internal
						print("
						<script>
							$('#webcomicMediaClickable$DivAdd').click
							(
								function()
								{
									$('#webcomicMediaInternal$DivAdd').slideToggle();
							});							
							</script>");
						print("<div id='addMediaWrap$DivAdd' class='dropLevel2'><span class='WebcomicProfileHeader'>Add Media</span></div>
						<div id='addMediaInternal$DivAdd' class='Internal'>");
						//Begin Add Media Internal
						print("<div id='addFromWeb$DivAdd'><span class='dropLevel3'>From the web</span></div>
						<div id='addFromLocal$DivAdd'><span class='dropLevel3'>From your computer</span></div>
						<div id='addFromWebInternal$DivAdd' class='Internal'>");
						//Begin add Media From Web Internal
						print("URL: <input type='text' id='addNewMediaURL$DivAdd' class='addFileText'><br>
						Media Type: <select class='cpanelSelect'  id='addFileTypeSelect$DivAdd' >");
						$MediaTypes = getAllUserMediaTypes();
						foreach($MediaTypes->getRecords() as $MediaTypeList)
						{
							$MediaType = $MediaTypeList->value("TypeName");
							if($MediaType != 'Cadence')
							{
								$MediaWidth = $MediaTypeList->value("Width");
								$MediaHeight = $MediaTypeList->value("Height");
								print("<option value='$MediaType'>$MediaType (".$MediaWidth."x".$MediaHeight.")</option>");
							}
						}
						
						print("</select><br>");
						$Artists = getWebcomicCrew($ComicID);
						print("Artist <select class='cpanelSelect'  id='addMediaArtist$DivAdd'>");
						foreach($Artists->getRecords() as $Artist)
						{
							$ArtistAlias = $Artist->value('Alias');
							if($alias == $ArtistAlias)
							{
								$Selected = 'Selected';
							}
							else
							{
								$Selected = '';
							}
							$ArtistFirstName = $Artist->value('FirstName');
							$ArtistLastName = $Artist->value('LastName');
							print("<option value='$ArtistAlias' $Selected>$ArtistFirstName $ArtistLastName</option>");
						}
						print("</select><br>
						Description: <br>
						<textarea id=\"addMediaDescriptionText$DivAdd\" class='profileTextarea'></textarea><br> ");
						print("<input type='button' id='addMediaButton$DivAdd' class='profileButton' value='Upload' onclick='uploadMediaFromURL(\"$ComicID\",\"$alias\",\"$DivAdd\")'>
						</div>");
						//End Add Media From Web Internal
						print("
						<div id='addFromLocalInternal$DivAdd' class='Internal'>");
						//Begin Add Media from Local Internal
						print("File Location: <br><input type='File' name='addNewMediaFileLocation' id='addNewMediaFileLocation$DivAdd' class='addFileText'><br>");
						print("Media Type: <select class='cpanelSelect'  id=\"addFileFromLocalTypeSelect$DivAdd\">");
						$MediaTypes = getAllUserMediaTypes();
						foreach($MediaTypes->getRecords() as $MediaTypeList)
						{
							
							$MediaType = $MediaTypeList->value("TypeName");
							if($MediaType != 'Cadence')
							{
								$MediaWidth = $MediaTypeList->value("Width");
								$MediaHeight = $MediaTypeList->value("Height");
								print("<option value='$MediaType'>$MediaType (".$MediaWidth."x".$MediaHeight.")</option>");
							}
						}
						
						print("</select><br>");
						print("Artist: <select class='cpanelSelect'  id='addMediaArtistForLocal$DivAdd' name='addMediaArtistForLocal'>");
						foreach($Artists->getRecords() as $Artist)
						{
							$ArtistAlias = $Artist->value('Alias');
							if($alias == $ArtistAlias)
							{
								$Selected = 'Selected';
							}
							else
							{
								$Selected = '';
							}
							$ArtistFirstName = $Artist->value('FirstName');
							$ArtistLastName = $Artist->value('LastName');
							print("<option value='$ArtistAlias' $Selected>$ArtistFirstName $ArtistLastName</option>");
						}
						print("</select><br>
						Description: <br>
						<textarea id='addMediaDescriptionForLocalText$DivAdd' class='profileTextarea'></textarea><br> ");
						print("<input type='button' class='profileButton' id='addMediaFromLocalButton$DivAdd' value='Upload' onclick='uploadMediaFromLocal(\"$ComicID\",\"$alias\",\"$DivAdd\")'>
						</div>");
						//End Add Media From Local Internal
						print("<div id='uploadMSG$DivAdd' class='errMSG'></div>
						</div>");
						//End Add Media Internal
						//Begin Scripts to show/hide local/web Internal, based on which option is selected.
						print("<script>
						
						$('#addFromLocal$DivAdd').click
						(
							function()
							{
								$('#addFromLocalInternal$DivAdd').show();
								
								$('#addFromWebInternal$DivAdd').hide();
								
							});
							
						$('#addFromWeb$DivAdd').click
						(
							function()
							{
								$('#addFromLocalInternal$DivAdd').hide();
								
								$('#addFromWebInternal$DivAdd').show();
								
							});
						$('#addMediaWrap$DivAdd').click
						(
							function()
							{
								$('#addMediaInternal$DivAdd').slideToggle();
							});
						</script>");
						
						$TypeCount = 0;
						$MediaTypes = getAllUserMediaTypes();
						//Begin Current Media Wrap
						print("<div id='currentMediaClickable$DivAdd' class='dropLevel2'><span class='WebcomicProfileHeader'>Current Media</span></div>
						<div id='currentMediaInternal$DivAdd' class='Internal'>");
						//Begin Current Media Internal
						print("
						<script>
							$('#currentMediaClickable$DivAdd').click
							(
								function()
								{
									$('#currentMediaInternal$DivAdd').slideToggle();
							});							
						</script>");
						//Loop through all types of available media in the database and create clickable objects for navigation purposes.
						foreach($MediaTypes->getRecords() as $mediaTypeClickables)
						{
							$mediaTypeClickable = $mediaTypeClickables->value("TypeName");
							$DivName = str_replace(' ', '', $mediaTypeClickable);
							print("<div id='media".$DivName."Clickable$DivAdd' class= 'dropLevel5 imgmedia'>$mediaTypeClickable</div>");
						}
						foreach($MediaTypes->getRecords() as $currentMediaTypes)
						{
							$currentMediaType = $currentMediaTypes->value("TypeName");
							$currentMediaDivName = str_replace(" ","",$currentMediaType);
							if(mediaCountForWebcomicByType($currentMediaType, $ComicID) > 0)
							{
								$MediaListByType = getWebcomicMediaOfType($currentMediaType, $ComicID);
								print("<div id='media".$currentMediaDivName."Internal$DivAdd' class='mediapreviewdiv'>");
								//Begin Specific Media Type Internal Loop
								$mediaCount = 0;
								foreach($MediaListByType->getRecords() as $currentMedia)
								{
									$MediaURL = $currentMedia->value("URL");
									$ArtistDetails = getArtistDetailsForMedia($MediaURL);
									$ArtistAlias = $ArtistDetails->value("Alias");
									$ArtistFirstName = $ArtistDetails->value("FirstName");
									$ArtistLastName = $ArtistDetails->value("LastName");
									$MediaType = $currentMedia->value("Type");
									$DivName = str_replace(' ', '', $MediaType);
									$MediaStatus = $currentMedia->value("Status");
									$MediaExtension = explode('.',$MediaURL);
									$MediaExt = end($MediaExtension);
									
									print("<div id='media".$currentMediaDivName.$mediaCount."' class='preview$currentMediaDivName imageblock'>");
									if($MediaExt == 'jpg' || $MediaExt =='gif' || $MediaExt == 'png' || $MediaExt == 'jpeg' || $MediaExt == '')
									{
										print("<div class='imgPreview$DivName'><img src='$MediaURL'></div>");
									}
									print("<div class='mediaOptions'>");
									if($MediaStatus == 'Active')
									{
										print("<input type='button' id='activate".$currentMediaDivName.$mediaCount."' class'mediaControlButton' value='Activate' style='display: none' onclick=\"activateMedia('$MediaURL','$currentMediaDivName','$mediaCount')\">");
										print("<input type='button' id='deactivate".$currentMediaDivName.$mediaCount."' class'mediaControlButton' value='Deactivate' onclick=\"deActivateMedia('$MediaURL','$currentMediaDivName','$mediaCount')\">");
									}
									elseif($MediaStatus == 'Inactive')
									{
										print("<input type='button' id='activate".$currentMediaDivName.$mediaCount."' class'mediaControlButton' value='Activate' onclick=\"activateMedia('$MediaURL','$currentMediaDivName','$mediaCount')\">");
										print("<input type='button' id='deactivate".$currentMediaDivName.$mediaCount."' class'mediaControlButton' value='Deactivate' style='display: none' onclick=\"deActivateMedia('$MediaURL','$currentMediaDivName','$mediaCount')\">");
									}
									else
									{
										print("Current Status: $MediaStatus<br>");
									}
									print("<input type='button' class='mediaControlPanelButton rem' id='delete".$currentMediaDivName.$mediaCount."' value='delete' onclick=\"deleteMedia('$MediaURL','$currentMediaDivName','$mediaCount')\"></div>");
									/*
									if($currentMediaType == 'Rotator')
									{
										$Views = getMediaViews($MediaURL);
										$Clicks = getMediaClicks($MediaURL);
										print("<div class='mediaViews'>Views: $Views</div>"); //Begin StatisticsDiv
										print("<div class='mediaClicks'>Clicks: $Clicks</div>"); //end Statistics div
									}
									*/
									print("<div id='MSG".$currentMediaDivName.$mediaCount."'></div></div>");
									//End Specific Media Internal Loop
									
									print("<script>
										$('#activate".$currentMediaDivName.$mediaCount."').click
										(
											function()
											{
												$('#deactivate".$currentMediaDivName.$mediaCount."').show();
												$('#activate".$currentMediaDivName.$mediaCount."').hide();
										});
										$('#deactivate".$currentMediaDivName.$mediaCount."').click
										(
											function()
											{
												$('#activate".$currentMediaDivName.$mediaCount."').show();
												$('#deactivate".$currentMediaDivName.$mediaCount."').hide();
										});							
									</script>");
								}
								print("<script>");
								//Complicated script that rebuilds the clickable media entries and assigns them to the appropriate Internal DIV tags for show/hide reasons
									foreach($MediaTypes->getRecords() as $mediaTypeClickables)
									{
										$mediaTypeClickable = $mediaTypeClickables->value("TypeName");
										$DivName = str_replace(' ', '', $mediaTypeClickable);
										print("
										$('#media".$DivName."Clickable$DivAdd').click
										(
											function()
											{");
											foreach($MediaTypes->getRecords() as $currentMedias)
											{
												$currentMediaType = $currentMedias->value("TypeName");
												$currentMediaDivName = str_replace(" ","",$currentMediaType);
												if($currentMediaDivName == $DivName)
												{
													$visible = 'show();';
												}
												else
												{
													$visible = 'hide();';
												}
												print("$('#media".$currentMediaDivName."Internal$DivAdd').$visible");
											}
											print("}
										);");				
									}
									print("</script>");
								$mediaCount +=1;
								$TypeCount+=1;
								print("</div>"); //End Current Media Wrap
								print("
								<script>
								$('#media".$currentMediaDivName."Clickable').click
								(
								function()
								{");
								foreach($MediaTypes->getRecords() as $mediaTypeComparables)
								{	
									$mediaTypeComparable = $mediaTypeComparables->value("TypeName");
									$mediaTypeComprableDiv = str_replace(" ","",$mediaTypeComparable);
									if($currentMediaType == $mediaTypeComparable)
									{
										$visible = 'show();';
									}
									else
									{
										$visible = 'hide();';
									}
									print("	$('#media".$mediaTypeComprableDiv."Internal').".$visible);
								}
								
								print("});							
								</script>");
								//End complicated javascript to rebuild clickable media and internal div
							}
							else
							{
								//Print a Simple - there is no media of this type message
								print("<div id='media".$currentMediaDivName."Internal' class='mediapreviewdiv'>");
								print("No media of this type, yet</div>");
								print("<script>
								$('#media".$currentMediaDivName."Clickable').click
								(
								function()
								{");
								foreach($MediaTypes->getRecords() as $mediaTypeComparables)
								{	
									$mediaTypeComparable = $mediaTypeComparables->value("TypeName");
									$mediaTypeComprableDiv = str_replace(" ","",$mediaTypeComparable);
									
									if($currentMediaType == $mediaTypeComparable)
									{
										$visible = 'show();';
									}
									else
									{
										$visible = 'hide();';
									}
									print("	$('#media".$mediaTypeComprableDiv."Internal').".$visible);
								}
								
								print("});	
								</script>");
							}
						}
						print("</div>");
						print("<div class='clear'></div>");
						print("</div>");  // End Internal wrap
						print("</div>");
						//End Current Media Wrap
						//End Current Media Internal
					}
					//Begin Genres Wrap
					print("<div id='GenresFor$DivAdd'>
					<div id='GenresClickable$DivAdd' class='dropLevel1'><span class='WebcomicProfileHeader'>Genres</span></div>
					<div id='GenresInternal$DivAdd' class='Internal'>");
					//Being Genres Internal
					print("Select Genres:");
					print("<script>
						$('#GenresClickable$DivAdd').click		
						(
							function()
							{
								$('#GenresInternal$DivAdd').slideToggle();
						});							
					</script>");
					$thisWebcomicsGenres = getThisWebcomicGenre($ComicID);
					/*
					Build an array to populate pre-selected Genres in dropdown
					*/
					
					$thisGenreList = array();				
					foreach($thisWebcomicsGenres->getRecords() as $SelectedGenres)
					{
						$thisGenreItem = $SelectedGenres->value("Name");
						array_Push($thisGenreList, $thisGenreItem);
					}
					
					$GenresList = getAllWebcomicGenres();
					$findme = array_shift($thisGenreList);
					print("<select class='cpanelSelect'  id='FirstGenre$DivAdd' name=\"$findme\">");
					print("<option value=''>None</option>");
					
					foreach($GenresList->getRecords() as $FirstGenre)
					{
						$GenreName = $FirstGenre->value("Name");
						$Selected = '';
						if($findme == $GenreName)
						{
							$Selected = 'Selected';
						}
						print("<option value='$GenreName' $Selected>$GenreName</option>");
					}
					
					print("</select>");
					
					$findme = array_shift($thisGenreList);
					print("<select class='cpanelSelect'  id='SecondGenre$DivAdd' name=\"$findme\">");
					print("<option value=''>None</option>");
					
					foreach($GenresList->getRecords() as $SecondGenre)
					{
						$Selected = '';
						$GenreName = $SecondGenre->value("Name");
						if($findme == $GenreName)
						{
							$Selected = 'Selected';
						}
						print("<option value='$GenreName' $Selected>$GenreName</option>");
					}
				
					print("</select>");
					
					$findme = array_shift($thisGenreList);
					print("<select class='cpanelSelect'  id='ThirdGenre$DivAdd' name=\"$findme\">");
					print("<option value=''>None</option>");
					
					foreach($GenresList->getRecords() as $ThirdGenre)
					{
						$GenreName = $ThirdGenre->value("Name");
						
						$Selected = '';
						if($findme == $GenreName)
						{
							$Selected = 'Selected';
						}
						print("<option value='$GenreName' $Selected>$GenreName</option>");
					}
					print("</select>");
					print("<input type='button' class='up' id='saveGenresFor$DivAdd' value='Save Genres' onclick=\"saveGenres('$ComicID','$DivAdd');\">");
					print("<div id='saveGenreMSG$DivAdd'></div>");
					print("</div></div>");
					//End Genres Internal
					//End Genres Wrap
					
					//Begin Themes Wrap
					print("<div id='ThemesFor$DivAdd'>
					<div id='ThemesClickable$DivAdd' class='dropLevel1'><span class='WebcomicProfileHeader'>Themes</span></div>
					<div id='ThemesInternal$DivAdd' class='Internal'>");
					//Begin Themes Internal
					print("<div id='themeCheckboxes'>");
					$ActiveThemeList = getActiveThemes();
					$currentThemeList = getWebcomicThemes($ComicID);
					$currentThemes = array();
					foreach($currentThemeList->getRecords() as $currentThemer)
					{
						$CurrentTheme = $currentThemer->value("Name");
						array_push($currentThemes, $CurrentTheme);
					}
					foreach($ActiveThemeList->getRecords() as $activeTheme)
					{
						$ThemeName = $activeTheme->value("Name");
						$Checked = '';
						foreach($currentThemes as $themeComparable)
						{
							if($ThemeName == $themeComparable)
							{
								$Checked = 'checked';
							}
						}
						print("<div class='checkboxWrap'><input type='checkbox' name='themeCheckbox$DivAdd' class='themeSelectCheckboxes' $Checked value='$ThemeName'>$ThemeName</div>");
					}
					print("<input type='button' id='saveThemes$DivAdd' class='profileButton up' value='Save Themes' onclick=\"saveThemesForWebcomic('$ComicID','$DivAdd');\"><br>");
					print("<div id='ThemeMSG$DivAdd' class='errMSG'></div>");
					print("</div>");//End Themes Internal
					print("</div>");//End Themes Wrap
					print("<div id='rotatorCodeClickable$DivAdd' class='dropLevel1'><span class='WebcomicProfileHeader'>Rotator Code</span></div>");
					print("<div id='rotatorCodeInternal$DivAdd' class='Internal'>");
					print("<div id='HorizontalRotatorClickable$DivAdd' class='dropLevel2'>Horizontal rotator (Non-Mobile-Ready sites)</div>");
					print("<div id='HorizontalRotatorInternal$DivAdd' class='Internal'>");
					print("&lt;link href=\"http://www.comicadia.com/css/rotator.css\" rel=\"stylesheet\" type=\"text/css\" / &gt;
					&lt;script type=\"text/javascript\" src=\"http://www.comicadia.com/rotator/rotate.php\"&gt; &lt;/script&gt;");
					print("&lt;div id='comicadia_rotator'&gt;&lt;/div&gt;");
					print("&lt;script&gt;

					function loadComicadiaRotate() {
					ComicadiaRotate($ComicID);
					}
	
					if(typeof jQuery=='undefined') {
						var headTag = document.getElementsByTagName(\"head\")[0];
						var jqTag = document.createElement('script');
						jqTag.type = 'text/javascript';
						jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
						jqTag.onload = loadComicadiaRotate();
						headTag.appendChild(jqTag);
					} else {
						 loadComicadiaRotate();
					}
					&lt;/script&gt;");
					print("</div>");// End HoizontalRotatorInternal
					
					print("<div id='HorizontalRotatorMobileClickable$DivAdd' class='dropLevel2'>Horizontal rotator (Mobile-Ready sites only)</div>");
					print("<div id='HorizontalRotatorMobileInternal$DivAdd' class='Internal'>");
					print("&lt;link href=\"http://www.comicadia.com/css/rotator.css\" rel=\"stylesheet\" type=\"text/css\" / &gt;
					&lt;script type=\"text/javascript\" src=\"http://www.comicadia.com/rotator/rotate.php\"&gt; &lt;/script&gt;");
					print("&lt;div id='comicadia_rotator'&gt;&lt;/div&gt;");
					print("&lt;script&gt;

					function loadComicadiaRotate() {
					ComicadiaRotateMobile($ComicID);
					}
	
					if(typeof jQuery=='undefined') {
						var headTag = document.getElementsByTagName(\"head\")[0];
						var jqTag = document.createElement('script');
						jqTag.type = 'text/javascript';
						jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
						jqTag.onload = loadComicadiaRotate();
						headTag.appendChild(jqTag);
					} else {
						 loadComicadiaRotate();
					}
					&lt;/script&gt;");
					print("</div>");// End HoizontalRotatorMobileInternal
					print("</div>"); // End rotatorcode Internal Div
					print("</div>"); //End 'For each webcomic' Wrap
					
					print("	
					<script>
					$('#comicSocialMedia$DivAdd').click		
					(
						function()
						{
							$('#comicSocialMediaInternal$DivAdd').slideToggle();
					});												
					$('#rotatorCodeClickable$DivAdd').click		
					(
						function()
						{
							$('#rotatorCodeInternal$DivAdd').slideToggle();
					});												
					$('#HorizontalRotatorClickable$DivAdd').click		
						(
							function()
							{
								$('#HorizontalRotatorInternal$DivAdd').slideToggle();
						});												
					$('#HorizontalRotatorMobileClickable$DivAdd').click		
						(
							function()
							{
								$('#HorizontalRotatorMobileInternal$DivAdd').slideToggle();
						});												
					$('#ThemesClickable$DivAdd').click		
						(
							function()
							{
								$('#ThemesInternal$DivAdd').slideToggle();
						});												
					</script>");
					print("</div></div></div>");
				}
				
				else
				{
					print("Roles: $RoleString</div> ");
				}
				print("</div>");//End Comic Details Wrap
				$DivAdd += 1;
			}
			print("</div></div>");//End Your Comics Wrap
		}
		else
		{
			returnHome();
		}
	}
	else
	{
		returnHome();
	}
}
?>