<?php

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

function getValidAdTypesArray()
{
	return ["Square","Leaderboard","Skyscraper"];
}

function getValidAdTypes()
{
	$ValidAdTypes = getValidAdTypesArray();
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType) 
	WHERE mt.Name IN {adtypes}
	RETURN mt.Name as Name,
	mt.Height as Height,
	mt.Width as Width",["adtypes"=>$ValidAdTypes]);
	return $query;
}


function getCountOfAdsByAlias($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Owns_Ad]->(a:Advertisement)
	RETURN count(a) as AdsOwned",["alias"=>$Alias]);
	$record = $query->getrecord();
	return $record->value("AdsOwned");
}


function getAdsOfAlias($Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}})-[:Owns_Ad]->(a:Advertisement)-[:Is_Ad_For]->(e)
	WITH a 
	MATCH (mt:MediaType)<-[:Uses_Type]-(a)-[:Uses_Media]->(m:Media)
	RETURN a.AdID as AdID,
	a.Name as AdName,
	a.URL as AdLink,
	a.Status as Status,
	mt.Type as Type,
	collect(m.URL) as URL",["alias"=>$Alias]);
	return $query;
}

function getAdTotalViewCountByAdID($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })<-[:Is_Media_For]-(m:Media)-[v:Was_Viewed_On]->(n)
	return count(v) as Views",["adid"=>$AdID]);
	$record = $query->getRecord();
	return $record->value("Views");
}

function getAdTotalClickCountByAdID($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })<-[:Is_Media_For]-(m:Media)-[c:Got_Clicked_On]->(n)
	return count(c) as Clicks",["adid"=>$AdID]);
	$record = $query->getRecord();
	return $record->value("Clicks");
}


function getMediaDetails($MediaURL)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:Media{URL: {url} })
	RETURN m.Status as Status",["url"=>$MediaURL]);
	return $query->getRecord();
}

function getMediaTotalViews($MediaURL)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:Media{URL: {url} })-[v:Was_Viewed_On]->(n)
	RETURN COUNT(v) as Totalviews",["url"=>$MediaURL]);
	$record = $query->getRecord();
	return $record->value("Totalviews");
}

function getMediaTotalClicks($MediaURL)
{
	$session = buildCon();
	$query = $session->run("MATCH (m:Media{URL: {url} })-[c:Got_Clicked_On]->(n)
	RETURN COUNT(c) as TotalClicks",["url"=>$MediaURL]);
	$record = $query->getRecord();
	return $record->value("TotalClicks");
}

function getAdMostRecentCampaignRunDetails($AdID)
{
	$sesion = buildCon();
	$query = $session->run("OPTIONAL MATCH (a:Advertisement{AdID: {adid} })-[rc:Ran_Campaign{Status: 'Active'}]->(ac:AdCampaign)
	return rc.StartDate as StartDate,
	rc.Status as Status,
	rc.RequestedDate as RequestedDate,
	rc.DateCreated as DateCreated,
	ac.Views as ViewsPurchased,
	ac.Cost as Cost",
	["adid"=>$AdID]);
	return $query->getRecord();
}

function getViewsFromDateToNowOfAdByID($AdID, $DateStarted)
{
	$session = buildcCon();
	$query = $session->run("MATCH (ac:AdCampaign)<-[rc:Ran_Campaign{Status: 'Active'}]-(a:Advertisement{AdID: toInt({adid}) })<-[:Is_Media_For]-(m:Media)[v:Was_Viewed_On]->(n)
	WHERE toInt(v.TimeViewed) > toInt({datestarted})
	return count(v) as views",
	["adid"=>$AdID,"datestarted"=>$DateStarted]);
	$record = $query->getRecord();
	return $record->value("views");
}

function getClicksFromDateToNowOfAdByID($AdID, $DateStarted)
{
	$session = buildcCon();
	$query = $session->run("MATCH (ac:AdCampaign)<-[rc:Ran_Campaign{Status: 'Active'}]-(a:Advertisement{AdID: toInt({adid}) })<-[:Is_Media_For]-(m:Media)[c:Got_Clicked_On]->(n)
	WHERE toInt(c.TimeClicked) > toInt({datestarted})
	return count(c) as clicks",
	["adid"=>$AdID,"datestarted"=>$DateStarted]);
	$record = $query->getRecord();
	return $record->value("clicks");
}

function getEntitiesOfUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Works_On]->(n)
	WHERE n:Webcomic OR n:Entity
	return n.ComicID as ComicID,
	n.EntityID as EntityID,
	n.Name as Name
	ORDER BY n.Name ASC",
	["alias"=>$Alias]);
	return $query;
}

function identifyEntity($IDNumber)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (n)
	WHERE n:Entity OR n:Webcomic
	AND (n.EntityID = toInt({idnumber}) OR n.ComicID = toInt({idnumber}))
	return n.ComicID as ComicID,
	n.EntityID as EntityID",["idnumber"=>$IDNumber]);
	$record = $query->getRecord();
	$ComicID = $record->value("ComicID");
	$EntityID = $record->value("EntityID");
	if($ComicID == '' AND $EntityID != '')
	{
		return "Entity";
	}
	elseif($ComicID != '' AND $EntityID == '')
	{
		return "Comic";
	}
	elseif($ComicID == '' AND $EntityID == '')
	{
		return "Not valid";
	}
	else
	{
		return "Unidentified";
	}
}

function createNewEntity($Alias, $URL, $Name)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })
	CREATE (e:Entity{Name: {name}, URL: {url}, DateCreated: timestamp() })<-[:Works_On{Role: 'Creator'}]-(p)",
	["alias"=>$Alias,"name"=>$Name,"url"=>$URL]);
}

function checkIfEntityNameExists($Name)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (e)
	WHERE e:Webcomic OR e:Entity
	AND toLower(e.Name) = toLower({name})
	RETURN count(e) as Exists",
	["name"=>$Name]);
	$record = $query->getRecord();
	if($record->value("Exists") > 0)
		return true;
	else
		return false;
}


function getCountOfAllPotentialMediaForEntityByIDAndType($ID, $Type)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (n)<-[:Is_Media_For]-(m:Media)-[:Is_Media_Of]->(mt:MediaType{Name: {adtype}})
	WHERE (n:Webcomic OR n:Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	RETURN count(m) as mediaCount",
	["adtype"=>$Type,"id"=>$ID]);
	$record = $query->getRecord();
	return $record->value("mediaCount");
	
}

function getAllPotentialMediaForEntityByIDAndType($ID, $AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (n)<-[:Is_Media_For]-(m:Media)-[:Is_Media_Of]->(mt:MediaType{Name: {adtype}})
	WHERE (n:Webcomic OR n:Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	RETURN m.URL as URL,
	m.Status as Status,
	m.Alt as Alt",
	["adtype"=>$AdType,"id"=>$ID]);
	return $query;
}

function addMediaForEntity($AdType, $ImgURL, $EntityID, $Alias, $Desc)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (e)
	where ((e:Webcomic) OR (e:Entity) )
	AND	(e.ComicID = toInt({id}) OR e.EntityID = toInt({id} ))
	WITH e
	MATCH (p:Person{Alias: {alias} })
	create (p)<-[:Was_Uploaded_By{DateCreated: timestamp()}]-(m:Media{URL: {imgurl}, Alt: {desc}, Status: 'Active' })-[:Is_Media_For]->(e)
	WITH m
	MATCH (mt:MediaType{Name: {adtype} })
	CREATE (m)-[:Is_Media_Of]->(mt)",
	["id"=>$EntityID,"alias"=>$Alias,"imgurl"=>$ImgURL,"desc"=>$Desc,"adtype"=>$AdType]);
}

function getCampaigns()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ac:AdCampaign)
	return ac.Name as Name,
	ac.CampaignID as CampaignID,
	ac.Cost as Cost,
	ac.Views as Views
	ORDER BY ac.Cost ASC");
	return $query;
}


function createNewAd($AdName, $AdType, $ImgURLs, $Alias, $EntityID, $ADLink)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} }),
	(mt:MediaType{Name: {adtype} })
	CREATE (p)-[:Owns_Ad]->(a:Advertisement{Name: {adname}, Status: 'Pending', DateCreated: timestamp(), URL: {adlink} })-[:Uses_Type]->(mt)
	WITH a
	MATCH (e)
	WHERE (e:Webcomic OR e:Entity)
	AND (e.ComicID = toInt({entityid}) OR e.EntityID = toInt({entityid}))
	CREATE (a)-[:Is_Ad_For]->(e)
	WITH toString(toInt(round(rand() * 100))) as Last,
		toString(timestamp()) as Stamp, a
	set a.AdID = toInt(Stamp + Last)
	With a
	MATCH (m:Media)
	WHERE m.URL IN {imgurls}
	CREATE (a)-[:Uses_Media]->(m)",
	["alias"=>$Alias,"adtype"=>$AdType,"imgurls"=>$ImgURLs,"adname"=>$AdName,"entityid"=>$EntityID,"adlink"=>$ADLink]);
}

function getEntityURLByID($EntityID)
{
	$session = buildCon();
	$query = $session->run("MATCH (e)
	WHERE ((e:Webcomic) OR (e:Entity) )
	AND (e.ComicID = toInt({entityid}) OR (e.EntityID = toInt({entityid}) ) )
	return e.URL as URL",
	["entityid"=>$EntityID]);
	$record = $query->getRecord();
	return $record->value("URL");
}

function deleteAd($AdID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })
	DETACH DELETE a",
	["adid"=>$AdID]);
}


function getRejectionReasonForAd($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })<-[r:Rejected_Ad]-(p)
	RETURN r.Reason as Reason,
	r.DateCreated as DateRejected",
	["adid"=>$AdID]);
	return $query->getRecord();
	
}

function getCountOfEntities($Alias)
{
	
}
	
function getEntitiesforUser($Alias)
{
	$session = buildCon();
	$query= $session->run("MATCH (p:Person{Alias: {alias}})-[wo:Works_On]->(e)
	RETURN e.ComicID as ComicID,
	e.EntityID as EntityID,
	e.Name as Name,
	wo.Role as Role",
	["alias"=>$Alias]);
	return $query;
}

function getAvailableAdBlockCountForUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Owns_Ad]->(a:Advertisement{Status: 'Approved'})-[:Is_Ad_For]->(e)
	WHERE NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Pending Payment'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Pending Review'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Active'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Paused'}]->(:AdCampaign) )
	RETURN count(a) as adCount",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("adCount");
}
	
function getAvailableAdBlocksForUser($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Owns_Ad]->(a:Advertisement{Status: 'Approved'})-[:Is_Ad_For]->(e)
	WHERE NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Pending Payment'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Pending Review'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Active'}]->(:AdCampaign) )
	AND NOT EXISTS ( (a)-[:Ran_Campaign{Status: 'Paused'}]->(:AdCampaign) )
	RETURN a.AdID as AdID,
	a.Name as Name",
	["alias"=>$Alias]);
	return $query;
}

function checkIfUserHasCreatorRole($RoleArray)
{
	$IsCreator = false;
	foreach($RoleList as $Role)
		{
			if($Role == 'Creator' || $Role == 'Co-Creator')
			{
				$IsCreator = true;
			}
		}
	return $IsCreator;
}

function checkIfAdIsInTransit($AdID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH p=(a:Advertisement{AdID: toInt({adid}) })-[rc:Ran_Campaign]->(ac)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	return count(p) as Status",["adid"=>$AdID]);
	$record = $query->getRecord();
	if($record->value("Status") > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getCountOfAdsInTransit($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(p:Person{Alias: {alias} })-[:Owns_Ad]->(a:Advertisement)-[rc:Ran_Campaign]->(ac)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	RETURN count(n) as InTransit",
	["alias"=>$Alias]);
	$record = $query->getRecord();
	return $record->value("InTransit");
}

function getAdCampaignStatus($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })-[rc:Ran_Campaign]->(ac:AdCampaign)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	RETURN rc.Status as Status",
	["adid"=>$AdID]);
	$record = $query->getRecord();
	return $record->value("Status");
}

function submitAdCampignForReview($AdID, $CampaignID, $StartDate)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (a:Advertisement{AdID: toInt({adid}) }), (ac:AdCampaign{CampaignID: toInt({campaignid}) })
	CREATE (a)-[:Ran_Campaign{Status: 'Pending Review', DateCreated: timestamp(), RequestedDate: toInt({startdate}) }]->(ac)",
	["adid"=>$AdID,"campaignid"=>$CampaignID,"startdate"=>$StartDate]);
}

function getUserCampaignsInTransit($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(p:Person{Alias: {alias} })-[:Owns_Ad]->(a:Advertisement)-[rc:Ran_Campaign]->(ac:AdCampaign)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	WITH a, ac, rc
	MATCH (a)-[:Uses_Type]->(mt:MediaType)
	RETURN rc.Status as Status,
	rc.DateCreated as DateCreated,
	rc.RequestedDate as RequestedDate,
	a.Name as AdName,
	a.AdID as AdID,
	mt.Name as AdType,
	ac.Name as CampaignName,
	ac.Cost as Cost,
	ac.Views as Views",
	["alias"=>$Alias]);
	return $query;
}

function getAllActiveAdsByAlias($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Works_On]->(w:Webcomic)<-[:Is_Ad_For]-(a:Advertisement)-[:Is_Advertisement_For]->(ar:AdRun)-[:Is_Using_Campaign]->(ac:AdCampaign)
	WHERE ar.Status IN ['Active','Paused']
	RETURN 
	ar.DateCreated as AdRunID, 
	ac.Name as CampaignName,
	ar.ViewsPurchased as PurchasedViews,
	ar.RequestedDate as RequestedDate 
	ORDER BY ar.DateCreated ASC",["alias"=>$Alias]);
	return $query;
}


function getViewsForAdRun($AdRunID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ar:AdRun{DateCreated: toInt({adrunid}) })-[:Received_View]-(v:View)
	RETURN COUNT(v) as CurrentViews",["adrunid"=>$AdRunID]);
	$result = $query->getRecord();
	return $result->value("CurrentViews");
}

function checkIfAnyComicsAvailableThatTheUserIsNotAMemberOf($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)
	WHERE NOT EXISTS( (as:AdSpace)<-[:Placed_Bid]-(:Person{Alias: {alias} }) )
	AND as.Status = 'Active'
	AND NOT EXISTS ((:Person{Alias: {alias}})-[:Works_On]->(w))
	RETURN COUNT(as) as Exists",["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("Exists") >0)
		return true;
	else
		return false;
}

function getComicsUserHasNoBidsOn($Alias,$StartResult,$NumberOfResultsPerPage)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)
	WHERE NOT EXISTS( (as:AdSpace)<-[:Placed_Bid]-(:Person{Alias: {alias} }) )
	AND as.Status = 'Active'
	AND NOT EXISTS ((:Person{Alias: {alias}})-[:Works_On]->(w))
	RETURN w.URL as URL,
	w.Name as Name,
	w.ComicID as ComicID,
	as.AdSpaceID as AdSpaceID
	ORDER BY w.Name ASC SKIP {start} LIMIT {limit}",["alias"=>$Alias,"start"=>$StartResult, "limit"=>$NumberOfResultsPerPage]);
	return $query;
}

function isUserCurrentlyBiddingOnComics($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person)
	WHERE p.Alias = {alias}
	AND as.Status = 'Active'
	AND b.Status = 'Active'
	RETURN COUNT(n) as Exists",["alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("Exists")>0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getAllWebcomicsUserIsCurrentlyBiddingOn($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person)
	WHERE p.Alias = {alias}
	AND as.Status = 'Active'
	AND b.Status = 'Active'
	WITH as, w, b
	MATCH (as)-[:Uses_Type]->(mt:MediaType)
	RETURN w.URL as URL,
	w.Name as Name,
	w.ComicID as ComicID,
	as.AdSpaceID as AdSpaceID,
	b.Status as BidStatus,
	b.CurrentBid as CurrentBid,
	b.MaxBid as MaxBid,
	mt.Name as Type
	ORDER BY w.Name ASC",["alias"=>$Alias]);
	return $query;
}

function getAllWebcomicsUserHasPausedBidsOn($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person)
	WHERE p.Alias = {alias}
	AND as.Status = 'Active'
	AND b.Status = 'Paused'
	WITH as, w, b
	MATCH (as)-[:Uses_Type]->(mt:MediaType)
	RETURN w.URL as URL,
	w.Name as Name,
	w.ComicID as ComicID,
	as.AdSpaceID as AdSpaceID,
	b.Status as BidStatus,
	b.CurrentBid as CurrentBid,
	b.MaxBid as MaxBid,
	mt.Name as Type
	ORDER BY w.Name ASC",["alias"=>$Alias]);
	return $query;
}	
function IsUserCurrentlyWinningBidOnAdSpace($Alias,$AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH rel= ((p:Person{Alias: {alias} })-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({adspaceid}) })-[:Is_Ad_On]->(w:Webcomic))
	WHERE b.Position = 'Winner'
	Return COUNT(rel) as Exists",["alias"=>$Alias,"adspaceid"=>$AdSpaceID]);
	$record = $query->getRecord();
	$Winner = $record->value("Exists");
	if($Winner > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function isUserBiddingOnSpecificComicAdSpace($AdSpaceID, $Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(p:Person)-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({adspaceid}) })-[:Is_Ad_On]->(w:Webcomic)
	WHERE toLower(p.Alias) = toLower({alias})
	AND b.Status IN ['Active', 'Paused']
	Return COUNT(n) as Exists",["adspaceid"=>$AdSpaceID,"alias"=>$Alias]);
	$record = $query->getRecord();
	if($record->value("Exists") >0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getAdSpaceDetailsForComicAdSpace($AdSpaceID, $Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person)
	WHERE p.Alias = {alias}
	AND as.Status = 'Active'
	AND toInt(as.AdSpaceID) = toInt({adspaceid})
	WITH as, b, w
	MATCH (b)-[:Is_Using_Advertisement]->(a:Advertisement)-[:Uses_Media]->(m:Media)
	WITH b, as, w
	MATCH (a)-[:Uses_Type]->(mt:MediaType)
	RETURN w.URL as URL,
	w.Name as Name,
	w.ComicID as ComicID,
	as.AdSpaceID as AdSpaceID,
	b.BidID as BidID,
	b.Status as BidStatus,
	b.CurrentBid as CurrentBid,
	b.MaxBid as MaxBid,
	b.Type as Type,
	a.AdID as AdID,
	mt.Name as MediaType
	ORDER BY w.Name ASC",["adspaceid"=>$AdSpaceID,"alias"=>$Alias]);
	return $query;
}

function getBidDetailsForAdSpace($AdSpaceID, $Alias)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({adspaceid}) })
	return b.CurrentBid as CurrentBid,
	b.MaxBid as MaxBid,
	b.Status as Status,
	b.BidID as BidID",["alias"=>$Alias,"adspaceid"=>$AdSpaceID]);
	return $query->getrecord();
}

function getCurrentWinningBidOnComicAdSpace($AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({AdSpaceID}) })-[:Is_Ad_On]->(w:Webcomic)
	WHERE b.Position = 'Winner'
	AND b.Status = 'Active'
	Return 
	p.Alias as Alias, 
	b.BidId as BidID,
	b.CurrentBid as CurrentBid",["AdSpaceID"=>$AdSpaceID]);
	$record = $query->getRecord();
	return $record;
}

function checkIfUserHasEnoughForBid($Alias, $Cost)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })
	return p.Funds as Funds",["alias"=>$Alias]);
	$record = $query->getrecord();
	$funds = $record->value("Funds");
	return compareFloatNumbers($Cost, $funds, '>');
}

function getHighestMaximumBidThatIsNotOfUser($Alias, $AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({AdSpaceID}) })-[:Is_Ad_On]->(w:Webcomic)
	WHERE toLower(p.Alias) <> toLower({alias})
	RETURN 
	b.BidID as BidID,
	b.MaxBid as Maximum ORDER BY b.MaxBid DESC LIMIT 1",["alias"=>$Alias, "adspaceid"=>$AdSpaceID]);
	return $query->getRecord();
	
}

function updateBidOfUser($Alias, $AdSpaceID,$UserBid,$MaxBid, $Status,$AdID)
{
	if($UserBid == '')
	{
		$UserBid = 0;
	}
	if($MaxBid == '')
	{
		$MaxBid == 0;
	}
	if($Status == 'None')
	{
		$Status = 'Active';
	}
	$UserBid = number_format((float)$UserBid, 2);
	$MaxBid = number_format((float)$MaxBid, 2);
	if(isUserBiddingOnSpecificComicAdSpace($AdSpaceID, $Alias))
	{
		updateCurrentBid($Alias,$AdSpaceID,$UserBid,$MaxBid, $Status,$AdID);
	}
	else
	{
		createNewUserBidForAdSpace($Alias,$AdSpaceID,$UserBid,$MaxBid, $Status,$AdID);
	}
}

function createNewUserBidForAdSpace($Alias,$AdSpaceID,$UserBid,$MaxBid, $Status,$AdID)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} }),(as:AdSpace{AdSpaceID: toInt({adspaceid}) })
	CREATE (p)-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as)
	WITH b,
	toFloat({currentbid}) AS value1,
	toFloat({maxbid}) AS value2,
	10^2 AS factor
	SET b.CurrentBid = round(factor * value1)/factor
	SET b.MaxBid = round(factor * value2)/factor
    SET b.Status = {status}
	SET b.BidID = timestamp()
	WITH b
	MATCH (a:Advertisement{AdID: toInt({adid}) })
	CREATE (a)<-[:Is_Using_Advertisement]-(b)",
	["adspaceid"=>$AdSpaceID,"alias"=>$Alias,"currentbid"=>$UserBid,"maxbid"=>$MaxBid,"status"=>$Status,"adid"=>$AdID]);
}

function getCurrentAdBeingUsedByBid($Alias, $AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b)<-[:Placed_Bid]-(p:Person{Alias: {alias} })
	WITH b
	MATCH (b)-[:Is_Using_Advertisement]->(a:Advertisement)
	RETURN a.AdID as AdID,
	a.URL as URL",["alias"]);
	return $query->getRecord();
}

function clearAdBeingUsedByBid($Alias, $AdSpaceID)
{
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b)<-[:Placed_Bid]-(p:Person{Alias: {alias} })
	WITH b
	MATCH (b)-[r:Is_Using_Advertisement]->(a:Advertisement)
	DELETE r",["alias"=>$Alias,"adspaceid"=>$AdSpaceID]);
}

function updateCurrentBid($Alias,$AdSpaceID,$UserBid,$MaxBid, $Status,$AdID)
{
	clearAdBeingUsedByBid($Alias, $AdSpaceID);
	$session = buildAdminCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({adspaceid}) })
	WITH b,
	toFloat({currentbid}) AS value1,
    toFloat({maxbid}) AS value2,
	10^2 AS factor
	SET b.CurrentBid = round(factor * value1)/factor
	SET b.MaxBid = round(factor * value2)/factor
    SET b.Status = {status}
	WITH b
	MATCH (a:Advertisement{AdID: toInt({adid}) })
	CREATE (a)<-[:Is_Using_Advertisement]-(b)",
	["adspaceid"=>$AdSpaceID,"alias"=>$Alias,"currentbid"=>$UserBid,"maxbid"=>$MaxBid,"status"=>$Status,"adid"=>$AdID]);
}

function setCurrentWinnerOfBidPositionToNull($AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person)-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({AdSpaceID}) })-[:Is_Ad_On]->(w:Webcomic)
	WHERE b.Position = 'Winner'
	remove b.Position",["AdSpaceID"=>$AdSpaceID]);
}

function getCostPerRotation($CurrentBid)
{
	$Total = $CurrentBid / 24;
	$Total = $Total / 60;
	$Total = $Total * 5;
	$Total = number_format((float)$Total, 2);
	return $Total;
}

function getComicDetailsByAdSpaceID($AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)
	WHERE toInt(as.AdSpaceID) = toInt({adspaceid})
	return w.Name as Name,
	w.ComicID as ComicID",["adspaceid"=>$AdSpaceID]);
	return $query->getRecord();
}


function getCountOfComicsWithBiddingEnabled()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (w:Webcomic)<-[:Is_Ad_On]-(as:AdSpace)
	WHERE as.Status = 'Active'
	RETURN COUNT(w) as Exists");
	$record = $query->getRecord();
	return $record->value("Exists");
}

function getAdSpaceType($AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (as:AdSpace)-[:Uses_Type]->(mt:MediaType)
	WHERE toInt(as.AdSpaceID) = toInt({adspaceid})
	RETURN mt.Name as MediaType",["adspaceid"=>$AdSpaceID]);
	$record = $query->getRecord();
	return $record->value("MediaType");
}

function getMediaTypeDimensions($AdType)
{
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType)
	WHERE mt.Name = {adtype}
	RETURN 
	mt.Height as Height,
	mt.Width as Width",["adtype"=>$AdType]);
	$record = $query->getRecord();
	return $record;
}

function setBidStatusToPaused($Alias, $AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person{Alias: {alias} })
	SET b.Status = 'Paused'",["alias"=>$Alias, "adspaceid"=>$AdSpaceID]);
}

function setBidStatusToActive($Alias, $AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b:Bid)<-[:Placed_Bid]-(p:Person{Alias: {alias} })
	SET b.Status = 'Active'",["alias"=>$Alias, "adspaceid"=>$AdSpaceID]);
}

function getCurrentAdUserSetForBid($Alias, $AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[:Placed_Bid]->(b:Bid)-[:Is_Bid_For]->(as:AdSpace{AdSpaceID: toInt({adspaceid}) })
	WITH b
	MATCH (m:Media)<-[:Uses_Media]-(a:Advertisement)<-[:Is_Using_Advertisement]-(b)
	RETURN 
	a.AdID as AdID,
	COLLECT(m.URL) as URL",["alias"=>$Alias, "adspaceid"=>$AdSpaceID]);
	return $query->getRecord();
	
}

function checkIfUserHasAnyActiveAdsThatMatchAdType($AdSpaceType,$Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Works_On]->(w:Webcomic)<-[:Is_Ad_For]-(a:Advertisement{Status: 'Approved' })-[:Uses_Type]->(mt:MediaType{Name: {adtype} })
	RETURN COUNT(a) as Exists",
	["adtype"=>$AdSpaceType, "alias"=>$Alias]);
	$record= $query->getRecord();
	if($record->value("Exists") >0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function getUserActiveAdsThatMatchAdType($AdSpaceType,$Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (p:Person{Alias: {alias} })-[:Works_On]->(w:Webcomic)<-[:Is_Ad_For]-(a:Advertisement{Status: 'Approved' })-[:Uses_Type]->(mt:MediaType{Name: {adtype} })
	RETURN a.AdID as AdID,
	a.Name as AdName ORDER BY a.Name ASC",
	["adtype"=>$AdSpaceType, "alias"=>$Alias]);
	return $query;
}

function calculateCurrentWinnerOfAdSpace($AdSpaceID)
{
	$session = buildAdminCon();
	
	if(checkIfAdSpaceHasActiveBids($AdSpaceID))
	{
		print("Active bids found<br>");
		$CurrentWinningBid = getCurrentWinningBidOnComicAdSpace($AdSpaceID);
		$CurrentWinningBidID = $CurrentWinningBid->value("BidID");
		if($CurrentWinningBidID != '')
		{
			print("Current Winner Found<br>");
			$CurrentWinner = true;
			$CurrentWinningBidAmount = $CurrentWinningBid->value("CurrentBid");
			$CurrentWinnerAlias = $CurrentWinningBid->value("Alias");
			if(checkIfUserHasEnoughForBid($CurrentWinner, $CurrentWinningBidAmount))
			{
				$WinningBidID = $CurrentWinningBidID;
				$HighestCurrentBid = CurrentWinningBidAmount;
			}
			else
			{
				print("Current winner did not have enough for bid<br>");
				//Email user indicating loss of win due to insufficient funds.
				setBidStatusToPaused($CurrentWinner, $AdSpaceID);
				$CurrentWinner = false;
				$HighestCurrentBid = 0;
			}
		}
		else
		{
			print("No current winner found<br>");
			$WinningBidID = '';
			$CurrentWinner = false;
			$HighestCurrentBid = 0;
		}
		
		$query = $session->run("MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b:Bid{Status: 'Active'} )
		remove b.Position
		WITH b
		return b.BidID as BidID,
		b.CurrentBid as CurrentBid
		ORDER BY b.CurrentBid DESC",["adspaceid"=>$AdSpaceID]);
		
		foreach($query->getRecords() as $Bid)
		{
			$ThisBidID = $Bid->value("BidID");
			$BidOwner = getBidOwner($ThisBidID);
			$ThisBidAmount = $Bid->value("CurrentBid");
			
			if(checkIfUserHasEnoughForBid($BidOwner, $ThisBidAmount))
			{
				print("Bid Owner has enough for bid<br>");
				if($ThisBidAmount > $HighestCurrentBid)
				{
					if($CurrentWinner)
					{
						if($CurrentWinnerAlias != $BidOwner)
						{
							if($ThisBidAmount > ( $CurrentWinningBidAmount *1.1))
							{
								$HighestCurrentBid = $ThisBidAmount;
								$WinningBidID =  $ThisBidID;
							}
							else
							{
								
							}
						}	
					}
					else
					{
						$HighestCurrentBid = $ThisBidAmount;
						$WinningBidID =  $ThisBidID;
					}
				}
			}
			else
			{
				print("Not enough funds found in account. Bidding has been paused.");
				//email current user that their bid has been paused due to insufficient funds
				setBidStatusToPaused($BidOwner, $AdSpaceID);			
			}
		}
		$query2 = $session->run("MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b:Bid{Status: 'Active'} )
		remove b.Position
		WITH b
		return b.BidID as BidID,
		b.MaxBid as MaxBid
		ORDER BY b.MaxBid DESC",["adspaceid"=>$AdSpaceID]);
		foreach($query2->getRecords() as $MaxBid)
		{
			$ThisMaxmimumBid = $MaxBid->value("MaxBid");
			$MaxBidID = $MaxBid->value("BidID");
			$BidOwner = getBidOwner($MaxBidID);
			if(checkIfUserHasEnoughForBid($BidOwner, $ThisMaxmimumBid))
			{
				if($MaxBidID != $WinningBidID)
				{
					$HighestCurrentBid = $HighestCurrentBid *1.1;
					if(checkIfUserHasEnoughForBid($BidOwner, $HighestCurrentBid))
					{
						print("Higher bid found");
						$WinningBidID =  $MaxBidID;
					}
				}
			}
		}
		if($WinningBidID != '')
		{
			$HighestCurrentBid = number_format($HighestCurrentBid,2);
			
			setWinnerOfAdSpace($WinningBidID, $HighestCurrentBid);
			$WinnerAlias = getBidOwner($WinningBidID);
			if($WinnerAlias != CurrentWinnerAlias)
			{
				//Send Email letting them know they've won;
			}
		}
	}
	else
	{
		print("No valid bids were found");
		setCurrentWinnerOfBidPositionToNull($AdSpaceID);
		//No active bids
	}
}

function getBidOwner($BidID)
{
	$session = buildCon();
	$query = $session->run("MATCH (p:Person)-[:Placed_Bid]->(b:Bid{BidID: toInt({bidid}) })
	return p.Alias as Alias",["bidid"=>$BidID]);
	$record = $query->getRecord();
	return $record->value("Alias");
}

function checkIfAdSpaceHasActiveBids($AdSpaceID)
{
	$session = buildCon();
	$query = $session->run("MATCH (as:AdSpace{AdSpaceID: toInt({adspaceid}) })<-[:Is_Bid_For]-(b:Bid{Status: 'Active'} )
		RETURN COUNT(b) as Exists",["adspaceid"=>$AdSpaceID]);
	$record = $query->getRecord();
	if($record->value("Exists") > 0 )
		return true;
	else
		return false;
}

function setWinnerOfAdSpace($BidID, $BidAmount)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (b:Bid{BidID: toInt({bidid}) })
	SET b.Position = 'Winner'
	SET b.CurrentBid = {currentbid}",["bidid"=>$BidID,"currentbid"=>$BidAmount]);
}


function compareFloatNumbers($float1, $float2, $operator='=')  
{  
    // Check numbers to 2 digits of precision  
    $epsilon = 0.01;  
      
    $float1 = (float)$float1;  
    $float2 = (float)$float2;  
      
    switch ($operator)  
    {  
        // equal  
        case "=":  
        case "eq":  
        {  
            if (abs($float1 - $float2) < $epsilon) {  
                return true;  
            }  
            break;    
        }  
        // less than  
        case "<":  
        case "lt":  
        {  
            if (abs($float1 - $float2) < $epsilon) {  
                return false;  
            }  
            else  
            {  
                if ($float1 < $float2) {  
                    return true;  
                }  
            }  
            break;    
        }  
        // less than or equal  
        case "<=":  
        case "lte":  
        {  
            if (compareFloatNumbers($float1, $float2, '<') || compareFloatNumbers($float1, $float2, '=')) {  
                return true;  
            }  
            break;    
        }  
        // greater than  
        case ">":  
        case "gt":  
        {  
            if (abs($float1 - $float2) < $epsilon) {  
                return false;  
            }  
            else  
            {  
                if ($float1 > $float2) {  
                    return true;  
                }  
            }  
            break;    
        }  
        // greater than or equal  
        case ">=":  
        case "gte":  
        {  
            if (compareFloatNumbers($float1, $float2, '>') || compareFloatNumbers($float1, $float2, '=')) {  
                return true;  
            }  
            break;    
        }  
        case "<>":  
        case "!=":  
        case "ne":  
        {  
            if (abs($float1 - $float2) > $epsilon) {  
                return true;  
            }  
            break;    
        }  
        default:  
        {  
            die("Unknown operator '".$operator."' in compareFloatNumbers()");     
        }  
    }  
      
    return false;  
}  
?>