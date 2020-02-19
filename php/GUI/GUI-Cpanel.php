<?php

include 'php/Functions/Functions-Cpanel.php';
/*

The following code is here specifically for the 
User Control Panel Graphical User Interface

*/

function buildUserManageMerch()
{
	if(array_key_exists('Alias',$_SESSION) && !empty($_SESSION['Alias'])) 
	{
		$alias = $_SESSION['Alias'];
		$type = getUserType($alias);
	
		if($type == 'Member' || $type == 'Admin')
		{
		print("<div id='userManageMerchPanelWrap'>
		<div id='userManageMerchPanelHeader' class='panelHeader'><h2>Manage Merch</h2></div><!-- end userManageMerchPanelHeader -->");
		buildUserAddMerch($alias);
		buildUserEditMerch($alias);
		print("</div><!-- end userManageMerchPanelWrap -->");
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

function buildUserAddMerch($Alias)
{
	print("
	<input type='button' id='addMerchClickable' value='Add Merch'>");
	print("<div id='addMerchInternal' class='Internal'>");
	print("
	<div id='merchTitle'><strong>Title:</strong><input type='text' id='merchTitleText' value=''></div><!-- end merchTitle -->
	<div id='merchName'><strong>Name</strong><input type='text' id='merchDetailsText'></div><!-- end merchType -->
	
	<div id='merchDesc'><strong>Details:</strong><br><textarea id='merchDescText' class='merchDetailsTextbox'></textarea></div><!-- end merchDesc -->
	<div id='merchSubmit'><input type='button' id='submitMerchButton' value='Add' onclick='addMerchByUser(\"$Alias\")' class='submitBTN'></div><!-- end merchSubmit -->
	<div id='PostMSG'></div><!-- end PostMSG -->");
	print("</div><!-- end addMerchInternal -->");
	
	print("<script>
	$('#addMerchClickable').click
			(
				function()
				{
					$('#addMerchInternal').slideToggle();
			});
	</script>");
	
}

function buildUserEditMerch($Alias)
{
/*	
	print("
	<input type='button' id='editMerchClickable' value='Add Merch'>
	<div id='editMerchInternal' class='Internal'>");
	$MerchList = getAllMerchAvailableToPerson($Alias);
	foreach($MerchList->getRecord() as $Merch)
	{
		$ItemID = $Merch->value("ItemID");
		print("
		<div id='merchTitle$ItemID'><strong>Title:</strong><input type='text' id='merchTitleText$ItemID' value=''></div><!-- end merchTitle -->
		<div id='merchURL$ItemID'><strong>URL:</strong><input type='text' id='merchURLText$ItemID' value=''></div><!-- end merchTitle -->
		<div id='merchDetails$ItemID'><strong>Details<br></strong><textarea id='newsDetailsText$ItemID'></textarea></div><!-- end merchType -->
		
		<div id='merchDesc$ItemID'><strong>Details:</strong><textarea id='merchDescText$ItemID' class='detailsTextbox'></div><!-- end merchDesc -->
		<div id='merchSaveEdits$ItemID'><input type='button' id='merchSaveEditsButton$ItemID' value='Save' onclick='merchSaveEdits(\"$ItemID\")' class='submitBTN'></div><!-- end merchSaveEdits -->
		<div id='PostMSG$ItemID'></div><!-- end PostMSG$ItemID -->");
		print("</div><!-- end addMerchInternal$ItemID -->");
	}
	print("<script>
	$('#editMerchClickable').click
			(
				function()
				{
					$('#editMerchInternal').slideToggle();
			});
	</script>");
	*/
}

?>