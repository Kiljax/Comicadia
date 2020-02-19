<?php

include 'php/GUI.php';
include 'php/testGUI.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
<link href="style.css" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Loading basic jquery -->
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>

<script type="text/javascript" src="./simpleTimePicker/jquery.simple-dtpicker.js"></script>
<link type="text/css" href="./simpleTimePicker/jquery.simple-dtpicker.css" rel="stylesheet" />

<script type="text/javascript">

function updateAvailableAds()
{
	AdType = document.getElementById("adTypeSelect").value;
	EntityID = document.getElementById("useCurrentEntitySelect").value;
	xmlhttp = getxml();
	fd = new FormData();
	fd.append("AdType",AdType);
	fd.append("ID",EntityID);
	xmlhttp.onreadystatechange = function()
	{
		document.getElementById("currentlyAvailableMedia").innerHTML = xmlhttp.responseText;
	}
	xmlhttp.open("POST", "./php/TestActions.php?F=reloadAvailableMediaForType", true);
	xmlhttp.send(fd);
}

function updateCurrentAds(Alias)
{
	fd = new FormData();
	fd.append("Alias", Alias);
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		document.getElementById("viewCurrentAdsInternal").innerHTML = xmlhttp.responseText;
	}
	xmlhttp.open("POST", "./php/TestActions.php?F=reloadAdsForAlias", true);
	xmlhttp.send(fd);
}

function uploadMediaFromLocal(Alias)
{
	var filename= document.getElementById('addMediaFromLocalFile').value;
	var AdType = document.getElementById("adTypeSelect").value;
	var EntityID = document.getElementById("useCurrentEntitySelect").value;
	
	var fd = new FormData();
	
	if(filename)
	{
		var file = document.getElementById('addMediaFromLocalFile').files[0];
		fd.append("EntityID", EntityID);
		fd.append("uploadedFile", file);
		fd.append("Alias", Alias);
		fd.append("AdType",AdType);
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
				document.getElementById("uploadMediaMSG").innerHTML= 'Progress: ' +done + ' / ' + total + (Math.floor(done/total*1000)/10) + '%';
			};
		}
		xmlhttp.onreadystatechange = function(e) 
		{
			if ( 4 == this.readyState ) 
			{
				console.log(['xhr upload complete', e]);
				document.getElementById("uploadMediaMSG").innerHTML='Image upload successful';
			}
		};
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
				var response = xmlhttp.responseText;
				if(response == 'Success')
				{
					
					document.getElementById("uploadMediaMSG").innerHTML="Success";
					document.getElementById("addMediaFromLocalFile").value="";
					updateAvailableAds();
				}
				else
				{
					document.getElementById("uploadMediaMSG").innerHTML=xmlhttp.responseText;
				}
		}
		xmlhttp.open("POST", "php/TestActions.php?F=uploadMediaFromLocalForAd", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("uploadMediaMSG").innerHTML="No file was selected.";
	}
}

function uploadMediaFromWeb(Alias)
{
	var URL = document.getElementById("addMediaFromWebText").value;
	var AdType = document.getElementById("adTypeSelect").value;
	var EntityID = document.getElementById("useCurrentEntitySelect").value;
	
	xmlhttp = getxml();
	if(URL)
	{
		var fd = new FormData();
		fd.append("URL", URL);
		fd.append("Alias", Alias);
		fd.append("EntityID", EntityID);
		fd.append("AdType", AdType);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("uploadMediaMSG").innerHTML=xmlhttp.responseText;
				updateAvailableAds();
			}			
		}
		xmlhttp.open("POST", "php/TestActions.php?F=uploadMediaFromWebForAdd", true);
		xmlhttp.send(fd);
	}
	else
	{
		document.getElementById("uploadMediaMSG").innerHTML='Please input a URL';
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

function createAdCampaign(Alias)
{
	StartDate = document.getElementById("createAdCampaignStartDateText").value;
	if(startDate == '')
	{
		document.getElementById("createNewAdMSG").innerHTML = 'You must choose a date you wish your ad campaign to start on.';
	}
}

function createNewAd(Alias)
{
	var AdName = document.getElementById("adNameText").value;
	var AdType = document.getElementById("adTypeSelect").value;
	var EntityID = document.getElementById("useCurrentEntitySelect").value;
	var mediaArray = getCheckedBoxes("potentialMediaCheckboxes");
	var AdLink = document.getElementById("createAdLinkText").value;
	
	if(mediaArray.length == 0)
	{
		document.getElementById("createNewAdMSG").innerHTML = 'You must have at least one image selected for an ad';
	}
	else
	{
		var mediaString = '';	
		for(i = 0; i< mediaArray.length;++i)
		{
			mediaString = mediaString + mediaArray[i] + ",";
		}
		
		fd = new FormData();
		fd.append("AdName", AdName);
		fd.append("AdType", AdType);
		fd.append("EntityID", EntityID);
		fd.append("Alias",Alias);
		fd.append("URLs", mediaString);
		fd.append("AdLink",AdLink);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				document.getElementById("createNewAdMSG").innerHTML=xmlhttp.responseText;
			}			
		}
		xmlhttp.open("POST", "php/TestActions.php?F=createNewAd", true);
		xmlhttp.send(fd);
	}
}

function deleteAd(AdID, Alias)
{
	var confirmDelete = confirm("If you delete this ad, you will not be able to recover any stats associated with it. Are you sure you wish to delete this ad?");
	if(confirmDelete)
	{
		xmlhttp = getxml();
		fd = new FormData();
		fd.append("AdID",AdID);
		
		xmlhttp.onreadystatechange = function()
		{
			if(xmlhttp.readyState == XMLHttpRequest.DONE)
			{
				updateCurrentAds(Alias);
			}			
		}
		xmlhttp.open("POST", "php/TestActions.php?F=deleteAd", true);
		xmlhttp.send(fd);
	}
}

function submitCampaignForReview(Alias)
{
	var CampaignID = document.getElementById("campaignTypeSelection").value;	
	var StartDate = document.getElementById("userCurrentEntityStartDate").value;
	var AdID = document.getElementById("chooseAdBlockForCampaignSelect").value;
	if(StartDate == '')
	{
		document.getElementById("submitCampaignForReviewMSG").innerHTML = 'You must choose a date you wish your ad campaign to start on.';
	}
	else
	{
		
		var cmd = "_xclick";
		var no_note = "1";
		var currency_code = "USD";
		var lc = "US"
		var bn = "PP-BuyNowBF:btn_buynow_LG.gif:NonHostedGuest";
		var url = 'https://www.comicadia.com/php/payments.php';
		var form = $('<form action="' + url + '" method="post">' +
		'<input type="hidden" name="CampaignID" value="' + CampaignID + '" />' +
		'<input type="hidden" name="Alias" value="' + Alias + '" />' +
		'<input type="hidden" name="StartDate" value="' + StartDate + '" />' +
		'<input type="hidden" name="AdID" value="' + AdID + '" />' +
		'<input type="hidden" name="Function" value="purchaseAdCampaign" />' +
		'</form>');
		$('body').append(form);
		form.submit();
	
	}
}

function getUserFirstNameByAlias(Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=getUserFirstNameByAlias", true);
	xmlhttp.send(fd);
}

function getUserLastNameByAlias(Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=getUserLastNameByAlias", true);
	xmlhttp.send(fd);
}

function getUserEmailByAlias(Alias)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=getUserEmailByAlias", true);
	xmlhttp.send(fd);
}

function getCampaignNameByID(CampaignID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("CampaignID",CampaignID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=getCampaignNameByID", true);
	xmlhttp.send(fd);
}

function getCampaignCostByID(CampaignID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("CampaignID",CampaignID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=getCampaignCostByID", true);
	xmlhttp.send(fd);
}

function checkIfAdIsAlreadyRunning(AdID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("AdID",AdID);
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			return xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=checkifAdIsCurrentlyRunningACampaign", true);
	xmlhttp.send(fd);
}

function testCreation()
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("testingstuff").innerHTML = xmlhttp.responseText;
		}			
	}
	xmlhttp.open("POST", "php/TestActions.php?F=testFinalStep", true);
	xmlhttp.send(fd);
}

function pauseBidding(Alias,AdSpaceID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("AdSpaceID",AdSpaceID);
		fd.append("F","PauseBid");
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			if(xmlhttp.responseText == 'Success')
			{
				
				document.getElementById("action-response-"+AdSpaceID).innerHTML = "Bid has been paused.";
				$('#pauseBid'+AdSpaceID).hide();
				$('#resumeBid'+AdSpaceID).show();
				document.getElementById("status"+AdSpaceID+"Hidden").value = "Paused";
			}
			else
			{
				document.getElementById("action-response-"+AdSpaceID).innerHTML = xmlhttp.responseText;
			}
		}		
		
	}
	xmlhttp.open("POST", "php/TestActions.php", true);
	xmlhttp.send(fd);
}

function resumeBidding(Alias,AdSpaceID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("AdSpaceID",AdSpaceID);
		fd.append("F","ResumeBid");
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			if(xmlhttp.responseText == 'Success')
			{
				
				document.getElementById("action-response-"+AdSpaceID).innerHTML = "Bid has been resumed.";
				$('#pauseBid'+AdSpaceID).show();
				$('#resumeBid'+AdSpaceID).hide();
				document.getElementById("status"+AdSpaceID+"Hidden").value = "Active";
			}
			else
			{
				document.getElementById("action-response-"+AdSpaceID).innerHTML = xmlhttp.responseText;
			}
		}		
		
	}
	xmlhttp.open("POST", "php/TestActions.php", true);
	xmlhttp.send(fd);
}

function submitNewCurrentBid(Alias,AdSpaceID)
{
	xmlhttp = getxml();
	MaxBid = document.getElementById("maximumBid"+AdSpaceID+"Text").value;
	NewBid = document.getElementById("currentBid"+AdSpaceID+"Text").value;
	Status = document.getElementById("status"+AdSpaceID+"Hidden").value;
	AdID = document.getElementById("advertisement-Select-"+AdSpaceID).value;
	
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("Alias",Alias);
		fd.append("AdSpaceID",AdSpaceID);
		fd.append("Bid",NewBid);
		fd.append("MaxBid",MaxBid);
		fd.append("Status",Status);
		fd.append("AdID",AdID);
		fd.append("F","submitNewCurrentBid");
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			response = xmlhttp.responseText;
			response = response.replace(/[\n\r]+/g, '');
			if(response == 'Reload')
			{
				rebuildAdFrame(Alias, AdSpaceID);
			}
			else
			{
				document.getElementById("action-response-"+AdSpaceID).innerHTML = response;
			}
		}		
		
	}
	xmlhttp.open("POST", "php/TestActions.php", true);
	xmlhttp.send(fd);
}

function refreshCurrentBidDetails(Alias,AdSpaceID)
{
	 rebuildAdFrame(Alias, AdSpaceID);
}

function rebuildAdFrame(Alias, AdSpaceID)
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd2 = new FormData();
		fd2.append("Alias",Alias);
		fd2.append("AdSpaceID",AdSpaceID);
		fd2.append("F","rebuildAdFrame");
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("bidDetails"+AdSpaceID).innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/TestActions.php", true);
	xmlhttp.send(fd2);
}

function testCalculate()
{
	xmlhttp = getxml();
	xmlhttp.onreadystatechange = function()
	{
		fd = new FormData();
		fd.append("AdSpaceID", "1549656075467");
		fd.append("F","calculateCurrentWinnerOfAdSpace");
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("testresponse").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/TestActions.php", true);
	xmlhttp.send(fd);
}


</script>
<meta description="Want to advertise on Comicadia? You've come to the right place.">
</head>
<title>Comicadia - Advertise with us</title>
<body>
<div id="AdsPanel">
	<div id ="topBar"><div id='home' onclick='goHome()'>Comicadia</div>
	  <?php
	  loadLogin();
	  ?>
	 </div>
	 <div id="AdminWrap">
		<div id="leftPanel">
		<?php	
		buildAdsPanel();
		?>
		</div>
		<div id="contentWrap">
			<div id='contentPanel'>
			<?php
			if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
			{	
				$alias = $_SESSION['Alias'];
							
				if (isset($_GET['submit']))
				{
					$call = $_GET['submit'];
					if($call == 'Manage Your Ads')
					{
						buildManageYourComicadiaAds($alias);
					}
					elseif($call == 'View Your Stats')
					{
					}
					elseif($call == 'Contact Comicadia')
					{
					}
					elseif($call == 'Manage Your Entities')
					{
						buildManageYourComicadiaEntities($alias);
					}
					elseif($call == 'Manage Your Campaigns')
					{
						buildManageYourComicadiaCampaigns($alias);
					}
					elseif($call == 'Manage Your Bids')
					{
						
						$articlesPerPage = 25;
						$totalAvailableComics = getCountOfComicsWithBiddingEnabled();
						$totalPages = ceil($totalAvailableComics / $articlesPerPage);
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
						else if($pageNumber > $totalPages)
						{
							$pageNumber = $totalPages;
						}
						$startArticle = ($pageNumber - 1) * $articlesPerPage;
						if(checkIfAnyComicsAvailableThatTheUserIsNotAMemberOf($alias))
						{
						buildComicsUserHasNoBidsOnPanel($alias, $startArticle, $articlesPerPage);
						}
						else
						{
							print("No comics available to advertise on at this time");
						}
					}
					else
					{
						buildAdvertiseWithUsWelcome($alias);
					}
				}
				else
				{
					buildAdvertiseWithUsWelcome($alias);
				}
			}
			else
			{
				buildAdvertiseWithUsWelcome('');
			}
			?>
			<div class="clear"></div>
		</div> <!-- End contentPanel -->
		</div> <!-- end contentWrap -->
	</div><!-- End AdminWrap -->
</div><!-- end AdsPanel -->
</body>
</html>
