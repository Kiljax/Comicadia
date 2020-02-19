<?php

/********************************************

The following code is for the MAX part of the website

*********************************************/

function getMAXStatusList()
{
	return ["Pending","Active","Completed"];
}

function getMAXSignup()
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX)
	WHERE m.Status = 'Registration'
	return COUNT(m) as Exists");
	$record = $query->getRecord();
	return $record->value("Exists");
}

function getMAXActiveRound()
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX)
	WHERE m.Status = 'Active'
	return COUNT(m) as Exists");
	$record = $query->getRecord();
	return $record->value("Exists");
}

function getCurrentMAXRound()
{
	$session=buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE m.Status = 'Active'
	RETURN m.StartDate as StartDate,
	m.EndDate as EndDate,
	m.SignUpEndDate as SignUpEndDate,
	m.Status as Status,
	m.Theme as Theme,
	m.DateCreated as DateCreated");
	return $query->getRecord();
}

function getCurrentMAXSignUpRound()
{
	$session=buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE m.Status = 'Registration'
	RETURN m.StartDate as StartDate,
	m.EndDate as EndDate,
	m.SignUpEndDate as SignUpEndDate,
	m.Status as Status,
	m.Theme as Theme,
	m.DateCreated as DateCreated");
	return $query->getRecord();
}

function getAllMAXRounds()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)
	RETURN m.EndDate as EndDate,
	m.StartDate as StartDate,
	m.SignUpEndDate as SignUpEndDate,
	m.Theme as Theme,
	m.Status as Status,
	m.DateCreated as DateCreated ORDER BY m.EndDate DESC");
	return $query;
}

function getAllCompletedMAXRounds()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX{Status: 'Completed'})
	RETURN m.EndDate as EndDate,
	m.StartDate as StartDate,
	m.SignUpEndDate as SignUpEndDate,
	m.Theme as Theme,
	m.Status as Status,
	m.DateCreated as DateCreated ORDER BY m.EndDate DESC");
	return $query;
}

function getAllPendingMAXRounds()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX{Status: 'Pending'})
	RETURN m.EndDate as EndDate,
	m.StartDate as StartDate,
	m.SignUpEndDate as SignUpEndDate,
	m.Theme as Theme,
	m.Status as Status,
	m.DateCreated as DateCreated ORDER BY m.EndDate DESC");
	return $query;
}

/***********************************************************

The following boxes of code are for Administrative functions

************************************************************/

function CreateMAXRound($StartDate, $EndDate, $SignUpEndDate, $Theme, $Status)
{
	$session = buildAdminCon();
	$query = $session->run("CREATE (m:MAX{
	StartDate: toInt({startdate}),
	EndDate: toInt({enddate}),
	SignUpEndDate: toInt({signupenddate}),
	Theme: {theme},
	Status: {status},
	DateCreated: timestamp() })"
	,["startdate"=>$StartDate,"enddate"=>$EndDate,"signupenddate"=>$SignUpEndDate,"theme"=>$Theme,"status"=>$Status]);
}

function checkIfMAXAlreadyScheduled($StartDate, $OneWeekLater, $OneWeekEarlier)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE m.StartDate = toInt({startdate})
	return COUNT(m) as result",
	["startdate"=>$StartDate]);
	$record = $query->getRecord();
	
	if($record->value("result") > 0)
	{
		return true;
	}
	
	$query2 = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE (m.StartDate > {oneweekearlier} 
	AND m.StartDate < {oneweeklater})
	return COUNT(m) as result",
	["oneweekearlier"=>$OneWeekEarlier,"oneweeklater"=>$OneWeekLater]);
	$record2 = $query2->getRecord();
	
	if($record2->value("result") > 0)
	{
		return true;
	}
	return false;
}

function checkIfUpdatedMAXAlreadyScheduled($NewStart,$Later,$Earlier, $MAXID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE m.StartDate = toInt({startdate})
	AND m.DateCreated <> toInt({maxid})
	return COUNT(m) as result",
	["startdate"=>$NewStart,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	
	if($record->value("result") > 0)
	{
		return true;
	}
	
	$query2 = $session->run("OPTIONAL MATCH (m:MAX)
	WHERE (m.StartDate > {oneweekearlier} 
	AND m.StartDate < {oneweeklater})
	AND m.DateCreated <> toInt({maxid})
	return COUNT(m) as result",
	["oneweekearlier"=>$Earlier,"oneweeklater"=>$Later,"maxid"=>$MAXID]);
	$record2 = $query2->getRecord();
	
	if($record2->value("result") > 0)
	{
		return true;
	}
	return false;
}

function setMAXRoundAsActive($DateCreated)
{
	$session = buildadminCon();
	$Deactivate = $session->run("MATCH (m:MAX) 
	WHERE m.Status = 'Active'
	SET m.Status = 'Complete'");
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({datecreated}) })
	SET m.Status = 'Active'",["datecreated"=>$DateCreated]);
}

function updateMAXRound($MAXID, $NewStart, $NewDeadline, $NewClose, $NewTheme, $NewStatus)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	SET m.StartDate = toInt({startdate})
	SET m.EndDate = toInt({enddate})
	SET m.SignUpEndDate = toInt({closedate})
	SET m.Theme = {theme}
	SET m.Status = {status}",
	["maxid"=>$MAXID,
	"startdate"=>$NewStart,
	"enddate"=>$NewDeadline,
	"closedate"=>$NewClose,
	"theme"=>$NewTheme,
	"status"=>$NewStatus]);
}

function deleteMAX($MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	DETACH DELETE m",["maxid"=>$MAXID]);
}

function markMAXRoundCompleted($MAXID)
{
	$session = buildAdminCon();
	$Submitted = $session->run("MATCH (p:Person)-[:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	return DISTINCT p.Alias as Alias, p.Email as Email",["maxid"=>$MAXID]);
	$SubmittedArray = array();
	foreach($Submitted->getRecords() as $WhiteList)
	{
		$Alias = $WhiteList->value("Alias");
		$Recipient = $WhiteList->value("Email");
		array_push($SubmittedArray,$Alias);
		$Subject = "MAX Round now completed";
		$Text = "Dear $Alias, <br><br> Thank you for taking part in the MAX round over at Comicadia. If you would like ".
		"to see what you received from another artist, please log in to the <a href='https://www.comicadia.com/MAX' target='_blank'>Comicadia MAX</a> site and have a look. <br>".
		"In the event that you did not receive an entry, as a recompense, your entry will have been registered for adoption. <br>".
		"In the event that a user submits your MAX entry late, you will not only receive the MAX entry, but your adoption will ".
		"remain open for others to adopt. <br><br>".
		"The Comicadia Team.";
		sendEmailFromNoReply($Recipient, $Subject, $Text);
	}
	$Blacklist = $session->run("MATCH (p:Person)-[:Signed_Up_For]-(m:MAX{DateCreated: toInt({maxid}) })
		WHERE NOT p.Alias IN {whitelist} 
		WITH p,m
		MATCH (p)-[:Was_Matched_Up]->(me:MAXEntry)-[:Was_Submitted_For]->(m)
        CREATE (p)-[:Made_The_List]->(b:Blacklist{DateCreated: timestamp(), 
		Reason: 'Did not upload entry for MAX Round: '+ {maxid}, 
		EntryID: me.DateCreated,
		BlacklistedBy: 'System',
		Status: 'Active'})
		return p.Email as Email,
		p.Alias as Alias",["maxid"=>$MAXID,"whitelist"=>$SubmittedArray]);
	
	foreach($Blacklist->getRecords() as $User)
	{
		$Recipient = $User->value("Email");
		$UserAlias  = $User->value("Alias");
		$Subject = "No entry received for MAX";
		$Text = "Dear $UserAlias, <br><br> You did not submit an entry for the MAX round ".
		"that has just closed. Until you submit a round, the account associated with ".
		"this profile will be unable to participate in future MAX Rounds. <br>".
		"Please submit an entry at your earliest convenience. <br><br>".
		"The Comicadia Team.";
		sendEmailFromNoReply($Recipient, $Subject, $Text);
	}
	$OrphanCount = $session->run("OPTIONAL MATCH (me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid})} )
	WHERE NOT EXISTS((:Person)-[:Submitted_Entry{Status: 'Completed'}]->(me))
	RETURN COUNT(me) as OrphanedEntries",
	["maxid"=>$MAXID]);
	$OrphanCount = $OrphanCount->getRecord();
	$OrphanCount = $OrphanCount->value("OrphanedEntries");
	
	if($OrphanCount > 0)
	{
		$Orphans = $session->run("MATCH (me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid})} )
		WHERE NOT EXISTS((:Person)-[:Submitted_Entry{Status: 'Completed'}]->(me))
		RETURN me.DateCreated as EntryID",
		["maxid"=>$MAXID]);
		foreach($Orphans->getRecord() as $Orphan)
		{
			$EntryID = $Orphan->value("EntryID");
			createOrphanEntry($EntryID);
		}
	}
	$query=$session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	SET m.Status = 'Completed'",
	["maxid"=>$MAXID]);
}

function createOprhanEntry($EntryID)
{
	$session = buildAdminCon();
	$OrphanEntry = $session->run("MATCH (me:MAXEntry{DateCreated: toInt({entryid}) })
		CREATE (o:OrphanEntry{DateCreated:timestamp(), Status: 'Active' })-[:Is_Orphan_Of]->(me)
		WITH toString(toInt(round(rand() * 100))) as Last,
		toString(timestamp()) as Stamp, o
		set o.OrphanID = toInt(Stamp + Last)",
		["entryid"=>$EntryID]);
}

function LockMAXRound($MAXID)
{
	$session = buildAdminCon();
	if(getMAXMatchupStatus($MAXID) != 'Generated')
	{
		print("Cannot lock MAX round");
	}
	else
	{
		$preferred = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
		WITH me
		OPTIONAL MATCH (me)-[:Is_Entry_For]->(p:Person)<-[:Belongs_To]-(c:Character{preferredStatus: 'Preferred'})
		SET me.CharacterPreferred = c.CharacterID",["maxid"=>$MAXID,]);
		
		$MailingList = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
		WITH me, m
		MATCH (sender:Person)-[:Was_Matched_Up]->(me)-[:Is_Entry_For]->(receiver:Person)
		return 
		sender.Email as Email,
		sender.Alias as Sender,
		receiver.Alias as Receiver,
		m.Theme as Theme,
		m.EndDate as EndDate,
		me.CharacterPreferred as CharacterID",
		["maxid"=>$MAXID]);
		
		$Subject = "MAX Round Generation Results";
		foreach($MailingList->getRecords() as $Mail)
		{
			$Alias = $Mail->value("Sender");
			$Receiver = $Mail->value("Receiver");
			$Recipient = $Mail->value("Email");
			$CharacterID = $Mail->value("CharacterID");
			$EndDate = $Mail->value("EndDate");
			$Theme = $Mail->value("Theme");
			$Deadline = date('F jS, Y', $EndDate/1000);
			$Body = "Hello $Alias, <br><br>The MAX round has been generated and you received: <a href='https://www.comicadia.com/MAX/index.php?MemberAlias=$Receiver&Fields=Users' target='_blank' >$Receiver</a>! <br>";
			
			if($CharacterID != '')
			{
				$CharacterName = getCharacterNameByID($CharacterID);
				$Body .= "They have requested that their character: <a href='https://www.comicadia.com/MAX/index.php?CharacterID=$CharacterID' target='_blank'>$CharacterName</a> be drawn for this round. \r\n";
			}
			if($Theme != '')
			{
				$Body .="The optional theme for this round is: $Theme.\r\n";
			}
			
			$Body .= "The final date to submit your entry will be $Deadline.\r\nThank you for your participation! We can't wait to see what you create!\r\n\r\nThe Comicadia Team";
			sendEmailFromNoReply($Recipient, $Subject, $Body);
		}
	
		$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
		SET m.MatchupStatus = 'Locked'
		SET m.Status ='Active'",["maxid"=>$MAXID]);
		print("MAX Round locked");	
	}
}
function buildMAXMatchupArray($MAXID)
{
	$EntryCount = 0;
	$MatchUpArray = array();
	$SignedUpUsers = getSignedUpUsersForMAXRound($MAXID);
	$SignUpCount = 0;
	foreach($SignedUpUsers->getRecords() as $User)
	{
		$UserAlias = $User->value('Alias');
		$Receiver = getAvailableSignedUpUsersForMAXThatAreNotThisUser($MAXID, $UserAlias);
		if($Receiver)
		{
			$subCount = getCountOfSubmissionFromUserToRecipientForMAXRound($UserAlias, $Receiver,$MAXID);
			$Matchup = array("User"=>$UserAlias,"Recipient"=>$Receiver,"SubCount"=>$subCount);
			AssignUserToReceiver($MAXID, $UserAlias, $Receiver);
			array_push($MatchUpArray, $Matchup);
		}
		$SignUpCount++;
	}
	if(count($MatchUpArray) == $SignUpCount)
	{
		setMatchupStatusAsGenerated($MAXID);
		return $MatchUpArray;
	}
	else
	{
		return false;
	}
}

function setMatchupStatusAsGenerated($MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	SET m.MatchupStatus = 'Generated'",["maxid"=>$MAXID]);
}
function setMatchupStatusAsError($MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	SET m.MatchupStatus = 'Error'",["maxid"=>$MAXID]);
}

function getTotalCountOfSubmissionFromUserToRecipient($UserAlias, $Receiver)
{
	$session = buildCon();
	$query = $session->run("	OPTIONAL MATCH p= (Sender:Person{Alias: {alias1} })-[:Was_Matched_Up]->(:MAXEntry)-[:Is_Entry_For]->(Receiver:Person{Alias: {alias2} })
	RETURN count(p) as TimesSubmitted",["alias1"=>$UserAlias,"alias2"=>$Receiver]);
	$record = $query->getRecord();
	return $record->value("TimesSubmitted");
}

function getCountOfSubmissionFromUserToRecipientForMAXRound($UserAlias, $Receiver, $MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{Status: 'Completed'})<-[:Was_Submitted_For]-(me:MAXEntry)
	WHERE m.DateCreated <> toInt({maxid})
	WITH m
	OPTIONAL MATCH p= (Sender:Person{Alias: {alias1} })-[:Submitted_Entry{Status: 'Completed'}]->(:MAXEntry)-[:Is_Entry_For]->(Receiver:Person{Alias: {alias2} })
	RETURN count(DISTINCT p) as TimesSubmitted",["maxid"=>$MAXID,"alias1"=>$UserAlias,"alias2"=>$Receiver]);
	$record = $query->getRecord();
	return $record->value("TimesSubmitted");
}

function getCountOfUserCompletedRounds($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=((p:Person{Alias: {alias}})-[:Signed_Up_For]-(m:MAX))
	return count(n) as MAXCount",["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("MAXCount");
}

function isUserBlacklisted($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Made_The_List]->(b:Blacklist{Status: 'Active'})
	RETURN count(b) as BlacklistCount",["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("BlacklistCount") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getMAXMatchupStatus($MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX{DateCreated: toInt({maxid}) })
	return m.MatchupStatus as MatchupStatus",["maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("MatchupStatus");
}

function getSignedUpUsersForMAXRound($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	RETURN p.Alias as Alias, 
	rand() as r ORDER BY r ASC",["maxid"=>$MAXID]);
	return $query;
}

function getAvailableSignedUpUsersForMAXThatAreNotThisUser($MAXID, $Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	WHERE p.Alias <> {alias}
	AND NOT EXISTS((m)<-[:Was_Submitted_For]-(:MAXEntry)-[:Is_Entry_For]->(p))
	WITH p
	OPTIONAL MATCH (p2:Person{Alias: {alias} })-[:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Is_Entry_For]->(p:Person)
	RETURN p.Alias as Alias, count(me) as EntriesForPerson, rand() as r ORDER BY EntriesForPerson ASC, r ASC",["maxid"=>$MAXID,"alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("Alias");
}

function clearMAXMatchups($MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	set m.MatchupStatus = 'Cleared'
	detach delete me",["maxid"=>$MAXID]);
}

function getAllPendingReferences()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)<-[:Belongs_To]-(c:Character)<-[:Is_Reference_For]-(r:Reference{Status: 'Pending'})
	RETURN
	p.Alias as Alias,
	c.CharacterID as CharacterID,
	c.Name as CharacterName,
	collect(r.URL) as URLs
	ORDER BY p.Alias");
	return $query;
}

function getAllApprovedReferences()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)<-[:Belongs_To]-(c:Character)<-[:Is_Reference_For]-(r:Reference{Status: 'Approved'})
	RETURN
	p.Alias as Alias,
	c.CharacterID as CharacterID,
	c.Name as CharacterName,
	collect(r.URL) as URLs
	ORDER BY p.Alias");
	return $query;
}

function adminApproveReference($URL, $Alias)
{
	$session = buildAdminCon();
	$query =$session->run("MATCH (r:Reference{URL: {url} }), (p:Person{Alias: {alias}})
	CREATE (p)<-[:Approved_By{DateCreated: timestamp()}]-(r)
	SET r.Status = 'Approved'",["url"=>$URL,"alias"=>$Alias]);
}

function adminRejectReference($URL, $Alias)
{
	$session = buildAdminCon();
	$query =$session->run("MATCH (r:Reference{URL: {url} }), (p:Person{Alias: {alias}})
	CREATE (p)<-[:Rejected_By{DateCreated: timestamp()}]-(r)
	SET r.Status = 'Rejected'",["url"=>$URL,"alias"=>$Alias]);
}

function countReferenceCountByStatus($Status)
{
	$session = buildCon();
	$query = $session->run("MATCH (r:Reference{Status: {status} })
	Return count(r) as RefCount",["status"=>$Status]);
	$record = $query->getRecord();
	return $record->value("RefCount");
}

function getCountOfActivelyBlacklistedMembers()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[m:Made_The_List]->(b:Blacklist{Status: 'Active'})
	RETURN count(p) as BlackListCount");
	$record = $query->getRecord();
	return $record->value("BlackListCount");
}

function getActivelyBlacklistedMembers()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Made_The_List]->(b:Blacklist{Status: 'Active' })
	RETURN DISTINCT b.DateCreated as DateCreated,
    p.Alias as Alias,
	b.Reason as Reason
	ORDER BY p.Alias");
	return $query;
}


function getMAXParticpantCount()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX)
	RETURN COUNT(DISTINCT p) as ParticipantCount");
	$record = $query->getRecord();
	return $record->value("ParticipantCount");
}

function getMAXParticpantCountByKeyword($Search)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX)
	WHERE toLower(p.Alias) CONTAINS toLower({search})
	RETURN COUNT(DISTINCT p) as ParticipantCount",
	["search"=>$Search]);
	$record = $query->getRecord();
	return $record->value("ParticipantCount");
}

function addUserToBlacklist($MemberAlias, $AdminAlias, $Reason)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (User:Person{Alias: {alias1}})
	CREATE (User)-[:Made_The_List]->(b:Blacklist{DateCreated: timestamp(), Reason: {reason}, BlacklistedBy: {admin}, Status: 'Active' })",
	["alias1"=>$MemberAlias,"reason"=>$Reason,"admin"=>$AdminAlias]);
	
}

function removeUserFromBlacklist($MemberAlias, $AdminAlias, $BlacklistID, $Reason)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (User:Person{Alias: {alias1} })-[:Made_The_List]->(b:Blacklist{DateCreated: toInt({blacklistid}) }), (Admin:Person{Alias: {alias2} })
	SET b.Status = 'Resolved'
	SET b.ReasonResolved = {reason}
	CREATE (Admin)-[:Marked_As_Resolved{DateCreated: timestamp()} ]->(b)",
	["alias1"=>$MemberAlias,"blacklistid"=>$BlacklistID,"alias2"=>$AdminAlias,"reason"=>$Reason]);
}

function getMAXMembersListFromSearchByPagination($Keyword,$Start,$NumberOfArticles)
{
	$session = buildCon();
	$MemberList = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX)
	WHERE toLower(p.Alias) CONTAINS toLower({keyword}) 
	RETURN 
	DISTINCT p.Alias as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType
	ORDER BY p.UserType ASC, p.Alias ASC SKIP {start} LIMIT {NoOfArticles}",
	["keyword"=>$Keyword,"start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $MemberList;
}

function getMAXMembersListByPagination($Start,$NumberOfArticles)
{
	$session = buildCon();
	$MemberList = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX)
	RETURN 
	DISTINCT p.Alias as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType
	ORDER BY p.UserType ASC, p.Alias ASC SKIP {start} LIMIT {NoOfArticles}",
	["start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $MemberList;
}

function getCountOfAllReportedEntries()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (receiver:Person)-[report:Reported_Entry{Status: 'Active'}]->(me:MAXEntry)<-[:Submitted_Entry{Status: 'Completed'}]-(sender:Person)
	RETURN 
	DISTINCT COUNT(report) as reportCount");
	$record = $query->getRecord();
	return $record->value("reportCount");;
}

function getAllActiveReportedEntries()
{
	$session = buildCon();
	$query = $session->run("MATCH (receiver:Person)-[report:Reported_Entry{Status: 'Active'}]->(me:MAXEntry)<-[:Submitted_Entry{Status: 'Completed'}]-(sender:Person)
	RETURN 
	DISTINCT report.DateCreated as ReportedOn,
	sender.Alias as Sender,
	receiver.Alias as Receiver,
	report.Reason as Reason,
	me.DateCreated as EntryID,
	me.URL as URL,
	me.Comments as Comments");
	return $query;
}

function hasReportAlreadyBeenHandled($Alias, $ReportedOn, $EntryID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[r:Reported_Entry{DateCreated: toInt({reportedon}), Status: 'Resolved'}]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	RETURN count(r) as Exists",
	["alias"=>$Alias,"reportedon"=>$ReportedOn,"entryid"=>$EntryID]);
	$record = $query->getRecord();
	if($record->value("Exists") > 0)
	{
		return true;
	}
	{
		return false;
	}
	
}

function resolveReportedEntry($EntryID, $Alias, $ReportedOn, $AdminAlias, $Reason, $Status)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (receiver:Person{Alias: {alias} })-[report:Reported_Entry{DateCreated: toInt({reportedon}) }]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	SET report.Status = {status}
	SET report.DateResolved = timestamp()
	SET report.AdminReason = {reason}
	SET report.AdminAlias = {admin}
	SET me.Status = {status}
	WITH me
	CREATE (p:Person{Alias: {admin}})-[:Marked_As_Resolved{DateCreated: timestamp(), Reason: {reason}, Status: {status} }]->(me)
	",
	["alias"=>$Alias,"entryid"=>$EntryID,"reportedon"=>$ReportedOn,"admin"=>$AdminAlias,"reason"=>$Reason,"status"=>$Status]);
}
/********************************************************************

The following boxes of code is for users to interact with a MAX round

*********************************************************************/
function checkIfUserIsSignedUp($Alias, $MAXID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	return count(p) as result"
	,["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	if($record->value("result") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function signUpForMax($Alias, $MAXRoundID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} }), (m:MAX{DateCreated: toInt({datecreated}) })
	CREATE (p)-[:Signed_Up_For{DateSignedUp: timestamp()}]->(m)",
	["alias"=>$Alias,"datecreated"=>$MAXRoundID]);
}

function withdrawUserFromMAX($Alias, $MAXID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[r:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	DELETE r",["alias"=>$Alias,"maxid"=>$MAXID]);
}

function isSignedUpForMAX($Alias, $MAXRoundID)
{
	$session = buildCon();
	$query = $session->run("MATCH p=((p:Person{Alias: {alias}} )-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({datecreated}) }))
	RETURN COUNT(p) as result"
	,["alias"=>$Alias,"datecreated"=>$MAXRoundID]);
	$record = $query->getRecord();
	if($record->value("result") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function withdrawFromMAX($Alias, $MAXRoundID)
{
	$session - buildadminCon();
	$query = $session->run("MATCH (p{Alias: {alias} })-[r:Is_Signed_Up_For]->(m:MAX{DateCreated: toInt({datecreated}) })
	DELETE r",["alias"=>$Alias,"datecreated"=>$MAXRoundID]);
}

function convertYmdToTimestamp($YMDString)
{
	$Stamp = DateTime::createFromFormat('Y-m-d', $YMDString);
	$Stamp = $Stamp->format('U');
	$Stamp = $Stamp * 1000;
	return $Stamp;
}

function isMAXRoundLocked($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	return m.MatchupStatus as LockedStatus",
	["maxid"=>$MAXID]);
	$record = $query->getRecord();
	if($record->value("LockedStatus") == 'Locked')
	{
		return true;
	}
	return false;
}

function checkIfUserHasSubmittedArtForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query=$session->run("MATCH (p:Person{Alias: {alias} })-[r:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	return count(r) as HasSubmitted",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	if($record->value("HasSubmitted") > 0)
	{
		return true;
	}
	return false;
}

function getEntryDetailsByAliasAndMAXID($Alias, $MAXID)
{
	$session = buildCon();
	$query=$session->run("MATCH (sender:Person{Alias: {alias} })-[r:Submitted_Entry]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	WITH me, sender
	MATCH (me)-[:Is_Entry_For]->(receiver:Person)
	RETURN 
	me.DateCreated as EntryID, 
	sender.Alias as Sender,
	receiver.Alias as Receiver,
	me.CharacterSubmitted as CharacterID,
	me.URL as URL,
	me.Comments as Comments,
	me.Status as Status",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	return $query->getRecord();
}

function checkIfUserReceivedArtForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query=$session->run("MATCH (me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	WITH me
	MATCH (p:Person{Alias: {alias}})<-[:Is_Entry_For]-(me)<-[:Submitted_Entry{Status: 'Completed'}]-(sender:Person)
	RETURN count(DISTINCT me) as WasSubmitted",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	if($record->value("WasSubmitted") > 0)
	{
		return true;
	}
	return false;
}

function getUserSubmissionForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query=$session->run("MATCH (p:Person{Alias: {alias} })-[:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	return me.URL as URL",[
	"alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function getUserRewardForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query=$session->run("MATCH (p:Person{Alias: {alias} })<-[:Is_Entry_For]-(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	RETURN me.URL as URL",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function AssignUserToReceiver($MAXID, $UserAlias, $Receiver)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (Sender:Person{Alias: {alias1} }), (Receiver:Person{Alias: {alias2} })
	CREATE (Sender)-[:Was_Matched_Up{DateCreated: timestamp()}]->
	(me:MAXEntry{DateCreated: timestamp()} )-[:Is_Entry_For]->(Receiver)
	WITH me
	MATCH (m:MAX{DateCreated: toInt({maxid}) })
	CREATE (m)<-[:Was_Submitted_For]-(me)",
	["maxid"=>$MAXID,"alias1"=>$UserAlias,"alias2"=>$Receiver]);
}

function isMAXSignupClosed($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })
	RETURN m.SignUpEndDate as SignUpCloseDate",["maxid"=>$MAXID]);
	$record = $query->getRecord();
	$Date = $record->value("SignUpCloseDate");
	$SignUpClose = date('F jS, Y', $Date/1000);
	if($Date < time())
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getMAXMatchupForRound($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
	with me
	MATCH (Sender:Person)-[:Was_Matched_Up]->(me)-[:Is_Entry_For]->(Receiver:Person)
	RETURN Sender.Alias as Sender,
	Receiver.Alias as Receiver",["maxid"=>$MAXID]);
	return $query;
}

function getAllMAXRoundsUserEntered($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX)
	return DISTINCT 
	m.DateCreated as MAXID,
	m.StartDate as StartDate,
	m.EndDate as EndDate,
	m.Theme as Theme",
	["alias"=>$Alias]);
	return $query;
}

function getAllPreviousMAXRoundsUserEntered($MAXID, $Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX)
	WHERE m.DateCreated <> toInt({maxid})
	return DISTINCT 
	m.DateCreated as MAXID,
	m.StartDate as StartDate,
	m.EndDate as EndDate,
	m.Theme as Theme",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	return $query;
}
/*********************************************************************

The following blocks of code below are for the MAX Characters
and their references

************************************************************************/
function getCountofCharactersOfUser($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)<-[:Belongs_To]-(c:Character)
	return count(c) as CharacterCount",["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("CharacterCount");
}

function getCharactersOfUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character)
	WHERE c.Status = 'Active'
	return c.CharacterID as CharacterID,
	c.Name as Name,
	c.Age as Age,
	c.Gender as Gender,
	c.Race as Race,
	c.Hair as Hair,
	c.Eyes as Eyes,
	c.Height as Height,
	c.Weight as Weight,
	c.WriteUp as WriteUp,
	c.Status as Status
	",["alias"=>$Alias]);
	return $query;
}

function getCharacterBio($CharacterID)
{
	$session = buildCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })
	return 
	c.Name as Name,
	c.Age as Age,
	c.Gender as Gender,
	c.Race as Race,
	c.Hair as Hair,
	c.Eyes as Eyes,
	c.Height as Height,
	c.Weight as Weight,
	c.WriteUp as WriteUp
	",["characterid"=>$CharacterID]);
	return $query->getRecord();
}

function addCharacterToUser($Alias, $Name, $Age, $Gender, $Race, $Hair, $Eyes, $Height, $Weight, $Writeup,$ComicID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})
	CREATE (p)<-[:Belongs_To]-(c:Character{Name: {name},
	Age: {age},
	Gender: {gender},
	Race: {race},
	Hair: {hair},
	Eyes: {eyes},
	Height: {height},
	Weight: {weight},
	WriteUp: {writeup},
	Status: 'Active'})
	WITH toString(toInt(round(rand() * 100))) as Last,
	toString(timestamp()) as Stamp, c
	set c.CharacterID = toInt(Stamp + Last)
	RETURN c.CharacterID as CharacterID",
	["alias"=>$Alias, 
	"name"=>$Name,
	"age"=>$Age,
	"gender"=>$Gender,
	"race"=>$Race,
	"hair"=>$Hair,
	"eyes"=>$Eyes,
	"height"=>$Height,
	"weight"=>$Weight,
	"writeup"=>$Writeup]);
	if($ComicID != 'None')
	{
		$record = $query->getRecord();
		$CharacterID = $record->value("CharacterID");
		assignWebcomicToCharacter($ComicID, $CharacterID);
	}
}

function assignWebcomicToCharacter($ComicID, $CharacterID)
{
	$session = buildAdminCon();
	$addWebcomic = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) }), (w:Webcomic{ComicID: toInt({comicid}) })
		CREATE (c)-[:Is_Character_Of{DateAdded: timestamp()}]->(w)",
		["characterid"=>$CharacterID, "comicid"=>$ComicID]);
}

function checkIfUserAlreadyHasCharacterRegistered($Alias, $Name)
{
	$session = buildCon();
	$query = $session->run("MATCH e=((p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character{Name: {name} }))
	RETURN count(e) as Exists",
	["alias"=>$Alias,"name"=>$Name]);
	$record = $query->getRecord();
	if($record->value("Exists") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
	
}

function getCharacterOfUserReferencesCount($Alias, $CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference)
	WHERE c.Status = 'Active'
	RETURN count(r) as ReferenceCount",
	["alias"=>$Alias,
	"characterid"=>$CharacterID]);
	$record = $query->getRecord();
	return $record->value("ReferenceCount");
}

function getCountOfRetiredCharactersForUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias}})<-[:Belongs_To]-(c:Character{Status: 'Retired'})
	RETURN count(DISTINCT c) as retiredCount",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("retiredCount");
}

function getRetiredCharactersForUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias}})<-[:Belongs_To]-(c:Character{Status: 'Retired'})
	return c.CharacterID as CharacterID,
	c.Name as Name,
	c.Age as Age,
	c.Gender as Gender,
	c.Race as Race,
	c.Hair as Hair,
	c.Eyes as Eyes,
	c.Height as Height,
	c.Weight as Weight,
	c.WriteUp as WriteUp,
	c.Status as Status
	",["alias"=>$Alias]);
	return $query;
	
}

function getCountOfCharacterReferencesByID($CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{Status: 'Approved'})
	RETURN count(r) as ReferenceCount",
	["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	return $record->value("ReferenceCount");
}

function getCharacterOfUserReferences($Alias, $CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference)
	RETURN r.URL as URL,
	r.Status as Status",
	["alias"=>$Alias,
	"characterid"=>$CharacterID]);
	return $query;
}

function addCharacterReference($ImgURL,$CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })
	CREATE (r:Reference{URL: {imgurl}, DateCreated: timestamp(), Status: 'Pending' })-[:Is_Reference_For]->(c)",
	["imgurl"=>$ImgURL,"characterid"=>$CharacterID]);
}


function deleteCharacterReference($ImgURL, $CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{URL: {url} })
	DETACH DELETE r",["characterid"=>$CharacterID, "url"=>$ImgURL]);
}

function clearThumbnailForCharacter($CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference)
	remove r.MAXChoice",["characterid"=>$CharacterID]);
}

function setReferenceAsThumbnailForCharacter($CharacterID,$ImgURL)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{URL: {url} })
	SET r.MAXChoice = 'Thumbnail'",["characterid"=>$CharacterID,"url"=>$ImgURL]);
}

function clearCharacterPreferenceAsThumbnail($CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference)
	WHERE r.MAXChoice = 'Thumbnail'
	remove r.MAXChoice",["characterid"=>$CharacterID]);
}

function clearCharacterPreferencesForUser($Alias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})<-[:Belongs_To]-(c:Character)
	set c.PreferredStatus = ''",["alias"=>$Alias]);
}
function setCharacterAsPreferred($Alias,$CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})<-[:Belongs_To]-(c:Character{CharacterID: toInt({characterid}) })
	set c.PreferredStatus = 'Preferred'",["alias"=>$Alias,"characterid"=>$CharacterID]);
}

function getCharacterThumbnail($CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{MAXChoice: 'Thumbnail'})
	RETURN r.URL as URL",["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function isCharacterCurrentlyPreferred($CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })
	return c.PreferredStatus as Status",["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	if($record->value("Status") == 'Preferred' )
	{
		return true;
	}
	else
	{
		return false;
	}
}

function checkIfMAXRoundIsMostRecentlyCompleted($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{Status: 'Completed'} )
	return m.DateCreated as MAXID ORDER BY m.EndDate DESC LIMIT 1");
	$record = $query->getRecord();
	if((int)$record->value("MAXID") == (int)$MAXID)
		return true;
	else
		return false;
}

function getMembersSignedUpForMAX($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	RETURN p.Alias as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType
	ORDER BY p.UserType ASC, p.Alias ASC",["maxid"=>$MAXID]);
	return $query;
}


function getCountOfMembersSignedUpForMAX($MAXID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })
	RETURN COUNT(p) as participants",["maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("participants");
}
/*****************************************************

The following blocks are code are generic for both 
users and admins

*******************************************************/

function getUserProfilePicForMAX($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })
	return p.ProfilePic as ProfilePic",["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("ProfilePic");
}

function getCountOfMAXRoundsUserParticipatedIn($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX{Status: 'Completed'})
	RETURN COUNT(DISTINCT m) as MAXRounds",["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("MAXRounds");
}

function getMAXRoundUserParticipatedIn($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX)
	WITH m, p
	MATCH (m)<-[:Was_Submitted_For]-(me:MAXEntry)<-[:Was_Matched_Up]-(p)
	WITH m, p, me
	MATCH (receiver:Person)<-[Is_Entry_For]-(me)<-[:Was_Drawn_For]-(c:Character)
	RETURN 
	me.URL as URL,
	receiver.Alias as Receiver,
	c.Name as CharacterName,
	m.StartDate as MAXStartDate,
	m.EndDate as MAXEndDate
	ORDER BY m.StartDate DESC");
}

function getUserBlacklistRecords($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})-[:Made_The_List]->(b:Blacklist)
	WHERE NOT EXISTS((:Person)-[:Marked_As_Resolved]->(b))
	RETURN b.Reason as Reason,
	b.DateCreated as DateCreated,
	b.BlacklistedBy as Blacklister 
	ORDER BY b.DateCreated DESC",["alias"=>$Alias]);
	return $query;
}

function getCharactersOfUserCount($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character)
	WHERE c.Status = 'Active'
	return count(c) as CharCount",["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("CharCount");;
}

function getMAXRecipientForUserForMAXRound($MAXID, $Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
	WITH me, p, m
	MATCH (p)-[:Was_Matched_Up]-(me)-[:Is_Entry_For]->(receiver:Person)
	RETURN receiver.Alias as Alias,
	receiver.Email as Email,
	receiver.ProfilePic as ProfilePic",["alias"=>$Alias,"maxid"=>$MAXID]);
	return $query->getRecord();
}

function addUserEntryForMAX($ImgURL,$MAXID,$Alias,$Recipient,$CharacterID,$Comments)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (sender:Person{Alias: {sender} })-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
	WITH sender,me
	MATCH (sender)-[:Was_Matched_Up]->(me)-[:Is_Entry_For]->
	(receiver:Person{Alias: {receiver} })
	<-[:Belongs_To]-(c:Character{CharacterID: toInt({characterid}) })
	SET me.CharacterSubmitted = c.CharacterID
	SET me.URL = {imgurl}
	SET me.DateSubmitted = timestamp()
	SET me.Comments = {comments}
	SET me.Status = 'Completed'
	WITH me, sender
	MERGE (sender)-[r:Submitted_Entry]->(me)
	ON MATCH 
	SET r.DateCreated = timestamp()
	ON CREATE
	set r.DateCreated = timestamp()
	SET r.Status = 'Completed'",
	["sender"=>$Alias,
	"maxid"=>$MAXID,
	"receiver"=>$Recipient,
	"characterid"=>$CharacterID,
	"imgurl"=>$ImgURL,
	"comments"=>$Comments]);
}

function addLateUserEntryForMAX($ImgURL,$MAXID,$Alias,$Recipient,$CharacterID,$Comments)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (sender:Person{Alias: {sender} })-[:Signed_Up_For]->(m:MAX{DateCreated: toInt({maxid}) })<-[:Was_Submitted_For]-(me:MAXEntry)
	WITH sender,me
	MATCH (sender)-[:Was_Matched_Up]->(me)-[:Is_Entry_For]->
	(receiver:Person{Alias: {receiver} })
	<-[:Belongs_To]-(c:Character{CharacterID: toInt({characterid}) })
	CREATE (sender)-[r:Submitted_Entry]->(me)
	WITH r, me
	SET r.CharacterSubmitted = toInt({characterid})
	SET r.URL = {imgurl}
	SET r.DateSubmitted = timestamp()
	SET r.Comments = {comments}
	SET r.Status = 'Pending'
	SET me.Status = 'Pending'
	SET me.URL = {imgurl}",
	["sender"=>$Alias,
	"maxid"=>$MAXID,
	"receiver"=>$Recipient,
	"characterid"=>$CharacterID,
	"imgurl"=>$ImgURL,
	"comments"=>$Comments]);
}


function getEntryURLFromUserForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	return me.URL as URL",["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function getUserReceivedArtForRound($Alias, $MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})<-[:Is_Entry_For]-(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated: toInt({maxid}) })
	WITH me, p
	MATCH (sender:Person)-[:Submitted_Entry{Status: 'Completed'}]->(me)
	RETURN 
    DISTINCT me.URL as URL,
	me.Comments as Comments,
	me.CharacterSubmitted as CharacterID,
	sender.Alias as Alias,
	me.DateCreated as EntryID",
	["alias"=>$Alias,"maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record;
}

function reportEntry($EntryID, $Alias, $Reason)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (me:MAXEntry{DateCreated: toInt({entryid}) })
	SET me.Status = 'Reported'
	WITH me
	MATCH (p:Person{Alias: {alias} })
	CREATE (p)-[:Reported_Entry{DateCreated: timestamp(), Reason: {reason}, Status: 'Active'}]->(me)",
	["alias"=>$Alias,"entryid"=>$EntryID,"reason"=>$Reason]);
}

function hasUserAlreadyReportedEntry($EntryID, $Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (me:MAXEntry{DateCreated: toInt({entryid})})<-[r:Reported_Entry{Status: 'Active'}]-(p:Person{Alias: {alias}})
	RETURN count(r) as ReportCount",
	["entryid"=>$EntryID, "alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("ReportCount") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getCharacterName($CharacterID)
{
	$session = buildCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })
	return c.Name as Name",["characterid"=>$CharacterID]);
	$record = $query->getrecord();
	return $record->value("Name");
}

function getMAXRoundsUserParticipatedIn($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sender:Person{Alias: {alias} })-[:Signed_Up_For]->(m:MAX{Status: 'Completed'})
	WITH m, sender 
	MATCH (m)<-[:Was_Submitted_For]-(me:MAXEntry)<-[:Submitted_Entry{Status:'Completed'}]-(sender)
	WITH m, me, sender
	MATCH (me)-[:Is_Entry_For]->(receiver:Person)
	return 
    DISTINCT me.URL as URL,
	receiver.Alias as Receiver,
	me.CharacterSubmitted as CharacterID,
	me.DateCreated as EntryID,
	m.StartDate as MAXStartDate,
	m.EndDate as MAXEndDate
	ORDER BY m.StartDate DESC
	",["alias"=>$Alias]);
	return $query;
	
}

function getMAXCompletedRoundsCount()
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{Status: 'Completed'})<-[:Was_Submitted_For]-(me:MAXEntry)
	RETURN COUNT(DISTINCT m) as CompletedRounds");
	$record = $query->getRecord();
	return $record->value("CompletedRounds");
}

function getAllCompletedMAXRoundsAndTheirEntriesByPagination($Start,$articlesPerPage)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{Status: 'Completed'})<-[:Was_Submitted_For]-(me:MAXEntry)
	RETURN m.DateCreated as MAXID, 
	m.StartDate as StartDate,
	m.EndDate as EndDate,
	m.Theme as Theme,
	COLLECT(me.DateCreated) as EntryIDs
	ORDER BY m.EndDate DESC
	SKIP {start} LIMIT {articles}",
	["start"=>$Start,"articles"=>$articlesPerPage]);
	return $query;
}
	
function checkIfEntryHasReceivedSubmission($EntryID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (receiver:Person)<-[:Is_Entry_For]-(me:MAXEntry{DateCreated: toInt({entryid}) })<-[r:Submitted_Entry{Status:'Completed'}]-(sender:Person)
	RETURN COUNT(DISTINCT r) as submitCount",["entryid"=>$EntryID]);
	$record = $query->getRecord();
	if($record->value("submitCount") >0)
		return true;
	else
		return false;
}

function checkIfReceiverSubmittedTheirEntryForMAXRoundBasedOnEntryID($EntryID, $Receiver)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX)<-[:Was_Submitted_For]-(me:MAXEntry{DateCreated: toInt({entryid}) })
with  m
OPTIONAL MATCH (p:Person{Alias: {receiver}} )-[:Submitted_Entry{Status: 'Completed'}]->(me:MAXEntry)-[:Was_Submitted_For]->(m)
	RETURN COUNT(me) as Proof",["receiver"=>$Receiver, "entryid"=>$EntryID]);
	$record = $query->getRecord();
	if($record->value("Proof") > 0)
		return true;
	else
		return false;
}

function getEntryOrOrphanByID($EntryID)
{
	$session = buildCon();
	$IsEntry = $session->run("OPTIONAL MATCH (me:MAXEntry{DateCreated: toInt({entryid})})
	RETURN count(me) as EntryCount",["entryid"=>$EntryID]);
	$IsOrphan =$session->run("OPTIONAL MATCH (o:OrphanEntry{OrphanID: toInt({orphanid})})
	return count(o) as OrphanCount",["orphanid"=>$EntryID]);
	$OrphanCount = $IsOrphan->getRecord();
	$EntryCount = $IsEntry->getRecord();
	if($EntryCount->value("EntryCount") >0)
		return getEntryDetails($EntryID);
	elseif($OrphanCount->value("OrphanCount") > 0)
		return getOrphanDetails($EntryID);
	else
		return null;
	
}

function getEntryDetails($EntryID)
{
	$session = buildCon();
	$query = $session->run("MATCH (receiver:Person)<-[:Is_Entry_For]-(me:MAXEntry{DateCreated: toInt({entryid}) })<-[r:Submitted_Entry]-(sender:Person)
	RETURN 
	me.DateCreated as EntryID, 
	sender.Alias as Sender,
	receiver.Alias as Receiver,
	me.CharacterSubmitted as CharacterID,
	me.URL as URL,
	me.Comments as Comments,
	me.Status as Status",
	["entryid"=>$EntryID]);
	return $query->getRecord();
}

function getOrphanDetails($OrphanID)
{
	$session = buildCon();
	$query = $session->run("MATCH (receiver:Person)<-[:Is_Entry_For]-(me:MAXEntry)<-[:Is_Orphan_Of]-(o:OrphanEntry{OrphanID: toInt({orphanid}) })<-[:Adopted_Entry]-(sender:Person)
	RETURN sender.Alias as Sender,
	receiver.Alias as Receiver,
	o.OrphanID as EntryID,
	o.CharacterSubmitted as CharacterID,
	o.URL as URL,
	o.Comments as Comments",
	["orphanid"=>$OrphanID]);
	return $query->getRecord();
}

function getUnsubmittedEntryDetails($EntryID)
{
	$session = buildCon();
	$query = $session->run("MATCH (receiver:Person)<-[:Is_Entry_For]-(me:MAXEntry{DateCreated: toInt({entryid}) })<-[r:Was_Matched_Up]-(sender:Person)
	RETURN sender.Alias as Sender,
	receiver.Alias as Receiver",
	["entryid"=>$EntryID]);
	return $query->getRecord();
}

function getCharacterNameByID($CharacterID)
{
	if($CharacterID != '')
	{
		$session = buildCon();
		$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })
		return c.Name as Name",
		["characterid"=>$CharacterID]);
		$record = $query->getRecord();
			return $record->value("Name");
	}
	else 
		return '';

}

function getMAXCharacterListAndReferencesByPagination($Search,$startArticle, $articlesPerPage)
{
	$session = buildCon();
	
	if($Search != '')
	{
		$query = $session->run("MATCH (c:Character)<-[:Is_Reference_For]-(r:Reference)
		WHERE toLower(c.Name) CONTAINS toLower({search})
		RETURN c.Name as Name,
		c.CharacterID as CharacterID,
		COLLECT(r.DateCreated) as RefIDs
		ORDER BY c.Name ASC
		SKIP {start} LIMIT {NoOfArticles}",
		["start"=>$startArticle,"search"=>$Search,"NoOfArticles"=>$articlesPerPage]);
	}
	else
	{
		$query = $session->run("MATCH (c:Character)<-[:Is_Reference_For]-(r:Reference)
		RETURN c.Name as Name,
		c.CharacterID as CharacterID,
		COLLECT(r.DateCreated) as RefIDs
		ORDER BY c.Name ASC
		SKIP {start} LIMIT {NoOfArticles}",
		["start"=>$startArticle,"NoOfArticles"=>$articlesPerPage]);
	}
	return $query;	
}

function getReferenceURLFromID($ReferenceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (r:Reference{DateCreated: toInt({refid}) })
	return r.URL as URL",
	["refid"=>$ReferenceID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function getMAXCharacterCount()
{
	$session = buildCon();
	$query = $session->run("MATCH (c:Character)
	RETURN COUNT(c) as CharacterCount");
	$record = $query->getrecord();
	return $record->value("CharacterCount");
}

function getMAXCharacterCountByKeyword($Keyword)
{
	$session = buildCon();
	$query = $session->run("MATCH (c:Character)
	WHERE toLower(c.Name) CONTAINS toLower({keyword})
	RETURN COUNT(c) as CharacterCount",["keyword"=>$Keyword]);
	$record = $query->getrecord();
	return $record->value("CharacterCount");
}


function checkIfUserHasACharacterWithReferences($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })<-[:Belongs_To]-(c:Character)<-[:Is_Reference_For]-(r:Reference)
	WHERE r.Status = 'Approved'
	RETURN 
	COUNT(r) as ReferenceCount",["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("ReferenceCount") >0)
		return true;
	else
		return false;
		
}

function getReferencesFromCharacterID($CharacterID)
{
	$session = buildcon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{Status: 'Approved'} )
	RETURN 
	r.URL AS URL",["characterid"=>$CharacterID]);
	return $query;
}

function sendEmailFromNoReply($Recipient, $Subject, $Text)
{
	$headers = "From: no-reply@comicadia.com" . "\r\n" .
	"Content-type: text/html; charset=utf-8 \r\n" .
    "X-Mailer: PHP/" . phpversion();
	if(mail($Recipient,$Subject,$Text,$headers))
		echo "Email sent<br>";
	else
		echo "Failed to send email to $Recipient<br>";
}

function getEmailAddressesForMAXRoundParticipants($MAXID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)-[:Was_Matched_Up]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{DateCreated : toInt({maxid}) })
	RETURN p.Email as Email,
	p.Alias as Alias",
	["maxid"=>$MAXID]);
	return $query;
}
	
	
/**************************************************************

The following blocks of code are for the Adoption part of MAX

***************************************************************/


function getPotentialMAXEntriesForAdoption($Start, $NumberOfArticles)
{
	$session = buildCon();
	$query = $session->run("MATCH (o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	WHERE NOT o.Status IN ['Completed','Claimed']
	RETURN o.OrphanID as OrphanID,
	me.DateCreated as EntryID,
	me.CharacterPreferred as CharacterID,
	receiver.Alias as Receiver
	ORDER BY o.DateCreated ASC
	SKIP {start} LIMIT {numberofarticles}",
	["start"=>$Start,"numberofarticles"=>$NumberOfArticles]);
	return $query;
}

function getAdoptionsSubmittedForReview()
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[ae:Adopted_Entry{Status:'Submitted'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	return DISTINCT o.OrphanID as OrphanID,
	ae.URL as URL,
	ae.CharacterSubmitted as SubmittedCharacter,
	ae.DateSubmitted as DateSubmitted,
	ae.DateCreated as DateCreated,
	ae.Comments as Comments,
	sender.Alias as Sender,
	receiver.Alias as Receiver,
	me.CharacterPreferred as PreferredCharacter");
	return $query;
}

function getMostRecentAdoptionStatusByOrphanID($OrphanID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sender:Person)-[ae:Adopted_Entry]->(o:OrphanEntry{OrphanID: toInt({orphanid}) })
	RETURN o.Status as Status 
	ORDER BY ae.DateCreated DESC LIMIT 1",
	["orphanid"=>$OrphanID]);
	$record = $query->getRecord();
	return $record->value("Status");
}

function adoptEntry($OrphanID,$EntryID,$Alias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} }), (o:OrphanEntry{OrphanID: toInt({orphanid}) })-[:Is_Orphan_Of]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	CREATE (p)-[:Adopted_Entry{DateCreated: timestamp(), Status: 'Claimed' }]->(o)
	WITH o
	SET o.Status = 'Claimed'",
	["orphanid"=>$OrphanID, "alias"=>$Alias,"entryid"=>$EntryID]);
}

function checkIfOrphanWasAdopted($OrphanID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[r:Adopted_Entry]->(o:OrphanEntry{OrphanID: toInt({orphanid}) })
	WHERE r.Status <> 'Rejected'
	RETURN count(r) as AdoptedCount",
	["orphanid"=>$OrphanID]);
	$record = $query->getRecord();
	if($record->value("AdoptedCount") >0)
		return true;
	else
		return false;
}

function checkIfUserHasAlreadyClaimedAnAdoption($Alias)
{
		$session = buildCon();
		$query = $session->run("OPTIONAL MATCH p=((sender:Person{Alias: {alias} })-[ae:Adopted_Entry]->(o:OrphanEntry))
		WHERE NOT ae.Status IN ['Completed','Rejected']
		RETURN count(DISTINCT p) as AdoptionCount",
		["alias"=>$Alias]);
		$record = $query->getRecord();
		if($record->value("AdoptionCount") >0)
			return true;
		else
			return false;
}

function submitAdoptionArtForReview($ImgURL,$Alias,$AdoptionID,$EntryID,$CharacterID,$Comments)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})-[ae:Adopted_Entry{DateCreated: toInt({adoptionid}) }]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	SET ae.Status = 'Submitted'
	SET ae.DateSubmitted = timestamp()
	SET ae.CharacterSubmitted = {characterid}
	SET ae.URL = {imgurl}
	SET ae.Comments = {comments}
	SET o.Status = 'Submitted'",
	["alias"=>$Alias,"adoptionid"=>$AdoptionID,"entryid"=>$EntryID,"characterid"=>$CharacterID,"imgurl"=>$ImgURL,"comments"=>$Comments]);
}

function getCountOfAdoptionsWaitingForReview()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[ae:Adopted_Entry{Status: 'Submitted'}]->(o:OrphanEntry)
	RETURN DISTINCT COUNT(ae) as ReviewCount");
	$record = $query->getRecord();
	return $record->value("ReviewCount");
}

function changeAdoptionStatus($EntryID, $Alias, $AdminAlias, $Status)
{
	
}

function rejectAdoptionSubmission($AdminAlias, $Reason, $AdoptionID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (:Person)-[ae:Adopted_Entry{DateCreated: toInt({adoptionid}) }]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)
	SET ae.Status = 'Rejected'
	SET ae.Reason = {reason}
	SET ae.DateRejected = timestamp()
	SET ae.RejectedBy = {adminalias}
	SET o.Status = 'Active'",
	["adoptionid"=>$AdoptionID,"reason"=>$Reason,"adminalias"=>$AdminAlias]);
}

function checkIfAdoptionHasBeenCompleted($EntryID,$OrphanID)
{
	$session = buildCon();
	$query = $sesson->run("MATCH (o:OrphanEntry{OrphanID: toInt({orphanid})})-[:Is_Orphan_Of]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	RETURN o.Status as Status",
	["orphanid"=>$OrphanID,"entryid"=>$EntryID]);
	$record = $session->getRecord();
	if($record->value("Status") == ' Completed')
		return true;
	else
		return false;
}

function getEntryAdoption($EntryID)
{
	$session = buildCon();
	$query = $sesson->run("MATCH (sender:Person)-[ae:Adopted_Entry{Status: 'Completed'}]-(me:MAXEntry{DateCreated: toInt({entryid}) })
	RETURN ae.URL as URL,
	sender.Alias as Sender",["entryid"=>$EntryID]);
	$record = $session->getRecord();
}

function getCountOfOrphanEntries()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (o:OrphanEntry)
WHERE NOT o.Status IN ['Claimed','Completed']
	RETURN COUNT(DISTINCT o) as AdoptableCount");
	$record = $query->getRecord();
	return $record->value("AdoptableCount");
}

function getOriginalSenderOfEntry($EntryID)
{
	$session = buildCon();
	$query = $session->run("Optional MATCH (sender:Person)-[:Was_Matched_Up]->(me:MAXEntry{DateCreated: toInt({entryid}) })
	return sender.Alias as Alias",["entryid"=>$EntryID]);
	$record=  $query->getRecord();
	return $record->value("Alias");
}
	
	
function getActiveAdoptionForUser($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias } })-[ae:Adopted_Entry]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	WHERE ae.Status IN ['Claimed','Submitted']
	return 
	ae.URL as URL,
	me.DateCreated as EntryID,
	ae.DateCreated as AdoptionID,
	me.CharacterPreferred as CharacterID,
	receiver.Alias as Receiver,
	ae.Status as Status,
	o.OrphanID as OrphanID",
	["alias"=>$Alias]);
	return $query->getRecord();
}

function checkIfUserHasAnyCompletedAdoptions($Alias)
{
	$session = buildCon();
	$query =$session->run("MATCH (p:Person{Alias: {alias}})-[ae:Adopted_Entry{Status:'Completed'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)
	RETURN COUNT(Distinct ae) as CompletedCount",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("CompletedCount") >0)
		return true;
	else
		return false;
}

function checkIfUserHasAnyClaimedAdoptions($Alias)
{
	$session = buildCon();
	$query =$session->run("MATCH (p:Person{Alias: {alias}})-[ae:Adopted_Entry]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)
	WHERE ae.Status IN ['Claimed','Submitted']
	RETURN COUNT(Distinct ae) as claimedCount",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("claimedCount") >0)
		return true;
	else
		return false;
}

function checkIFUserHasAnyRejectedAdoptions($Alias)
{
	$session = buildCon();
	$query =$session->run("MATCH (p:Person{Alias: {alias}})-[ae:Adopted_Entry]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)
	WHERE ae.Status = 'Rejected'
	RETURN COUNT(Distinct ae) as rejectedCount",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("rejectedCount") >0)
		return true;
	else
		return false;
}

function getAllRejectedAdoptions()
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[ae:Adopted_Entry{Status: 'Rejected'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	RETURN DISTINCT
	ae.RejectedBy as RejectedAdmin,
	ae.URL as URL,
	ae.Reason as Reason,
	ae.DateRejected as DateRejected,
	ae.DateCreated as AdoptionID,
	sender.Alias as Alias,
	ae.CharacterSubmitted as SubmittedCharacter,
	me.PreferredCharacter as PreferredCharacter,
	receiver.Alias as Receiver");
	return $query;
}

function getAllApprovedAdoptions()
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[ae:Adopted_Entry{Status: 'Completed'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	RETURN DISTINCT
	ae.ApprovedBy as ApprovedBy,
	ae.URL as URL,
	ae.DateAccepted as DateApproved,
	ae.DateCreated as AdoptionID,
	ae.Comments as Comments,
	sender.Alias as Alias,
	ae.CharacterSubmitted as SubmittedCharacter,
	me.PreferredCharacter as PreferredCharacter,
	receiver.Alias as Receiver");
	return $query;
}

function acceptAdoptionSubmission($Alias,$AdoptionID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (sender:Person)-[ae:Adopted_Entry{DateCreated: toInt({adoptionid}) }]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	SET ae.Status ='Completed'
	SET ae.DateAccepted = timestamp()
	SET ae.ApprovedBy = {alias}
	SET o.Status = 'Completed'
	SET o.DateSubmitted = ae.DateSubmitted
	SET o.URL = ae.URL
	SET o.CharacterSubmitted = ae.CharacterSubmitted
	SET o.Comments = ae.Comments
	SET o.DateAccepted = timestamp()
	WITH o
	MATCH (p:Person{Alias: {alias}})
	CREATE (p)-[:Approved_Adoption{DateCreated: timestamp()}]->(o)",
	["adoptionid"=>$AdoptionID,"alias"=>$Alias]);
	
}

function getRejectedAdoptionDetails($AdoptionID)
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[ae:Adopted_Entry{DateCreated: toInt({adoptionid}) }]->(o:OrphanEntry)
	RETURN 
	ae.RejectedBy as AdminAlias,
	ae.Reason as Reason",
	["adoptionid"=>$AdoptionID]);
	return $query->getRecord();
}

function getAllUserRejectedAdoptions($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person{Alias: {alias} })-[ae:Adopted_Entry{Status: 'Rejected'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	RETURN DISTINCT
	ae.RejectedBy as RejectedAdmin,
	ae.URL as URL,
	ae.Reason as Reason,
	ae.DateRejected as DateRejected,
	ae.DateCreated as AdoptionID,
	ae.Comments as Comments,
	sender.Alias as Alias,
	ae.CharacterSubmitted as SubmittedCharacter,
	me.PreferredCharacter as PreferredCharacter,
	receiver.Alias as Receiver",
	["alias"=>$Alias]);
	return $query;
}

function getAllUserApprovedAdoptions($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person{Alias: {alias} })-[ae:Adopted_Entry{Status: 'Completed'}]->(o:OrphanEntry)-[:Is_Orphan_Of]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	RETURN DISTINCT
	ae.ApprovedBy as ApprovedBy,
	ae.URL as URL,
	ae.DateAccepted as DateApproved,
	ae.DateCreated as AdoptionID,
	ae.Comments as Comments,
	sender.Alias as Alias,
	ae.CharacterSubmitted as SubmittedCharacter,
	me.PreferredCharacter as PreferredCharacter,
	receiver.Alias as Receiver",
	["alias"=>$Alias]);
	return $query;
}

function checkIfCharacterHasAnySubmittedArt($CharacterID)
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[r{Status: 'Completed'}]->(n)-[]->(m:MAX{Status: 'Completed'})
	WHERE toInt(n.CharacterSubmitted) = toInt({characterid})
	return count(DISTINCT n) as Exists",
	["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	if($record->value("Exists") > 0)
		return true;
	else
		return false;
}

function getAllSubmittedCharacterArt($CharacterID)
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[r{Status: 'Completed'}]->(n)-[]->(m:MAX{Status: 'Completed'})
	WHERE toInt(n.CharacterSubmitted) = toInt({characterid})
	return DISTINCT 
	n.OrphanID as OrphanID,
	n.URL as URL,
	n.DateCreated as DateCreated,
	sender.Alias as Sender,
	n.Comments as Comments
	ORDER BY n.DateCreated DESC",
	["characterid"=>$CharacterID]);
	return $query;
}

function saveCharacterEdits($CharacterID, $Name, $Age, $Gender, $Race, $Hair, $Eyes, $Height, $Weight, $WriteUp, $ComicID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid})})
	SET c.Name = {name}
	SET c.Age = {age}
	SET c.Gender = {gender}
	SET c.Race = {race}
	SET c.Hair = {hair}
	SET c.Eyes = {eyes}
	SET c.Height = {height}
	SET c.Weight = {weight}
	SET c.WriteUp = {writeup}",
	["characterid"=>$CharacterID,
	"name"=>$Name,
	"age"=>$Age,
	"gender"=>$Gender,
	"race"=>$Race,
	"hair"=>$Hair,
	"eyes"=>$Eyes,
	"height"=>$Height,
	"weight"=>$Weight,
	"writeup"=>$WriteUp]);
	
	$CurrentWebcomic = getCharacterWebcomic($CharacterID);
	$CurrentWebcomicID = $CurrentWebcomic->value("ComicID");
	
	if($ComicID == 'None')
	{
		clearCharacterOfWebcomics($CharacterID);
	}
	elseif((int)$CurrentWebcomicID != (int)$ComicID)
	{
		clearCharacterOfWebcomics($CharacterID);
		assignWebcomicToCharacter($ComicID, $CharacterID);
	}
	else
	{
		
	}
}

function getCharacterWebcomic($CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })-[r:Is_Character_Of]-(w:Webcomic)
	return w.ComicID as ComicID,
	w.Name as Name,
	w.URL as URL",["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	return $record;
}

function clearCharacterOfWebcomics($CharacterID)
{
	$session = buildAdminCon();
	$clearComic = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })-[r:Is_Character_Of]-(w:Webcomic)
		DELETE r",["characterid"=>$CharacterID]);
}

function retireCharacter($CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })
	SET c.Status = 'Retired'",
	["characterid"=>$CharacterID]);
}

function reviveCharacter($CharacterID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })
	SET c.Status = 'Active'",
	["characterid"=>$CharacterID]);
}

function getCountOfAllPendingLateEntries()
{
	$session = buildCon();
	$query = $session->run("MATCH (me:MAXEntry)-[r:Submitted_Entry]-(sender:Person)
	WHERE r.Status = 'Pending'
	return count(DISTINCT r) as pendingCount");
	$record = $query->getRecord();
	return $record->value("pendingCount");	
}

function getAllPendingLateEntries()
{
	$session = buildCon();
	$query = $session->run("MATCH (sender:Person)-[r:Submitted_Entry{Status: 'Pending'}]->(me:MAXEntry)-[:Is_Entry_For]->(receiver:Person)
	return DISTINCT
	me.Comments as Comments,
	me.DateCreated as EntryID,
	me.URL as URL,
	sender.Alias as Sender,
	receiver.Alias as Receiver
	");
	return $query;
}

function checkIfLateEntryIsStillPending($EntryID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sender:Person)-[r:Submitted_Entry]->(me:MAXEntry{DateCreated: toInt({entryid}) })-[:Is_Entry_For]->(receiver:Person)
	RETURN r.Status as Status",["entryid"=>$EntryID]);
	$record = $query->getRecord();
	if($record->value("Status") == 'Pending')
	{
		return true;
	}
	else
		return false;
}

function checkIfAdoptionIsStillPending($AdoptionID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)-[:AdoptedEntry{DateCreated: toInt({adoptionid})}]->(o:OrphanEntry)
	RETURN ae.Status as Status",["orphanid"=>$OrphanID]);
	$record = $query->getRecord();
	if($record->value("Status") == 'Submitted')
	{
		return true;
	}
	else
		return false;
}

function approveLateEntry($EntryID, $AdminAlias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (me:MAXEntry{DateCreated: toInt({entryid}) })-[r:Submitted_Entry{Status: 'Pending'}]-(sender:Person)
	set me.Status = 'Completed'
	set r.Status = 'Completed'
	set me.URL = r.URL
	set me.CharacterSubmitted = r.CharacterSubmitted
	SET me.DateSubmitted = r.DateSubmitted
	SET me.Comments = r.Comments
	WITH me
	MATCH (p:Person{Alias: {alias}})
	CREATE (p)-[:Approved_Late_Entry{DateCreated: timestamp()}]->(me)",["entryid"=>$EntryID,"alias"=>$AdminAlias]);
}

function rejectLateEntry($EntryID, $AdminAlias, $Reason)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (me:MAXEntry{DateCreated: toInt({entryid})})-[r:Submitted_Entry{Status: 'Pending'}]-(sender:Person)
	SET me.Status ='Rejected'
	SET r.Status = 'Rejected'
	SET r.RejectedReason = {reason}
	SET r.RejectedBy = {alias}
	WITH me
	MATCH (p:Person{Alias: {alias}})
	CREATE (p)-[rl:Rejected_Late_Entry{DateCreated: timestamp(), Reason: {reason} }]->(me)
	WITH me, rl
	SET rl.URL = me.URL
	WITH me
	REMOVE me.URL",
	["entryid"=>$EntryID,"alias"=>$AdminAlias,"reason"=>$Reason]);
}


function getCountOfCompletedMAXRounds()
{
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{Status:'Completed'})
	return COUNT(m) as MAXCount");
	$record = $query->getRecord();
	return $record->value("MAXCount");
}


function clearUserFromBlacklistForLateSubmission($EntryID,$AdminAlias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (b:Blacklist{EntryID: toInt({entryid}) })
	set b.Status = 'Resolved'
	set b.ReasonResolved = 'User submitted acceptable entry late'
	WITH b
	MATCH (admin:Person{Alias: {alias} })
	CREATE (admin)-[:Marked_As_Resolved{DateCreated: timestamp()}]->(b)",
	["entryid"=>$EntryID,"alias"=>$AdminAlias]);
	
}


function getCountOfPendingMAXRounds()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:MAX{Status: 'Pending'})
	return count(m) as MAXCount");
	$record = $query->getRecord();
	return $record->value("MAXCount");
}

function doesUserHaveAnyRoundsIncomplete($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH counter=((p:Person{Alias: {alias}})-[:Was_Matched_Up]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX{Status: 'Completed'}))
	WHERE NOT EXISTS((p)-[:Submitted_Entry{Status: 'Completed'}]->(me))
	RETURN count(counter) as IncompleteRounds",["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("IncompleteRounds") > 0)
		return true;
	else
		return false;
}

function getUserIncompleteRoundsEntryIDs($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})-[:Was_Matched_Up]->(me:MAXEntry)-[:Was_Submitted_For]->(m:MAX)
	WHERE NOT EXISTS((p)-[:Submitted_Entry{Status: 'Completed'}]->(me))
	RETURN DISTINCT m.DateCreated as MAXID",["alias"=>$Alias]);
	return $query;
}

function checkIfReferencesAlreadyRequestedForCharacter($CharacterID, $Status)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })-[rr:Requested_Reference]-(p:Person)
	WHERE rr.Status IN {status}
	return count(p) as requestCount",["characterid"=>$CharacterID,"status"=>$Status]);
	$record = $query->getRecord();
	if($record->value("requestCount") > 0)
		return true;
	else
		return false;
}

function userSubmitRequestForMoreReferences($Requester, $CharacterID, $Reason)
{
	$session = buildAdminCon();
	$session->run("MATCH (p:Person{Alias: {alias} }), (c:Character{CharacterID: toInt({characterid}) })
	CREATE (p)-[:Requested_Reference{DateCreated: timestamp(), Reason: {reason}, Status: 'Requested'}]->(c)",["characterid"=>$CharacterID,"reason"=>$Reason,"alias"=>$Requester]);
}

function getCharacterOwnerAliasByID($CharacterID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })-[:Belongs_To]->(p:Person)
	return p.Alias as Alias",["characterid"=>$CharacterID]);
	$record = $query->getRecord();
	return $record->value("Alias");
}

function adminRequestForMoreReferences($Receiver, $AdminAlias, $CharacterID, $Reason, $Sender)
{
	$CharacterName = getCharacterNameByID($CharacterID);
	
	$Subject = "Reference request for $CharacterName";
	$Body = "Hello $Receiver,<br><br>This is a semi-automated message from Comicadia.<br>";
    $Body = $Body . "There has been a request to add more references for <a href='https://www.comicadia.com/MAX/inmdex.php?CharacterID=$CharacterID&Fields=Characters'>$CharacterName</a>. <br>";
    $Body = $Body . "The request came with the following additional information:<br>";
    $Body = $Body . "$Reason <br>";
    $Body = $Body . "An admin has reviewed the request and agree that more references would be helpful for the user who received your name for the current MAX round.<br>";
    $Body = $Body . "If you could access <a href='https://www.comicadia.com/MAX'>MAX</a> and add another reference, it would be greatly appreciated and we are sure it will assist the user who received your name in better realizing your character.";
	$Body = $Body . "<br><br>The Comicadia Team";
	$User = getUserDetails($Receiver);
	$Email = $User->value("Email");
	sendEmailFromNoReply($Email, $Subject, $Body);
	setRequestForReferenceStatusForUser($CharacterID, $Sender, "Sent");
}

function adminRejectRequestForMoreReferences($Receiver, $AdminAlias, $CharacterID, $Reason)
{
	$CharacterName = getCharacterNameByID($CharacterID);
	
	$Subject = "Reference request for $CharacterName";
	$Body = "Hello $Receiver,<br><br>This is a semi-automated message from Comicadia.<br>";
    $Body = $Body . "You had made a request to add more references for <a href='https://www.comicadia.com/MAX/inmdex.php?CharacterID=$CharacterID&Fields=Characters'>$CharacterName</a>. <br>";
    $Body = $Body . "The request came with the following additional information:<br>";
    $Body = $Body . "$Reason <br>";
    $Body = $Body . "An admin has reviewed the request and has decided to deny the forwarding of the request to the owner of the character.<br>";
    $Body = $Body . "If you believe that your request is valid, please do not hesitate to seek out one of the admin on the discord. Any one of us would be more than happy to help resolve this issue with you.";
	$Body = $Body . "<br><br>The Comicadia Team";
	$User = getUserDetails($Receiver);
	$Email = $User->value("Email");
	sendEmailFromNoReply($Email, $Subject, $Body);
	setRequestForReferenceStatusForUser($CharacterID, $Receiver, "Rejected");
}

function setRequestForReferenceStatusForUser($CharacterID, $Sender, $Status)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (c:Character{CharacterID: toInt({characterid}) })<-[rr:Requested_Reference{Status: 'Requested'}]-(p:Person{Alias: {alias} })
	SET rr.Status = {status}",
	["characterid"=>$CharacterID,"alias"=>$Sender,"status"=>$Status]);
}

 function getCountOfReferenceRequests()
 {
	 $session = buildCon();
	 $query = $session->run("MATCH n=(c:Character)<-[:Requested_Reference{Status: 'Requested'}]-(p:Person)
	 RETURN count(n) as RequestCount");
	 $record = $query->getRecord();
	 return $record->value("RequestCount");
 }
 
 function getAllPendingRequestedReferences()
 {
	 $session = buildCon();
	 $query = $session->run("MATCH (owner:Person)<-[:Belongs_To]-(c:Character)<-[rr:Requested_Reference{Status: 'Requested'}]-(p:Person)
	 return rr.Status as Status,
	 c.CharacterID as CharacterID,
	 rr.Reason as Reason,
	 p.Alias as Requester,
	 owner.Alias as Owner,
	 rr.DateCreated as RequestID");
	 return $query;
 }
 
 function getCharacterReferences($CharacterID)
 {
	 $session = buildCon();
	 $query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[:Is_Reference_For]-(r:Reference{Status: 'Approved'})
	 return r.URL as URL,
	 r.Status as Status",
	 ["characterid"=>$CharacterID]);
	 return $query;
 }
 
 function getAllSentRequestsForReferencesForCharacterByID($CharacterID)
 {
	$session = buildCon();
	$query = $session->run("MATCH (c:Character{CharacterID: toInt({characterid}) })<-[rr:Requested_Reference{Status: 'Requested'}]-(p:Person)
	RETURN	rr.Reason as Reason,
	p.Alias as Person",
	["characterid"=>$CharacterID]);
	return $query;
 }
 
 
 function adminSendMAXReminder($MAXID, $Alias)
 {
	$session = buildCon();
	$currentMAX =  getCurrentMAXRound();
	$EndDate = $currentMAX->value("EndDate");
	$Deadline = date('F jS, Y', $EndDate/1000);
	$Subject = "MAX Round end date is approaching";
	$UnsubmittedCount = getCountOfUnsubmittedEntriesForMAX($MAXID);
	$ReminderList = getMembersWhoHaveNotSubmittedEntryForMAX($MAXID);
	foreach($ReminderList->getRecords() as $Person)
	{
		$Email = $Person->value("Email");
		$UserAlias = $Person->value("Alias");
		$Text = "Hello $UserAlias,<br><br>";
		$Text = $Text . "The MAX round is coming to a close soon and the system has not received your entry, yet.<br>";
		$Text = $Text . "Please log into <a href='https://www.comicadia.com/MAX'>MAX</a> and upload your entry before $Deadline to avoid being put on the blacklist.<br><br>";
		$Text = $Text . "The Comicadia Team";
		sendEmailFromNoReply($Email, $Subject, $Text);
	}
	setMAXRoundReminderStatusAsSent($MAXID, $Alias);	
 }
 
 function setMAXRoundReminderStatusAsSent($MAXID, $Alias)
 {
	 $session = buildAdminCon();	 
	 $query = $session->run("MATCH (p:Person{Alias: {alias}}),(m:MAX{DateCreated: toInt({maxid}) })
	 CREATE (p)-[:Sent_Reminder{DateCreated: timestamp()}]->(m)",
	 ["alias"=>$Alias,"maxid"=>$MAXID]);
 }
 
 function checkIfReminderHasBeenSent($MAXID)
 {
	 $session = buildCon();
	 $query = $session->run("OPTIONAL MATCH n=(m:MAX{DateCreated: toInt({maxid}) })<-[:Sent_Reminder]-(p:Person)
	 return count(n) as ReminderCount",
	 ["maxid"=>$MAXID]);
	 $record = $query->getRecord();
	 if($record->value("ReminderCount") > 0)
		 return true;
	 else
		 return false;
 }
 
 function getMembersWhoHaveNotSubmittedEntryForMAX($MAXID)
 {
	$session = buildCon();
	$ReminderList = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid})})<-[:Was_Submitted_For]-(me:MAXEntry)-[:Is_Entry_For]->(p:Person)
	WHERE NOT EXISTS ((me)<-[:Submitted_Entry]-())
	WITH me
	MATCH (me)-[:Was_Matched_Up]-(p2:Person)
	return p2.Alias as Alias, p2.Email as Email",["maxid"=>$MAXID]);
	return $ReminderList;
 }
 
 function getCountOfUnsubmittedEntriesForMAX($MAXID)
 {
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX{DateCreated: toInt({maxid})})<-[:Was_Submitted_For]-(me:MAXEntry)-[:Is_Entry_For]->(p:Person)
	WHERE NOT EXISTS ((me)<-[:Submitted_Entry]-())
	WITH me
	MATCH result=(me)-[:Was_Matched_Up]-(p2:Person)
	return count(result) as UnsubmittedCount",["maxid"=>$MAXID]);
	$record = $query->getRecord();
	return $record->value("UnsubmittedCount");
 }
 
?>