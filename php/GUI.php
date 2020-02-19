<?php
include "functions.php";
function buildCommunitySocialMedia()
{
	print("<div class='CommunitySocial'>
			<a href='https://twitter.com/comicadiatweets' target='_blank'><div id='TwitterDiv' class='SocDiv fa fa-twitter'>
			<span>Twitter</span>
			</div></a>
			<div id='TumblrDiv' class='SocDiv fa fa-tumblr'>
			<span>Tumblr</span>
			</div>
			<a href='https://www.facebook.com/Comicadia/' target='_blank'><div id='FacebookDiv' class='SocDiv fa fa-facebook'>
			<span>Facebook</span>
			</div></a>
			<a href='https://www.instagram.com/comicadia/' target='_blank'><div id='InstagramDiv' class='SocDiv fa fa-instagram'>
			<span>Instagram</span></div></a>
			<a href='https://discord.gg/JHmKedD' target='_blank'><div id='DiscordDiv' class='SocDiv fa fa-heart-o'>
			<span>Discord</span>
			</div></a>
			</div>
			");
}

function loadSocialMedia()
{
	print("<div class='Social'>
			<a href='https://twitter.com/comicadiatweets' target='_blank'><div id='TwitterDiv' class='SocDiv fa fa-twitter'>
			<span>Twitter</span>
			</div></a>
			<div id='TumblrDiv' class='SocDiv fa fa-tumblr'>
			<span>Tumblr</span>
			</div>
			<a href='https://www.facebook.com/Comicadia/' target='_blank'><div id='FacebookDiv' class='SocDiv fa fa-facebook'>
			<span>Facebook</span>
			</div></a>
			<a href='https://www.instagram.com/comicadia/' target='_blank'><div id='InstagramDiv' class='SocDiv fa fa-instagram'>
			<span>Instagram</span></div></a>
			<a href='https://discord.gg/JHmKedD' target='_blank'><div id='DiscordDiv' class='SocDiv fa fa-heart-o'>
			<span>Discord</span>
			</div></a>
			<a href='https://patreon.com/comicadia' target='_blank'><div id='PatreonDiv' class='SocDiv fa fa-heart'>
			<span>Patreon</span>
			</div></a>
			");
}

//<img src='https://www.comicadia.com/media/ComicadiaHeaderFull.png'>
function loadLogin()
{
	print("<div id='TopLogin'>");
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		print("
		<nav>
			<ul>
				<li id='loggedIn'>
					<a id='login-trigger' href='#'>
						$alias <span>▼</span>
					</a>
				<div id='login-content'>
					<form>
					<fieldset id='inputs'>
							<a href='https://www.comicadia.com/cpanel.php'><input type='button' id='Profile' value='User Control Panel' ></a>");
							if($type == 'MAX')
							{
								print("<a href='https://www.comicadia.com/MAX/admin.php'><input type='button' id='MAXControlPanel' value='MAX Control Panel'></a>");
							}
							if($type =='Admin')
							{
								print("<a href='https://www.comicadia.com/MAX/admin.php'><input type='button' id='MAXControlPanel' value='MAX Control Panel'></a>");
								print("<a href='https://www.comicadia.com/admin.php'><input type='button' id='Admin' value='Admin Control Panel'></a>");
							}   
							print("
							<a href='https://www.comicadia.com/php/Logout.php'><input type='button' id='Logout' value='Logout'></a>
						</fieldset>
						<span id='ERRMSG'></span>
						</form>
				</div>                     
				</li>
			</ul>
		</nav>");	
	}
	else
	{
		print("
		<nav>
  			<ul>
    			<li id='login'>
      				<a id='login-trigger' href='#'>
        			<i class='fa fa-sign-in' aria-hidden='true'></i> Log in <span>▼</span>
      				</a>
      			<div id='login-content'>
      	  			<form><fieldset id='inputs'>
					  <div class='input-group margin-bottom-sm'>
					  <span><i class='fa fa-envelope-o fa-fw'></i></span>
					  <input id='username' type='email' name='Email' placeholder='Email address' required>
					</div>
					<div class='input-group'>
					  <span><i class='fa fa-key fa-fw'></i></span>
					  <input id='password' type='password' name='Password' placeholder='Password' required>
					</div>		
          				</fieldset>
          			<fieldset id='actions'>
            			<input type='button' id='Login' value='Log in' onclick='AttemptLogin()'>
            			<span id='PassMSG'></span>
          			</fieldset>
        				</form>
      			</div>                     
    			</li>
			<li>
				<div id='Register' onclick='window.open(\"https://www.comicadia.com/register.php\", \"_blank\")'><i class='fa fa-user-circle' aria-hidden='true'></i> Register</div>
			</li>
    		</ul>
		</nav>");
	}
	print("</div>");
	
	//header("Location: https://www.comicadia.com/Login.php");
		//die();
   /*
    <li id="signup">
      <a href="">Sign up FREE</a>
    </li>
  */
}

function buildComicadiaRandomSpotlightHeader()
{
	loadGoogleAnalytics();
	print("<div id='TopHeader'>");
	loadLogin(); 
	$HeaderInfo = getRandomSpotlightInfo(); 
	$HeaderURL = $HeaderInfo->value('SRC'); 
	$HeaderArtist = $HeaderInfo->value('Artist'); 
	$HeaderAlias = $HeaderInfo->value("Alias");
	$HeaderComic = $HeaderInfo->value('Name');
	$HeaderComicURL = $HeaderInfo->value('URL');
	print("<div class='HeaderBG' style='background-image: url($HeaderURL)'></div><div id='HeaderCredit'> by <a href='https://www.comicadia.com/members.php?MemberAlias=$HeaderAlias'>$HeaderArtist</a> artist of <a href='$HeaderComicURL'>$HeaderComic</a></div>");	
	print("<div class='TopBar'> 
			<div class='ComicadiaLogo'><div id='home' onclick='goHome()'><img src='https://www.comicadia.com/media/ComicadiaHeader-full.png'></div></div>");
	loadSocialMedia();
		print("</div>");
		print("<div class='clear'></div></div>");
		buildTopNav();
	print("</div>");
	
}

function loadGoogleAnalytics()
{
	print("<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-69208507-3\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-69208507-3');
</script>");
}

function buildCarousel()
{
	$CarouselAdList = getCarouselAds();
	
	print('<div id="CarouselWrap">
				<div id="ComicCarousel" class="carousel slide" data-ride="carousel">

        <!-- Carousel indicators -->
        <ol class="carousel-indicators">
            <li data-target="#ComicCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#ComicCarousel" data-slide-to="1"></li>
            <li data-target="#ComicCarousel" data-slide-to="2"></li>
        </ol>   
        <!-- Wrapper for carousel items -->
         <div class="carousel-inner">');
   $count = 0;
	foreach ($CarouselAdList->getRecords() as $record) 	
	{
		$ComicName = $record->value('Name');
		$URL = $record->value('URL');
		$imgURL = $record->value('imgURL');
		$Alt = $record->value('Alt');
		
		if($count == 0)		
		{
			print("<a href=\"$URL\" target=\"_blank\"><div id='startItem' class='item active'> <img src='$imgURL'></img></a>");
		}
		else 
		{
			print("<a href=\"$URL\" target=\"_blank\"><div class=\"item\"> <img src=\"$imgURL\"></img></a>");
		}		
		print($ComicName);
		print('</div>');
		$count++;
	}
	print('</div>');
	print(' <!-- Carousel controls -->

        <a class="carousel-control left" href="#ComicCarousel" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </a>
        <a class="carousel-control right" href="#ComicCarousel" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </a>

    </div></div>');
}

function buildAdminPanel()
{
	print("<input type='submit' id='GoToDashboard' class='leftPanelItem' value='Dashboard' onclick=\"window.location.href = 'https://www.comicadia.com/admin.php'\">");
    print("<form action='?' method='GET'>");
	print("<input type='submit' id='ManageSplashButton' class='leftPanelItem' value='Manage Splash' name='submit'>");
	print("<input type='submit' id='ManageUsersButton' class='leftPanelItem' value='Manage Users' name='submit'>");
	print("<input type='submit' id='ManageAdsButton' class='leftPanelItem' value='Manage Ads' name='submit'>");
	print("<input type='submit' id='ManageNewsButton' class='leftPanelItem' value='Manage News' name='submit'> ");
	print("<input type='submit' id='ManageEventsButton' class='leftPanelItem' value='Manage Events' name='submit'>");
	print("<input type='submit' id='ManageMessagesButton' class='leftPanelItem' value='Manage Messages' name='submit'>");
	print("<input type='submit' id='ManageWebcomicsButton' class='leftPanelItem' value='Manage Webcomics' name='submit'>");
	print("<input type='submit' id='ManageThemesButton' class='leftPanelItem' value='Manage Themes' name='submit'>");
	print("<input type='submit' id='ManageGenresButton' class='leftPanelItem' value='Manage Genres' name='submit'>");
	print("<input type='submit' id='ManageMediaButton' class='leftPanelItem' value='Manage Media' name='submit'>");
	print("<input type='submit' id='ManageSocialMediaTypeButton' class='leftPanelItem' value='Social Media Types' name='submit'>");
	print("</form>");
}

function buildTopNav()
{
	print("
	<div id='TopNav'> 
		<ul class='active'>
			<li><a href='https://www.herald.comicadia.com/'><i class='fa fa-newspaper-o' aria-hidden='true'></i> The Herald</a>
				<ul class='submenu'>
					<li><a href='https://www.comicadia.com/events.php'><i class='fa fa-calendar' aria-hidden='true'></i> Events</a></li> 
				</ul>
			</li> 
			<li><a href='https://www.comicadia.com/comics.php'><i class='fa fa-comment-o' aria-hidden='true'></i> Comics</a>
				<ul class='submenu'>
					<li><a href='https://www.comicadia.com/presents/'><i class='fa fa-film' aria-hidden='true'></i> Presents </a></li>
					<li><a href='https://www.comicadia.com/mentorship'><i class='fa fa-graduation-cap' aria-hidden='true'></i> Mentorships</a></li>
					<li><a href='https://www.comicadia.com/zines'><i class='fa fa-book'></i> Zines</a></li>
				</ul>
			</li> 
			<li><a href='https://www.comicadia.com/about.php'><i class='fa fa-info-circle' aria-hidden='true'></i> About</a>
				<ul class='submenu'>
					<li><a href='https://www.comicadia.com/support.php'><i class='fa fa-money-bill-wave-alt'></i> Support</a></li>
				</ul>
			</li> 
			<li><a href='https://www.comicadia.com/community.php'><i class='fa fa-users' aria-hidden='true'></i> Community</a>
				<ul class='submenu'>
					<li><a href='https://www.comicadia.com/members.php'><i class='fa fa-address-book'></i> Subscribers</a></li>
					<li><a href='https://www.comicadia.com/forums'><i class='fa fa-comments'></i> Forum</a></li>
				</ul>
			</li> 
			<li><a href='https://www.comicadia.com/network.php'><i class='fa fa-bookmark'></i> Network</a>
				<ul class='submenu'>
					<li><a href='https://www.comicadia.com/MAX'><i class='fa fa-paint-brush'></i> MAX</a></li>
					<li><a href='https://www.comicadia.com/affiliates.php'><i class='fa fa-bullhorn'></i> Affiliates</a></li>
				</ul>
			</li>
			<li><a href='https://www.comicadia.com/submissions.php'><i class='fa fa-paper-plane' aria-hidden='true'></i> Submissions</a></li> 
			<li class='responsive'>");
			print("</li>
		</ul>
		<a class='toggle-nav' href='#'><i class='fa fa-bars' aria-hidden='true'></i> <span>Menu</span></a>
		</div>
		");
}

function buildWriteNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Admin')
		{
			$categories = getAllNewsCategories();
			print("
			<div id='WriteNews'>");
			print("<input type='button' id='adminWriteNewsButton' class='addNewsButton' value='Write News'>");
			print("<div id='adminWriteNewsInternal' class='Internal'>");
			print("<script>
				$('#adminWriteNewsButton').click
				(
					function()
					{
						$('#adminWriteNewsInternal').slideToggle();
					});
			</script>");
			print("<div id='newsCategory'><strong>Category:</strong><select id='newsCategorySelect' name='$type'>");
			foreach($categories as $record)
			{
				print("<option value='$record'>$record</option>");
			}
			print("</select>
			<div id='newsTitle'><strong>Title:</strong><input type='text' id='newsTitleText' name='$alias' value='Title goes here'></div>
			<div id='newsPubDate'><strong>Date to Publish:</strong><input type='text' name='newsDatepicker' id='newsDatepicker'></p></div> 
			<div id='newsDetails'><strong>Details<br></strong><textarea id='newsDetailsText' class='summernote' name='$email'>Text goes here</textarea></div>
			<div id='newsSubmit'><input type='button' id='submitNewsButton' name='CreateNewsButton' value='Create' onclick='postNews(\"$alias\")' class='submitBTN'></div>
			<div id='PostMSG'></div>
			</div>");
			print("<script>
				$(function(){
					$('*[name=newsDatepicker]').appendDtpicker({
											\"dateFormat:\": \"DD-MM-YYYY\",
											\"futureOnly\": true,
											\"dateOnly\": true											
					});
				});
			</script>");
			print("</div>");
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
}

function buildEditNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			$NewsList = getAdminEditNews();
			print("<div id='adminEditNewsContent'>");
			$NewsCount = 0;
			foreach($NewsList->getRecords() as $record)
			{
				$Poster = $record->value("Alias");
				$Text = $record->value('Details');
				$PubDate = $record->value('DatePublished');
				$Title = $record->value('Title');
				//$PubDate = date('m/d/Y', $PubDate / 1000);
				$humanPubDate = date('jS, M, Y', $PubDate /1000);
				$PubDate = date('Y-m-d', $PubDate / 1000);
				$epochDateWritten = $record->value('DateWritten');
				$DateWritten = date('jS M, Y', $epochDateWritten /1000);
				$Category = $record->value('Category');
				$Status = $record->value('Status');
				
				print("<div id='newsItem$NewsCount' class='AdminNewsItem$Status'>");
				print("<div id='newsItemClickable$NewsCount' class='dropLevel1'>$Title by $Poster for $humanPubDate</div>");
				print("<div id='newsItemInternal$NewsCount' class='Internal'>");
				print("<form>");
				print("<span class='IDSpan'>ID: $epochDateWritten</span>");
				print("<span class='TitleSpan'>Title: <input type='text' class='NewstitleText' name='$epochDateWritten' id='newsTitleText$NewsCount' value='$Title' disabled></span>");
				print("<span class='PosterSpan'>Posted By: $Poster</span>");
				print("<span class='PubDateSpan'>Date to Publish: <input type='text' id='newsPubDate$NewsCount' name='newsPubDate$NewsCount' value='$PubDate' disabled></span>");
				print("<script>
						$(function(){
							$('*[name=newsPubDate$NewsCount]').appendDtpicker({
													\"dateOnly\": true,
													\"current\": \"$PubDate\"
							});
						});
					</script>");
				print("<span class='CategorySpan'>Category: <select id='newsCategorySelect$NewsCount' disabled>");	
				$NewsCategories = getCategories();
				foreach($NewsCategories as $PotentialCategory)
				{
					if($PotentialCategory == $Category)
					{
						$Selected = 'Selected';
					}
					else
					{
						$Selected = '';
					}
					print("<option value='$PotentialCategory' $Selected>$PotentialCategory</option>");
				}
				print("</select>");
				print("</span>");
				print("<span class='StatusSpan'>Status: <select id='newsStatusSelect$NewsCount' disabled>");
				$StatusList = getNewsStatusList();
				foreach($StatusList as $PotentialStatus)
				{
					if($PotentialStatus == $Status)
					{
						$Selected = 'Selected';
					}
					else
					{
						$Selected = '';
					}
					print("<option value='$PotentialStatus' $Selected>$PotentialStatus</option>");
				}
				print("</select>");
				print("</span>");
				print("<span class='DetailsSpan'><textarea id='newsDetails$NewsCount' disabled>$Text</textarea>");
				print("</span>");
				print("<input type='button' id='EditNewsItem$NewsCount' value='Edit'> ");
				print("<input type='button' id='SaveNewsItem$NewsCount' value='Save' disabled onclick=\"saveNewsEdits('$alias','$NewsCount','$Poster','$epochDateWritten')\">");
				print("<input type='reset' id='CancelNewsItem$NewsCount' value='Reset' disabled>");
				print("<input type='button' id='DeleteNewsItem$NewsCount' value='Delete' onclick='deleteNews(\"$epochDateWritten\",\"$NewsCount\",\"$Poster\");'");
				print("</form>");
				print("<div id='newsItemMSG$NewsCount'></div>");
				print("</div>"); //End News item Internal Div
				print("</div>"); //End News item Wrap Div
				print("
				<script>
				$('#newsItemClickable$NewsCount').click
				(
					function()
					{
						$('#newsItemInternal$NewsCount').slideToggle();
					});
				$('#EditNewsItem$NewsCount').click
				(
					function()
					{
						$('#EditNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#SaveNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#CancelNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsPubDate$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsTitleText$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsStatusSelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsCategorySelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsDetails$NewsCount').summernote({focus: true, height: 200,
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
				
				$('#SaveNewsItem$NewsCount').click
				(
					function()
					{
						$('#EditNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#SaveNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#CancelNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsPubDate$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsTitleText$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsStatusSelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsCategorySelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						var markup = $('#newsDetails$NewsCount').summernote('code');
						$('#newsDetails$NewsCount').summernote('destroy');
				});
				
				$('#CancelNewsItem$NewsCount').click
				(
					function()
					{
						$('#EditNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#SaveNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#CancelNewsItem$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsPubDate$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsTitleText$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsStatusSelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						$('#newsCategorySelect$NewsCount').prop('disabled', function(i, v) { return !v; });
						var markup = $('#newsDetails$NewsCount').summernote('code');
						$('#newsDetails$NewsCount').summernote('destroy');
				});
				</script>");
				$NewsCount ++;
			}
			
			print("</div>");//End News Panel Div
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
}


function buildAddUser()
{
	$UserTypes = getUserTypes();
	print("
	<div id='editUser'>
	<input type='button' id='adminAddUserbutton' class='addUserButton' value='Add User'>
	<div id='adminAddUserInternal' class='Internal'>
	<div id='PersonalInfo'></div>
	<div id='newAlias' class='Personal'><strong>Alias:</strong><input type='text' name='' id='newAliasText' value=''></div>
	<div id='newFirstName' class='Personal'><strong>First Name:</strong><input type='text' id='newFirstNameText' value=''></div>
	<div id='newLastName' class='Personal'><strong>Last Name</strong><input type='text' id='newLastNameText' value=''></div>	
	<div id='newEmail' class='Personal'><strong>Email:</strong><input type='email' name='' id='newEmailText' value=''></div>
	<div id='newUserType' class='Administration'><strong>Member type:</strong>
	<br><select id='UserTypeSELECT'>");
	foreach($UserTypes as $record)
	{
		$PType = $record;
		if($PType == 'Member')
		{
			$Selected = 'Selected';
		}
		else 
		{
			$Selected = '';
		}
		print("<option value='$PType' $Selected>$PType</option>");
	}
	print("</select>
	<div id='PassDiv'><strong>Password</strong><input type='password' id='newPasswordText' value=''></div>
	<input type='button' id='saveEdits 'name='submit' value='Save' onclick='saveNewUser()'>
	<div id='saveERR' class='ErrMSG'></div>
	</div>");
	print("</div>");
	print("
	<script>
	$('#adminAddUserbutton').click
	(
		function()
		{
			$('#adminAddUserInternal').slideToggle();
		});
	</script>
	");
}

function buildEditUser()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			print("<div id='SearchUsersToEdit'>
			<strong>Search by:</strong>
			<select id='SearchUserSELECT'>
			<option value='Alias'>Alias</option>
			<option value='First_Name'>First Name</option>
			<option value='Email'>Email</option>
			</select><input type='text' id='searchKeywordsTEXT'><input type='button' onclick='searchUsersByKeywords();' value='Search'>
			</div>");
			print("<div id='searchResults'>");
			$DefaultUserList = getAllUsers();
			$UserCount = 0;
			foreach($DefaultUserList->getRecords() as $User)
			{
				$UserAlias = $User->value("Alias");
				buildAdminSearchResult($UserAlias, $UserCount, $alias);
				
				$UserCount++;
			}
			
			print("<div id='EditUserContent'></div>");
			print("</div>");
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
}

function buildAdminSearchResult($Alias, $Counter,$alias)
{
	$UserDetails = getUserDetails($Alias);
	$FirstName = $UserDetails->value("FirstName");
	$LastName = $UserDetails->value("LastName");
	$Email = $UserDetails->value("Email");
	$Type = $UserDetails->value("Type");
	$PicURL = $UserDetails->value("Pic");
	
	print("<div id='UserDetails$Counter'>");
	print("<div id='UserClickable$Counter'>$Alias</div>");
	print("<div id='UserInternal$Counter' class='Internal'>");
	print("<span class='UserBio'> 
	<span id='ProfilePic$Counter'><img src='$PicURL'></span><br>
	Profile Picture:
	<input type='button' id='removePic$Counter' value='Remove' onclick='disableProfilePic(\"$Alias\",\"$Counter\");' disabled>
	<form>
	Alias: <input type='text' id='UserAlias$Counter' name='$Alias' value='$Alias' disabled><br>
	First Name: <input type='text' id='UserFirstName$Counter' value='$FirstName' disabled><br>
	Last Name: <input type='text' id='UserLastName$Counter' value='$LastName' disabled><br>
	Email: <input type='text' id='UserEmail$Counter' name='$Email' value='$Email' disabled><br>
	User Type: <select id='UserType$Counter' disabled>");
	$UserTypeList = getUserTypes();
	foreach($UserTypeList as $UserType)
	{
		$Selected = '';
		if($UserType == $Type)
		{
			$Selected = 'Selected';
		}
		print("<option value='$UserType' $Selected>$UserType</option>");
	}
	print("</select><br>
	<input type='button' id='EditUser$Counter' value='Edit'>
	<input type='button' id='SaveUser$Counter' value='Save' disabled onclick='saveUserEdits(\"$Counter\")'>
	<input type='Reset' id='ResetUser$Counter' value='Reset' disabled>
	<input type='button' id='DeleteUser$Counter' value='Delete' onclick='deleteUser(\"$Alias\",\"$Counter\",\"$alias\");'></form></span>");
	print("<div id='userMSG$Counter'></div>");
	print("<div id='resetPasswordClickable$Counter' class='dropLevel2'>Reset Password</div>");
	print("<div id='resetPasswordInternal$Counter' class='Internal'>");	
	print("<input type='button' id='resetPassword$Counter' value='Reset Password' onclick=\"adminResetPass('$Counter','$Alias');\">");
	print("<div id='resetPasswordMSG$Counter' class='errMSG'></div>");
	print("</div>"); //End resetPasswordInternal
	print("</div>"); //End Internal Div
	print("</div>"); //End UserDetails Div
	print("
	<script>
	$('#resetPasswordClickable$Counter').click
	(
		function()
		{
			$('#resetPasswordInternal$Counter').slideToggle();
		});
	
	$('#UserClickable$Counter').click
				(
					function()
					{
						$('#UserInternal$Counter').slideToggle();
					});
	$('#EditUser$Counter').click
			(
				function()
				{
					$('#EditUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#SaveUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#ResetUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserAlias$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserFirstName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserLastName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserEmail$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserType$Counter').prop('disabled', function(i, v) { return !v; });
					$('#removePic$Counter').prop('disabled', function(i, v) { return !v; });
			});
	$('#SaveUser$Counter').click
			(
				function()
				{
					$('#EditUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#SaveUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#ResetUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserAlias$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserFirstName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserLastName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserEmail$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserType$Counter').prop('disabled', function(i, v) { return !v; });
					$('#removePic$Counter').prop('disabled', function(i, v) { return !v; });
			});
	$('#ResetUser$Counter').click
			(
				function()
				{
					$('#EditUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#SaveUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#ResetUser$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserAlias$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserFirstName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserLastName$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserEmail$Counter').prop('disabled', function(i, v) { return !v; });
					$('#UserType$Counter').prop('disabled', function(i, v) { return !v; });
					$('#removePic$Counter').prop('disabled', function(i, v) { return !v; });
			});
	</script>");
}

function buildEditPost()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			$DateWritten = $_SESSION['PostID'];
			$Post = getSpecificNews($DateWritten);
			$Category = $Post->value('Category');
			$Details = $Post->value('Details');
			$Title = $Post->value('Title');
			$PubDate = $Post->value('DatePublished');
			$Status = $Post->value('Status');
			$PubDate = date('d-m-Y', $PubDate / 1000);
			
			
				$categories = getAllNewsCategories();
				print("
				<div id='edittingNews'>
				<div id='editNewsCategory'>
				<strong>Category:</strong>
				<br><select id='newsCategorySelect' name='$type'>");
				foreach($categories as $record)
				{
					if($record == $Category)
					{
						$Selected = 'Selected';
					}
					else 
					{
						$Selected  = '';
					}
					print("<option value='$SelectCategory' $Selected>$SelectCategory</option>");
				}
				print("</select>
				<div id='newsTitle'><strong>Title:</strong><br>
				<input type='text' id='newsTitleText' name='$alias' value='$Title'></div>
				<div id='newsDetails'><strong>Details<br>
				</strong><textarea id='newsDetailsText' name='$email'>$Details</textarea></div>
				<div id='newsPubDate'><strong>Date to Publish:</strong><br>
				<input type='text' id='editDatepicker' name='' value='$PubDate'></p></div>");
				print("<script> $('#editDatepciker').datepicker().datepicker('setDate', $PubDate);</script>");
				print("<div id='newsStatus'><strong>Status:</strong>
				<br><select id='newsStatusSELECT' name='$Status' class='editSelect'>");
				$StatusList = getNewsStatusList();
				foreach($StatusList as $record)
				{
					$ListStatus = $record;
					if($Status == $ListStatus)
					{
						$Selected = 'Selected';
					}
					else 
					{
						$Selected = '';
					}
					print("<option value='$ListStatus' $Selected>$ListStatus</option>");
				}
			print("</select>
				<div id='newsSubmit'><input type='button' id='EditNewsButton' name='$DateWritten' value='Save' onclick='ConfirmEditNews()' class='submitBTN'></div>
				<div id='PostMSG'></div>
				</div>");
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
}

function buildAddWebcomic()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			$UserList = getAllUsers();
			$MembershipList = getComicMemberships();
			$StatusList = getWebcomicStatusList();
			print("<div id='addWebomic'>
			<input type='button' id='adminAddWebcomicButton' value='Add Webcomic'>
			<div id='adminAddWebcomicInternal' class='Internal'>
			<div id='webcomicTitle' class='addWebcomicDIV'><strong>Webcomic Name:</strong>
			<br><input type='text' class='webcomicText' id='webcomicNameText'></div>
			<div id='webcomicURL' class='addWebcomicDIV'><strong>URL:</strong>
			<br><input type='text' class='webcomicText' id='webcomicURLText'></div>
			<div id='webcomicRSS' class='addWebcomicDIV'><strong>RSS feed:</strong>
			<br><input type='text' class='webcomicText' id='webcomicRSSText'></div>
			<br><div id='webcomicCreator' class='addWebcomicDIV'><strong>Creator</strong>
			<br><select id='webcomicCreatorSelect' class='webcomicSelect'>");
			foreach($UserList->getRecords() as $user)
			{
				$FirstName = $user->value('FirstName');
				$LastName = $user->value('LastName');
				$Email = $user->value('Email');
				print("<option value='$Email'>$LastName, $FirstName</option>");
			}
			print("</select></div>
			<div id='webcomicMembership' class='webcomicDIV'><strong>Membership</strong>
			<br><select id='webcomicMembershipSelect' class='webcomicSelect'>");
			foreach($MembershipList as $Membership)
			{
				print("<option value='$Membership'>$Membership</option>");
			}
			print("</select></div>

			<div id='webcomicStatus' class='webcomicDIV'><strong>Status:</strong>
			<br><select id='webcomicStatusSelect' class='webcomicSelect'>");
			foreach($StatusList as $Status)
			{
				print("<option value='$Status'>$Status</option>");
			}
			print("<option value='System'>System</option>");
			print("</select></div>		
			<br><input type='button' name='addWebcomic' value='Save' id='addWebcomicBTN' onclick='addWebcomic()'>
			<div id='addWebcomicMSG' class='errMSG'></div>
			</div>");
			print("</div>");
			print("
			<script>
			$('#adminAddWebcomicButton').click
				(
					function()
					{
						$('#adminAddWebcomicInternal').slideToggle();
					});
			</script>");
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
}

function buildManageWebcomics()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			print("<div id='SearchWebcomicsToEdit'>
			<strong>Search :</strong>
			</select><input type='text' id='searchKeywordsTEXT'><input type='button' onclick='searchWebcomicsByName()' value='Search'>
			</div>");
			$WebcomicList = getWebcomicList();
			print("<div id='editWebcomicSearchResults'>");
			$ComicCount = 0;
			foreach($WebcomicList->getRecords() as $record)
			{
				$ComicID = $record->value('ComicID');  
				buildAdminSearchWebcomicResult($ComicID, $ComicCount);
				$ComicCount++;
			}
			print("<div id='editWebcomicMSG'></div>");
			print("</div>");
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
}

function buildAdminSearchWebcomicResult($ComicID, $Counter)
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			$Webcomic = getWebcomicDetails($ComicID);
			$ComicName = $Webcomic->value("Name");
			$RSS = $Webcomic->value("RSS");
			$URL = $Webcomic->value("URL");
			$Synopsis = $Webcomic->value("Synopsis");
			$Pitch = $Webcomic->value("Pitch");
			$WebcomicExtras = getComicDetails($ComicID);
			$Status = $WebcomicExtras->value("Status");
			$Membership = $WebcomicExtras->value("Membership");
			print("<div id='comicSearchResult$Counter' class='adminWebcomicSearchResult'>");
			print("<div id='comicSearchResultClickable$Counter'>$ComicName</div>"); 
			print("<div id='comicSearchResultInternal$Counter' class='Internal'>");
			print("<div id='comicBioClickable$Counter' class='dropLevel1'>Comic Details</div>");
			print("<div id='comicBioInternal$Counter' class='Internal'>");
			print("<form>");
			print("<span class ='searchComicName'>Name: <input type='text' id='comicName$Counter' value=\"$ComicName\" disabled></span>");
			print("<span class ='searchComicRSS'>RSS: <input type='text' id='comicRSS$Counter' value='$RSS' disabled></span>");
			print("<span class ='searchComicURL'>URL: <input type='text' id='comicURL$Counter' value='$URL' disabled></span>");
			print("<span class ='searchComicMembership'>Membership: <select id='comicMembership$Counter' disabled>");
			$MembershipList = getComicMemberships();
			foreach($MembershipList as $PossibleMembership)
			{
				$Selected = '';
				if($PossibleMembership == $Membership)
				{
					$Selected = 'Selected';
				}
				print("<option value='$PossibleMembership' $Selected>$PossibleMembership</option>");
			}
			print("</select></span>");
			print("<span class ='searchComicStatus'>Status: <select id='comicStatus$Counter' disabled>");
			$StatusList = getWebcomicStatusList();
			foreach($StatusList as $PossibleStatus)
			{
				$Selected = '';
				if($PossibleStatus == $Status)
				{
					$Selected = 'Selected';
				}
				print("<option value='$PossibleStatus' $Selected>$PossibleStatus</option>");
			}
			print("</select></span>");
			print("<span class='searchComicPitch'>Pitch:<br><input type='text' id='comicPitch$Counter' value='$Pitch' disabled></span>");
			print("<span class ='searchComicSynposis'>Synopsis:<br> <textarea id='comicSynopsis$Counter' disabled>$Synopsis</textarea></span>");
			print("<input type='button' id='editComic$Counter' value='Edit'> <input type='button' id='saveComic$Counter' value='Save' disabled onclick='saveWebcomicDetails(\"$ComicID\",\"$Counter\");'><input type='reset' id='resetComic$Counter' value='Reset' disabled>");
			print("<div id='comicMSG$Counter' class='errMSG'></div>");
			print("</form>");
			print("</div>"); // End comicBioInternal Div
			print("<script>
			$('#comicSearchResultClickable$Counter').click
			(
				function()
				{
					$('#comicSearchResultInternal$Counter').slideToggle();
			
			});

			$('#comicBioClickable$Counter').click
			(
				function()
				{
					$('#comicBioInternal$Counter').slideToggle();
				});
			
			$('#editComic$Counter').click
					(
						function()
						{
							$('#editComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#saveComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#resetComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicName$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicRSS$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicURL$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicMembership$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicStatus$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicPitch$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicSynopsis$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicSynopsis$Counter').summernote({focus: true, height: 200,
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
			$('#saveComic$Counter').click
					(
						function()
						{
							$('#editComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#saveComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#resetComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicName$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicRSS$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicURL$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicMembership$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicStatus$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicPitch$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicSynopsis$Counter').prop('disabled', function(i, v) { return !v; });
							var markup = $('#comicSynopsis$Counter').summernote('code');
							$('#comicSynopsis$Counter').summernote('destroy');
					});
			$('#resetComic$Counter').click
					(
						function()
						{
							$('#editComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#saveComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#resetComic$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicName$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicRSS$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicURL$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicMembership$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicStatus$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicPitch$Counter').prop('disabled', function(i, v) { return !v; });
							$('#comicSynopsis$Counter').prop('disabled', function(i, v) { return !v; });
							var markup = $('#comicSynopsis$Counter').summernote('code');
							$('#comicSynopsis$Counter').summernote('destroy');
					});
			</script>");
			
			print("<div id='comicSearchManageCrewClickable$Counter' class='dropLevel1'>Crew</div>");
			print("<div id='comicSearchManageCrewInternal$Counter' class='Internal'>");
			$CrewList = getWebcomicCrew($ComicID);
			print("<div id='comicSearchManageCrewAddClickable$Counter' class='dropLevel2'>Add Crew</div>");
			print("<div id='comicSearchManageCrewAddInternal$Counter' class='Internal'>");
			print("Crewmate to add: <select id='addCrew$Counter'>");
			$PotentialCrewList = getAllUsersNotWorkingOnWebcomic($ComicID);
			foreach($PotentialCrewList->getRecords() as $PotentialCrew)
			{
				$PotCrewAlias = $PotentialCrew->value("Alias");
				print("<option value='$PotCrewAlias'>$PotCrewAlias</option>");
			}
			print("</select><br>");
			print("Roles: <input type='text' id='addCrewRoles$Counter'>");
			print("<input type='button' id='addCrew$Counter' value='Add' onclick='adminAddCrew(\"$ComicID\",\"$Counter\");'>");
			print("<div id='addCrewMSG$Counter' class='errMSG'></div>");
			print("</div>"); // end comicSearchManageCrewAddInternal Div
			
			print("
			<script>
			$('#comicSearchManageCrewAddClickable$Counter').click
			(
				function()
				{
					$('#comicSearchManageCrewAddInternal$Counter').slideToggle();
			
			});
			</script>");
			print("<div id='comicSearchManageCrewCurrentCrewClickable$Counter' class='dropLevel2'>Current Crew</div>");
			print("<div id='comicSearchManageCrewCurrentCrewInternal$Counter' class='Internal'>");
			$CrewCount = 0;
			foreach($CrewList->getRecords() as $crewmate)
			{
				print("<div id='manageCrew$Counter".$CrewCount."'>");
				$CrewAlias = $crewmate->value("Alias");
				$CrewRoles = $crewmate->value('Role');
				$CrewRoleString = '';
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
				print("$CrewAlias<br>Roles: <input type='text' id='crewRoles$Counter".$CrewCount."' value='$CrewRoleString' disabled>");
				print("<input type='button' id='editCrew".$Counter.$CrewCount."' value='Edit'> 
				<input type='button' id='saveCrew".$Counter.$CrewCount."' value='Save' onclick='adminUpdateCrew(\"$ComicID\",\"$CrewCount\",\"$Counter\",\"$CrewAlias\");' disabled>
				<input type='button' id='removeCrew".$Counter.$CrewCount."' value='Remove' onclick='adminRemoveCrew(\"$ComicID\",\"$CrewCount\",\"$Counter\",\"$CrewAlias\");'>");
				print("<div id='manageCrewMSG$Counter".$CrewCount."' class='errMSG'></div>");
				print("</div>");
				print("<script>
				$('#editCrew$Counter".$CrewCount."').click
				(
					function()
					{
						$('#crewRoles$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
						$('#saveCrew$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
						$('#editCrew$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
				});
				$('#saveCrew$Counter".$CrewCount."').click
				(
					function()
					{
						$('#crewRoles$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
						$('#saveCrew$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
						$('#editCrew$Counter".$CrewCount."').prop('disabled', function(i, v) { return !v; });
				});
				</script>");
				$CrewCount++;
			}
			print("</div>"); // End comicSearchManageCrewManageCurrentCrewInternal Div
			
			print("<script>
			$('#comicSearchManageCrewCurrentCrewClickable$Counter').click
			(
				function()
				{
					$('#comicSearchManageCrewCurrentCrewInternal$Counter').slideToggle();
			
			});
			</script>");
			print("</div>"); // end ManageCrewInternalDiv
			print("<script>
			$('#comicSearchManageCrewClickable$Counter').click
			(
				function()
				{
					$('#comicSearchManageCrewInternal$Counter').slideToggle();
			
			});
			</script>");
			
			print("<div id='comicGenreClickable$Counter' class='dropLevel1'>Manage Genres</div>");
			print("<div id='comicGenreInternal$Counter' class='Internal'>");
			print("Select Genres:");
			print("<script>
				$('#comicGenreClickable$Counter').click		
				(
					function()
					{
						$('#comicGenreInternal$Counter').slideToggle();
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
			print("<select class='cpanelSelect'  id='FirstGenre$Counter' name=\"$findme\">");
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
			print("<select class='cpanelSelect'  id='SecondGenre$Counter' name=\"$findme\">");
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
			print("<select class='cpanelSelect'  id='ThirdGenre$Counter' name=\"$findme\">");
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
			print("<input type='button' class='up' id='saveGenresFor$Counter' value='Save Genres' onclick=\"adminSaveGenres('$ComicID','$Counter');\">");
			print("<div id='saveComicGenreMSG$Counter'></div>");
			print("</div>"); // End comicGenreDiv
			print("<div id='comicThemeClickable$Counter' class='dropLevel1'>Manage Themes</div>");
			print("<div id='comicThemeInternal$Counter' class='Internal'>");
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
				print("<div class='checkboxWrap'><input type='checkbox' name='themeCheckbox$Counter' class='themeSelectCheckboxes' $Checked value='$ThemeName'>$ThemeName</div>");
			}
			print("<input type='button' id='saveThemes$Counter' class='profileButton up' value='Save Themes' onclick=\"adminSaveThemesForWebcomic('$ComicID','$Counter')\">
			<div id='ThemeMSG$Counter' class='errMSG'></div>");
			
			print("<script>
			$('#comicThemeClickable$Counter').click		
			(
				function()
				{
					$('#comicThemeInternal$Counter').slideToggle();
			});							
			</script>");
			print("</div>"); // End comicThemeInternalDiv
			print("<div id='deleteComicClickable$Counter' class='dropLevel1'>Delete Comic</div>");
			print("<div id='deleteComicInternal$Counter' class='Internal'>");
			print("<input type='button' id='deleteComicButton$Counter' value='Delete' onclick='adminDeleteComic(\"$ComicID\",\"$Counter\",\"$alias\");'>");
			print("<div id='deleteComicMSG$Counter' class='errMSG'></div>");
			
			print("<script>
			$('#deleteComicClickable$Counter').click		
			(
				function()
				{
					$('#deleteComicInternal$Counter').slideToggle();
			});							
			</script>");
			print("</div>"); // End deleteComicInternal
			print("</div>"); // End comicSearchResultInternal Div
			print("</div>"); // End adminWebcomicSearchResult Div
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
}

function buildEditWebcomic()
{
	$ComicID = $_SESSION['ComicID'];
	$Comic = getComicDetails($ComicID);
	$ComicName = $Comic->value("Name");
	$URL = $Comic->value('URL');
	$RSS = $Comic->value('RSS');
	$Status = $Comic->value('Status');
	$Membership = $Comic->value('Membership');
	$UserList = getAllUsers();
	$MembershipList = getComicMemberships();
	$StatusList = getWebcomicStatusList();
	$CrewList = getWebcomicCrew($ComicID);
	print("<div id='WebcomicDetailsDIV'>
	<strong>Name:</strong><br>
	<input type='text' class='editText' name='$ComicName' id='editComicNameText' value='$ComicName'>
	<br><strong>URL:</strong><br>
	<input type='text' class='editText' name='$URL' id='editComicURLText' value='$URL'>
	<br><strong>RSS:</strong><br>
	<input type='text' class='editText' name='$RSS' id='editComicRSSText' value='$RSS'>
	<br><strong>Status:</strong><br>");
	print("<select id='editWebcomicStatusSelect'>");
	foreach($StatusList as $PStatus)
	{
		if($Status == $PStatus)
		{
			$Selected = "Selected";
		}
		else 
		{
			$Selected = "";
		}
		print("<option value='$PStatus'$Selected>$PStatus</option>");
	}
	print("</select><br>
	<strong>Membership:</strong><br>
	<select id='editWebcomicMembershipSelect'>");
	foreach($MembershipList as $PMembership)
	{
		if($Membership == $PMembership)
		{
			$Selected = 'Selected';
		}
		else 
		{
			$Selected = '';
		}
		print("<option value='$PMembership' $Selected>$PMembership</option>");
	}
	print("</select>
	<br>
	<input type='button' value='Save Changes' id='saveWebcomicBTN' onclick='saveComicChanges()'>
	<div id='saveWebcomicMSG' class='errMSG'></div>
	</div>");
	
	print("<div id='CrewDetailsDIV'>
	<h2>Webcomic team:</h2>");
	$crewCount = 0;
	foreach($CrewList->getRecords() as $Crew)
	{	
		$UserAlias = $Crew->value('Alias');
		$User = getUserDetails($UserAlias);
		$FirstName = $User->value('FirstName');
		$LastName = $User->value('LastName');
		$Email = $User->value('Email');
		$PullRole = $Crew->value('Role');
		if(is_array($PullRole))
		{
			$Role = '';
			foreach($PullRole as $item)
			{
				$Role = $Role.$item.',';
			}
			$Role = rtrim(trim($Role),',');
		}
		else 
		{
			$Role = $PullRole;
		}
		$Alias = $User->value('Alias');
		print("<div id='Crew$crewCount class='CrewDIV'>
		<div class='quickEdit'><strong>$FirstName $LastName</strong>
		<br><strong>Role(s):</strong> <input type='text' id='Role$crewCount' value='$Role'></div>
		<br><input type='button' class='editBTN' value='Save' onclick='saveUserRoles(\"$Alias\",\"$crewCount\",\"$ComicID\")'>
		<input type='button' class='editBTN' value='Remove' onclick=\"removeCrew('$Alias','$crewCount','$ComicID')\">
		<div id='crewMSG$crewCount' class='errMSG'></div>
		</div>");
		$crewCount +=1;
	}
	print("</div>");
	print("<div id='addCrewDIV'>
	<div id='addCrewSelectDIV'><strong>Add a new teammate:</strong>
	<br><select id='addNewCrewSelect'>");
	$UserList = getAllUsersNotWorkingOnWebcomic($ComicID);
	foreach($UserList->getRecords() as $user)
	{
		$FirstName = $user->value('FirstName');
		$LastName = $user->value('LastName');
		$Alias = $user->value('Alias');
		print("<option value='$Alias'>$LastName, $FirstName</option>");
	}
	print("</select>
	<br><strong>Role(s):</strong>
	<br><input type='text' class='editText' id='addCrewRoleText'>
	<br><input type='button' class='editBTN' value='Add Crewmate' onclick='addCrew(\"$ComicID\")'>
	<div id='addCrewMSG' class='errMSG'></div>	
	</div>");
}

function buildAddEvent()
{
	print("
	<div id='addEventDIV'>
	<input type='button' id='adminAddEventButton' value='Add Event'>
	<div id='adminAddEventInternal' class='Internal'>
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
											\"dateFormat:\": \"DD-MM-YYYY hh:mm\",
											\"minuteInterval\": 30,
											\"futureOnly\": true			
					});
				});
			</script>
			</div>");
    		print("<div id='addEventOrganizer' class='cpanelItem'>
    		<strong>Organizer:</strong><br>
    			<select id='addEventSELECT'>");
    				$UserList = getAllUsersNotWorkingOnWebcomic($ComicID);
					foreach($UserList->getRecords() as $user)
					{
						$FirstName = $user->value('FirstName');
						$LastName = $user->value('LastName');
						$Email = $user->value('Email');
						print("<option value='$Email'>$LastName, $FirstName</option>");
					}		
  print("</select></div>
  			<div id='addEventCategory' class='cpanelItem'>
  			<strong>Category</strong><br>
  			<select id='addEventCategorySELECT'>");
  				$CategoryList = getCategories();
  				foreach($CategoryList as $Category)
  				{
  					print("<option value='$Category'>$Category</option>");
  				}
  			print("</select></div>
  			<div id='addEventType' class='cpanelItem'>
  				<strong>Event type:</strong><br>
  				<select id='addEventTypeSELECT'>");
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
  			<input type='button' name='addEventButton' id='addEventBTN' onclick='scheduleEvent()' value='Schedule Event'>
  			<div id='addEventMSG' class='errMSG'></div>
    	</div>");
		print("</div>");
		print("
		<script>
		$('#adminAddEventButton').click		
			(
				function()
				{
					$('#adminAddEventInternal').slideToggle();
			});	
		</script>");
} 

function buildEditEvent()
{
	print("<div id='editEventHeader' class='cpanelHeader'><h2>Edit Event:</h2></div>
	<div id='editEventWrap'>");
	$EventList = getAllEvents();
	foreach($EventList->getRecords() as $Event)
	{
		$Start = $Event->value("Start_Time");
		$DateCreated = $Event->value("DateCreated");
		$Type = $Event->value('Type');
		$DivAdd = str_replace(' ', '', $Type);
		$Title = $Event->value('Title');
		$Organizer = $Event->value('Alias');
		$Location = $Event->value('Location');
		$Email = $Event->value('Email');
		$Category = $Event->value('Category');
		$DisplayDate = date('jS M, Y', $Start / 1000);
		$CalendarDefault = date('Y-m-d H:i', $Start / 1000);
		$Details = $Event->value('Details');
		$Status = $Event->value('Status');
		
		$DivID = $DivAdd . 'Event' . $Start;
		print("<div class='EventEntry' id='Event$DivID'>
					<div id='eventClickable$DivID' class='dropLevel1'>$Title - $Organizer - $DisplayDate</div>
					<div id='eventInternal$DivID' class='Internal'>
					<div class='EventItem'><strong>Title:</strong> <input type='text' id='Title$DivID' value=\"$Title\" name=\"$Title\" disabled class='edit$DivID'></div>
					<div class='EventItem'><strong>Location:</strong> <input type='text' id='Location$DivID' name='$Start' value=\"$Location\" disabled class='edit$DivID'></div>
					<div class='EventItem'><strong>Organizer: </strong> <select id='Organizer$DivID' name='$Email' disabled class='edit$DivID'>");
					$UserList = getAllUsers();
					print("<script>
					$('#eventClickable$DivID').click		
			(
				function()
				{
					$('#eventInternal$DivID').slideToggle();
			});							
					</script>");
					foreach($UserList->getRecords() as $User)
					{
						$UserEmail = $User->value('Email');
						$LastName = $User->value('LastName');
						$FirstName = $User->value('FirstName');
						if($Email == $UserEmail)
						{
							$Selected = 'Selected';
						}
						else 
						{
							$Selected = '';
						}
						
						print("<option value='$UserEmail' $Selected>$LastName, $FirstName</option>");
					}
		 			
		 print("		</select>
					 </div>
					 <div class='EventItem'>
					 <strong>Type:</strong><select id='Type$DivID' class='edit$DivID' name='$Type' disabled>");
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
					<strong>Category:</strong><select id='Category$DivID' class='edit$DivID' disabled>");
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
		print("</select>
							</div>
					<div class='EventItem'>");
			print("<strong>Event Date/Time</strong><br>
			<input type='text' id='editEventDate$DivID' value='' disabled name='EventDatePicker$DivID'>
			<script>
				$(function(){
					$('*[name=EventDatePicker$DivID]').appendDtpicker({
											\"dateFormat:\": \"DD-MM-YYYY hh:mm\",
											\"minuteInterval\": 30,
											\"current\": \"$CalendarDefault\",
											\"futureOnly\": true							
					});
				});
			</script>
			</div>");
					print("<div class='EventItem'>
					<strong>Status: </strong><select id='Status$DivID' disabled>");
					$EventStatusList = getEventStatuses();
					foreach($EventStatusList as $EventStatus)
					{
						if($Status == $EventStatus)
						{
							$Selected = 'Selected';
						}
						else 
						{
							$Selected = '';
						}
						print("<option value='$EventStatus' $Selected>$EventStatus</option>");
					}
					print("</select></div>");
					print("<div class='EventDetails'>
					<strong>Details:</strong><br><textarea id='Details$DivID' class='EventDetailsArea' disabled>$Details</textarea>
					</div>
					<div id='Options$DivID'>
						<input type='button' id='Edit$DivID' class='EnableEdit' value='Edit'> 
						<input type='button' id='Save$DivID' class='SaveEdits' value='Save' disabled onclick='saveEventEdits(\"$DivID\")'> 
						<input type='button' id='Delete$DivID' class='DeleteEvent' onclick=\"deleteEvent('$DateCreated','$Type','$Organizer','$DivID')\" value='Remove'> 
					</div>
				</div>
				<script>
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
					});
				</script>
				<div id='err$DivID' class='errMSG'></div>");
				print("</div>");
	}
	print("</div>");
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

function buildAdminDashboard()
{
	$EventList = getAllEvents();
	$NewsList =getAllNews();
	print("
	<div id='AdminDashboardWrap'>
		<div id='AdminDashboard'>
			<div id='leftDash'>");
			$NewsCount = 0;
			print("<div id='DashboardNewsWrap'><h2>News:</h2>");
			foreach($NewsList->getRecords() as $NewsItem)
			{
				$NewsTitle = $NewsItem->value('Title');
				$NewsDetails = $NewsItem->value('Details');
				$NewsPoster = $NewsItem->value('Alias');
				$NewsStatus = $NewsItem->value('Status');
				$NewsPubDate = $NewsItem->value('DatePublished');
				$NewsID = $NewsItem->value("DateWritten");
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
					<div class='DashboardNewsStatus'>Status: $NewsStatus</div>			
					<div class='DashboardNewsRead'><a href='https://www.comicadia.com/news.php?NewsID=$NewsID' target='_blank'>Check it out!</a></div>
				</div>"); 
			}
			print("</div>");
			print("
			</div>
			<div id='middleDash'>
				I am the middle bar.
			</div>
			<div id='rightDash'>");
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
					<div class='DashboardEventRead'><a href='https://www.comicadia.com/events.php?EventID=$EventID'></a></div>
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

function buildFrontPageNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		if($type == 'Member')
		{
			$filter = ['Public','Members'];
		}
		else if($type =='Admin')
		{
			$filter = ['Public','Members','Admin'];
		}
		else
		{
			$filter = ['Public'];
		}
	}
	else
	{
		$filter = ['Public'];
	}
	
	$NewsList = getCollectiveNews($filter);
	print("<div id='frontPageNewsWrap'>");
	foreach($NewsList->getRecords() as $record)
	{
		$Alias = $record->value("Alias");
		//$FirstName = $record->value('First');
		//$LastName = $record->value('Last');
		//$Poster = $FirstName . ' ' . $LastName;
		$Text = $record->value('Details');
		$CleanPreview = strip_tags($Text);
		if(strlen($CleanPreview) > 300)
		{
			$Preview = substr($CleanPreview,0,298).'...';
		}
		else
		{
			$Preview = $CleanPreview;
		}
		$Title = $record->value('Title');
		$ProfilePic = $record->value("Profile");
		$PubDate = $record->value('DatePublished');
		$DateWritten = $record->value("DateWritten");
		$PubDate = date('jS M, Y', $PubDate / 1000);
		print("<div class='newsBox' onclick='window.location.href = \"https://www.comicadia.com/news.php?NewsID=".$DateWritten."\"'>
		<div class='NewsHeader'>
			<div class='iconHolder'><img src='$ProfilePic'></div>
			<div class='newsTitle'>$Title</div>			
			<div class='Poster'>			
			<sub><strong>Post By:</strong> $Alias</sub>
			<br><sup><strong>Date:</strong> $PubDate</div></sup>
		</div>
		<div class='NewsText'>$Preview</div></div>");
	}
	print("<input type='button' class='moreButton' value='More News' onclick='window.location.href = \"https://www.comicadia.com/news.php\"'> ");
	print("</div>");
}

function buildFrontPageEvents()
{
	print("
		<div id='eventwrap'>");
			$EventList = getFrontPageEvents();
			foreach($EventList->getRecords() as $Event)
			{
				$EventTitle = $Event->value('Title');
				$EventOrganizer = $Event->value('Alias');
				$EventStartTime = $Event->value('StartTime');
				$EventType = $Event->value('Type');
				$EventID = $Event->value("DateCreated");
				$EventStart = date('jS M, Y @ H:i', $EventStartTime/1000);
				$ClassName = explode(' ', $EventType);
				print("<div class='eventelement' onclick='window.location.href = \"https://www.comicadia.com/events.php?EventID=".$EventID."\"'><div class='Event$ClassName[0]'>
						<h5>
							$EventTitle
						</h5>
						<span>
							$EventStart
						</span>
					</div></div>");	
			}
	print("<input type='button' id='moreEventsButton' class='moreButton' value='More Events' onclick='window.location.href = \"https://www.comicadia.com/events.php\"'>");
	print("</div>");
}

function buildComicSearch()
{
	print("	<div class='comicsearching'> <div id='comicSearchClickable' class='dropLevel0'>Search Comics</div>
				<div id='comicSearchInternal' class='Internal'>
				<script>
						$('#comicSearchClickable').click
							(
								function()
								{
									$('#comicSearchInternal').slideToggle();
								});
						</script>
				
					<div id='ComicSearchParameters'>
						Search By: 
						<span id='comicSearchByNameClickable' class='dropLevel1'><i class='fa fa-comment-o' aria-hidden='true'></i> Name</span>
						<span id='comicSearchByGenreClickable' class='dropLevel1'><i class='fa fa-hashtag' aria-hidden='true'></i>  Genres</span>
						<span id='comicsSearchByThemeClickable' class='dropLevel1'><i class='fa fa-tag' aria-hidden='true'></i>  Themes</span>
						
						<div id='comicSearchByNameInternal' class='Internal'>
							Name Contains: <input type='text' id='searchComicNameText' name='Inactive'>
						</div> <!-- End searchByName internal -->
						<script>
						$('#comicSearchByNameClickable').click
							(
								function()
								{
									$('#comicSearchByNameInternal').show();
									$('#comicSearchByGenreInternal').hide();
									$('#comicSearchByThemeInternal').hide();
								});
						</script>
					<div id='comicSearchByGenreInternal' class='Internal'>");
					$GenresList = getAllWebcomicGenres();
					foreach($GenresList->getRecords() as $Genres)
					{
						$Genre = $Genres->value("Name");
						print("<div class='checkboxWrap'><input type='checkbox' name='genreCheckbox' class='genreSelectCheckboxes' value='$Genre'>$Genre</div>");
					}
					print("</div>
					<script>
					$('#comicSearchByGenreClickable').click
						(
							function()
							{
									$('#comicSearchByNameInternal').hide();
									$('#comicSearchByGenreInternal').show();
									$('#comicSearchByThemeInternal').hide();
							});
					</script>
					</div>
					<div id='comicSearchByThemeInternal' class='Internal'>");
					$ActiveThemeList = getActiveThemes();
					foreach($ActiveThemeList->getRecords() as $activeThemes)
					{
						$activeTheme = $activeThemes->value("Name");
						print("<div class='checkboxWrap'><input type='checkbox' name='themeCheckbox' class='themeSelectCheckboxes' value='$activeTheme'>$activeTheme</div>");
					}
					print("</div>
						<script>
					$('#comicsSearchByThemeClickable').click
						(
							function()
							{
									$('#comicSearchByNameInternal').hide();
									$('#comicSearchByGenreInternal').hide();
									$('#comicSearchByThemeInternal').show();
							});
					</script>
					
					<input type='button' value='Search' id='searchComicButton' name='searchComicButton'>
					<script>
					$('#searchComicButton').click
						(
							function()
							{
								var SearchBy ='';
								if($('#comicSearchByNameInternal').is(':visible'))
								{
									SearchBy = 'Name';
								}
								if($('#comicSearchByGenreInternal').is(':visible'))
								{
									SearchBy='Genres';
								}
								if($('#comicSearchByThemeInternal').is(':visible'))
								{
									SearchBy='Themes';
								}
								searchComicadiaComics(SearchBy);
							});
					</script>	
				<div id='searchMSG' class='errMSG'></div>
				<div id='testMSG'></div>
				</div>
				</div>");
}

function buildAllComicsSquares()
{
	print("<div id='ComicSearchResults'>");
	$comicList = getAllComicadiaComicsInRandomOrder();
	foreach($comicList->getRecords() as $Comic)
	{
		$ComicID = $Comic->value('ComicID');
		buildComicSquare($ComicID);
	}	
	print("	</div>");
}

function buildComicSquare($ComicID)
{
	$ComicDetails = getComicSquareDetails($ComicID);
	$Comic = $ComicDetails->getRecord();
	$ComicName = $Comic->value("Name");
	$ComicPitch = $Comic->value("Pitch");
	$ComicURL = $Comic->value('URL');
	$ComicThemes = $Comic->value('Themes');
	$ComicRating = caclulateWebcomicRating($ComicID);
	$RatingString = '';
	$SquareURL = getSingleSquareBannerForWebcomic($ComicID);
	$SquareURL = str_replace(" ","%20",$SquareURL);
	$GenreList = getWebcomicGenres($ComicID);
	
	if($ComicRating > 100)
	{
		$RatingString = 'Mature';
		$style = 'Adult';
	}
	elseif($ComicRating > 50)
	{
		$RatingString = 'PG-16';
		$style = 'PG';
	}
	elseif($ComicRating > 25)
	{
		$RatingString = 'PG-13'; 
		$style = 'PG';
	}
	else 
	{
		$RatingString = 'Everyone';
		$style='General';
	}		
				
	$ThemeString = "";
	foreach($ComicThemes as $Theme)
	{
		$ThemeString = $ThemeString + $Theme +', ';
	} 
	 rtrim($ThemeString, '');					
	print("<article class='squaregridparent grid$style' style='background-image:url($SquareURL)'>");
	print("<div style='opacity: 0;' class='squaregrid'>");
	print("<h4>$ComicName</h4>");
	print("<p class='squaredescr'>$ComicPitch</p>");
	print("<div class='fdw-subtitle a-center'><i class='fa fa-hashtag' aria-hidden='true'></i> ");
	foreach($GenreList->getRecords() as $ComicGenres)
	{
		$GenreName = $ComicGenres->value("Name");
		print("<a href='https://www.comicadia.com/comics.php?searchForComicsBy=Genres&Genres=$GenreName'>".$GenreName."</a> - ");
	}
	print("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
	print(" $RatingString </div>");
	print("<p class='fdw-port'>
				<a href='$ComicURL'><i class='fa fa-external-link' aria-hidden='true'></i> Read Comic</a> <a class='fdw-more' href='https://www.comicadia.com/comics.php?ComicID=$ComicID'><i class='fa fa-book' aria-hidden='true'></i> More Info</a>
			</p>");
	print("</div>
	</article>");
}


function buildManageThemes()
{
	print("<div id='ManageThemes'>
				<div id='AddTheme'><h5>Add Theme:</h5>
					Name:<input type='text' id='AddThemeNameText'> 
					Rating Value: <input type='text' id='AddThemeValueText'> 
					<input type='button' id='CreateNewThemeButton' value='Add Theme' onclick='createTheme();'> 
					<div id='addThemeERR'></div>
				</div>
				<div id='CurrentThemes'>");
				buildThemeList();	
	print("</div>
			</div>");
}

function buildThemeList()
{
	$ThemeList = getAllWebcomicThemes();
	$ThemeCount = 0;
	foreach($ThemeList->getRecords() as $Theme)
	{
		$Name = $Theme->value('Name');
		$Rating = $Theme->value('Value');
		print("<div id='EditTheme$ThemeCount'>
			<form>
			<fieldsets>
				<input type='text' id='Theme$ThemeCount' name='$Name' value='$Name' disabled>:
				<input type='text' id='Rating$ThemeCount' name='$Rating' value='$Rating' disabled> 
				<input type='button' id='edit$ThemeCount' class='themeButton' value='Edit'>
				<input type='button' id='save$ThemeCount' class='themeButton' value='Save' disabled onclick=\"saveThemeEdits('$ThemeCount');\">
				<input type='reset' id='cancel$ThemeCount' class='themeButton' value='Reset' disabled>
				<input type='button' id='remove$ThemeCount' class='removeThemeButton' value='Delete' onclick=\"deleteTheme('$ThemeCount');\">
			</fieldsets>
			</form>
			<div id='MSG$ThemeCount' class='errMSG'></div>
			</div>");
		print("<script>
					$('#edit$ThemeCount').click
					(
						function()
						{
							$('#Theme$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#Rating$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#edit$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#save$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#cancel$ThemeCount').prop('disabled', function(i, v) { return !v; });
					});
					
					
					$('#save$ThemeCount').click
					(
						function()
						{
							$('#Theme$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#Rating$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#edit$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#save$ThemeCount').prop('disabled', function(i, v) { return !v; });
        					$('#cancel$ThemeCount').prop('disabled', function(i, v) { return !v; });
					});
				</script>");
		$ThemeCount++;
	}
}

function buildManageGenres()
{
	print("<div id='ManageThemes'>
				<div id='AddTheme'><h5>Add Genre:</h5>
					Name:<input type='text' id='AddGenreNameText'> 
					<input type='button' id='CreateNewGenreButton' value='Add Genre' onclick='createGenre();'> 
					<div id='addGenreERR'></div>
				</div>
				<div id='CurrentGenres'>");
				buildGenreList();	
	print("</div>
			</div>");
}

function buildGenreList()
{
	$GenreList = getAllWebcomicGenres();
	$GenreCount = 0;
	foreach($GenreList->getRecords() as $Genre)
	{
		$Name = $Genre->value("Name");
		print("<div id='editGenre$GenreCount'>");
		print("<form>
		<fieldsets>");
		print("Name: <input type='text' id='Genre$GenreCount' name='$Name' value='$Name' disabled>");
		print("<input type='button' id='edit$GenreCount' value='Edit'> ");
		print("<input type='button' id='save$GenreCount' value='Save' disabled onclick=\"saveGenreEdits('$GenreCount');\"> ");
		print("<input type='Reset' id='reset$GenreCount' value='Reset' disabled> ");
		print("<input type='button' id='delete$GenreCount' value='Delete' onclick=\"deleteGenre('$GenreCount');\"> ");
		print("<div id='MSG$GenreCount' class='ERRMSG'></div>");
		print("</fieldsets>
		</form>");
		print("</div>"); //end editGenre
		print("
		<script>
		$('#edit$GenreCount').click
		(
			function()
			{
				$('#Genre$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#reset$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#edit$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#save$GenreCount').prop('disabled', function(i, v) { return !v; });
		});
		
		$('#save$GenreCount').click
		(
			function()
			{
				$('#Genre$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#reset$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#edit$GenreCount').prop('disabled', function(i, v) { return !v; });
				$('#save$GenreCount').prop('disabled', function(i, v) { return !v; });
		});
		</script>");
		$GenreCount++;
	}
}

function buildFrontPageHeraldNews()
{
	$RSSList = HeraldRSSFeeder();
	print("<div id='frontPageNewsWrap'>");
	
	foreach($RSSList as $RSSItem)
	{
		//$CreatorExists = false;
		$PubDate = $RSSItem["pubDate"];
		
		$PubDate = date('jS M, Y', $PubDate);
		/*
		$Creator = $RSSItem["Creator"];
		if(checkIfCreatorAliasExists($Creator))
		{
			$Creator = getHeraldCreatorComicadiaAlias($Creator);
			$CreatorExists = true;
			$CreatorAlias = $Creator->value("Alias");
			$CreatorProfilePic = $Creator->value("ProfilePic");
			$CreatorUserType = $Creator->value("UserType");
			
		}
		else
		{
			$CreatorAlias = $Creator;
			$CreatorProfilePic = "https://www.comicadia.com/media/user.png";
			$CreatorUserType = "Subscriber";
		}
		*/
		$Link = $RSSItem["link"];
		$Title = $RSSItem["title"];
		$Desc = $RSSItem["description"];
		$Text = $Desc;
		$CleanPreview = strip_tags($Text);
		if(strlen($CleanPreview) > 300)
		{
			$Preview = substr($CleanPreview,0,298).'...';
		}
		else
		{
			$Preview = $CleanPreview."...";
		}
		print("<div class='newsBox' onclick='window.location.href = \"".$Link."\"'>
		<div class='NewsHeader'>");
			//<div class='iconHolder'><img src='$CreatorProfilePic'></div>
			
		print("<div class='newsTitle'>$Title</div>			
			<div class='Poster'>");
			//<sub><strong>Post By:</strong> $CreatorAlias</sub>
		print("<br><sup><strong>Date:</strong> $PubDate</div></sup>
		</div><!-- End NewsHeader -->
		<div class='NewsText'>$Preview</div><!-- end NewsText --></div><!-- End newsBox-->");
	}
	print("<input type='button' class='moreButton' value='More News' onclick='window.location.href = \"https://herald.comicadia.com\"'>");
	print("</div><!-- end frontPageNewsWrap -->");
}

function buildHorizontalRSS()
{
	$RSSList = RSSFeeder();

	foreach($RSSList as $RSSItem)
	{
		try
		{
			$ComicName = $RSSItem['Comic'];
			$ComicID = $RSSItem['ID'];

			$HorizBannerURL = getSingleHorizontalBannerForWebcomic($ComicID);
			$HorizBannerHoverURL = getSingleHorizontalBannerHoversForWebcomic($ComicID);
			if($HorizBannerURL)
			{
			}
			
			if(!$HorizBannerURL)
			{
				//Set a default image for banners if somehow one doesn't exists
			}
			
			if(!$HorizBannerHoverURL)
			{
				//Set a default hover image for banners if somehow one doesn't exists.
			}
			
			$WebcomicDetails = getWebcomicDetails($ComicID);
			$Rating = caclulateWebcomicRating($ComicID);
			
			$GenreList = getWebcomicGenres($ComicID);
			$ComicURL = $WebcomicDetails->value("URL");
			$ComicPitch = $WebcomicDetails->value("Pitch");
			
			
			if($Rating > 100)
			{
				//NSFW Symbol
				$RatingString = 'Mature';
				$style = 'Adult';
			}
			elseif($Rating > 50)
			{
				//PG-16 or some such Symbol
				$RatingString = 'PG-16';
				$style = 'PG';
			}
			elseif($Rating > 25)
			{
				//PG-13 Symbol
				$RatingString = 'PG-13';
				$style = 'PG';
			}
			else
			{
				//General Audience
				$RatingString = 'Everyone';
				$style = 'General';
			}
			
			if(strlen($ComicPitch) > 160)
			{
				$Pitch = substr($ComicPitch,0,157).'...';
			}
			else
			{
				$Pitch = $ComicPitch;
			}
			print("<article class='horizgridparent rss$style' style='background-image:url($HorizBannerURL)'>
				<div style='opacity: 0; background-image:url($HorizBannerHoverURL)' class='horizgrid'>
					<div class='tophoriztitle'><span class='horiztitle'>$ComicName</span> <span class='fdw-subtitle'> <i class='fa fa-hashtag' aria-hidden='true'></i>&nbsp;");
					
			foreach($GenreList->getRecords() as $GenreRecord)
			{
				$GenreName = $GenreRecord->value('Name');
				print("<a href='https://www.comicadia.com/comics.php?searchForComicsBy=Genres&Genres=$GenreName'>".$GenreName."</a> · ");
			}
			print("<i class='fa fa-exclamation-triangle' aria-hidden='true'></i>");
			print("&nbsp;$RatingString </span>");
			print("<p class='fdw-port'>
							<a href='$ComicURL'><i class='fa fa-external-link' aria-hidden='true'></i> Read</a> 
							<a class='fdw-more' href=\"https://www.comicadia.com/comics.php?ComicID=$ComicID\"><i class='fa fa-book' aria-hidden='true'></i> + Info</a>
						</p>
						</div>");
			print("<p class='horizdescr'>$Pitch</p></div></article>");
		}
		catch (Exception $e) 
		{
			
			sendGenericEmailFromNoReply("Kiljax@gmail.com", "Failure to read RSS for $ComicName, Error is as follows:<br><br>$e");
		}
	}	
	
}
/*
function buildAllNewsPanel()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		$User = getUserDetails($email);
		
		if($type == 'Member')
		{
			buildMemberNewsPanel();
		}
		else if($type == 'Admin')
		{
			buildAdminNewsPanel();
		}
		else
		{
			buildPublicNewsPanel();
		}
	}
	else
	{
		buildPublicNewsPanel();
	}
}

function buildMemberNewsPanel()
{
	$NewsList = getMemberNews();
	buildNewsPanelFromList($NewsList);
}

function buildAdminNewsPanel()
{
	$NewsList = getAllNews();
	buildNewsPanelFromList($NewsList);
}

function buildPublicNewsPanel()
{
	$NewsList = getPublicNews();
	buildNewsPanelFromList($NewsList);
}
*/

function buildMemberSearch()
{
	print("<div id='searchMembers'>");
	print("<span class='searchBar'>Alias contains: <input type='text' id='searchMembersText'> <input type='button' id='searchMembersButton' value='Search' onclick='searchMembers();'></span>");
	print("<div id='searchMSG' class='ErrMSG'></div>");
	print("</div>");
}

function buildMemberListFromSearchAsPerPagination($Search, $Start, $NumberOfArticles)
{
	$MemberList = getMembersListFromSearchByPagination($Search,$Start,$NumberOfArticles);
	buildMemberPanelFromList($MemberList);
}

function buildMemberListAsPerPagination($Start, $NumberOfArticles)
{
	$MemberList = getMembersListByPagination($Start,$NumberOfArticles);
	buildMemberPanelFromList($MemberList);
}

function buildMemberPanelFromList($MemberList)
{
		print("<div id='searchResults' class='membersPagination'>");
		foreach($MemberList->getRecords() as $Member)
		{
		
			$MemberAlias = $Member->value("Alias");
			$MemberType = $Member->value("UserType");
			$MemberProfile = $Member->value("ProfilePic");
			$WebcomicList = getUsersWebcomicNames($MemberAlias);
			print("<div class='membersSearchResult $MemberType'><a href=\"https://www.comicadia.com/members.php?MemberAlias=$MemberAlias\">");
			if($MemberProfile != "")
			{
				print("<img src='$MemberProfile' />");
			}
			else
			{
				print("<img src='https://www.comicadia.com/media/user.png' />");
			}
			print("$MemberAlias</a>");
			if($WebcomicList)
			{
				$WebcomicListString = "";
				print("<p class='membersWebcomics'>Webcomics: ");
					
				foreach($WebcomicList->getRecords() as $Webcomic)
				{
					$ComicName = $Webcomic->value("Name");
					$ComicURL = $Webcomic->value("URL");
					$WebcomicListString .= "<a href='$ComicURL' target='_blank'>$ComicName</a>, ";
				}
				$WebcomicListString = rtrim($WebcomicListString, ', ');
				print("$WebcomicListString</p>");			
			}
			$SocialMediaList = getAllSocialMediaTypes();
			foreach($SocialMediaList->getRecords() as $SocMedia)
			{
				$Class = $SocMedia->value("Class");
				$SocName = $SocMedia->value("Name");
				$BGColor = $SocMedia->value("BGColor");
				$YourSocMediaURL = getSpecificSocialMediaURLByName($SocName,$MemberAlias);
				if($YourSocMediaURL)
				{
					print("<span class='memberSocialMedia'><a href='$YourSocMediaURL' target='_blank'><span class='memberProfileSocialMediaIcon' style='background-color: $BGColor;'><i class='$Class'></i></span></a></span>");	
				}
				else
				{
					$YourSocMediaURL = '';
				}
			}
			print("<div class='clear'></div></div>");
			
		}
		print("</div>");
}

function buildNewsPanelByTypeAsPerPagination($Type, $Start, $NumberOfArticles)
{
	if($Type == 'Member')
	{
		$searchBy = ['Members','Public'];
	}
	else if($Type =='Admin')
	{
		$searchBy = ['Members','Public','Admin'];
	}
	else
	{
		$searchBy = ['Public'];
	}
	
	$NewsList = getNewsByTypeForPagnation($searchBy,$Start,$NumberOfArticles);
	buildNewsPanelFromList($NewsList);
}

function buildNewsPanelFromList($NewsList)
{
	print("<div id='newsPagePanel' class='newsPagination'>");
	foreach($NewsList->getRecords() as $record)
	{
		$Alias = $record->value("Alias");
		//$FirstName = $record->value('First');
		//$LastName = $record->value('Last');
		//$Poster = $FirstName . ' ' . $LastName;
		$Text = $record->value('Details');
		
		$CleanPreview = strip_tags($Text);
		if(strlen($CleanPreview) > 300)
		{
			$Preview = substr($CleanPreview,0,298).'...';
		}
		else
		{
			$Preview = $CleanPreview;
		}
		
		$Title = $record->value('Title');
		$ProfilePic = $record->value("Profile");
		$PubDate = $record->value('DatePublished');
		$DateWritten = $record->value("DateWritten");
		$PubDate = date('jS M, Y', $PubDate / 1000);
		print("<div class='newsBox'>
		<div id='NewsHeader$DateWritten' class='NewsHeader'>
			<div class='iconHolder'><img src='$ProfilePic'></div>
			<div class='newsTitle'>$Title</div>			
			<div class='Poster'>			
			<sub><strong>Post By:</strong> $Alias</sub>
			<br><sup><strong>Date:</strong> $PubDate</div></sup>
		</div>
		<div id='NewsText$DateWritten' class='NewsText'>$Preview</div>");
		print("<div id='NewsMore$DateWritten'><a href='https://www.comicadia.com/news.php?NewsID=$DateWritten'>See full article</a></div>");
		print("</div>");
	}
	print("</div>");
}

function buildEventPanelByTypeAsPerPagination($Type, $startArticle, $articlesPerPage,$Alias)
{
	if($Type == 'Member')
		{
			$searchBy = ['Members','Public'];
		}
		else if($Type =='Admin')
		{
			$searchBy = ['Members','Public','Admin'];
		}
		else
		{
			$searchBy = ['Public'];
		}
		
		$EventList = getEventByTypeForPagnation($searchBy,$startArticle,$articlesPerPage);
		buildEventPanelFromList($EventList,$Type,$Alias);
}

function buildEventPanelFromList($EventList,$Type,$alias)
{
	print("<div id='EventPagePanel' class='eventPagination'>");
	foreach($EventList->getRecords() as $record)
	{
		$Alias = $record->value("Alias");
		//$FirstName = $record->value('First');
		//$LastName = $record->value('Last');
		//$Poster = $FirstName . ' ' . $LastName;
		$Text = $record->value('Details');
		$Title = $record->value('Title');
		$ProfilePic = $record->value("Profile");
		$PubDate = $record->value('StartTime');
		$DateCreated = $record->value("DateCreated");
		$PubDate = date('H:i - jS M, Y', $PubDate / 1000);
		$EventType = $record->value("Type");
		$DivName = str_replace(' ', '', $EventType);
		
		$CleanPreview = strip_tags($Text);
		if(strlen($CleanPreview) > 300)
		{
			$Preview = substr($CleanPreview,0,298).'...';
		}
		else
		{
			$Preview = $CleanPreview;
		}
		print("<div class='eventBox$DivName'><div>
		<div id='EventHeader$DateCreated' class='EventHeader'>
			<div class='eventTitle'>$Title <span class='eventType'>$EventType</span></div>			
		</div>
		<div id='EventText$DateCreated' class='EventText'>$Preview
		<div id='EventMore$DateCreated' class='boton'><a href='https://www.comicadia.com/events.php?EventID=$DateCreated'>See full article</a></div>
		<div class='clear'></div>
		</div>
					<div class='Organizer'>	
				<div class='iconHolder'><img src='$ProfilePic'></div>
				<strong>Organizer:</strong> $Alias
				<br><strong>Event Starts::</strong> $PubDate
				<div class='clear'></div>
			</div>
		");
		if($Type == 'Member' || $Type == 'Admin')
		{
			$Attending = getWhetherMemberIsAttendingEvent($alias,$DateCreated);
			if($Attending)
			{
				print("<input type='button' class='eventButton' id='attendingButton$DateCreated' value='Cancel attendance'><br>");
			}
			else
			{
				print("<input type='button' class='eventButton' id='attendingButton$DateCreated' value='Attend'><br>");
			}
			print("<div id='eventMSG$DateCreated'></div>");
		}
		$AttendeeList = getEventAttendees($DateCreated);
		if(count($AttendeeList) > 0)
		{
			print("<i class='fa fa-ticket'></i> <strong>Currently attending: </strong>");
			foreach($AttendeeList->getRecords() as $Attendee)
			{	
				$AttendeeAlias = $Attendee->value('Alias');
				print("<a href='https://www.comicadia.com/members.php?Alias=$AttendeeAlias'>$AttendeeAlias</a> ");
			}
		}
		
		print("</div></div>
		<script>
		$('#attendingButton$DateCreated').click
		(
			function()
			{
				if(document.getElementById('attendingButton$DateCreated').value == 'Attend')
				{
					confirmAttendance('$DateCreated','$alias');
					document.getElementById('attendingButton$DateCreated').value = 'Cancel attendance';
				}
				else
				{
					document.getElementById('attendingButton$DateCreated').value = 'Attend';
					cancelAttendance('$DateCreated','$alias');
				}
		});
					
		</script>");
	}
	print("</div>");
}

function buildNewsSearch()
{
	print("<div id='newsSearch'>
	<div id='searchNewsClickable'class='dropLevel1'>Search News</div>
	<div id='searchNewsInternal' class='Internal'>
	Keyword or phrase: <input type='text' id='searchNewsText'>
	<input type='button' id='searchNewsButton' value='Search' onclick='searchNewsForKeywords()'></div>");
	print("
	<script>
		$('#searchNewsClickable').click
					(
						function()
						{
							$('#searchNewsInternal').slideToggle();
					});
		</script>");
}

function buildComicProfile($ComicID)
{
	$ComicDetails = getComicDetails($ComicID);
	$ComicName = $ComicDetails->value("Name");
	print("<div id='ComicProfile'><div class='profil1'>
	<h2 class='ComicTitle'><i class='fa fa-comment-o' aria-hidden='true'></i> $ComicName</h2>");
	
	$ComicID = $ComicDetails->value("ComicID");
	$SquareDetails = getComicSquareDetails($ComicID);
	$SquareBanner = getSingleSquareBannerForWebcomic($ComicID);
	$Square = $SquareDetails->getRecord();
	$ComicThemes = getWebcomicThemes($ComicID);
	$ComicGenres = getWebcomicGenres($ComicID);
	$ComicPitch = $ComicDetails->value("Pitch");
	$ComicSynopsis = $ComicDetails->value("Synopsis");
	$ComicURL = $ComicDetails->value("URL");
	$ComicRSS = $ComicDetails->value("RSS");
	$GenreString = '';
	
	foreach($ComicGenres->getRecords() as $Genres)
	{
		$Genre = $Genres->value("Name");
		$GenreString = $GenreString ."<span class='genreSpan'>".$Genre."</span>";
	}
	$GenreString = rtrim(trim($GenreString),',');
	
	
	$ThemeString = '';
	foreach($ComicThemes->getRecords() as $Themes)
	{
		$Theme = $Themes->value("Name");
		$ThemeString = $ThemeString ."<span class='themeSpan'>".$Theme."</span>";
	}
	$ThemeString = rtrim(trim($ThemeString),',');
	
	print("<div id='SquarePreview'><img src='$SquareBanner'></div>");
	print("<a id='comicURL' href='$ComicURL' target='_blank'><i class='fa fa-home' aria-hidden='true'></i> Visit the Website</a>");
	print("<a id='comicRSS' href='$ComicRSS' target='_blank'><i class='fa fa-rss' aria-hidden='true'></i> Comic RSS</a>
	<div class='clear'></div>
	");
	print("
	<div id='comicCrew'><h3><i class='fa fa-user-circle' aria-hidden='true'></i> Comic Crew</h3>");
	$ComicCrew = getWebcomicCrew($ComicID);
	$CrewCount = 0;
	foreach($ComicCrew->getRecords() as $Crewmate)
	{
		$ProfilePic = $Crewmate->value("Profile");
		$CrewHeadAlias = $Crewmate->value("Alias");
		if($ProfilePic != '')
		{
			print("<div id='comicCrewClickable$CrewCount' class='dropLevel1'><img src='$ProfilePic'></div>");
		}
		else
		{
			print("<div id='comicCrewClickable$CrewCount' class='dropLevel1'><strong>$CrewHeadAlias</strong></div>");
		}
		$CrewCount++;
	}
	$CrewCount = 0;
	foreach($ComicCrew->getRecords() as $Crewmate)
	{
		$CrewAlias = $Crewmate->value("Alias");
		$Profile = $Crewmate->value("Profile");
		$RoleList = $Crewmate->value("Role");
		$RoleString = '';
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
		print("<div id='comicCrewInternal$CrewCount' class='Internal'>
		<div id='profileAlias$CrewCount'><strong>$CrewAlias</strong></div>
		<div id='crewRoles$CrewCount'>$RoleString</div>
		</div>");
		print("<script>
		$('#comicCrewClickable$CrewCount').click
					(
						function()
						{
							$('#comicCrewInternal$CrewCount').slideToggle();
					});
		</script>");
		$CrewCount++;
	}
	
	print("<div class='clear'></div></div>	
	
	<h3><i class='fa fa-share' aria-hidden='true'></i> Social Media</h3> <br>");
	$SocialMediaList = getAllSocialMediaTypes();
	foreach($SocialMediaList->getRecords() as $SocMedia)
	{
		$Class = $SocMedia->value("Class");
		$SocName = $SocMedia->value("Name");
		$BGColor = $SocMedia->value("BGColor");
		$YourSocMediaURL = getSpecificSocialMediaURLByNameForComic($SocName,$ComicID);
		if($YourSocMediaURL)
		{
			print("<span class='webcomicSocialMedia'><a href='$YourSocMediaURL' target='_blank'><span class='webcomicProfileSocialMediaIcon' style='background-color: $BGColor;'><i class='$Class'></i></span></a></span>");	
		}
	}
	print("<h3><i class='fa fa-rss' aria-hidden='true'></i> Comic Feed</h3> <br>
	</div>
	");
	
	print("<div class='profil2'>
	<h2><i class='fa fa-info-circle' aria-hidden='true'></i> Comic Info</h2>
	<div id='comicPitch' class='comicPitch'>$ComicPitch</div>
	<div id='comicGenres'><span class='comilefti'><i class='fa fa-hashtag' aria-hidden='true'></i> <strong>Genres:</strong></span> <span class='comicDetailsText'>$GenreString</span></div>
	<div class='clear'></div>
	<h3 id='comicDetails'><i class='fa fa-bookmark' aria-hidden='true'></i> Synopsis</h3>	
		<div class='comicDetailsText'>$ComicSynopsis</div>
		<div class='clear'></div>
	<div id='comicThemes'><span class='comilefti'><i class='fa fa-tags' aria-hidden='true'></i> <strong>Themes:</strong></span> <span class='comicDetailsText'>$ThemeString</span></div>
	<div class='clear'></div>
	</div>");
	print("<div class='clear'></div></div>");
}

function buildManageSplash()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Admin')
		{
			print("<div id='manageSplashDIV'>");
			print("<div id='cadenceArtPlaceholder'>");
			print("Current Cadence art:<br>");
			$Splash = getSplash();
			if($Splash)
			{
				$CurrentCadence = $Splash->value("URL");
				print("<span id='cadencePreview'><img src='$CurrentCadence' /></span>");
			}
			else
			{
				print("<span id='cadencePreview'>No image selected.</span>");
			}
			print("<br><input type='button' value='Change' id='changeCadenceSplashButton'>");
			print("<div id='changeCadenceSplashInternal' class='Internal'>");
			print("<input type='button' id='selectCadenceSplashFromDatabaseButton' value='Select from database'>");
			/*print("<input type='button' id='uploadCadenceSplashArtButton' value='Upload New Art'>");*/
			$CadenceArtCount = 0;
			print("<div id='selectCadenceSplashFromDatabaseInternal' class='Internal'>");
			print("<div id='CadencePreviews'>");
			$CadenceArtList = getCadenceArtList();
			foreach($CadenceArtList->getRecords() as $PotentialCadence)
			{
				
				$PreviewURL = $PotentialCadence->value("URL");
				$Checked = '';
				if($Splash)
				{
					if($CurrentCadence == $PreviewURL)
					{
						$Checked = 'checked';
					}
				}
				print("<span class='selectCadence'><input type='radio' id='select$CadenceArtCount' name='CadenceSelector' value='$PreviewURL' $Checked class='previewCadenceAnnounceSelect'><label for='select$CadenceArtCount'><img src='$PreviewURL' /></label>");
				print("</span>");
				$CadenceArtCount++;
			}
			print("<input type='button' id='updateCadenceSplashArtButton' value='Save' onclick='updateCadenceSplashArt();'>");
			print("<div id='updateCadenceSplashMSG'></div>");
			print("</div>"); //End CadencePreviews
			print("</div>"); //End selectFromDatabaseInternal
			/*
			print("<div id='uploadCadenceSplashArtInternal' class='Internal'>");
			print("<input type='button' id='uploadCadenceFromWebURLButton' value='From The Web'>");
			print("<input type='button' id='uploadCadenceFromLocalFileButton' value='From Computer'>");
			print("<div id='uploadCadenceFromWebURLInternal' class='Internal'>");
			print("<input type='text' id='uploadCadenceSplashFromURLText'>");
			print("<input type='button' id='uploadCadenceSplashFromURLButton' onclick='uploadCadenceSplashFromURL('$alias');' value='Upload'>");
			print("</div>"); //End uploadCadenceFromWebURL Internal
			print("<div id='uploadCadenceFromLocalFileInternal' class='Internal'>");
			print("<input type='file' id='uploadCadenceFromLocalFile'>");
			print("Artist: <select id='uploadCadenceFromLocalArtistSelect'>");
			$UserList = getAllComicadiaMembers();
			foreach($UserList-getRecords() as $Member)
			{
				$MemberAlias = $Member->value("Alias");
				print("<option value='$MemberAlias'>$MemberAlias</option>");
			}
			print("</select>");
			print("<input type='button' id='uploadCadenceSplashFromFileButton' onclick='uploadCadenceSplashFromLocalFile('$alias');' value='Upload'>");
			print("</div>"); // End uploadcadenceFromLocalFileInternal
			print("<div id='uploadCadencesplashMSG' class='ERRMSG'></div>");
			print("</div>"); // End uploadNewCadenceSplash
			*/
			print("</div>"); //End changeCadenceSplashInternal
			print("</div>"); //End cadenceArtPlaceholder
			if($Splash)
			{
				$SplashTitle = $Splash->value("Title");
				$SplashText = $Splash->value("Text");
			}
			else
			{
				$SplashTitle = '';
				$SplashText = '';
			}
			print("<div id='splashTitle'>Title:<br><input type='text' id='SplashTitle' value='$SplashTitle'></div>");
			print("<div id='splashMessage'>");
			print("Text:<br>");
			print("<textarea id='splashMessageTEXT' class='summernote'>$SplashText</textarea>");
			print("</div>"); // End splashMessage
			print("<input type='button' value='Update Splash Message' id='saveSplashButton' onclick=\"updateSplash('$alias');\"> ");
			print("<div id='updateSplashMSG'></div>");
			print("</div>");
			print("<h2>Preview:</h2>");
			print("<div id='splashPreview'>");
			buildSplashPreview();
			print("</div>");
			print("<script>
			$('#changeCadenceSplashButton').click
			(
				function()
				{
					$('#changeCadenceSplashInternal').slideToggle();
			});
			$('#uploadCadenceSplashArtButton').click
			(
				function()
				{
					$('#uploadCadenceSplashArtInternal').show();
					$('#selectCadenceSplashFromDatabaseInternal').hide();
			});
			$('#selectCadenceSplashFromDatabaseButton').click
			(
				function()
				{
					$('#selectCadenceSplashFromDatabaseInternal').show();
					$('#uploadCadenceSplashArtInternal').hide();
					
			});
			$('#uploadCadenceFromWebURLButton').click
			(
				function()
				{
					$('#uploadCadenceFromWebURLInternal').show();
					$('#uploadCadenceFromLocalFileInternal').hide();
					
			});
			$('#uploadCadenceFromLocalFileButton').click
			(
				function()
				{
					$('#uploadCadenceFromLocalFileInternal').show();
					$('#uploadCadenceFromWebURLInternal').hide();
					
			});
			$('#updateCadenceSplashArtButton').click
			(
				function()
				{
					$('#changeCadenceSplashInternal').slideToggle();
			});
			</script>");
		}
		else
		{
			
		}
	}
	else
	{
		
	}
}

function buildSplashPreview()
{
	$Splash = getSplash();
	if($Splash)
	{
		$SplashTitle = $Splash->value("Title");
		$CadenceArt = $Splash->value("URL");
		$SplashText = $Splash->value("Text");
		print("<div id='FrontPageSplash'>
		<div id='CadenceAnnounces' style='background-image: url($CadenceArt); Height: 200px; Width: 150px'></div>
		<div id='splashMessage'><span class='splashHeader'>$SplashTitle</span><span class='splashMessage'>$SplashText</span></div>
		<div class='clear'></div></div>");
	}
	else
	{
		print("No preview available at this time");
	}
}

function buildAddMedia()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Admin')
		{
			$Artists = getAllUsers();
			print("<div id='adminAddMedia'>");
			print("<input type='button' id='adminAddMediaButton' value='Add Media'>");
			print("<div id='adminAddMediaInternal' class='Internal'>");
			print("<input type='button' id='adminAddMediaFromURLButton' value='From The Web'>");
			print("<input type='button' id='adminAddMediaFromLocalButton' value='From your Computer'>");
			print("<div id='adminAddMediaFromURLInternal' class='Internal'>");
			
			print("URL: <input type='text' id='addNewMediaURL' class='addFileText'><br>
			Media Type: <select class='cpanelSelect'  id='addFileFromWebTypeSelect' >");
			$MediaTypes = getCadenceMediaTypes();
			foreach($MediaTypes->getRecords() as $MediaTypeList)
			{
				$MediaType = $MediaTypeList->value("TypeName");
				$MediaWidth = $MediaTypeList->value("Width");
				$MediaHeight = $MediaTypeList->value("Height");
				print("<option value='$MediaType'>$MediaType (".$MediaWidth."x".$MediaHeight.")</option>");
			}
			
			print("</select><br>");
			print("Artist <select class='cpanelSelect'  id='addMediaArtistFromURL'>");
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
				print("<option value='$ArtistAlias' $Selected>$ArtistAlias ($ArtistFirstName $ArtistLastName)</option>");
			}
			print("</select><br>
			Description: <br>
			<textarea id='addMediaFromURLDescriptionText' class='profileTextarea'></textarea><br> ");
			print("<input type='button' id='addMediaFromURLButton' class='profileButton' value='Upload' onclick=\"uploadMediaFromURL('$alias')\">");
			print("</div>"); //End adminAddMediaFromURLInternal
			
			print("<div id='adminAddMediaFromLocalInternal' class='Internal'>");
			
			print("File Location: <br><input type='File' name='addNewMediaFileLocation' id='addNewMediaFileLocation' class='addFileText'><br>");
			print("Media Type: <select class='cpanelSelect'  id='addFileFromLocalTypeSelect'>");
			foreach($MediaTypes->getRecords() as $MediaTypeList)
			{
				$MediaType = $MediaTypeList->value("TypeName");
				$MediaWidth = $MediaTypeList->value("Width");
				$MediaHeight = $MediaTypeList->value("Height");
				print("<option value='$MediaType'>$MediaType (".$MediaWidth."x".$MediaHeight.")</option>");		
			}
			
			print("</select><br>");
			print("Artist: <select class='cpanelSelect'  id='addMediaArtistForLocal' name='addMediaArtistFromLocal'>");
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
				print("<option value='$ArtistAlias' $Selected>$ArtistAlias ($ArtistFirstName $ArtistLastName)</option>");
			}
			print("</select><br>
			Description: <br>
			<textarea id='addMediaDescriptionForLocalText' class='profileTextarea'></textarea><br> ");
			print("<input type='button' class='profileButton' id='addMediaFromLocalButton' value='Upload' onclick=\"uploadMediaFromLocal('$alias');\">");
			
			print("</div>"); //End adminAddMediaFromLocalInternal
			print("<div id='adminAddMediaERRMSG' class='ERRMSG'></div>");
			print("</div>"); // End adminAddMediaInternal
			print("</div>");
			
			print("
			<script>
				$('#adminAddMediaButton').click
				(
					function()
					{
						$('#adminAddMediaInternal').slideToggle();
				});
				$('#adminAddMediaFromURLButton').click
				(
					function()
					{
						$('#adminAddMediaFromURLInternal').show();
						$('#adminAddMediaFromLocalInternal').hide();
				});
				$('#adminAddMediaFromLocalButton').click
				(
					function()
					{
						$('#adminAddMediaFromLocalInternal').show();
						$('#adminAddMediaFromURLInternal').hide();
				});
			</script>");
		}
		else
		{
			
		}
	}
	else
	{
		
	}
}

function buildManageMedia()
{
	
	print("<div id='adminManageMediaDIV'>");
	$MediaTypeList = getAllMediaTypes();
	print("<div id='adminManageMediaChoice'>");
	$MediaHeadTypeCount = 0;
	foreach($MediaTypeList->getRecords() as $MediaType)
	{
		$MediaName = $MediaType->value("TypeName");
		print("<input type='button' id='mediaTypeButton$MediaHeadTypeCount' class='manageMediaButton' value='$MediaName'>");
		$MediaHeadTypeCount++;
	}
	print("</div>"); //End adminManageMediaChoice Div
	print("<div id='adminManageMediaSelection'>");
	$MediaTypeCount = 0;
	foreach($MediaTypeList->getRecords() as $MediaType)
	{
		
		$MediaTypeName = $MediaType->value("TypeName");
		print("<div id='mediaTypeInternal$MediaTypeCount' class='Internal'>");
		$MediaList = getAllMediaByType($MediaTypeName);
		$MediaCount = 0;
		foreach($MediaList->getRecords() as $CurrentMedia)
		{
			$CurrentMediaURL = $CurrentMedia->value("URL");
			$CurrentMediaArtist = $CurrentMedia->value("Artist");
			$CurrentMediaStatus = $CurrentMedia->value("Status");
			$MediaCount++;
			print("<div id='mediaType".$MediaTypeCount."Media".$MediaCount."' class='currentMediaPreview'>");
			print("<span class='adminManageMediaPreview'><img src='$CurrentMediaURL' /></span>");
			print("<span class='adminManageMediaArtist'>Artist: $CurrentMediaArtist</span>");
			if($CurrentMediaStatus == 'Active')
			{
				print("<input type='button' value='Activate' id='activateMediaButton".$MediaTypeCount."".$MediaCount."' class='activateMediaButton' onclick=\"activateMedia('$CurrentMediaURL','$MediaTypeCount','$MediaCount');\" style='display: none'>");
				print("<input type='button' value='Deactivate' id='deactivateMediaButton".$MediaTypeCount."".$MediaCount."' class='activateMediaButton' onclick=\"deactivateMedia('$CurrentMediaURL','$MediaTypeCount','$MediaCount');\">");
			}
			else
			{
				print("<input type='button' value='Activate' id='activateMediaButton".$MediaTypeCount."".$MediaCount."' class='activateMediaButton' onclick=\"activateMedia('$CurrentMediaURL','$MediaTypeCount','$MediaCount');\">");
				print("<input type='button' value='Deactivate' id='deactivateMediaButton".$MediaTypeCount."".$MediaCount."' class='activateMediaButton' onclick=\"deactivateMedia('$CurrentMediaURL','$MediaTypeCount','$MediaCount');\" style='display: none'>");
			}
			print("<input type='button' value='Delete' if='deleteMediabutton".$MediaTypeCount."".$MediaCount."' class='deleteMediabutton' onclick=\"deleteMedia('$CurrentMediaURL','$MediaTypeCount','$MediaCount');\">");
			print("<div id='ERRMSG$MediaTypeCount".$MediaCount."' class='ERRMSG'></div>");
			print("</div>");
			print("<script>
			
			$('#activateMediaButton".$MediaTypeCount."".$MediaCount."').click
				(
					function()
					{
						$('#deactivateMediaButton".$MediaTypeCount."".$MediaCount."').show();
						$('#activateMediaButton".$MediaTypeCount."".$MediaCount."').hide();
				});
			$('#deactivateMediaButton".$MediaTypeCount."".$MediaCount."').click
				(
					function()
					{
						$('#deactivateMediaButton".$MediaTypeCount."".$MediaCount."').hide();
						$('#activateMediaButton".$MediaTypeCount."".$MediaCount."').show();
				});
			</script>");
		}
		print("</div>"); // End mediaTypeInternal Div
		
		print("<script>"); // Begin Script to show/hide all internals based on which media button is pressed
		print("$('#mediaTypeButton$MediaTypeCount').click
				(
					function()
					{");
				
		$MediaJSCount = 0;
		foreach($MediaTypeList->getRecords() as $MediaTypeJS)
		{
			$MediaTypeJSName = $MediaTypeJS->value("TypeName");
			if($MediaJSCount == $MediaTypeCount)
			{
				$Visible = '.show()';
			}
			else
			{
				$Visible = '.hide()';
			}
			print("$('#mediaTypeInternal$MediaJSCount')".$Visible.";");
			$MediaJSCount++;
			
		}
		print("});");
		print("</script>"); // End Script to show/hide all internals based on which media button is pressed
		$MediaTypeCount++;
	}
	print("</div>"); // End adminManageMediaDIV
	
}

function buildApproveEvent()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Admin')
		{
			$UnapprovedList = getUnapprovedEvents();
			$UnapprovedEventCount = getUnapprovedEventsCount();
			print("<div id='adminApproveEvents'>");
			print("<input type='button' id='approveEventsButton' value='Approve Events ($UnapprovedEventCount)'>");
			print("<div id='adminapproveEventsInternal' class='Internal'>");
			$UnapprovedCount = 0;
			foreach($UnapprovedList->getRecords() as $UnapprovedEvent)
			{
				$Poster = $UnapprovedEvent->value("Alias");
				$Title = $UnapprovedEvent->value("Title");
				$Type = $UnapprovedEvent->value("Type");
				$Details = $UnapprovedEvent->value("Details");
				$PubDate = $UnapprovedEvent->value("PubDate");
				$EventID = $UnapprovedEvent->value("DateCreated");
				$HumanDate = date('jS, M, Y', $PubDate /1000);
				print("<div id='unapprovedEvent$UnapprovedCount'>");
				print("<span class='quickViewEventPoster'>Organizer: $Poster</span>");
				print("<span class='quickViewEventPubDate'>Event Start Time: $HumanDate</span>");
				print("<span class='quickViewEventType'>Event Type: $Type</span>");
				print("<span class='quickViewEventDetails'>Details:<br>
				<textarea id='quickViewEventDetailsText$UnapprovedCount' class='quickViewEventDetails' name='$EventID' disabled>$Details</textarea>'</span>");
				print("<input type='button' class='quickViewButton' value='Approve' onclick=\"approveEvent('$EventID','$alias','$UnapprovedCount')\"> ");
				print("<input type='button' class='quickViewButton' value='Delete' onclick=\"adminDeleteEvent('$EventID','$alias','$UnapprovedCount')\"> ");
				print("<div id='unapprovedEventMSG$UnapprovedCount' class='ERRMSG'></div>");
				print("</div>");
				$UnapprovedCount++;
			}
			print("</div>"); // End adminApprovEventsInternal
			print("</div>");
			print("<script>");
			print("$('#approveEventsButton').click
						(
							function()
							{
								$('#adminapproveEventsInternal').slideToggle();
						});");
			print("</script>");
		}
		else
		{
			
		}
	}
	else
	{
	}
}

function buildApproveNews()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	
		if($type == 'Admin')
		{
			$UnapprovedList = getUnapprovedNews();
			$UnapprovedNewsCount = getUnapprovedNewsCount();
			print("<div id='adminApproveNews'>");
			print("<input type='button' id='approveNewsButton' value='Approve News ($UnapprovedNewsCount)'>");
			print("<div id='adminapproveNewsInternal' class='Internal'>");
			$UnapprovedCount = 0;
			foreach($UnapprovedList->getRecords() as $UnapproveNews)
			{
				$Poster = $UnapproveNews->value("Alias");
				$Title = $UnapproveNews->value("Title");
				$Type = $UnapproveNews->value("Type");
				$Details = $UnapproveNews->value("Details");
				$PubDate = $UnapproveNews->value("PubDate");
				$NewsID = $UnapproveNews->value("DateCreated");
				$HumanDate = date('jS, M, Y', $PubDate /1000);
				print("<div id='unapprovedNews$UnapprovedCount'>");
				print("<span class='quickViewEventPoster'>Poster By: $Poster</span>");
				print("<span class='quickViewEventPubDate'>Publication Date: $HumanDate</span>");
				print("<span class='quickViewNewsType'>Audience: $Type</span>");
				print("<span class='quickViewEventDetails'>Details:<br>
				<textarea id='quickViewEventDetailsText$UnapprovedCount' class='quickViewEventDetails' name='$NewsID' disabled>$Details</textarea>'</span>");
				print("<input type='button' class='quickViewButton' value='Approve' onclick=\"approveNews('$NewsID','$alias','$UnapprovedCount')\"> ");
				print("<input type='button' class='quickViewButton' value='Delete' onclick=\"adminDeleteNews('$NewsID','$alias','$UnapprovedCount')\"> ");
				print("<div id='unapprovedNewsMSG$UnapprovedCount' class='ERRMSG'></div>");
				print("</div>");
				$UnapprovedCount++;
			}
			print("</div>"); // End adminApprovEventsInternal
			print("</div>");
			print("<script>");
			print("$('#approveNewsButton').click
						(
							function()
							{
								$('#adminapproveNewsInternal').slideToggle();
						});");
			print("</script>");
		}
		else
		{
			
		}
	}
	else
	{
	}
}

/*****************************************************************************

The following blocks are to call individual, socketable parts of code for the 
administrator control panel. It is designed this way to easily add/remove
different segments of the code as time goes on.

*****************************************************************************/

function buildAdminManageAds($Alias)
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Ads</h2></div>");
	buildAdminManagePendingAds($Alias);
	buildAdminManagePendingCampaigns($Alias);
	buildAdminManageRunningCampaigns($Alias);
	buildAdminManageFinishedCampaigns($Alias);
	print("</div>");
}

function buildAdminNewsPanel()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage News</h2></div>");
	buildWriteNews();
	buildApproveNews();
	buildEditNews();
	print("</div>");
}

function buildAdminManageUsers()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Users</h2></div>");
	buildAddUser();
	buildEditUser();			  	
	print("</div>");
}

function buildAdminManageWebcomics()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Webcomics</h2></div>");
	buildAddWebcomic();
	buildManageWebcomics();
	print("</div>");
}

function buildAdminManageEvents()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Events</h2></div>");
	buildAddEvent();
	buildApproveEvent();
	buildEditEvent();
	print("</div>");
}

function buildAdminManageThemes()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Themes</h2></div>");
	buildManageThemes();
	print("</div>");
}

function buildAdminManageGenres()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Genres</h2></div>");
	buildManageGenres();
	print("</div>");
}

function buildAdminManageSplash()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Splash</h2></div>");
	buildManageSplash();
	print("</div>");
}

function buildAdminManageMedia()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Media</h2></div>");
	buildAddMedia();
	buildManageMedia();
	print("</div>");
}

function buildAdminManageSocialMediaTypes()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Social Media Types</h2></div>");
	buildAddSocialMediaTypes();
	buildManageSocialMediaTypes();
	print("</div>");
}

function buildAdminManageMessages()
{
	print("<div id='AdminPanelWrap'>
	<div id='adminPanelHeader' class='panelHeader'><h2>Manage Messages</h2></div>");
	buildAdminSendMessage();
	buildAdminReviewMessages();
	
	print("</div>");
}

function buildAdminSendMessage()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			print("<div id='writeMessagePanel'>");
			print("<div id='writeMessagePanelHeader' class='dropLevel1'>Write An Email</div>");
			print("<div id='writeMessagePanelInternal' class='Internal'>");
			$UserList = getAllUsers();
			print("Recipient: <select id='messagerecipientSELECT'>");
			foreach($UserList->getRecords() as $Recipient)
			{
				$UserEmail = $Recipient->value("Email");
				$UserAlias = $Recipient->value("Alias");
				print("<option value='$UserEmail'>$UserAlias</option>");
			}
			print("</select>");
			print("<br>");
			print("Subject: <input type-='text' id='messageSubjectText'>");
			print("<br>");
			print("Message:");
			print("<br>");
			print("<textarea id='messageBodyText'></textarea>");
			print("<div id='sendMessageButtons'><input type='button' id='messageSendButton' value='Send' onclick=\"sendEmailAsNoReply('$alias')\"></div>");
			print("<div id='sendMessageMSG' class='errMSG'></div>");
			print("</div> <!-- end writeMessagePanelInternal -->");
			print("<script>
			$('#writeMessagePanelHeader').click
			(
				function()
				{
					$('#writeMessagePanelInternal').slideToggle();
			});
			</script>");
			print("</div> <!-- end writeMessagePanel -->");
		}
		else
		{
			print("You are not an administrator and are not allowed to be here.");
		}
	}
	else
	{
		print("You are not an administrator and are not allowed to be here.");
	}
}

function buildAdminReviewMessages()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
		
		if($type == 'Admin')
		{
			$UnreadMessageList = getUnreadMessages();
			$ReadMessageList = getReadMessages();
			print("<div id='unreadMessagesPanel'>");
			$unreadMessageCount = getUnreadMessageCount();
			print("<div id=unreadMessagePanelHeader' class='dropLevel1'>Unread Messages ($unreadMessageCount)</div>");
			if($unreadMessageCount > 0)
			{
				foreach($UnreadMessageList->getrecords() as $UnreadMessage)
				{
					$MessageID = $UnreadMessage->value("DateCreated");
					buildContactMessagePreview($MessageID,$alias);
				}
			}
			else
			{
				print("There are no unread messages at this time");
			}
			print("</div>"); // end unreadMessagePanel
			print("<div id='readMessagePanel'>");
			print("<div id='readMessagePanelHeader' class='dropLevel1'>Read Messages</div>");
			print("<div id='readMessageInternal' class='Internal'>");
			$ReadMessageCount = getReadMessageCount();
			if($ReadMessageCount > 0)
			{
				foreach($ReadMessageList->getRecords() as $ReadMessage)
				{
					$MessageID = $ReadMessage->value("DateCreated");
					buildContactMessagePreview($MessageID,$alias);
				}	
			}
			else
			{
				print("There are no read messages at this time");
			}
			print("</div>"); // end readMessageInternal
			print("</div>");
			print("</div>");
			print("<script>
			$('#readMessagePanelHeader').click
			(
				function()
				{
					$('#readMessageInternal').slideToggle();
			});
			</script>");
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
}

function buildContactMessagePreview($MessageID,$alias)
{
	$MessageRecord = getMessageDetails($MessageID);
	$Message = $MessageRecord->getRecord();
	$Name = $Message->value("Name");
	$Type = $Message->value("Type");
	$MessageText = $Message->value("Text");
	$Email = $Message->value("Email");
	$Status = $Message->value("Status");
	print("<div id='message$MessageID' class='$Status"."Message'>");
	print("<div class='contactMessageFrom'>From: <span class='messageName'>$Name</span></div>");
	print("<div class='contacMessageEmail'>Email: <span class='messageEmail'>$Email</span></div>");
	print("<div class='contactMessageType'>Type: <span class='messageType'>$Type</span></div>");
	print("<div class='contactMessageText'>Message: <br><span class='messageType'>$MessageText</span></div>");
	print("<div class='messageActions'>");
	if($Status == 'Unread')
	{
		print("<input type='button' id='markAsRead$MessageID' value='Mark As Read' onclick=\"markMessageRead('$MessageID','$alias');\"> ");
	}
	//print("<input type='button' id='deleteMessage$MessageID' value='Delete' onclick=\"deleteMessage('$MessageID','$alias');\"> ");
	print("</div>");
	print("<div id='messageMSG$MessageID' class='errMSG'></div>");
	print("</div>");
}

function buildSpecificEvent($EventID)
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$email = $_SESSION['Email'];
		$type = getUserType($alias);
	}
	else 
	{
		$type = "Subscriber";
	}
		
	$Event = getEventByID($EventID);
	$Status = $Event->value("Status");
	if($Status == 'Approved')
	{
		print("<div id='specificEvent' class='viewEvent'>");
		$PubDate =$Event->value("StartTime");
		$humanPubDate = date('M jS, Y @ h:i', $PubDate /1000);
		$Organizer = $Event->value("Alias");
		$ProfilePic = $Event->value("ProfilePic");
		$Details = $Event->value("Details");
		$Title = $Event->value("Title");
		$Category = $Event->value("Category");
		$Location = $Event->value("Location");
		$Type = $Event->value("Type");
		
		print("<h2 class='eventTitle Event$Type'>$Title</h2>
		<div class='EVdetails eventBox$Type'><div>
		<span class='eventProfilePic'><img src='$ProfilePic'></span>
		<span class='eventOrganizer'><strong>Organizer:</strong> $Organizer</span><br>
		<span class='eventType'><strong>Type:</strong> $Type</span><br>
		<span class='eventStartTime'><strong>Start Time:</strong> $humanPubDate</span><br>
		<span class='eventLocation'><strong>Location:</strong> $Location</span>
		<div class='clear'></div>
		</div></div>
		<span class='eventText'>$Details</span>
		");
		print("<span class='eventAttendees'>");
		$AttendeeList = getEventAttendees($EventID);
		
		if($type == 'Member' || $type == 'Admin')
		{
			$Attending = getWhetherMemberIsAttendingEvent($alias,$EventID);
			if($Attending)
			{
				print("<input type='button' class='eventButton' id='attendingButton$EventID' value='Cancel attendance'><br>");
			}
			else
			{
				print("<input type='button' class='eventButton' id='attendingButton$EventID' value='Attend'><br>");
			}
			print("<div id='eventMSG$EventID'></div>");
			print("<script>
		$('#attendingButton$EventID').click
					(
						function()
						{
							if(document.getElementById('attendingButton$EventID').value == 'Attend')
							{
								confirmAttendance('$EventID','$alias');
								document.getElementById('attendingButton$EventID').value = 'Cancel attendance';
							}
							else
							{
								document.getElementById('attendingButton$EventID').value = 'Attend';
								cancelAttendance('$EventID','$alias');
							}
					});
		</script>");
		}
		$AttendeeList = getEventAttendees($EventID);
		if(count($AttendeeList) > 0)
		{
			print("Currently attending: ");
			foreach($AttendeeList->getRecords() as $Attendee)
			{	
				$AttendeeAlias = $Attendee->value('Alias');
				print("<a href='https://www.comicadia.com/members.php?Alias=$AttendeeAlias'>$AttendeeAlias</a> ");
			}
		}
		print("</span>");
		print("<h3><i class='fa fa-comments'></i>  Comments </h3><div id='disqus_thread'></div>");
		print("<script>
			var disqus_config = function () 
			{
				this.page.identifier = 'ComicadiaEvent-".$EventID."';
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
	else
	{
		print("This Event is not available at this time");
	}
}

function buildSpecificNews($NewsID)
{
	if(doesNewExistByID($NewsID))
	{
		$News = getNewsByID($NewsID);
		$Status = $News->value("Status");
		if($Status == 'Approved')
		{
			print("<div id='specificNews' class='viewNews'>");
			$PubDate =$News->value("PubDate");
			$humanPubDate = date('M jS, Y', $PubDate /1000);
			$Poster = $News->value("Alias");
			$ProfilePic = $News->value("ProfilePic");
			$Details = $News->value("Details");
			$Title = $News->value("Title");
			$Category = $News->value("Category");
			
			print("<h1 class='newsTitle'><i class='fa fa-pagelines'></i> $Title</h1>
			<span class='newsText'>$Details</span>
			<div id='infoNews' class='Newsinfo'>
			<span class='newsProfilePic'><img src='$ProfilePic'></span>
			<span class='newsPoster'><strong><i class='fa fa-user-circle'></i> Posted by:</strong> $Poster</span> | 
			<span class='newsPubDate'><strong><i class='fa fa-calendar'></i> Date Published:</strong> $humanPubDate</span>
			</div>
			");
			
			print("<h3><i class='fa fa-comments'></i> Comments </h3> <div id='disqus_thread'></div>");
			print("<script>
				var disqus_config = function () 
				{
					this.page.identifier = 'ComicadiaNews-".$NewsID."';
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
		else
		{
			print("This News post is not available at this time");
		}
	}
	else
	{
		print("There is no news article with the id: $NewsID");
	}
}

/*
function buildComment($DateCreated,$ParentCommentCount, $Layer)
{
	$ParentComment = getCommentDetails($DateCreated);
	$ParentAlias = $ParentComment->value("Alias");
	$ParentAvatar = $ParentComment->value("ProfilePic");
	$ParentText = $ParentComment->value("Text");
	$HumanDate = $DateCreated;
	
	print("<div class='comment'>");
	print("<span class='CommentHeader'><span class='profilePic'><img src='$ParentAvatar' /></span><span class='commentAlias'>$Alias</span><span class='commentPostDate'>$HumanDate</span></span>");
	print("<span class'commentText'>$ParentText</span>");
	$ChildrenCommentList = getCommentReplies($DateCreated);
	$ChildCount = 0;
	foreach($ChildrenCommentList->getRecords() as $ChildComment)
	{
		$ChildDateCreated = $ChildComment->getValue("DateCreated");
		$LayerCount = $Layer + 1;
		buildComment($ChildDateCreated, $ChildCount, $LayerCount);
		$ChildCount++;
	}
	/*
	Build reply if person is member
	
	print("</div>");
}
*/


function buildMemberProfile($Alias)
{
	$Member = getUserDetails($Alias);
	$Alias = $Member->value("Alias");
	$ProfilePic = $Member->value("Pic");
	$UserType = $Member->value("Type");
	print("<div id='specificMember'>");
	print("<div id='specificMemberBio'>");
	print("<h2 class='specificMemberAlias'>$Alias</h2>");
	print("<span class='specificMemberAvatar'><img src='$ProfilePic' /></span>");
	print("<span class='specificMemberType'><i class='fa fa-user-circle'></i> <strong>$UserType</strong></span>");
	print("</div>"); // End Specific Member Bio
	print("<div id='specificMemberComics'");
	print("<div id='socialMediaLinks'>");
	print("<h3><i class='fa fa-share' aria-hidden='true'></i> Social Media</h3>");
	$SocialMediaList = getAllSocialMediaTypes();
	foreach($SocialMediaList->getRecords() as $SocMedia)
	{
		$Class = $SocMedia->value("Class");
		$SocName = $SocMedia->value("Name");
		$BGColor = $SocMedia->value("BGColor");
		$YourSocMediaURL = getSpecificSocialMediaURLByName($SocName,$Alias);
		if($YourSocMediaURL)
		{
			print("<span class='memberSocialMedia'><a href='$YourSocMediaURL' target='_blank'><span class='memberProfileSocialMediaIcon' style='background-color: $BGColor;'><i class='$Class'></i></span></a></span>");	
		}
		else
		{
			$YourSocMediaURL = '';
		}
	}
	print("</div>"); //End Specific Bio
	$ComicList = getUsersWebcomicNames($Alias);
	if($ComicList)
	{
		$memberComicCount = 0;
		print("<div id='specificMemberComicList'>");
		print("<h3><i class='fa fa-comment-o' aria-hidden='true'></i> Comics</h3>");
		foreach($ComicList->getRecords() as $Comic)
		{
			print("<div id='specificMemberComic$memberComicCount' class='specificMemberComicBlurb'>");
			$ComicID = $Comic->value("ComicID");
			$ComicMembership = $Comic->value("Membership");
			$ComicURL = $Comic->value("URL");
			$ComicName = $Comic->value("Name");
			$UserRoleList = getCrewRoles($Alias, $ComicID);
			$RoleList = $UserRoleList->getRecord();
			$RoleList = $RoleList->value("Role");
			$RoleString = '';
			if(is_array($RoleList))
			{
				foreach($RoleList as $Role)
				{
					$RoleString = $RoleString .$Role.", ";
				}
				$RoleString = rtrim(trim($RoleString),', ');
			}
			else
			{
				$RoleString = $RoleList;
			}
			
			if($ComicMembership == 'Comicadia')
			{
				print("<div class='comicadiaComic'>");
				$ImgURL = getComicHorizontalURL($ComicID);
				if($ImgURL)
				{
					print("<a href='$ComicURL'><span class='comicadiaHorizontalBanner'><img src='$ImgURL' /></span></a>");
				}
				else
				{
					print("<h5 class='comicName'><a href='$ComicURL'>$ComicName</a></h5>");
				}
				print("<span class='specificMemberRoles'><strong>Roles:</strong> $RoleString</span>");
				print("</div>");
			}
			else
			{
				print("<div class='specifiMemberSpecificComic'>");
				print("<a href='$ComicURL'><h5 class='specificMemberSpecificComicTitle'>$ComicName</h5></a>");
				print("<span class='specificMemberRoles'><strong>Roles:</strong> $RoleString</span>");
				print("</div>");
			}
			print("</div>");
			$memberComicCount++;
		}
		print("</div>");
	}	
	else
	{
		print("$Alias is not currently working on any webcomics known to Comicadia");
	}
	print("</div>"); // End Specific Member
	
}

function buildStaffPage()
{
	print("<div id='staffPage");
	$StaffList = getComicadiaStaff();
	print("<span id='staffTitle'>Comicadia Staff</span>");
	$StaffCount = 1;
	foreach($StaffList->getRecords() as $Staff)
	{
		if($Staff % 2 == 0)
		{
			$DivOrder = 'Even';
		}
		else
		{
			$DivOrder = 'Odd';
		}
		$StaffName = $Staff->value("FullName");
		$StaffAlias = $Staff->value("Alias");
		print("<div class='staffMember$DivOrder'>");
		print("<span class='staffPic'></span>");
		print("<span class='staffName'></span>");
		print("<span class='staffPosition'></span>");
		print("<span class='staffBlurb'></span>");
		print("<span class='staffExperience'></span>");
		print("<span class='staffSocialMedia'>");
		
		print("</span>");
		print("</div>");
	}
	print("</div>");
}

function buildSidebar()
{
	print("<div id='Sidebar'>
		<div id='CarouselHolder'>");
	buildCarousel();
	print("</div><!-- End carousel holder -->");
	print("<div id='comicAdSquare'>");
	print("<script type=\"text/javascript\" src=\"https://www.comicad.net/r/6c9Ka06Ga2/\"></script>");
	print("</div><!-- End comicAdSquare -->");
	print("<div class='clear'></div>");
	print("<div style='margin:0 auto';><img src='https://www.comicadia.com/media/herald_logo.png' /></div>");
		//print("<h2><i class='fa fa-newspaper-o' aria-hidden='true'></i> News</h2>");
		buildFrontPageHeraldNews();
	print("<div id='Eventlista' class='eventlist'>");
	print("<div id='comicAdTower' style=\"width: 160px;height: 600px;float: left; margin-top: 20px\">");
	print("<script type=\"text/javascript\" src=\"https://www.comicad.net/r/D9L5GDdAgA/\" ></script>");
	print("</div><!-- end comicAdTower -->");
	//	<h2><i class='fa fa-calendar' aria-hidden='true'></i> Events </h2>");
	//buildFrontPageEvents();
	print("</div><!-- End Enventlista -->");
	//Sidebar tower ad
	
	print("<div id='googleTower' class='adTower'>");
	loadGoogleTower();
	print("	</div><!-- end googleTower -->");
	
		print("</div>");
		print("</div>");
}

function loadGoogleAds()
{	
print("<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-113653645-1\"</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-113653645-1');
</script>");

}

function loadGoogleTower()
{
	print("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- Comicadia Sky -->
<ins class=\"adsbygoogle\"
     style=\"display:inline-block;width:160px;height:600px\"
     data-ad-client=\"ca-pub-6158727735075887\"
     data-ad-slot=\"4104466394\"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>");
}

function loadGoogleLeader()
{
	print("<div id='googleLeaderWrap'>");
	print("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- Comicadia Leader -->
<ins class=\"adsbygoogle\"
     style=\"display:inline-block;width:728px;height:90px\"
     data-ad-client=\"ca-pub-6158727735075887\"
     data-ad-slot=\"8151522613\"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>");	
print("</div>");
}


function loadGoogleSquare()
{
	print("<div id='googleSquareWrap'>");
	print("<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
<!-- Comicadia Square -->
<ins class=\"adsbygoogle\"
     style=\"display:inline-block;width:300px;height:250px\"
     data-ad-client=\"ca-pub-6158727735075887\"
     data-ad-slot=\"1911092489\"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>");
print("</div>");
}

function buildFooter()
{
	print("<div id='googleLeaderWrap'>");
	loadGoogleLeader();
	print("</div>");
	print("	<div id='Footer'><div class='footertop'>");
	print("<div class='footergrid'>
	<h2>Promotional Consideration</h2>
	<div id='googleSquare' class='googleSquare'>");
	loadGoogleSquare();
	print("</div>");
	print("</div>");
	print("<div class='footergrid'>
	<h2>Cadence Tweets</h2>
	");
	buildCadenceTweets();
	print("</div>");
	print("<div class='footergrid'>
	<h2>Support</h2>
	...</div>");
	// print("<div class='footergrid'>
	// <h2>Cadence's Corner</h2>");
	// buildCadenceWidget();
	// print("</div>");
	print("<div class='clear'></div>
	</div></div>
	<div class='copyright'> Comicadia ©2017- <?php echo date('Y'); ?> Cadence the faun, the Comicadia Logo and the the Comicadia Pan Flute icon are all © of Comicadia. All other images, characters and icons are © of their respective owners and are used with their express permission for this site. · Site Design by <a href='http://monicang.com/' target='_blank'>MonicaNG</a><br>");
	print("</div>");
	
	print("</div>");
}

function buildCadenceWidget()
{
	$CadenceImg = getRandomCadenceWidget();
	print("<img src='$CadenceImg' />");
}

function buildContactForm()
{
	print("<div id='contactForm'>");
	print("<form>");
	print("<fieldsets>");
	print("Message type:");
	print("<select id='contactTypeSELECT'>");
	print("<option value='General'>General</option>");
	print("<option value='Advertising'>Advertising</option>");
	print("<option value='Work Opportunity'>Work Opportunity</option>");
	print("</select>");
	print("<br>");
	print("Name: <input type='text' id='contactName'>");
	print("<br>");
	print("E-mail Address: <input type='email' id='contactEmail'>");
	print("<br>");
	print("Message: <br>");
	print("<textarea id='contactMessage'></textarea>");
	print("<input type='button' id='contactSubmit' value='Send' onclick=\"sendContactMessage();\">");
	print("</fieldsets>");
	print("</form>");
	print("<div id='contactMSG' class='errMSG'></div>");
	print("</div>");
}

function buildAddSocialMediaTypes()
{
	print("<div id='addSocialMediaHeader' class='dropLevel0'>Add Social Media</div>");
	print("<div id='addSocialMediaInternal' class='Internal'>");
	print("Name: <input type='text' id='addSocialMediaName'>");
	print(" Background Color: <input type='text' id='addSocialMediaColor'>");
	print(" Icon class: <input type='text' id='addSocialMediaIcon'>");
	print("<div id='saveNewSocMedia'><input type='button' onclick='saveNewSocMedia();' value='Add Social Media'></div>");
	print("<div id='addSocMediaMSG' class='ErrMSG'></div>");
	print("</div>");
	print("<script>");
	print("$('#addSocialMediaHeader').click
			(
				function()
				{
					$('#addSocialMediaInternal').slideToggle();
			});");
	print("</script>");
}

function buildManageSocialMediaTypes()
{
	print("<div id='manageSocialMediaHeader' class='dropLevel0'>Manage Social Media</div> ");
	print("<div id='adminManageSocialMediaInternal' class='Internal'>");
	$SocialMediaList = getAllSocialMediaTypes();
	$SocMediaTypeCount = 0;
	foreach($SocialMediaList->getRecords() as $SocMedia)
	{
		$Class = $SocMedia->value("Class");
		$SocName = $SocMedia->value("Name");
		$BGColor = $SocMedia->value("BGColor");
		print("<div class='adminModifySocialMedia' id='ModSocMedia$SocMediaTypeCount'>");
		print("<h3>$SocName</h3>");
		print("Icon class: <input type='text' name='$SocName' id='adminModifySocIcon$SocMediaTypeCount' value='$Class'> ");
		print("Background Colour: <input type='text' id='adminModifySocColor$SocMediaTypeCount' value='$BGColor'>");
		print("<div id='adminControlPanel'><input type='button' id='adminUpdateSocialMedia' value='Save Changes' onclick=\"adminUpdateSocialMedia('$SocMediaTypeCount');\">");
		print("<input type='button' id='adminRemoveSocialMedia' value='Delete' onclick=\"adminDeleteSocialMedia($SocMediaTypeCount);\">");
		print("</div>");
		print("<div id='adminSocialMediaMSG$SocMediaTypeCount' class='errMSG'></div>");
		print("</div>");
		$SocMediaTypeCount++;
	}
	print("</div>"); //end adminManageSocialMediaInternal
	print("<script>");
	print("$('#manageSocialMediaHeader').click
			(
				function()
				{
					$('#adminManageSocialMediaInternal').slideToggle();
			});");
	print("</script>");
}

function buildCadenceTweets()
{
	print("<a class=\"twitter-timeline\" data-width=\"100%\" data-height=\"260\" data-theme=\"light\" href=\"https://twitter.com/comicadiatweets?ref_src=twsrc%5Etfw\">Tweets by comicadiatweets</a> 
	<script async src=\"https://platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>");
}

function buildAdminManagePendingAds($Alias)
{
	$PendingCount = getCountOfAllAdsByStatus(["Pending"]);
	print("<div id='managePendingAds' class='dropLevel0'>Manage Ads awaiting review ($PendingCount)</div> ");
	print("<div id='adminManagePendingAdsInternal' class='Internal'>");
	if($PendingCount > 0)
	{
		$PendingAdList = getAllAdsByStatus(['Pending']);
		foreach($PendingAdList->getRecords() as $Ad)
		{
			$AdID = $Ad->value("AdID");
			$EntityID = $Ad->value("EntityID");
			$AdName = $Ad->value("AdName");
			$DateCreated = $Ad->value("DateCreated");
			$Status = $Ad->value("Status");
			$MediaList = $Ad->value("Media");
			$AdType = $Ad->value("AdType");
			$Owner = $Ad->value("Alias");
			$AdLink = $Ad->value("AdLink");
			$ComicID = $Ad->value("ComicID");
			$EntityID = $Ad->value("EntityID");
			
			if($ComicID != '')
			{
				$EntityID = $ComicID;
			}
			
			$EntityName = getNameForEntityByID($EntityID);
			
			$DateCreated = date('jS, M, Y', $DateCreated /1000);
			
			print("<div id='managePendingAd$AdID' class='pendingAd'>");
			print("<strong>Ad Name:</strong> $AdName<br>");
			print("<strong>Date Created:</strong> $DateCreated<br>");
			print("<strong>Ad type:</strong> $AdType<br>");
			print("<strong>Ad is for:</strong> $EntityName<br>");
			print("<strong>Ad Links to:</strong> <a href='$AdLink'>$AdLink</a><br>");
			print("<strong>Ad using:</strong><br>");
			print("<div class='previewMediaListForAds'>");
			foreach($MediaList as $MediaURL)
			{
				print("<a href='$MediaURL' target='_blank'><img src='$MediaURL' class='previewMedia$AdType' /></a>");
			}
			print("</div> <!-- end previewMediaListForAds -->");
			print("<div class='adminControlButtons'>");
			print("<input type='button' id='setAdToApproved$AdID' class='approveAdButton' value='Approve Ad' onclick=\"setAdStatus('$AdID', 'Approved', '$Alias');\" >");
			print("<input type='button' id='setAdToRejected$AdID' class=rejectbutton' value='Reject Ad' onclick=\"setAdStatus('$AdID', 'Rejected', '$Alias');\">");
			print("</div><!-- end adminControlButtons' -->");
			print("<div id='managePendingAdMSG$AdID' class='errMSG'> </div><!-- end managePendingAdMSG$AdID -->");
			print("</div><!-- end managePendingAd$AdID -->");
		}
	}
	else
	{
		print("No Ads are pending review at this time");
	}
	
	print("</div><!-- end adminManagePendingAdsInternal -->");
	print("</div><!-- end managePendingAds -->");
	
	print("<script>");
	print("$('#managePendingAds').click
			(
				function()
				{
					$('#adminManagePendingAdsInternal').slideToggle();
			});");
	print("</script>");
}

function buildAdminManagePendingCampaigns($Alias)
{
	print("<div id='managePendingCampaigns' class='dropLevel0'>Manage Campaigns Pending Review</div> ");
	print("<div id='adminManagePendingCampaignsInternal' class='Internal'>");
	if(getAllPendingCampaignsCount() > 0)
	{
		$PendingCampaignList = getAllPendingCampaigns();
		foreach($PendingCampaignList->getRecords() as $Campaign)
		{
			$AdID = $Campaign->value("AdID");
			$AdStatus = $Campaign->value("Status");
			$DateSubmitted = $Campaign->value("DateCreated");
			$DateSubmitted = date('F jS, Y', $DateSubmitted/1000);
			$DateRequested = $Campaign->value("RequestedDate");
			$DateRequested = date('F jS, Y', $DateRequested/1000);
			$AdName = $Campaign->value("AdName");
			$AdType = $Campaign->value("AdType");
			$CampaignName = $Campaign->value("CampaignName");
			$Cost = $Campaign->value("Cost");
			$Views = $Campaign->value("Views");
			$Requester = $Campaign->value("Alias");
			$URLs = $Campaign->value("URLs");
			print("<div id='pendingCampaign$AdID' class='adminPendingCampaign'>");
			print("<strong>Requested by:</strong> $Requester<br>");
			print("<strong>Ad Name: </strong>$AdName<br>");
			print("<strong>Date Submitted: </strong>$DateSubmitted<br>");
			print("<strong>Requested Start Date:</strong> $DateRequested<br>");
			print("<strong>Ad Type:</strong> $AdType<br>");
			print("<strong>Campaign Requested: </strong>$CampaignName<br>");
			print("<strong>Value:</strong> ".number_format($Views)." views for $$Cost<br>");
			print("<input type='button' value='View media for this ad' id='viewMediaForAdClickable$AdID'>");
			print("<div id='viewMediaForAdInternal$AdID' class='Internal'");
			foreach($URLs as $URL)
			{
				print("<div class='mediaPreview'><a href='$URL' target='_blank'><img src='$URL' class='smallAdminPreview' /></a></div><!-- end mediaPreview -->");
			}
			print("</div><!-- end viewMediaForAdInternal$AdID'>");
			print("</div><!-- end pendingCampaign$AdID -->");
			print("<script>");
			print("$('#viewMediaForAdClickable$AdID').click
			(
				function()
				{
					$('#viewMediaForAdInternal$AdID').slideToggle();
			});");
			print("</script>");
			
		}
	}
	else
	{
		print("There are no Campaigns awaiting any administration");
	}
	print("</div><!-- end adminManagePendingCampaignsInternal -->");
	print("<script>");
	print("$('#managePendingCampaigns').click
			(
				function()
				{
					$('#adminManagePendingCampaignsInternal').slideToggle();
			});");
	print("</script>");
}

function buildAdminManageRunningCampaigns($Alias)
{
	print("<div id='manageRunningCampaigns' class='dropLevel0'>Manage Running Campaigns </div> ");
	print("<div id='adminManageRunningCampaignsInternal' class='Internal'>");
	
	print("</div><!-- end adminManageRunningCampaignsInternal -->");
	print("</div><!-- end manageRunningCampaigns -->");
	print("<script>");
	print("$('#manageRunningCampaigns').click
			(
				function()
				{
					$('#adminManageRunningCampaignsInternal').slideToggle();
			});");
	print("</script");
}

function buildAdminManageFinishedCampaigns($Alias)
{
	print("<div id='manageFinishedCampaigns' class='dropLevel0'>Manage Pending Ads </div> ");
	print("<div id='adminManageFinishedCampaignsInternal' class='Internal'>");
	
	print("</div><!-- end adminManageFinishedCampaignsInternal -->");
	print("</div><!-- end manageFinishedCampaigns -->");
	print("<script>");
	print("$('#manageFinishedCampaigns').click
			(
				function()
				{
					$('#adminManageFinishedCampaignsInternal').slideToggle();
			});");
	print("</script");
}


function buildComicadiaNewsDesc($NewsID)
{
	$Content = getNewsBlurb($NewsID);
	$CleanPreview = strip_tags($Content);
	if(strlen($CleanPreview) > 300)
	{
		$Preview = substr($CleanPreview,0,298).'...';
	}
	else
	{
		$Preview = $CleanPreview;
	}
	
	print("<meta name='description' content=\"$Preview\">");
}
?>