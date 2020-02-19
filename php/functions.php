<?php
include 'errors.php';
include 'Connector.php';

ini_set('display_errors', 1);
error_reporting(E_ALL|E_STRICT);

/*
Hard coded Database values. If ever more are needed, add them to the appropriate list.
*/

function getWebcomicFormats()
{
	$ComicFormat = ["Long Form","Gag Strip","Infinite Canvas"];
	return $ComicFormat;
}

function getAllNewsCategories()
{
	$NewsCategories = ["Public","Members","Admins"];
	return $NewsCategories;
}

function getUserTypes()
{
	$UserTypes = ["Subscriber","Member","Admin"];
	return $UserTypes;
	/*
	$session = buildCon();
	$results = $session->run('MATCH (p:Person) return DISTINCT p.UserType as Type');
	return $results;
	*/
}

function getAllMediaByType($MediaType)
{
	$session = buildCon();
	$MediaTypes = $session->run("MATCH (p:Person)<-[:Was_Drawn_By]-(m:Media)-[:Is_Media_Of]->(mt:MediaType{Name: {mediatype} }) 
	return p.Alias as Artist,
	m.URL as URL,
	m.Status as Status",
	["mediatype"=>$MediaType]);
	return $MediaTypes;
}

function getAllMediaTypes()
{
	$session = buildCon();
	$MediaTypes = $session->run("MATCH (mt:MediaType) 
	return mt.Name as TypeName,
	mt.Height as Height,
	mt.Width as Width");
	return $MediaTypes;
}

function getAllUserMediaTypes()
{
	$session = buildCon();
	$MediaTypes = $session->run("MATCH (mt:MediaType) 
	WHERE NOT mt.Name CONTAINS 'Cadence'
	return mt.Name as TypeName,
	mt.Height as Height,
	mt.Width as Width");
	return $MediaTypes;
}

function getAllMediaStatuses()
{
	$MediaStatuses = ["Active","Inactive","Pending"];
	return $MediaStatuses;
}

function getEventStatuses()
{
	$EventStatuses = ["Approved","Pending","Canceled"];
	return $EventStatuses;
}

function getEventByID($DateCreated)
{
	$session = buildCon();
	$EventDetails = $session->run("MATCH (e:Event{DateCreated: toInt({datecreated}) } )<-[:Is_Organizer_Of]-(p:Person)
	return e.StartTime as StartTime,
	e.Location as Location,
	e.Status as Status,
	e.Title as Title,
	e.Category as Category,
	e.Details as Details,
	e.Type as Type,
	p.Alias as Alias,
	p.ProfilePic as ProfilePic",
	["datecreated"=>$DateCreated]);
	return $EventDetails->getRecord();
}

function getNewsByID($DateWritten)
{
	$session = buildCon();
	$NewsDetails = $session->run("MATCH (n:News{DateWritten: toInt({datewritten}) } )-[:Was_Posted_By]->(p:Person)
	return n.DatePublished as PubDate,
	n.Status as Status,
	n.Title as Title,
	n.Category as Category,
	n.Details as Details,
	p.Alias as Alias,
	p.ProfilePic as ProfilePic",
	["datewritten"=>$DateWritten]);
	return $NewsDetails->getRecord();
}

function getNewsStatusList()
{
	$StatusList = ["Approved","Pending","Deleted"];
	return $StatusList;
}

function getComicMemberships()
{
	$Collectives = ["Independent","Comicadia","Hiveworks","SpiderForest"];
	return $Collectives;
}

function getWebcomicStatusList()
{
	$WebcomicStatusList = ["Active","Hiatus","Completed","Stopped"];
	return $WebcomicStatusList;
}

function getEventTypes()
{
	$EventList = ["Live Stream",
	"Drawpile","Meet Up",
	"Crowdfunding","Emergency Commission Requests",
	"Convention","Birthday",
	"Chapter Start-up","Other"];
	return $EventList;
}

function getCategories()
{
	$CategoryList = ["Public", "Members", "Admins"];
	return $CategoryList;
}

function getCadenceMediaTypes()
{
	$session = buildCon();
	$MediaTypes = $session->run("MATCH (mt:MediaType)
	WHERE mt.Name Contains 'Cadence'
	return mt.Name as TypeName,
	mt.Height as Height,
	mt.Width as Width");
	return $MediaTypes;
}

function getAllWebcomicGenres()
{
	$session = buildCon();
	$GenresList = $session->run("MATCH (g:Genre)
	return g.Name as Name ORDER BY g.Name ASC");
	return $GenresList;
}
/*
function getAllComicadiaMembers()
{
	$session = buildCon();
	$MemberList = $session->run("MATCH (p:Person)
	WHERE p.UserType IN ['Member','Admin']
	return p.Alias as Alias,
	p.First_Name as FirstName,
	p.Last_name as LastName,");
}
*/
function getUsersWebcomicNames($Alias)
{
	$session = buildCon();
	$Webcomics = $session->run("MATCH (p:Person{Alias: {alias} })
	-[r:Works_On]->
	(w:Webcomic)
	RETURN w.Name as Name, 
	w.URL as URL, 
	w.ComicID as ComicID,
	w.Membership as Membership ORDER BY w.Membership ASC",["alias"=>$Alias]);
	return $Webcomics;
}

function getCarouselAds()
{
	$client = buildCon();
	$query = "MATCH (mt:MediaType{Name: 'Carousel'})<-[]-(b:Media)-[:Is_Media_For]->(w:Webcomic) 
	where w.Membership = 'Comicadia' 
	and b.Status = 'Active'
	and w.Status = 'Active'
	return b.URL as imgURL, 
	b.alt as Alt, 
	w.Name as Name, 
	w.URL as URL, 
	rand() as r ORDER BY r LIMIT 3";
	$result = $client->run($query);
	return $result;
}

function getUnapprovedEvents()
{
	$session = buildCon();
	$EventList = $session->run("MATCH (p:Person)-[:Is_Organizer_Of]->(e:Event)
	WHERE e.Status = 'Pending'
	return p.Alias as Alias,
	e.Title as Title,
	e.Type as Type,
	e.Details as Details,
	e.StartTime as PubDate,
	e.DateCreated as DateCreated ORDER BY e.StartTime");
	return $EventList;
}

function getUnapprovedNews()
{
	$session = buildCon();
	$NewsList = $session->run("MATCH (p:Person)<-[:Was_Posted_By]-(n:News)
	WHERE n.Status = 'Pending'
	return p.Alias as Alias,
	n.Title as Title,
	n.Type as Type,
	n.Details as Details,
	n.DatePublished as PubDate,
	n.DateWritten as DateCreated ORDER BY n.DatePublished");
	return $NewsList;
}

function getUnapprovedEventsCount()
{
	$session= buildCon();
	$EventList = $session->run("MATCH (p:Person)-[:Is_Organizer_Of]->(e:Event)
	WHERE e.Status = 'Pending'
	return COUNT(e) as EventCount");
	$record = $EventList->getRecord();
	$EventCount = $record->value("EventCount");
	return $EventCount;
}

function getUnapprovedNewsCount()
{
	$session= buildCon();
	$NewsList = $session->run("MATCH (p:Person)<-[:Was_Posted_By]->(n:News)
	WHERE n.Status = 'Pending'
	return COUNT(n) as NewsCount");
	$record = $NewsList->getRecord();
	$NewsCount = $record->value("NewsCount");
	return $NewsCount;
}

function getAllSocialMediaTypes()
{
	$session = buildCon();
	$query = $session->run("MATCH (sm:SocialMedia)
	RETURN sm.Name as Name,
	sm.CSSClass as Class,
	sm.BGColor as BGColor");
	return $query;
}

function getSpecificSocialMediaURLByComic($Name, $ComicID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sm:SocialMedia{Name: {name} })<-[r:Is_Subscribed_To]-(p:Webcomic{ComicID: toInt({comicid}) })
	RETURN r.URL as URL",
	["name"=>$Name,"comicid"=>$ComicID]);
	$record = $query->getRecord();
	if($record != null)
	{
		return $record->value("URL");
	}
	else
	{
		return null;
	}
}
function getSpecificSocialMediaURLByName($Name,$Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sm:SocialMedia{Name: {name} })<-[r:Is_Subscribed_To]-(p:Person{Alias: {alias} })
	RETURN r.URL as URL",
	["name"=>$Name,"alias"=>$Alias]);
	$record = $query->getRecord();
	if($record != null)
	{
		return $record->value("URL");
	}
	else
	{
		return null;
	}
}

function getUserSocialMedia($Alias)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sm:SocialMedia)<-[r:Is_Subscribed_To]-(p:Person{Alias: {alias} })
	return r.URL as URL,
	sm.Name as SocMediaName",
	['alias'=>$Alias]);
	return $query;
}

function getSpecificSocialMediaURLByNameForComic($Name, $ComicID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (sm:SocialMedia{Name: {name} })<-[r:Is_Subscribed_To]-(p:Webcomic{ComicID: toInt({comicid}) })
	RETURN r.URL as URL",
	["name"=>$Name,"comicid"=>$ComicID]);
	$record = $query->getRecord();
	if($record != null)
	{
		return $record->value("URL");
	}
	else
	{
		return null;
	}
}

function getRandomCadenceWidget()
{
	$session = buildCon();
	$query = $session->run("MATCH (m:Media)-[:Is_Media_Of]->(mt:MediaType{Name: 'Cadence Widget'})
	RETURN m.URL as URL, rand() as r ORDER BY r ASC LIMIT 1");
	$record = $query->getRecord();
	return $record->value("URL");
}

/*
function getCommentIDsOfEvent($DateCreated)
{
	$session = buildCon();
	$CommentList = $session->run("MATCH (c:Comment)-[:Is_Comment_Of]->(e:Event{DateCreated: toInt({datecreated}) })
	WHERE NOT EXISTS((c)-[:Is_Reply_To]->(:Comment))
	return c.DateCreated as DateCreated,
	",["datecreated"=>$DateCreated]);
	return $CommentList;
}

function getCommentCountOfEvent($DateCreated)
{
	$session = buildCon();
	$CommentCount = $session->run("MATCH(c:Comment)-[:Is_Comment_Of]->(E:Event{DateCreated: toInt({datecreated}) })
	return count(c) as CommentCount");
	$record = $CommentCount->getRecord();
	$result = $record->value("CommentCount");
	return $result;
}

function getCommentDetails($DateCreated)
{
	$session = buildCon();
	$EventDetails = $session->run("MATCH (p:Person)<-[:Was_Posted_By]-(c:Comment{DateCreated: toInt({datecreated}) })
	return c.Text as Text,
	p.Alias as Alias,
	p.ProfilePic as ProfilePic",
	["datecreated"=>$DateCreated]);
	return $EventDetails->getrecord();
}

function getCommentReplies($DateCreated)
{
	$session = buildCon();
	$ReplyList = $session->run("MATCH (c:Comment{DateCreated: toInt({datecreated}) })<-[:Is_Reply_To]-(ce:Comment)
	return ce.DateCreated as DateCreated");
	return $ReplyList;
}
*/

function CalcRatingString($ComicRating)
{
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
	return $RatingString;
}

function CalcRatingStyle($ComicRating)
{
	if($ComicRating > 100)
	{
		$style = 'Adult';
	}
	elseif($ComicRating > 50)
	{
		$style = 'PG';
	}
	elseif($ComicRating > 25)
	{
		$style = 'PG';
	}
	else 
	{
		$style='General';
	}		
	return $style;
}

function HeraldRSSFeeder()
{
	$RSS = "https://herald.comicadia.com/feed/";
	$RSSList = array();
	try
		{
			if(checkOnline($RSS))
			{
				$content = file_get_contents($RSS);
				$xml = new SimpleXmlElement($content);
				$Counter = 0;
				foreach ($xml->channel->item as $item)
				{
					if($Counter <5)
					{
					$article = array();
					$article["title"] = $item->title;
					$article["link"] = $item->link;
					$article["pubDate"] = strtotime($item->pubDate);
					$article["timestamp"] = strtotime($item->pubDate);
					$article["description"] = (string) trim($item->description);
					$RSSList[] = $article;
					}
					$Counter += 1;
				}
			}
			else
			{
				print("Feed currently down");
			}
		}
		catch (Exception $e) 
		{
			
			sendGenericEmailFromNoReply("Kiljax@gmail.com", "Failure to read RSS for The Herald, Error is as follows:/r/n/r/n $e");
		}
	return $RSSList;			
}

function RSSFeeder()
{
	$cachefile = 'rssFeeds/RSSList.json';
	if(file_exists($cachefile))
	{
		 if (filemtime($cachefile) < strtotime('now -240 minutes')) 
		 {
		   // if stale, rebuild it

		   // .. do your normal building of the $RSSList here ..
			readRSSSites($cachefile);
		} 
		else 
		{
			// else output cache
			return json_decode(file_get_contents($cachefile),true);
		}	
	}
	else
	{
		readRSSSites($cachefile);
	}
}

function readRSSSites($cachefile)
{
	$client = buildCon();
	$query = "MATCH (w:Webcomic) 
	where EXISTS(w.RSS) 
	and w.Membership = 'Comicadia' 
	and w.Status = 'Active' 
	return w.ComicID as ComicID, w.Name as Name, w.URL as URL, w.RSS as RSS";
	$result = $client->run($query);
	$RSSList = array();
	foreach($result->getRecords() as $record)
	{
		
			$ComicArray = array();
			$ComicID = $record->value("ComicID");
			$ComicName = $record->value('Name');
			$RSS = $record->value('RSS');
			$URL = $record->value('URL');
		try
		{
			if(checkOnline($RSS))
			{
				$content = file_get_contents($RSS);
				$x = new SimpleXmlElement($content);
				for($i=0; $i<1; $i++)
				{
				$profile = $x->channel->item[$i];
					$pubDate = $profile->{"pubDate"};
				}
				$ComicArray['URL'] = $URL;
				$ComicArray['ID'] = $ComicID;
				$ComicArray['Comic'] = $ComicName; 
				$ComicArray['pubDate'] = $pubDate;
				$RSSList[] = $ComicArray;
			}
		}
		catch (Exception $e) 
		{
			
			sendGenericEmailFromNoReply("Kiljax@gmail.com", "Failure to read RSS for $ComicName, Error is as follows:<br><br>$e");
		}
	}
	
	#usort($RSSList, "sortFunction");
	usort($RSSList, "compareRSSTimes");
	file_put_contents($cachefile,json_encode($RSSList));
	return $RSSList;
}

function checkOnline($domain)
{
 ini_set("default_socket_timeout","05");
       set_time_limit(5);
	   try
	   {
		   $f=fopen($domain,"r");
		   $r=fread($f,1000);
		   fclose($f);
		   if(strlen($r)>1) 
		   {
			return true;
		   }
		   else 
		   {
			return false;
		   }
	   }
	   catch(Exception $e)
	   {
		   sendGenericEmailFromNoReply("Kiljax@gmail.com", "Failure to open site for $ComicName RSS Feed, Error is as follows:\n\r\n\r$e");
	   }
}
/*
function checkOnline($domain)
{
	$curlInit = curl_init($domain);
	curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,20);
	curl_setopt($curlInit,CURLOPT_HEADER,true);
	curl_setopt($curlInit,CURLOPT_NOBODY,true);
	curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

	//get answer
	$response = curl_exec($curlInit);

	curl_close($curlInit);
	if ($response) return true;
	return false;
}
*/
function compareRSSTimes($a, $b){

    $a = strtotime($a['pubDate']);
    $b = strtotime($b['pubDate']);

    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

function getSingleHorizontalBannerForWebcomic($ComicID)
{
	$session = buildCon();
	$HorizBanner = $session->run("MATCH (mt:MediaType{Name: 'Horizontal Banner'})
	<-[:Is_Media_Of]-
	(m:Media)
	-[:Is_Media_For]->
	(w:Webcomic{ComicID: toInt({comicid}) })
	WHERE w.Status = 'Active'
	AND m.Status = 'Active'
	return m.URL as URL, rand() as r ORDER BY r LIMIT 1",["comicid"=>$ComicID]);
	$record = $HorizBanner->getRecord();
	if($record)
	{
		$result=$record->value("URL");
	}
	else
	{
		$result = null;
	}
	
	return $result;
}

function getSingleHorizontalBannerHoversForWebcomic($ComicID)
{
	$session = buildCon();
	$HorizBannerHover = $session->run("MATCH (mt:MediaType{Name: 'Horizontal Banner Hover'})
	<-[:Is_Media_Of]-
	(m:Media)
	-[:Is_Media_For]->
	(w:Webcomic{ComicID: toInt({comicid}) })
	WHERE m.Status = 'Active'
	AND w.Status = 'Active'
	return m.URL as URL, rand() as r ORDER BY r LIMIT 1",["comicid"=>$ComicID]);
	$record = $HorizBannerHover->getRecord();
	if($record)
	{
		$result=$record->value("URL");
	}
	else
	{
		$result = null;
	}
	return $result;
}

function getWebcomicDetails($ComicID)
{
	$session= buildCon();
	$WebcomicDetails = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	return w.Name as Name,
	w.URL as URL,
	w.RSS as RSS,
	w.Pitch as Pitch,
	w.Synopsis as Synopsis",["comicid"=>$ComicID]);
	return $WebcomicDetails->getRecord();
	
}

function caclulateWebcomicRating($ComicID)
{
	$session = buildCon();
	$RatingList = $session->run("MATCH (t:Theme)<-[:Has_Elements_Of]-(w:Webcomic{ComicID: toInt({comicid} )})
	return SUM(toInt(t.Value)) as Rating",["comicid"=>$ComicID]);
	$record = $RatingList->getRecord();
	$result = $record->value("Rating");
	return $result;
}

function getWebcomicGenres($ComicID)
{
	$session = buildCon();
	$GenreList = $session->run("MATCH (g:Genre)<-[:Is_Of_Genre]-(w:Webcomic{ComicID: toInt({comicid}) })
	return g.Name as Name ORDER BY g.Name ASC",["comicid"=>$ComicID]);
	return $GenreList;
}
/*
function getNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)-[:Was_Posted_By]-(p:Person) WHERE n.DatePublished < timestamp() and n.Category = 'Public' return n.Details as Details, n.Title as Title, p.First_Name as First, p.Last_Name as Last,	n.DatePublished as DatePublished ORDER BY n.DatePublished DESC LIMIT 3";
	$result = $session->run($query);
	return $result;
	foreach($result->getRecords() as $record)
	{
		$FirstName = $record->value('First');
		$LastName = $record->value('Last');
		$Poster = $FirstName . ' ' . $LastName;
		$Title = $record->value('Title');
		$Text = $record->value('Details');
		$PubDate = $record->value('DatePublished');
		$PubDate = date('m/d/y', $PubDate / 1000);
		print("<div class='newsBox'>
		<div class='NewsHeader'>
			<div class='iconHolder'></div>
			<div class='newsTitle'>$Title</div>			
			<div class='Poster'>			
			<sub><strong>Post By:</strong> $Poster</sub>
			<br><sup><strong>Date:</strong> $PubDate</div></sup>
		</div>
		<div class='NewsText'>$Text</div></div>");
	}	 
}
*/

function getNewsBlurb($NewsID)
{
	$session = buildCon();
	$query = $session->run("MATCH (n:News{DateWritten: toInt({newsid}) })
	return n.Details as Details",
	["newsid"=>$NewsID]);
	$record = $query->getRecord();
	return $record->Value("Details");
}

function getMemberCount()
{
	$session = buildCon();
	$MemberCount = $session->run("MATCH (p:Person)
	return COUNT(p) as MemberCount");
	$record = $MemberCount->getrecord();
	return $record->value("MemberCount");
}

function getMembersCountByKeyword($Search)
{
	$session = buildCon();
	$MemberCount = $session->run("MATCH (p:Person)
	WHERE toLower(p.Alias) CONTAINS toLower({keyword})
	return COUNT(p) as MemberCount",
	["keyword"=>$Search]);
	$record = $MemberCount->getrecord();
	return $record->value("MemberCount");
}

function getMemberNewsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Status = 'Approved'
	and n.Category IN ['Members','Public']
	return count(n) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}
function getMemberEventsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (e:Event)-[:Is_Organizer_Of]-(p:Person) 
	WHERE e.StartTime > timestamp() 
	and e.Status = 'Approved'
	and e.Category IN ['Public','Members']
	return count(e) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}

function getAllNewsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Status = 'Approved'
	return count(n) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}

function getAllEventsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (e:Event)-[:Is_Organizer_Of]-(p:Person) 
	WHERE e.StartTime > timestamp() 
	and e.Status = 'Approved'
	return count(e) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}
function getPublicNewsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category = 'Public'
	and n.Status = 'Approved'
	return count(n) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}

function getPublicEventsCount()
{
	$session = buildCon();
	$NewsCount = $session->run("MATCH (e:Event)-[:Is_Organizer_Of]-(p:Person) 
	WHERE e.StartTime > timestamp() 
	and e.Category = 'Public'
	and e.Status = 'Approved'
	return count(e) as counter");
	$Counter = $NewsCount->getRecord();
	$Count = $Counter->value("counter");
	return $Count;
}

function getAllNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	WHERE n.DatePublished < timestamp() 
	return n.Details as Details, 
	n.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last, 
	p.Alias as Alias, 
	n.DatePublished as DatePublished, 
	n.DateWritten as DateWritten, 
	n.Status as Status ORDER BY n.DatePublished DESC";
	$result = $session->run($query);
	return $result;
}

/*
function getMemberNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)
	-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category IN ['Public','Members'] 
	return n.Details as Details, 
	n.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last,
	p.ProfilePic as Profile,
	p.Alias as Alias,
	n.DatePublished as DatePublished 
	ORDER BY n.DatePublished";
	$result = $session->run($query);
	return $result;
}
*/

function getMembersListFromSearchByPagination($Keyword,$Start,$NumberOfArticles)
{
	$session = buildCon();
	$MemberList = $session->run("OPTIONAL MATCH (p:Person)
	WHERE toLower(p.Alias) CONTAINS toLower({keyword}) 
	RETURN p.Alias as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType
	ORDER BY p.UserType ASC, p.Alias ASC SKIP {start} LIMIT {NoOfArticles}",
	["keyword"=>$Keyword,"start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $MemberList;
}

function getMembersListByPagination($Start,$NumberOfArticles)
{
	$session = buildCon();
	$MemberList = $session->run("OPTIONAL MATCH (p:Person)
	RETURN p.Alias as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType
	ORDER BY p.UserType ASC, p.Alias ASC SKIP {start} LIMIT {NoOfArticles}",
	["start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $MemberList;
}

function getNewsByTypeForPagnation($searchBy,$Start,$NumberOfArticles)
{
	$session = buildCon();
	$NewsList = $session->run("MATCH (n:News)
	-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category IN {searchby}
	and n.Status = 'Approved'
	return n.Details as Details, 
	n.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last,
	p.ProfilePic as Profile,
	p.Alias as Alias,
	n.DateWritten as DateWritten,
	n.DatePublished as DatePublished 
	ORDER BY n.DatePublished DESC SKIP {start} LIMIT {NoOfArticles}
	",["searchby"=>$searchBy,"start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $NewsList;
}

function getEventByTypeForPagnation($searchBy,$Start,$NumberOfArticles)
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)
	<-[:Is_Organizer_Of]-(p:Person) 
	WHERE toFloat(e.StartTime) > toFloat(timestamp())
	and e.Category IN {searchby}
	and e.Status = 'Approved'
	return e.Details as Details, 
	e.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last,
	p.ProfilePic as Profile,
	p.Alias as Alias,
	e.DateCreated as DateCreated,
	e.Type as Type,
	e.StartTime as StartTime
	ORDER BY e.StartTime ASC SKIP {start} LIMIT {NoOfArticles}
	",["searchby"=>$searchBy,"start"=>$Start,"NoOfArticles"=>$NumberOfArticles]);
	return $EventList;
}

function getUserType($Alias)
{
	$session = buildCon();
	$results = $session->run('MATCH (p:Person{Alias: {alias} }) 
	return p.UserType as Type', ['alias' => $Alias]);
	$record = $results->getRecord();
	$Type = $record->value('Type');
	return $Type;
}

function getCollectiveNews($Filter)
{
	
	$session = buildCon();
	$query = $session->run("MATCH (n:News)
	-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Status = 'Approved'
	and n.Category IN {filter} 
	return n.Details as Details, 
	n.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last,
	p.ProfilePic as Profile,
	p.Alias as Alias,
	n.DatePublished as DatePublished,
	n.DateWritten as DateWritten
	ORDER BY n.DatePublished DESC LIMIT 3",["filter"=>$Filter]);
	return $query;
}

function getPublicNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)
	-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category = 'Public' 
	and n.Status = 'Approved'
	return n.Details as Details, 
	n.Title as Title, 
	p.First_Name as First, 
	p.Last_Name as Last,
	p.ProfilePic as Profile,
	p.Alias as Alias,
	n.DatePublished as DatePublished,
	n.DateWritten as DateWritten
	ORDER BY n.DatePublished DESC";
	$result = $session->run($query);
	return $result;
}

/*
Password and login functions
*/

function Login($Email, $Password)
{
	$result = 0;
	$session = buildCon();
	$getClient = $session->run("OPTIONAL MATCH (p:Person) 
	where toLower(p.Email) = toLower({emailaddress})
	return p.Email as Email, p.Password as Password, p.Alias as Alias",['emailaddress'=> $Email]);
	$User = $getClient->getRecord();		
	$Email = $User->value('Email');
	$Alias = $User->value('Alias');
	if (is_null($Alias))
	{
		$result = 0;
	}
	else 
	{
		$checkPass = $User->value('Password');	
		//if($Password == $checkPass)
		if(password_verify($Password, $checkPass))
		{
			$_SESSION["Alias"] = $Alias;
			$_SESSION['Email'] = $Email;
			$result = 1;
		}
	}
	return $result;
}

function resetUserPass($Email, $Password)
{
	$session = buildAdminCon();
	$record = $session->run("MATCH (p:Person)
	WHERE toLower(p.Email) = toLower({email}) 
	SET p.Password = {password}",["email"=>$Email, "password"=>$Password]);
}


function getUserDetails($Alias)
{
	$Alias = strtolower($Alias);
	$session = buildCon();
	$record = $session->run('MATCH (p:Person) 
	where toLower(p.Alias) = toLower({alias}) 
	return p.First_Name as FirstName, 
	p.Last_Name as LastName, 
	p.Alias as Alias, 
	p.UserType as Type, 
	p.Email as Email,
	p.ProfilePic as Pic'
	,['alias' => $Alias]);
	$result = $record->getRecord(); 
	return $result;
}

/************************************************************************************



functions to ensure no duplication is found in the database entries.



*************************************************************************************/

function checkDuplicateSocialMediaName($SocMediaName)
{
	$session = buildCon();
	$result = $session->run("MATCH (sm:SocialMedia)
	WHERE toLower(sm.Name) = toLower({socname})
	return count(sm) as DupCount
	",["socname"=>$SocMediaName]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateGenre($GenreName)
{
	$session = buildCon();
	$result = $session->run("MATCH (g:Genre)
	WHERE toLower(g.Name) = toLower({genrename})
	return count(g) as DupCount
	",["genrename"=>$GenreName]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateTheme($ThemeName)
{
	$session = buildCon();
	$result = $session->run("MATCH (t:Theme)
	WHERE toLower(t.Name) = toLower({themename})
	return count(t) as DupCount
	",["themename"=>$ThemeName]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateEvent($PubDate, $Organizer)
{
	$session = buildCon();
	$result = $session->run("MATCH (e:Event{StartTime: {starttime} })<-[:Is_Organizer_Of]-(p:Person{Alias: {organizer} })
	return count(e) as DupCount
	",["starttime"=>$PubDate,
	"organizer"=>$Organizer]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkEventDuplicates($Title,$Time,$Email)
{
	$session = buildCon();
	$result = $session->run("MATCH (e:Event)
	<-[:Is_Organizer_Of]-
	(p:Person{Email: {email} })
	WHERE toLower(e.Title) = toLower({title})
	AND e.StartTime = {time}
	return count(e) as DupCount
	",["email"=>$Email, "title"=>$Title,"time"=>$Time]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateWebcomicName($WebcomicName)
{
	$ComicName = strtolower($WebcomicName); 
	$session = buildCon();
	$result = $session->run("MATCH (w:Webcomic) 
	WHERE toLower(w.Name) = {comicname}
	return count(w) as DupCount",["comicname"=>$ComicName]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateWebcomicURL($WebcomicURL)
{
	$ComicURL = strtolower($WebcomicURL); 
	$session = buildCon();
	$result = $session->run("MATCH (w:Webcomic) 
	WHERE toLower(w.URL) = {comicurl}
	return count(w) as DupCount",["comicurl"=>$ComicURL]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateWebcomicRSS($WebcomicRSS)
{
	$ComicRSS = strtolower($WebcomicRSS); 
	$session = buildCon();
	$result = $session->run("MATCH (w:Webcomic) 
	WHERE toLower(w.RSS) = {comicRSS}
	return count(w) as DupCount",["comicRSS"=>$ComicRSS]);
	$record = $result->getRecord();
	$count = $record->value("DupCount");
	return $count;
}

function checkDuplicateAlias($NewAlias,$CurrentEmail)
{
	$Alias = strtolower($NewAlias);
	$email = strtolower($CurrentEmail);
	$session = buildCon();
	$result = $session->run("OPTIONAL MATCH (p:Person) where toLower(p.Alias) = {alias} and toLower(p.Email) <> {email} return count(p) as DupCount",['alias'=>$Alias, 'email'=>$email]);
	$record = $result->getRecord();
	$count = $record->value('DupCount');
	return $count;
}

function checkDuplicateEmail($NewEmail, $CurrentAlias)
{
	$email = strtolower($NewEmail);
	$Alias = strtolower($CurrentAlias);
	$session = buildCon();
	$result = $session->run("OPTIONAL MATCH (p:Person) where toLower(p.Email) = {email} and toLower(p.Alias) <> {alias} return count(p) as DupCount",['email'=>$email,'alias'=>$Alias]);
	$record = $result->getRecord();
	$count = $record->value('DupCount');
	return $count;
}

/**********************************************

End Duplicate checkers

***********************************************/

function isEmailAvailable($Email)
{
	$session = buildCon();
	$Available = $session->run("OPTIONAL MATCH (p:Person)
	WHERE toLower(p.Email) = toLower({email}) return count(p) as Exists",["email"=>$Email]);
	$record = $Available->getRecord();
	$count = $record->value('Exists');
	return $count;
}

function isAliasAvailable($Alias)
{
	$session = buildCon();
	$Available = $session->run("OPTIONAL MATCH (p:Person)
	WHERE toLower(p.Alias) = toLower({alias}) return count(p) as Exists",["alias"=>$Alias]);
	$record = $Available->getRecord();
	$count = $record->value('Exists');
	return $count;
}


function updateUser($CurrentEmail,$CurrentAlias,$FirstName, $LastName,$Alias,$Email,$Type)
{	
	$session = buildAdminCon();
	$record = $session->run("MATCH (p:Person) 
	WHERE toLower(p.Alias) = {currentAlias} 
	and toLower(p.Email) = {currentEmail} 
	SET p.Email = toString({email}), 
	p.Alias = toString({alias}),
	p.First_Name = toString({FirstName}),
	p.Last_Name = toString({LastName}),
	p.UserType = toString({Type})
	return p.Alias as Alias, p.Email as Email
	",["currentAlias"=>strtolower($CurrentAlias), 
	"currentEmail"=>strtolower($CurrentEmail),
	"email"=>$Email, 
	"alias"=>$Alias,
	"FirstName"=>$FirstName, 
	"LastName"=>$LastName,
	"Type"=>$Type]);
	$result = $record->getRecord();
}	


function createNewNews($Alias, $PubDate, $Category,$Details,$Title)
{
	$UserRecord = getUserType($Alias);
	$UserType = $UserRecord;
	if($Category == 'Public')
	{
		if($UserType == 'Admin')
		{
			$Status = 'Approved';
		} 
		else 
		{
			$Status = 'Pending';
		}
	}
	else 
	{
		$Status = 'Approved';
	}
	$session = buildAdminCon();
	$record = $session->run("MATCH (p:Person{Alias: {alias} })
	CREATE (m:News{Title: {title}, Details: {details}, Category:{category}, Status:{status}, DatePublished: toFloat({pubDate}), DateWritten: timestamp()} )-[:Was_Posted_By]->(p)
	", ['alias' => $Alias, 'title'=>$Title, 'details'=>$Details,'category'=>$Category,'status'=>$Status, 'pubDate' => $PubDate]);
}

function editNews($DateWritten,$Alias,$PubDate, $Category,$Details,$Title,$Status)
{
	$session = buildAdminCon();
	$record = $session->run("MATCH (n:News), (p:Person{Alias: {alias} })
	WHERE toString(n.DateWritten) = toString({dateWritten})
	SET n.Category = {category}
	SET n.DatePublished = toFloat({pubdate})
	SET n.Details = {details}
	SET n.Title = {title}
	SET n.Status = {status}
	CREATE (n)-[:Was_Edited_By{EditDate: timestamp()}]->(p)
	",["alias"=>$Alias,"dateWritten" => $DateWritten,"category"=>$Category,"pubdate"=>$PubDate,"details" =>$Details,"title" =>$Title,'status' =>$Status]);
}

function getAdminEditNews()
{
	$session = buildCon();
	//$query = "MATCH (n:News)-[:Was_Posted_By]-(p:Person) where n.Status <> 'Approved' OR n.DatePublished > timestamp() return n.DateWritten as DateWritten, n.Details as Details, n.Category as Category, n.Title as Title, n.Status as Status, p.First_Name as First, p.Last_Name as Last,	n.DatePublished as DatePublished ORDER BY n.DatePublished";
	$query = "MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	WHERE n.Status = 'Approved'
	return n.DateWritten as DateWritten, 
	n.Details as Details, 
	n.Category as Category, 
	n.Title as Title, 
	n.Status as Status, 
	p.Alias as Alias,
	n.DatePublished as DatePublished ORDER BY n.DatePublished DESC";
	$result = $session->run($query);
	return $result;	
}

function getSpecificNews($DateWritten)
{
	$session = buildCon();
	$result = $session->run("MATCH (n:News)
	WHERE toString(n.DateWritten) = toString({dWritten})  
	return n.DatePublished as DatePublished, n.Title as Title, n.Status as Status, n.Category as Category, n.Details as Details
	",["dWritten" =>$DateWritten]);
	$record = $result->getRecord();	
	return $record;
}

function test_input($data) 
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function createUser($FirstName, $LastName,$Alias,$Email,$Type,$Password)
{
	$Password = password_hash($Password, PASSWORD_DEFAULT);
	$session = buildAdminCon();
	$result = $session->run("CREATE (p:Person{First_Name: toString({Fname}), 
	Last_Name: toString({Lname}),
	Alias: toString({alias}),
	Email: toString({email}),
	UserType: toString({UType}),
	Password: toString({password}) })
	",['Fname' => $FirstName, 'Lname' =>$LastName, 'alias' =>$Alias,'email' => $Email,'UType'=>$Type,'password'=>$Password]);
}

function checkAlias($Alias)
{
	$Alias = strtolower($Alias);
	$session = buildCon();
	$result = $session->run("OPTIONAL MATCH (p:Person) where toLower(p.Alias) = {alias} return count(p) as DupCount",['alias'=>$Alias]);
	$record = $result->getRecord();
	$count = $record->value('DupCount');
	return $count;
}

function checkEmail($Email)
{
	$Alias = strtolower($Email);
	$session = buildCon();
	$result = $session->run("OPTIONAL MATCH (p:Person) where toLower(p.Email) = {email} return count(p) as DupCount",['email'=>$Email]);
	$record = $result->getRecord();
	$count = $record->value('DupCount');
	return $count;
}

function getAllUsers()
{
	$session = buildCon();
	$UserList = $session->run("MATCH (p:Person) 
	return p.First_Name as FirstName, 
	p.Last_Name as LastName, 
	p.Email as Email,
	p.Alias as Alias
	ORDER BY p.Alias");
	return $UserList;		
}

function getAllUsersNotWorkingOnWebcomic($ComicID)
{
	$session = buildCon();
	$UserList = $session->run("MATCH (p:Person) 
	WHERE NOT EXISTS((p)-[:Works_On]->(:Webcomic{ComicID: toInt({comicid}) })) 
	return p.First_Name as FirstName, 
	p.Last_Name as LastName, 
	p.Email as Email,
	p.Alias as Alias
	ORDER BY p.Alias",["comicid"=>$ComicID]);
	return $UserList;
}

function createWebcomic($ComicName, $ComicURL, $Membership,$ComicRSS,$Status)
{
	$session = buildAdminCon();
	$result = $session->run("CREATE (w:Webcomic{Name: {name}, URL:{url}, RSS: {rss}, Membership: {membership}, Status: {status} })
	WITH toString(toInt(round(rand() * 100))) as First,
	toString(timestamp()) as Stamp, w
	set w.ComicID = toInt(First + Stamp) 
	",["name"=>$ComicName,"url"=>$ComicURL,"rss"=>$ComicRSS,"membership"=>$Membership, "status" =>$Status]);
}

function updateWebcomic($ComicID, $ComicName, $ComicURL, $ComicRSS,$Status,$Membership,$Synopsis,$Pitch)
{
	$session = buildAdminCon();
	$result = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	SET w.Name = {newname}
	SET w.RSS = {rss}
	SET w.URL = {url}
	SET w.Membership = {membership}
	SET w.Synopsis = {synopsis}
	SET w.Status= {status}
	SET w.Pitch = {pitch}
	",["comicid"=>$ComicID,"newname"=>$ComicName,"rss"=>$ComicRSS,"url"=>$ComicURL,"membership"=>$Membership,"synopsis"=>$Synopsis,"status"=>$Status,"pitch"=>$Pitch]);
}

function linkUserToComic($Email, $ComicID)
{
	$session = buildAdminCon();
	$result = $session->run("MATCH (p:Person{Email: {email} }), (w:Webcomic{ComicID: toInt({comicid} )})
	CREATE (p)-[:Works_On{Role: 'Creator'}]->(w)",["email" =>$Email,"comicid"=>$ComicID]);
}

function getWebcomicList()
{
	$session = buildCon();
	$WebcomicList = $session->run("MATCH (w:Webcomic)
	return w.ComicID as ComicID, w.Name as Name, w.URL as URL, w.RSS as RSS, w.Status as Status, w.Membership as Membership ORDER BY Name");
	return $WebcomicList;
}

function getComicDetails($ComicID)
{
	$session = buildCon();
	$ComicDetails = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	return w.Name as Name, 
	w.RSS as RSS, 
	w.URL as URL, 
	w.Status as Status, 
	w.Membership as Membership,
	w.ComicID as ComicID,
	w.Pitch as Pitch,
	w.Synopsis as Synopsis
	",["comicid"=>$ComicID]);
	$Comic = $ComicDetails->getRecord();
	return $Comic;
}

function getComicDetailsByID($ComicID)
{
	$session = buildCon();
	$ComicDetails = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	return w.Name as Name,
	w.URL as URL,
	w.RSS as RSS",["comicid"=>$ComicID]);
	return $ComicDetails->getRecord();
}

function getWebcomicCrew($ComicID)
{
	$session = buildcon();
	$CrewList = $session->run("MATCH (p:Person)-[r:Works_On]->(w:Webcomic{ComicID: {comicid} })
	return p.Email as Email, 
	p.ProfilePic as Profile,
	p.First_Name as FirstName,
	p.Last_Name as LastName,
	r.Role as Role, 
	p.Alias as Alias",
	["comicid"=>$ComicID]);
	return $CrewList;
}

function getCrewRoles($Alias, $ComicID)
{
	$session = buildCon();
	$RoleList = $session->run("MATCH (p:Person{Alias: {alias} })-[r:Works_On]->(w:Webcomic{ComicID: toInt({comicid}) })
	return r.Role as Role",["alias"=>$Alias,"comicid"=>$ComicID]);
	return $RoleList;
}

function saveUserRoles($ComicID,$Alias,$Role)
{
	$session = buildAdminCon();
	$record = $session->run("MATCH (p:Person{Alias: {alias} })-[r:Works_On]->(w:Webcomic{ComicID: toInt({comicid}) })
	SET r.Role= {role}",["alias"=>$Alias,"comicid"=>$ComicID,"role"=>$Role]);
}

function createUserRoles($ComicID, $Alias,$Role)
{
	$session = buildAdminCon();
	$record = $session->run("Match (w:Webcomic{ComicID: toInt({comicid}) }), (p:Person{Alias: {alias} })
	CREATE (w)<-[:Works_On{Role: {role} }]-(p)",["comicid"=>$ComicID,"alias"=>$Alias,"role"=>$Role]);
}

function removeCrewFromWebcomic($ComicID, $Alias)
{
	$session = buildAdminCon();
	$record = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })<-[r:Works_On]-(p:Person{Alias: {alias} })
	delete r",["comicid" =>$ComicID, "alias"=> $Alias ]);
}

function getRandomSpotlightInfo()
{
	$session = buildCon();
	$URLSelect = $session->run("MATCH (mt:MediaType{Name: 'Header'})<-[]-(m:Media)
	WITH m
	MATCH (p:Person)<-[:Was_Drawn_By]-(m)-[:Is_Media_For]->(w:Webcomic)
	where w.Membership = 'Comicadia'
	and m.Status = 'Active'
	AND w.Status = 'Active'
	return m.URL as SRC, p.First_Name + ' ' +  p.Last_Name as Artist, p.Alias as Alias, w.Name as Name, w.URL as URL, rand() as r ORDER BY r LIMIT 1");
	$record = $URLSelect->getRecord();
	return $record;
}

function createEvent($EventDate,$Title,$Location,$Organizer,$Details,$Type,$Category)
{
	$session = buildAdminCon();
	$newevent = $session->run("MATCH (p:Person{Email:{email} })
	CREATE (e:Event{Title: {title}, StartTime: {eventdate}, Location: {location}, 
	Details:{details}, Category: {category}, Type:{type}, Status: 'Approved', DateCreated: timestamp() })<-[:Is_Organizer_Of]-(p)
	",["email"=>$Organizer,"title"=>$Title,"eventdate"=>$EventDate,"location"=>$Location,
	"details"=>$Details,"category"=>$Category,"type"=>$Type]);
}


function getAllEvents()
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person) 
	return e.Title as Title, 
	e.StartTime as Start_Time, 
	e.Category as Category, 
	e.DateCreated as DateCreated,
	e.Location as Location, 
	e.Type as Type, 
	e.Details as Details, 
	e.Status as Status, 
	p.Alias as Alias, 
	p.Email as Email
	ORDER BY e.StartTime ASC");
	return $EventList;
}

function getFrontPageEvents()
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person) 
	WHERE toFloat(e.StartTime) > toFloat(timestamp())
	and e.Status = 'Approved'
	return e.Title as Title, 
	e.StartTime as StartTime, 
	e.DateCreated as DateCreated,
	e.Category as Category, 
	e.Location as Location, 
	e.Type as Type, 
	e.Details as Details, 
	p.Alias as Alias, 
	p.Email as Email
	ORDER BY e.StartTime ASC LIMIT 10");
	return $EventList;
}

function deleteEvent($DateCreated, $Type,$Alias)
{
	$session =buildAdminCon();
	$query = $session->run("MATCH (e:Event{DateCreated: toInt({datecreated}) })<-[:Is_Organizer_Of]-(p:Person{Alias:{alias} })
	DETACH DELETE e",["datecreated"=>$DateCreated,"type"=>$Type,"alias"=>$Alias]);
}

function adminDeleteEvent($DateCreated)
{
	$session =buildAdminCon();
	$query = $session->run("MATCH (e:Event{DateCreated: toInt({datecreated}) })
	DETACH DELETE e",["datecreated"=>$DateCreated]);
}

function adminDeleteNews($DateCreated)
{
	$session =buildAdminCon();
	$query = $session->run("MATCH (n:News{DateWritten: toInt({datecreated}) })
	DETACH DELETE n",["datecreated"=>$DateCreated]);
}

function editEvent($OldTitle,$OldOrg,$OldType,$OldStart,$NewTitle,$NewDate,$NewAlias,$NewLoc,$NewOrg,$NewDetails,$NewCat,$NewType,$NewStatus)
{
	$session = buildAdminCon();
	$ModifiedEvent = $session->run("
	MATCH (e:Event{Title: {oldtitle}, StartTime: toInteger({oldstart}), Type: {oldtype} })<-
	[r:Is_Organizer_Of]-
	(p:Person{Email: {email} })
	delete r
	WITH e
	set e.Title = {newtitle}
	set e.StartTime = toInteger({newstart})
	set e.Location = {newloc}
	set e.Details = {newdetails}
	set e.Category = {newcat}
	set e.Type = {newtype}
	set e.Status = {newstatus}
	WITH e
	MATCH (p2:Person{Email: {neworg} })
	CREATE (p2)-[:Is_Organizer_Of]->(e)
	",
	["oldtitle"=>$OldTitle,
	"oldstart"=>$OldStart,
	"oldtype"=>$OldType,
	"email"=>$OldOrg,
	"newtitle"=>$NewTitle,
	"newstart"=>$NewDate,
	"newloc"=>$NewLoc,
	"newdetails"=>$NewDetails,
	"newcat"=>$NewCat,
	"newtype"=>$NewType,
	"newstatus"=>$NewStatus,
	"neworg"=>$NewOrg]);
}

function registerNewUser($Email, $Password, $Alias, $FirstName, $LastName)
{
	$Password = password_hash($Password, PASSWORD_DEFAULT);
	$session = buildAdminCon();
 	$NewUser = $session->run("CREATE (p:Person{First_Name: {firstname},
	Last_Name: {lastname}, 
	Alias: {alias}, 
	Password: {password}, 
	DateJoined: timestamp(), 
	UserType: 'Subscriber',
	Email: {email} })",
  ["firstname"=>$FirstName,"lastname"=>$LastName,"alias"=>$Alias,"password"=>$Password,"email"=>$Email]);
}

function searchAllUsers($SearchBy)
{
	$session = buildCon();
	$UserList = $session->run("MATCH (p:Person) return p.First_Name as FirstName, p.Last_Name as LastName, 
	p.Alias as Alias, p.Email as Email ORDER by p.{searchby} ASC",["searchby"=>$SearchBy]);
	return $UserList;
}

function searchUsersBySpecifics($SearchBy, $Keyword)
{
	$session = buildCon();
	$UserList = $session->run("MATCH (p:Person)
	WHERE toLower(p.{searchby}) CONTAINS {keyword} return p.First_Name as FirstName, p.Last_Name as LastName, 
	p.Alias as Alias, p.Email as Email ORDER BY p.{searchParams} ASC",
	["searchby"=>$SearchBy,"keyword"=>$Keyword,"searchParams"=>$SearchBy]);
	return $UserList;
}

function getAllComicadiaComics()
{
	$session = buildCon();
	$ComicList = $session->run("OPTIONAL MATCH (t:Theme)<-[:Has_Elements_Of]-(w:Webcomic)<-[:Works_On]-(p:Person)
	WHERE w.Membership = 'Comicadia'
	and w.Status = 'Active'
	return w.Name as Name, w.URL as URL, w.Synopsis as Synopsis, w.Pitch as Pitch, w.Status as Status, collect(DISTINCT t.Name) as Themes, SUM(toInt(t.Value)) as Rating ORDER BY w.Name ASC");
	return $ComicList;
}

function getAllComicadiaComicsInRandomOrder()
{
	$session = buildCon();
	$ComicList = $session->run("OPTIONAL MATCH (t:Theme)<-[:Has_Elements_Of]-(w:Webcomic)<-[:Works_On]-(p:Person)
	WHERE w.Membership = 'Comicadia'
	and w.Status = 'Active'
	WITH w.ComicID as ComicID, w.Name as Name, w.URL as URL, w.Synopsis as Synopsis, w.Pitch as Pitch, w.Status as Status, collect(DISTINCT t.Name) as Themes, SUM(toInt(t.Value)) as Rating
    RETURN ComicID, Name, URL, Synopsis, Pitch, Status, Themes, Rating,  rand() as r	ORDER BY r ASC");
	return $ComicList;
}

function getAllWebcomicThemes()
{
	$session = buildCon();
	$ThemeList = $session->run("MATCH (t:Theme)
	RETURN t.Name as Name, t.Value as Value 
	ORDER BY t.Name ASC");
	return $ThemeList;
}

function createTheme($ThemeName, $ThemeRating)
{
	$session = buildAdminCon();
	$NewTheme = $session->run("CREATE (t:Theme{Name: {name}, 
	Value: {value}, 
	Status: 'Approved' })",
	["name"=>$ThemeName,"value"=>$ThemeRating]);
}

function editTheme($OldName, $NewName, $NewRating)
{
	$session = buildAdminCon();
	$ThemeEdit = $session->run("MATCH (t:Theme{Name: {oldname} })
	SET t.Name = {newname}
	SET t.Value = {newrating}",
	["oldname"=>$OldName,"newname"=>$NewName,"newrating"=>$NewRating]);
}

function deleteTheme($Name)
{
	$session = buildAdminCon();
	$Deleter = $session->run("MATCH (t:Theme{Name: {name}})
	DETACH DELETE t",
	["name"=>$Name]);
}

function createGenre($Name)
{
	$session = buildAdminCon();
	$NewGenre = $session->run("CREATE (g:Genre{Name: {name}, 
	Status: 'Approved', 
	DateCreated: timestamp() })",
	["name"=>$Name]);
}

function editGenre($OldName, $NewName)
{
	$session = buildAdminCon();
	$Modify = $session->run("MATCH (g:Genre{Name: {oldname} })
	SET g.Name = {newname}",
	["oldname"=>$OldName,"newname"=>$NewName]);
}

function deleteGenre($Name)
{
	$session = buildAdminCon();
	$Deleter = $session->run("MATCH (g:Genre{Name: {name}})
	DETACH DELETE g",
	["name"=>$Name]);
}

function getAllMediaForWebcomic($ComicID)
{
	$session = buildCon();
	$MediaList = $session->run("MATCH (mt:MediaType)<-[]-(m:Media)-[]->(w:Webcomic{ComicID: toInt({comicid}) })
	return mt.Type as Type, 
	m.URL as URL, 
	m.Status as Status 
	ORDER BY m.Type ASC"
	,["comicid"=>$ComicID]);
	return $MediaList;
}

function getWebcomicMediaOfType($MediaType, $ComicID)
{
	$session = buildCon();
	$MediaList = $session->run("OPTIONAL MATCH (mt:MediaType{Name: {type}})<-[:Is_Media_Of]-(m:Media)-[:Is_Media_For]->(w:Webcomic{ComicID: {comicid} })
	return mt.Name as Type, 
	m.URL as URL, 
	m.Status as Status 
	ORDER BY m.Type ASC"
	,["type"=>$MediaType,"comicid"=>$ComicID]);
	return $MediaList;
}

function mediaCountForWebcomicByType($MediaType, $ComicID)
{
	$session = buildCon();
	$MediaList = $session->run("OPTIONAL MATCH (mt:MediaType{Name:{type}})<-[:Is_Media_Of]-(m:Media)-[:Is_Media_For]->(w:Webcomic{ComicID: toInt({comicid}) })
	return count(m) as mediaCount"
	,["type"=>$MediaType,"comicid"=>$ComicID]);
	$record = $MediaList->getRecord();
	$mediaCount = $record->value('mediaCount');
	return $mediaCount;
}

function getUserProfilePic($Alias)
{
	$session = buildCon();
	$ProfilePic = $session->run("MATCH (p:Person{Alias: {alias} })
	return p.ProfilePic as ProfilePic"
	,["alias"=>$Alias]);
	$ImgURL = $ProfilePic->getRecord();
	$URL = $ImgURL->value('ProfilePic');
	return $URL;
}

function checkURL($URL)
{
	$websiteErr = 'Website';
	$website = test_input($URL);
	if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website)) 
	{
		$websiteErr = "Invalid URL"; 
	}
	return $websiteErr;
}

function addProfilePic($ImgURL,$Alias)
{
	$session = buildAdminCon();
	$Uploader = $session->run("MATCH (p:Person{Alias: {alias} })
	SET p.ProfilePic = {imgurl}
	",["alias"=>$Alias,"imgurl"=>$ImgURL]);
}

function addMediaForWebcomic($Type, $ImgURL, $WebcomicID, $Artist, $Uploader, $Alt)
{
	$session = buildAdminCon();
	$AddedMedia = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) }), 
	(a:Person{Alias: {artistalias} }), 
	(u:Person{Alias: {uploader} }),
	(mt:MediaType{Name:{type}} )
	CREATE (a)<-[:Was_Drawn_By]-
	(m:Media{URL: {url},
	Status: 'Active', 
	DateUploaded: timestamp(),
	Alt: {alt},
	Click: 0,
	Views: 0 })
	-[:Was_Uploaded_By]->(u)
	WITH m, w, mt
	CREATE (mt)<-[:Is_Media_Of]-(m)-[:Is_Media_For]->(w)",
	["comicid"=>$WebcomicID,"artistalias"=>$Artist,"uploader"=>$Uploader,"type"=>$Type,"url"=>$ImgURL,"alt"=>$Alt]);
}

function getArtistDetailsForMedia($URL)
{
	$session = buildCon();
	$ArtistDetails = $session->run("OPTIONAL MATCH (m:Media{URL: {url}})
	-[:Was_Drawn_By]->(p:Person)
	RETURN p.Alias as Alias, 
	p.First_Name as FirstName, 
	p.Last_Name as LastName",["url"=>$URL]);
	$result = $ArtistDetails->getRecord();
	return $result;
}

/*

The following two functions are to check whether a URL is valid or not. 

*/

function isValidUrl($url)
{
	// first do some quick sanity checks:
	if(!$url || !is_string($url))
	{
		return false;
	}
	// quick check url is roughly a valid http request: ( http://blah/... ) 
	if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) )
	{
		return false;
	}
	// the next bit could be slow:
	//if(getHttpResponseCode_using_curl($url) != 200)
	
	if(getHttpResponseCode_using_getheaders($url) != 200)
	{  // use this one if you cant use curl
		return false;
	}
	// all good!
	return true;
}

function getHttpResponseCode_using_getheaders($url)
{
	$followredirects = true;
	// returns string responsecode, or false if no responsecode found in headers (or url does not exist)
	// NOTE: could potentially take up to 0-30 seconds , blocking further code execution (more or less depending on connection, target site, and local timeout settings))
	// if $followredirects == false: return the FIRST known httpcode (ignore redirects)
	// if $followredirects == true : return the LAST  known httpcode (when redirected)
	if(! $url || ! is_string($url)){
		return false;
	}
	$headers = @get_headers($url);
	if($headers && is_array($headers)){
		if($followredirects){
			// we want the the last errorcode, reverse array so we start at the end:
			$headers = array_reverse($headers);
		}
		foreach($headers as $hline){
			// search for things like "HTTP/1.1 200 OK" , "HTTP/1.0 200 OK" , "HTTP/1.1 301 PERMANENTLY MOVED" , "HTTP/1.1 400 Not Found" , etc.
			// note that the exact syntax/version/output differs, so there is some string magic involved here
			if(preg_match('/^HTTP\/\S+\s+([1-9][0-9][0-9])\s+.*/', $hline, $matches) ){// "HTTP/*** ### ***"
				$code = $matches[1];
				return $code;
			}
		}
		// no HTTP/xxx found in headers:
		return false;
	}
	// no headers :
	return false;
}

function updateWebcomicFromUser($ComicID,$NewComicName,$URL,$RSS,$Synopsis,$Format,$Pitch)
{
	$session = buildAdminCon();
	$result = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) } )
	SET w.Name = {newname}
	SET w.URL = {url}
	SET w.RSS = {rss}
	SET w.Synopsis = {synopsis}
	SET w.Format = {format}
	SET w.Pitch = {pitch}
	",["comicid"=>trim($ComicID),"newname"=>$NewComicName,"url"=>$URL,"rss"=>$RSS,"synopsis"=>$Synopsis,"format"=>$Format,"pitch"=>$Pitch]);
}


function deleteMedia($ImgURL)
{
	$session = buildAdminCon();
	$result = $session->run("MATCH (m:Media{URL: {imgurl} })
	DETACH DELETE m",["imgurl"=>$ImgURL]);
}

function activateMedia($ImgURL)
{
	$session= buildAdminCon();
	$resul = $session->run("MATCH (m:Media{URL: {imgurl} })
	SET m.Status = 'Active'",["imgurl"=>$ImgURL]);
}

function deactivateMedia($ImgURL)
{
	$session= buildAdminCon();
	$result = $session->run("MATCH (m:Media{URL: {imgurl} })
	SET m.Status = 'Inactive'",["imgurl"=>$ImgURL]);
}

function getThisWebcomicGenre($ComicID)
{
	$session = buildCon();
	$result = $session->run("MATCH (g:Genre)
	<-[:Is_Of_Genre]-
	(w:Webcomic{ComicID: toInt({comicid})} )
	return g.Name as Name LIMIT 3
	",["comicid"=>$ComicID]);
	return $result;
}

function getAllThemes()
{
	$session = buildCon();
	$ThemeList = $session->run("MATCH (t:Theme)
	return t.Name as Name ORDER BY t.Name ASC");
	return $ThemeList;
}

function getFilteredThemes($Term)
{
	$session = buildCon();
	$ThemeList = $session->run("OPTIONAL MATCH (t:Theme)
	WHERE toLower(t.Name) CONTAINS toLower({term})
	return t.Name as Name ORDER BY t.Name ASC",["term"=>$Term]);
	return $ThemeList;
}

function getWebcomicThemes($ComicID)
{
	$session = buildCon();
	$ThemeList = $session->run("MATCH (t:Theme)<-[:Has_Elements_Of]-(w:Webcomic{ComicID: toInt({comicid}) })
	return t.Name as Name ORDER BY t.Name ASC",["comicid"=>$ComicID]);
	return $ThemeList;
}

function clearGenresOfWebcomic($ComidID)
{
	$session = buildAdminCon();
	$Eraser = $session->run("MATCH (g:Genre)<-[r:Is_Of_Genre]-(w:Webcomic{ComicID: toInt({comicid}) })
	delete r",["comicid"=>$ComicID]);
}

function addGenreToWebcomic($Genre,$ComicID)
{
	$session = buildAdminCon();
	$newTheme = $session->run("MATCH (g:Genre{Name: {genre} }), (w:Webcomic{ComicID: toInt({comicid}) })
	CREATE (g)<-[:Is_Of_Genre]-(w)
	",["genre"=>$Genre,"comicid"=>$ComicID]);
}

function getActiveThemes()
{
	$session = buildCon();
	$ThemeList = $session->run("MATCH (t:Theme)
	where t.Status = 'Approved'
	return t.Name as Name ORDER BY t.Name ASC");
	return $ThemeList;
}

function clearAllThemesFromComic($ComicID)
{
	$session = buildAdminCon();
	$Remover = $session->run("MATCH (t:Theme)<-[r:Has_Elements_Of]-(w:Webcomic{ComicID: toInt({comicid}) })
	DELETE r",["comicid"=>$ComicID]);
}

function addThemeToComic($Theme, $ComicID)
{
	$session = buildAdminCon();
	$Adder = $session->run("MATCH (t:Theme{Name:{themename}}), (w:Webcomic)
	WHERE toInt(w.ComicID) = toInt({comicid})
	CREATE (t)<-[:Has_Elements_Of]-(w)
	",["themename"=>$Theme,"comicid"=>$ComicID]);
}	

function createUserEvent($PubDate,$Title,$Organizer,$Details,$Type,$Location,$Category,$Status)
{
	$session = buildAdminCon();
	$newEvent = $session->run("MATCH (p:Person{Alias: {alias} })
	CREATE (e:Event{Category: {category},
	Details: {details},
	Location: {location},
	Status: {status},
	Title: {title},
	Type: {type},
	StartTime: {pubdate},
	DateCreated: timestamp() })<-[:Is_Organizer_Of]-(p)
	",["alias"=>$Organizer,
	"category"=>$Category,
	"details"=>$Details,
	"location"=>$Location,
	"status"=>$Status,
	"title"=>$Title,
	"type"=>$Type,
	"pubdate"=>$PubDate]);

	
}

function removeOrganizer($DateWritten, $Alias)
{
	$session = buildAdminCon();
	$removal = $session->run("MATCH (e:Event{DateCreated: {datewritten} })>-[r:Is_Organizer_Of]-(p:Person{Alias:{organizer}} )
	delete r",["datewritten"=>$DateWritten, "organizer"=>$Alias]);
}

function editUserEvent($DateWritten,$PubDate,$Title,$Organizer,$Details,$Type,$Location,$Category)
{
	$session = buildAdminCon();
	$Event = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person{Alias:{organizer} })
	WHERE e.DateCreated = toFloat({datewritten})
	SET e.Title = {title}
	SET e.StartTime = {pubdate}
	SET e.Details = {details}
	SET e.Type = {type}
	SET e.Location = {location}
	SET e.Category = {category}
	",["organizer"=>$Organizer,
	"datewritten"=>$DateWritten,
	"title"=>$Title,
	"pubdate"=>$PubDate,
	"details"=>$Details,
	"type"=>$Type,
	"location"=>$Location,
	"category"=>$Category]);
}

function deleteUserEvent($DateCreated)
{
	$session = buildAdminCon();
	$Deleted = $session->run("MATCH (e:Event)
	WHERE e.DateCreated = toFloat({datecreated})
	DETACH DELETE e",["datecreated"=>$DateCreated]);
}

function createUserWebcomic($Title, $Alias, $URL, $RSS, $Synopsis, $Format, $Pitch)
{
	$session=buildAdminCon();
	$NewWebcomic = $session->run("MATCH (p:Person{Alias: {alias}})
	CREATE (w:Webcomic{Name: {title},
	URL: {url},
	RSS: {rss},
	Synopsis: {synopsis},
	Format: {format},
	DateAdded: timestamp(),
	Pitch: {pitch},
	Status: 'Pending'})<-[:Works_On{Role: 'Creator'}]-(p)
	WITH toString(toInt(round(rand() * 100))) as First,
	toString(timestamp()) as Stamp, w
	set w.ComicID = toInt(First + Stamp) 
	",["alias"=>$Alias,
	"title"=>$Title,
	"url"=>$URL,
	"rss"=>$RSS,
	"synopsis"=>$Synopsis,
	"format"=>$Format,
	"pitch"=>$Pitch]);
}

function getMediaDimensionsByType($MediaType)
{
	$session = buildCon();
	$Dimensions = $session->run("MATCH (mt:MediaType{Name: {mediatype}})
	return mt.Height as Height, mt.Width as Width",["mediatype"=>$MediaType]);
	$AcceptableDimensions = $Dimensions->getRecord();
	return $AcceptableDimensions;
}

function getSingleSquareBannerForWebcomic($ComicID)
{
	$session = buildCon();
	$HorizBanner = $session->run("MATCH (mt:MediaType{Name: 'Square'})
	<-[:Is_Media_Of]-
	(m:Media)
	-[:Is_Media_For]->
	(w:Webcomic{ComicID: toInt({comicid}) })
	WHERE m.Status = 'Active'
	AND w.Status = 'Active'
	return m.URL as URL, rand() as r ORDER BY r LIMIT 1",["comicid"=>$ComicID]);
	$record = $HorizBanner->getRecord();
	if($record)
	{
		$result=$record->value("URL");
	}
	else
	{
		$result = null;
	}
	
	return $result;
}

function getMostRecentlyAddedWebcomic()
{
	$session = buildCon();
	$ComicList = $session->run("MATCH (w:Webcomic) 
	return w.Name as ComicName, w.ComicID as ComicID, w.DateAdded as DateAdded
	ORDER BY w.DateAdded DES LIMIT 1");
	$result = $ComicList->getRecord();
	return $ComicList->value("ComicID");
}

function searchComicByName($ComicName)
{
	$session = buildCon();
	$ComicList = $session->run("MATCH (w:Webcomic) 
	WHERE toLower(w.Name) CONTAINS toLower({comicname})
	and w.Membership = 'Comicadia'
	and w.Status = 'Active'
	return w.Name as ComicName ORDER BY w.Name ASC",["comicname"=>$ComicName]);
	return $ComicList;
}

function searchComicByTheme($ThemeArray)
{
	$session = buildCon();
	$ComicList = $session->run("MATCH (w:Webcomic)-[:Has_Elements_Of]->(t:Theme) 
	WHERE t.Name IN {themearray}
	and w.Membership = 'Comicadia'
	and w.Status = 'Active'
	return DISTINCT w.Name as ComicName ORDER BY w.Name ASC",["themearray"=>$ThemeArray]);
	return $ComicList;
}

function searchComicByGenre($GenreArray)
{
	$session = buildCon();
	$ComicList = $session->run("MATCH (w:Webcomic)-[:Is_Of_Genre]->(g:Genre) 
	WHERE g.Name IN {genrearray}
	and w.Membership = 'Comicadia'
	and w.Status = 'Active'
	return DISTINCT w.Name as ComicName ORDER BY w.Name ASC",["genrearray"=>$GenreArray]);
	return $ComicList;
}

function getComicSquareDetails($ComicID)
{
	
	$session = buildCon();
	$ComicList = $session->run("OPTIONAL MATCH (t:Theme)<-[:Has_Elements_Of]-(w:Webcomic{ComicID: toInt({comicid}) })<-[:Works_On]-(p:Person)
	WHERE w.Membership = 'Comicadia'
	and w.Status = 'Active'
	return w.Name as Name, w.URL as URL, w.Pitch as Pitch, w.Status as Status, collect(DISTINCT t.Name) as Themes, SUM(toInt(t.Value)) as Rating
	",['comicid'=>$ComicID]);
	return $ComicList;
}

function getEventAttendees($DateCreated)
{
	$session = buildCon();
	$AttendeeList = $session->run("MATCH (e:Event{DateCreated: {datecreated} })<-[:Is_Attending]-(p:Person)
	return p.Alias as Alias",["datecreated"=>$DateCreated]);
	return $AttendeeList;
}

function getWhetherMemberIsAttendingEvent($Alias, $DateCreated)
{
	$session = buildCon();
	$IsAttending = $session->run("OPTIONAL MATCH (e:Event{DateCreated: {datecreated}})<-[:Is_Attending]-(p:Person)
	where p.Alias = {alias}
	return count(p) as counter",["datecreated"=>$DateCreated,"alias"=>$Alias]);
	$Answer = $IsAttending->getRecord();
	$count = $Answer->value("counter");
	if($count > 0)
	{
		$result = TRUE;
	}
	else
	{
		$result = FALSE;
	}
	return $result;
}

function confirmMemberAttendanceToEvent($Alias,$DateCreated)
{
	$session = buildAdminCon();
	$confirmUser = $session->run("MATCH (p:Person{Alias: {alias} }), (e:Event)
	WHERE toFloat(e.DateCreated) = toFloat({datecreated})
	CREATE (p)-[:Is_Attending{DateConfirmed: timestamp()}]->(e)",["alias"=>$Alias,"datecreated"=>$DateCreated]);
}

function cancelMemberAttendanceToEvent($Alias, $DateCreated)
{
	$session = buildAdminCon();
	$confirmUser = $session->run("MATCH (p:Person{Alias: {alias} })-[r:Is_Attending]->(e:Event)
	WHERE toFloat(e.DateCreated) = toFloat({datecreated})
	DELETE r",["alias"=>$Alias,"datecreated"=>$DateCreated]);
}

function deleteNewsPost($DateWritten, $Alias)
{
	$session = buildAdminCon();
	$deleteNews = $session->run("match (n:News)-[:Was_Posted_By]->(p:Person{Alias: {alias} })
	WHERE toFloat(n.DateWritten) = toFloat({datewritten})
	DETACH DELETE n",["alias"=>$Alias,"datewritten"=>$DateWritten]);
}


function deleteWebcomic($ComicID, $Alias)
{
	$session = buildAdminCon();
	$Deleter = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })<-[:Works_On]-(p:Person{Alias: {alias} })
	DETACH DELETE w",["comicid"=>$ComicID,"alias"=>$Alias]);
}

function deleteUser($Alias)
{
	$session = buildAdminCon();
	$Deleter = $session->run("MATCH (p:Person{Alias: {alias} })
	DETACH DELETE p",["alias"=>$Alias]);
}

function removeProfilePic($Alias)
{
	$session =buildAdminCon();
	$Deleter = $session->run("MATCH (p:Person{Alias: {alias} })
	set p.ProfilePic = ''",["alias"=>$Alias]);
}

/********************************************************
The following blocks of code are specifically dsigned to work with
the rotating boxes for the Comicadia widget.
********************************************************/
function rotateHorizontalAdsByType($AdType,$Count,$ComicID)
{	
	$session = buildCon();
	
	$Ads = $session->run("MATCH (mt:MediaType{Name: {mediatype}})<-[:Is_Media_Of]-
	(m:Media)-[:Is_Media_For]->
	(w:Webcomic) 
	WHERE w.ComicID <> toInt({comicid}) 
	AND w.Membership = 'Comicadia'
	AND w.Status = 'Active' 
	WITH distinct w, mt, collect(m) as ms 
	RETURN ms[0].URL as ImgURL, w.Name as ComicName, rand() as r 
	ORDER BY r ASC Limit toInt({adcount}) ",
	["mediatype"=>$AdType,"adcount"=>$Count,"comicid"=>$ComicID]);
	return $Ads;
}

function getComicHorizontalURLCount($ComicID)
{
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType{Name: 'Horizontal Banner'})<-[:Is_Media_Of]-(m:Media)-[:Is_Media_For]->(w:Webcomic{ComicID: toInt({comicid}) } )
	return COUNT(m) as mediaCount",
	["comicid"=>$ComicID]);
	$record = $query->getRecord();
	return $record->value("mediaCount");
}

function getComicHorizontalURL($ComicID)
{
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType{Name: 'Horizontal Banner'})<-[:Is_Media_Of]-(m:Media)-[:Is_Media_For]->(w:Webcomic{ComicID: toInt({comicid}) } )
	return m.URL as URL, rand() as r ORDER BY r ASC LIMIT 1",
	["comicid"=>$ComicID]);
	$record = $query->getRecord();
	if($record)
	{
		return $record->value("URL");
	}
	else
	{
		return null;
	}
}

function getWebcomicURLbyImgURL($ImgURL)
{
	$session = buildCon();
	$query = $session->run("MATCH (w:Webcomic)<-[:Is_Media_For]-(m:Media{URL: {ImgURL}} )
	return w.URL as ComicURL",["ImgURL"=>$ImgURL]);
	$record = $query->getRecord();
	return $record->value("ComicURL");
}

function getWebcomicURLbyComicID($ComicID)
{
	$session = buildCon();
	$URL = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	return w.URL as ComicURL",["comicid"=>$ComicID]);
	$record = $URL->getRecord();
	$result = $record->value("ComicURL");
	return $result;
}

function addClickToMediaByComicID($ImgURL,$ComicID)
{
	$session = buildAdminCon();
	$AddClick = $session->run("MATCH (m:Media{URL: {imgurl} }), (w:Webcomic{ComicID: toInt({comicid}) })
	CREATE (m)-[:Got_Clicked_On{TimeClicked: timestamp()}]->(w)",["imgurl"=>$ImgURL,"comicid"=>$ComicID]);
}

function getMediaClicks($ImgURL)
{
	$session = buildCon();
	$Stats = $session->run("MATCH (m:Media{URL: toString({imgurl}) } )-[c:Got_Clicked_On]->(w:Webcomic)
	return COUNT(c) as Clicks
	",["imgurl"=>$ImgURL]);
	$record = $Stats->getRecord();
	$result = $record->value("Clicks");
	return $result;
}

function getMediaViews($ImgURL)
{
	$session = buildCon();
	$Stats = $session->run("MATCH (m:Media{URL: toString({imgurl}) } )-[v:Was_Viewed_On]->(w:Webcomic)
	return COUNT(v) as Views
	",["imgurl"=>$ImgURL]);
	$record = $Stats->getRecord();
	$result = $record->value("Views");
	return $result;
}

function addViewToMediaByURL($imgURL)
{
	$session = buildAdminCon();
	$updater = $session->run("Match (m:Media{URL: {imgurl} }), (w:Webcomic{ComicID: toInt({comicid}) })
	SET m.Views = m.Views + 1",["imgurl"=>$imgURL,"comicid"=>$ComicID]);
}

function addViewToMediaByComicID($ImgURL, $ComicID)
{
	$session = buildAdminCon();
	$updater = $session->run("Match (m:Media{URL: {imgurl} }), (w:Webcomic{ComicID: toInt({comicid}) })
	CREATE (m)-[:Was_Viewed_On{TimeViewed: timestamp()}]->(w)",
	["imgurl"=>$ImgURL,"comicid"=>$ComicID]);
}

function checkComicID($ComicID)
{
	$session = buildCon();
	$Comic = $session->run("OPTIONAL MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	return count(w) as ComicCheck",
	["comicid"=>$ComicID]);
	$record = $Comic->getRecord();
	$result = $record->value("ComicCheck");
	if($result != 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}


/*****************************************************
End Rotator codes
******************************************************/
function deleteComicByID($ComicID)
{
	$session = buildAdminCon();
	$Deleter = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })
	DETACH DELETE w",["comicid"=>$ComicID]);
}

function getCadenceSplash()
{
	$session = buildCon();
	$CadenceList = $session->run("OPTIONAL MATCH (m:Media)-[:Is_Media_Of]->(mt:MediaType{Name: 'Cadence Announce'})
	WHERE m.Status = 'Active'
	return m.URL as URL");
	$MediaURL = $CadenceList->getRecord();
	$URL = $MediaURL->value("URL");
	return $URL;
}

function getCadenceArtList()
{
	$session = buildCon();
	$CadenceList = $session->run("OPTIONAL MATCH (m:Media)-[:Is_Media_Of]->(mt:MediaType)
	WHERE mt.Name CONTAINS 'Cadence'
	return m.URL as URL");
	return $CadenceList;
}

function approveEvent($DateCreated, $Alias)
{
	$session = buildAdminCon();
	$Approver = $session->run("MATCH (e:Event{DateCreated: toInt({datecreated}) })
	SET e.Status = 'Approved'
	WITH e
	MATCH (p:Person{Alias: {alias} })
	CREATE (p)-[:Approved]->(e)",["datecreated"=>$DateCreated,"alias"=>$Alias]);
}


function approveNews($DateCreated, $Alias)
{
	$session = buildAdminCon();
	$Approver = $session->run("MATCH (n:News{DateWritten: toInt({datecreated}) })
	SET n.Status = 'Approved'
	WITH n
	MATCH (p:Person{Alias: {alias} })
	CREATE (p)-[:Approved]->(n)",["datecreated"=>$DateCreated,"alias"=>$Alias]);
}


function wasEventAlreadyApproved($EventID)
{
	$session = buildCon();
	$Event = $session->run("OPTIONAL MATCH (e:Event{DateCreated: toInt({datecreated}), Status: 'Approved' })
	RETURN COUNT(e) as result",["datecreated"=>$EventID]);
	$record = $Event->getRecord();
	$Count = $record->value("result");
	$result = FALSE;
	if($Count > 0)
	{
		$result = TRUE;
	}
	return $result;
}

function wasNewsAlreadyApproved($NewsID)
{
	$session = buildCon();
	$Post = $session->run("OPTIONAL MATCH (n:News{DateWritten: toInt({datecreated}), Status: 'Approved' })
	RETURN COUNT(n) as result",
	["datecreated"=>$NewsID]);
	$record = $Post->getRecord();
	$Count = $record->value("result");
	$result = FALSE;
	if($Count > 0)
	{
		$result = TRUE;
	}
	return $result;
}

function getSplash()
{
	$session = buildCon();
	$Splash = $session->run("MATCH (p:Person)<-[:Was_Posted_By]-(s:Splash)-[:Is_Using]->(m:Media)
	WHERE s.Status = 'Active'
	return s.Text as Text,
	s.Title as Title,
	m.URL as URL,
	p.Alias as Poster,
	s.DateCreated as DateCreated LIMIT 1");
	$result = $Splash->getRecord();
	return $result;	
}


function addMediaByAdmin($Type, $ImgURL, $Artist,$Uploader, $Alt)
{
	$session = buildAdminCon();
	$AddedMedia = $session->run("MATCH (a:Person{Alias: {artistalias} }), 
	(u:Person{Alias: {uploader} }),
	(mt:MediaType{Name:{type}} )
	CREATE (a)<-[:Was_Drawn_By]-
	(m:Media{URL: {url},
	Status: 'Active', 
	DateUploaded: timestamp(),
	Alt: {alt} })
	-[:Was_Uploaded_By]->(u)
	WITH m, mt
	CREATE (mt)<-[:Is_Media_Of]-(m)",
	["artistalias"=>$Artist,"uploader"=>$Uploader,"type"=>$Type,"url"=>$ImgURL,"alt"=>$Alt]);
}

function updateSplashArt($URL)
{
	$session = buildAdminCon();
	$NewArt = $session->run("MATCH (m:Media)<-[r:Is_Using]-(s:Splash{Status: 'Active'})
	DELETE r
	with s
	MATCH (newm:Media{URL: {url} }) 
	CREATE (newm)<-[:Is_Using]-(s)",
	["url"=>$URL]);
}

function createSplashMessage($Title,$Text,$Alias,$URL)
{
	$session = buildAdminCon();
	$Splash = $session->run("MATCH (p:Person{Alias: {alias}}), (m:Media{URL: {url}})
	CREATE (p)<-[:Was_Posted_By]-(s:Splash{DateCreated: timestamp(), Text: {text}, Title: {title}, Status: 'Active' })-[:Is_Using]->(m)",
	["alias"=>$Alias,"url"=>$URL,"text"=>$Text,"title"=>$Title]);
}

function updateSplashMessage($Title,$Text,$Alias,$URL)
{
	$session = buildAdminCon();
	$OldSplash = $session->run("MATCH (s:Splash{Status:'Active'})
	SET s.Status = 'Inactive'");
	$NewSplash = $session->run("MATCH (p:Person{Alias: {alias}}), (m:Media{URL: {url}})
	CREATE (m)<-[:Is_Using]-(s:Splash{Status: 'Active', DateCreated: timestamp(), Text: {text}, Title: {title} })-[:Was_Posted_By]->(p)",
	["alias"=>$Alias,"url"=>$URL,"text"=>$Text,"title"=>$Title]);
}

function checkIfSplashMessageExists()
{
	$session = buildCon();
	$Exists = $session->run("MATCH (p:Person)<-[:Was_Posted_By]-(s:Splash{Status: 'Active'})-[:Is_Using]->(m:Media)
	return count(s) as Counter ");
	$record = $Exists->getRecord();
	$result = $record->value("Counter");
	if($result == 0)
	{
		$Answer = false;
	}
	else
	{
		 $Answer = true;
	}
	return $Answer;
}

function saveContactMessage($Name, $Email, $Message, $Type)
{
	$session = buildAdminCon();
	$Message = $session->run("CREATE (cm:ContactMessage{Type: {type}, Name:{name}, Email:{email}, Message: {message}, DateCreated: timestamp(), Status: 'Unread' })",
	["type"=>$Type,"name"=>$Name,"email"=>$Email,"message"=>$Message]);
}

function getUnreadMessageCount()
{
	$session = buildCon();
	$query = $session->run("MATCH (cm:ContactMessage{Status: 'Unread'})
	RETURN COUNT(cm) as MessageCount");
	$record = $query->getRecord();
	return $record->value("MessageCount");
}

function getReadMessageCount()
{
	$session = buildCon();
	$query = $session->run("MATCH (cm:ContactMessage)
	WHERE cm.Status <> 'Unread'
	RETURN COUNT(cm) as MessageCount");
	$record = $query->getRecord();
	return $record->value("MessageCount");
}

function getMessageDetails($DateCreated)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (cm:ContactMessage{DateCreated: toInt({datecreated}) })
	RETURN cm.Name as Name, cm.Email as Email, cm.Type as Type, cm.Message as Text, cm.DateCreated as DateCreated, cm.Status as Status",
	["datecreated"=>$DateCreated]);
	return $query;
}

function getUnreadMessages()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (cm:ContactMessage{Status: 'Unread'})
	RETURN cm.DateCreated as DateCreated");
	return $query;
}

function getReadMessages()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (cm:ContactMessage)
	WHERE cm.Status <> 'Unread'
	RETURN cm.DateCreated as DateCreated");
	return $query;
}

function markMessageAsRead($MessageID, $Alias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (cm:ContactMessage{DateCreated: toInt({messageid}) }), (p:Person{Alias: {alias} })
	SET cm.Status = 'Read'
	CREATE (cm)-[:Was_Read_By{DateRead: timestamp()}]->(p)",["messageid"=>$MessageID,"alias"=>$Alias]);
	
}

function removeSocialMediaURL($Alias, $SocMedia)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} })-[r:Is_Subscribed_To]->(sm:SocialMedia{Name: {socmedia}})
	DELETE r"
	,["alias"=>$Alias,"socmedia"=>$SocMedia]);
}

function removeSocialMediaURLFromComic($ComicID, $SocMedia)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) })-[r:Is_Subscribed_To]->(sm:SocialMedia{Name: {socmedia}})
	DELETE r"
	,["comicid"=>$ComicID,"socmedia"=>$SocMedia]);
}

function addSocialMediaURL($Alias, $SocMedia, $URL)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} }), (sm:SocialMedia{Name: {socmedia} }) 
	CREATE (p)-[r:Is_Subscribed_To{URL: {url} }]->(sm)"
	,["alias"=>$Alias,"socmedia"=>$SocMedia,"url"=>$URL]);
}

function addSocialMediaURLToComic($ComicID, $SocMedia, $URL)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (w:Webcomic{ComicID: toInt({comicid}) }), (sm:SocialMedia{Name: {socmedia} }) 
	CREATE (w)-[r:Is_Subscribed_To{URL: {url} }]->(sm)"
	,["comicid"=>$ComicID,"socmedia"=>$SocMedia,"url"=>$URL]);
}

function saveNewSocialMediaType($Name, $Color, $Class)
{
	$session = buildAdminCon();
	$query = $session->run("CREATE (sm:SocialMedia{Name: {name}, BGColor: {color}, CSSClass: {class} })",
	["name"=>$Name,"color"=>$Color,"class"=>$Class]);
}

function removeSocialMediaType($Name)
{
	$session=buildAdminCon();
	$query=$session->run("MATCH (sm:SocialMedia{Name: {name} })
	DETACH DELETE sm",["name"=>$Name]);
}

function editSocialMediaType($Name, $Color, $Class)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (sm:SocialMedia{Name: {name} })
	SET sm.BGColor = {color}
	SET sm.CSSClass = {class}",
	["name"=>$Name,"color"=>$Color,"class"=>$Class]);
}


function doesNewExistByID($NewsID)
{
	$session = buildCon();
	$query  = $session->run("OPTIONAL MATCH (n:News{DateWritten: toInt({newsid})})
	return count(n) as NewsCount",["newsid"=>$NewsID]);
	$record = $query->getRecord();
	if($record->value("NewsCount") > 0)
		return true;
	else
		return false;	
}

function adminSendMessageFromNoReply($Alias, $Recipient, $Subject, $Text)
{
	$headers = "From: no-reply@comicadia.com" . "\r\n";
	$headers .= "Reply-To: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "Return-Path: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "From: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "Organization: Comicadia\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
  
	if(mail($Recipient,$Subject,$Text,$headers))
		echo "Email sent<br>";
	else
		echo "Failed to send email to $Recipient<br>";
	recordAdminSentNoReplyEmail($Alias, $Recipient, $Subject, $Text);
}

function recordAdminSentNoReplyEmail($Alias, $Recipient, $Subject, $Text)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}}), (r:Person{Email: {recipient}})
	CREATE (p)-[:Sent_Email_From_NoReply{DateCreated: timestamp(), Text: {text}, Subject: {subject} }]->(r)",["alias"=>$Alias,"recipient"=>$Recipient,"text"=>$Text,"subject"=>$Subject]);
}

function sendGenericEmailFromNoReply($Recipient, $Subject, $Text)
{
	$headers = "From: no-reply@comicadia.com" . "\r\n";
	$headers .= "Reply-To: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "Return-Path: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "From: No-Reply <no-reply@comicadia.com>\r\n";
	$headers .= "Organization: Comicadia\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
	$headers .= "X-Priority: 3\r\n";
	$headers .= "X-Mailer: PHP". phpversion() ."\r\n";
	mail($Recipient,$Subject,$Text,$headers);
}

/**********************************************************************

The following blocks of code are built for the Comicadia Ad Network

***********************************************************************/

function getCountOfAllAdsByStatus($Status)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement)
	WHERE a.Status IN {status}
	return count(DISTINCT a) as AdCount",
	["status"=>$Status]);
	$record= $query->getRecord();
	return $record->value("AdCount");
}

function getAllAdsByStatus($Status)
{
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType)<-[:Uses_Type]-(a:Advertisement)-[:Uses_Media]->(m:Media)
	WITH m,a,mt
	MATCH (a)-[:Is_Ad_For]->(e)
	WHERE (e:Webcomic OR e:Entity)
	WITH e, m, a, mt
	MATCH (p:Person)-[:Owns_Ad]->(a)
	WHERE a.Status IN {status}
	return a.AdID as AdID,
	a.Name as AdName,
	a.URL as AdLink,
	a.DateCreated as DateCreated,
	a.Status as Status,
	e.ComicID as ComicID,
	e.EntityID as EntityID,
	collect(m.URL) as Media,
	mt.Name as AdType,
	p.Alias as Alias",
	["status"=>$Status]);
	return $query;
}

function getNameForEntityByID($EntityID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	return n.Name as Name",["id"=>$EntityID]);
	$record = $query->getRecord();
	return $record->value("Name");
}

function adminRejectAd($AdID, $Alias, $Reason)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias} }), (a:Advertisement{AdID: toInt({adid}) })
	SET a.Status = 'Rejected'
	CREATE (p)-[r:Rejected_Ad{DateCreated: timestamp(), Reason: {reason} }]->(a)",
	["alias"=>$Alias,"adid"=>$AdID,"reason"=>$Reason]);
}

function adminApproveAd($AdID, $Alias)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (p:Person{Alias: {alias}}), (a:Advertisement{AdID: toInt({adid}) })
	SET a.Status = 'Approved'
	CREATE (p)-[:Approved_Ad{DateCreated: timestamp()}]->(a)",
	["alias"=>$Alias,"adid"=>$AdID]);
}

function getAllPendingCampaignsCount()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(p)-[:Owns_Ad]->(a:Advertisement)-[rc:Ran_Campaign]->(ac)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	RETURN count(n) as PendingCount");
	$record = $query->getRecord();
	return $record->value("PendingCount");
}

function getAllPendingCampaigns()
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH n=(p)-[:Owns_Ad]->(a:Advertisement)-[rc:Ran_Campaign]->(ac)
	WHERE rc.Status IN ['Pending Payment', 'Pending Review', 'Active','Paused']
	WITH a, ac, rc, p
	MATCH (a)-[:Uses_Type]->(mt:MediaType)
	WITH a, ac, rc, p, mt
	MATCH (a)-[:Uses_Media]->(m)
	RETURN rc.Status as Status,
	rc.DateCreated as DateCreated,
	rc.RequestedDate as RequestedDate,
	a.Name as AdName,
	a.AdID as AdID,
	mt.Name as AdType,
	p.Alias as Alias,
	ac.Name as CampaignName,
	ac.Cost as Cost,
	ac.Views as Views,
	COLLECT(m.URL) as URLs");
	return $query;
}

/**********************************************************************************************

The following block of code is for the PayPal integration

**********************************************************************************************/

function check_txnid($tnxid)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (t:Transaction{TransactionID: {txnid} })
	RETURN COUNT(t) as Exists",	["txnid"=>$txnid]);
	$record = $query->getRecord();
	
	if($record->value("Exists") == 0)
		return true;
	else
		return false;
}

function checkAdCampaignPrice($price, $CampaignID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ad:AdCampaign{CampaignID: toInt({campaignid}) })
	RETURN ad.Cost as Cost",["campaignid"=>$CampaignID]);
	$record = $query->getRecord();
	if((int)$record->value("Cost") == (int)$price)
		return true;
	else
		return false;
}
/*
function check_price($price, $id)
{
	$valid_price = false;
	//you could use the below to check whether the correct price has been paid for the product

	
	$sql = mysql_query("SELECT amount FROM `products` WHERE id = '$id'");
	if (mysql_num_rows($sql) != 0) {
		while ($row = mysql_fetch_array($sql)) {
			$num = (float)$row['amount'];
			if($num == $price){
				$valid_price = true;
			}
		}
	}
	return $valid_price;
	
	return true;
}
*/

function checkAdCampaignPriceByID($PaymentAmount, $CampaignID)
{
	$session = buildCon();
$query = $session->run("OPTIONAL MATCH (c:AdCampaign)
	WHERE toInt(c.CampaignID) = toInt({campaignid})
	AND toInt(c.Cost) = toInt({paid}) 
	RETURN COUNT(c) as exists",["campaignid"=>$CampaignID, "paid"=>$PaymentAmount]);
	$result = $query->getRecord();
	if($result->value("exists") > 0)
		return true;
	else
		return false;
}

function updatePayments($data)
{
	global $link;

	if (is_array($data)) 
	{
		$sql = mysql_query("INSERT INTO `payments` (txnid, payment_amount, payment_status, itemid, createdtime) VALUES (
				'".$data['txn_id']."' ,
				'".$data['payment_amount']."' ,
				'".$data['payment_status']."' ,
				'".$data['item_number']."' ,
				'".date("Y-m-d H:i:s")."'
				)", $link);
		return mysql_insert_id($link);
	}
}

function checkifAdIsCurrentlyRunningACampaign($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a)-[:Is_Advertisement_For]->(ar:AdRun)
	WHERE ar.Status IN ['Active','Pending Payment', 'Pending Review']
	RETURN count(ar) as activeCampaign",["adid"=>$AdID]);
	$record = $query->getRecord();
	if($record->value("activeCampaign") > 0)
		return true;
	else
		return false;
}
function getCampaignDetails($CampaignID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ac:AdCampaign{CampaignID: toInt({campaignid}) })
	return ac.Name as Name,
	ac.CampaignID as CampaignID,
	ac.Cost as Cost,
	ac.Views as Views",
	["campaignid"=>$CampaignID]);
	return $query->getRecord();
}

function formatDateToNeo4JDateStamp($StringAsDate)
{
	$Date = DateTime::createFromFormat('Y-m-d', $StringAsDate);
	$Date = $Date->format('U');
	$Date = $Date * 1000;
	return $Date;
}

function getAdOwnerAlias($AdID)
{
	$session = buildCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: toInt({adid}) })<-[:Owns_Ad]-(p:Person)
	return p.Alias as Alias",
	["adid"=>$AdID]);
	$record = $query->getRecord();
	return $record->value("Alias");
}

function createAdCampaignTransaction($txn_id, $PaymentAmount, $PaymentStatus, $Alias, $StartDate, $CampaignID, $AdID)
{
	$session = buildAdminCon();
	$query = $session->run("CREATE (t:Transaction{TransactionID: {txnid}, DateCreated: timestamp(), AmountPaid: toFloat({paymentamount}), Status: 'Completed' })
	WITH t
	MATCH (p:Person{Alias: {alias} })
	CREATE (p)<-[:Is_Receipt_For]-(t)
    WITH t
    MATCH (ac:AdCampaign)
    WHERE toInt(ac.CampaignID) = toInt({campaignid})
    WITH t, ac
    MATCH (a:Advertisement)
    WHERE toInt(a.AdID) = toInt({adid}) 
	WITH a, ac, t
	CREATE (a)-[:Is_Advertisement_For]->(ar:AdRun{Status: 'Active', DateCreated: timestamp(), RequestedDate: toInt({startdate}) })-[:Is_Using_Campaign]->(ac)
	WITH ar, t, ac
    CREATE (t)-[:Is_Transaction_For]->(ar)
	SET ar.ViewsPurchased = ac.Views
	return t.TransactionID as TransactionID",
	["txnid"=>$txn_id,"paymentamount"=>$PaymentAmount,"status"=>$PaymentStatus,"alias"=>$Alias,"startdate"=>$StartDate,"campaignid"=>$CampaignID,"adid"=>$AdID]);
	$record = $query->getRecord();
	return $record->value("TransactionID");
}


function checkIfAdIsValidType($AdType)
{
	$session = buildCon();
	$query = $session->run("MATCH (mt:MediaType)
	WHERE toLower(mt.Name) = toLower({adtype})
	RETURN COUNT(mt) as Exists",["adtype"=>$AdType]);
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

function checkIfEntityExists($EntityID)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	RETURN COUNT(n) as Exists",["id"=>$EntityID]);
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

function rotatePanelAdsByType($AdType,$Count,$EntityID)
{	
	$session = buildCon();
	
	$Ads = $session->run("MATCH (mt:MediaType{Name: {mediatype}})<-[:Is_Media_Of]-
	(m:Media)-[:Is_Media_For]->
	(w:Webcomic) 
	WHERE w.ComicID <> toInt({comicid}) 
	AND w.Membership = 'Comicadia'
	AND w.Status = 'Active' 
	WITH distinct w, mt, collect(m) as ms 
	RETURN ms[0].URL as ImgURL, w.Name as ComicName, rand() as r 
	ORDER BY r ASC Limit toInt({adcount}) ",
	["mediatype"=>$AdType,"adcount"=>$Count,"comicid"=>$ComicID]);
	return $Ads;
}

function checkIfThisEntityIsCurrentlyPurchased($EntityID, $AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ad:MediaType{Name: {adtype} })<-[:Uses_Type]-(ar:AdRun{Status: 'Active'})-[r:Purchased_Specific_Ad]->(n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	RETURN COUNT(ar) as Exists",["adtype"=>$AdType,"id"=>$EntityID]);
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

function getCurrentlySpecifiedAdPurchasedForEntity($EntityID, $AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (mt:MediaType{Name:{adtype} })<-[:Uses_Type]-(ar:AdRun{Status: 'Active'})-[r:Purchased_Specific_Ad]->(n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}) )
	WITH ar, mt
	MATCH (ar)<-[:Is_Advertisement_For]-(a:Advertisement)-[:Uses_Media]->(m:Media)
	RETURN 
	a.URL as URL, 
	ar.DateCreated as AdRunID, 
	m.URL as MediaURL, 
	m.DateUploaded as MediaID, 
	mt.Height as MediaHeight, 
	mt.Width as MediaWidth, 
	rand() as r ORDER BY r ASC LIMIT 1",["adtype"=>$AdType,"id"=>$EntityID]);
	return $query->getRecord();

}
function checkIfAnyGenericPaidAdsAreActiveForAdType($AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ar:AdRun{Status: 'Active'})<-[:Is_Advertisement_For]-(n)-[:Uses_Type]->(mt:MediaType{Name: {adtype} })
	RETURN COUNT(ar) as Exists",["adtype"=>$AdType]);
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

function getGenericPaidAdByType($AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (ar:AdRun{Status: 'Active'})<-[:Is_Advertisement_For]-(a:Advertisement)-[:Uses_Type]->(mt:MediaType{Name: {adtype} })
	WITH a, ar, mt
	MATCH (a)-[:Uses_Media]->(m:Media)
	RETURN a.URL as URL, 
	ar.DateCreated as AdRunID, 
	m.URL as MediaURL, 
	m.DateUploaded as MediaID, 
	mt.Height as MediaHeight, 
	mt.Width as MediaWidth, 
	rand() as r ORDER BY r ASC LIMIT 1",["adtype"=>$AdType]);
	return $query->getRecord();
}

function getComicadiaDefaultAd($AdType)
{
	$session = buildCon();
	$query = $session->run("OPTIONAL MATCH (m:Media)<-[:Uses_Media]-(a:Advertisement{Status: 'Default'})-[:Uses_Type]-(mt:MediaType{Name: {adtype} })
	RETURN a.URL as URL, 
	a.AdID as AdRunID, 
	m.URL as MediaURL, 
	m.DateUploaded as MediaID, 
	mt.Height as MediaHeight, 
	mt.Width as MediaWidth, 
	rand() as r ORDER BY r ASC LIMIT 1",["adtype"=>$AdType]);
	return $query->getRecord();
}

function addViewToAdRun($AdRunID, $Referrer, $ViewerIP, $MediaID, $EntityID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (ar:AdRun{DateCreated: toInt({adrunid}) })<-[:Is_Advertisement_For]-(a:Advertisement)-[:Uses_Media]->(m:Media{DateUploaded: toInt({mediaid}) })
	WITH ar, m
	CREATE (ar)-[:Received_View]->(v:View{DateCreated: timestamp(), Referrer: {referrer}, ViewerIP: {viewerip} })-[:Showed_Media]->(m)
	WITH v
	MATCH (n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	CREATE (v)-[:Was_Viewed_On]->(n)",
	["adrunid"=>$AdRunID, "mediaid"=>$MediaID,"referrer"=>$Referrer,"viewerip"=>$ViewerIP,"id"=>$EntityID]);
}

function addViewToDefaultAdSpot($AdRunID, $Referrer, $ViewerIP, $MediaID, $EntityID)
{
	$session = buildAdminCon();
	$query = $session->run("MATCH (a:Advertisement{AdID: {adrunid} })-[:Uses_Media]->(m:Media{DateUploaded: toInt({mediaid}) })
	CREATE (aa)-[:Received_View]->(v:View{DateCreated: timestamp(), Referrer: {referrer}, ViewerIP: {viewerip} })-[:Showed_Media]->(m)
	WITH v
	MATCH (n)
	WHERE (n:Webcomic OR n.Entity)
	AND (n.ComicID = toInt({id}) OR n.EntityID = toInt({id}))
	CREATE (v)-[:Was_Viewed_On]->(n)",
	["adrunid"=>$AdRunID, "mediaid"=>$MediaID,"referrer"=>$Referrer,"viewerip"=>$ViewerIP,"id"=>$EntityID]);
}

?>
