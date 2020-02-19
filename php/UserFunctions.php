<?php

function getUsersWebcomics($Alias)
{
	$session = buildCon();
	$Webcomics = $session->run("MATCH (p:Person{Alias: {alias} })
	-[r:Works_On]->
	(w:Webcomic)
	RETURN w.Name as Name, w.URL as URL, 
	w.RSS as RSS, r.Role as Role, 
	w.Synopsis as Synopsis,
	w.Format as Format,
	w.ComicID as ComicID,
	w.Pitch as Pitch,
	w.Membership as Membership",["alias"=>$Alias]);
	return $Webcomics;
}

function getUserWebcomicsCount($Alias)
{
	$session = buildcon();
	$CountComics = $session->run("MATCH (p:Person{Alias: {alias} })
	-[r:Works_On]->
	(w:Webcomic)
	RETURN COUNT(w) as WebcomicCount",["alias"=>$Alias]);
	$Counter = $CountComics->getRecord();
	$totalCount = $Counter->value("WebcomicCount");
	return $totalCount;
}

function getWebcomicCrewCount($ComicName)
{
	$session = buildcon();
	$CrewList = $session->run("MATCH (p:Person)-[r:Works_On]->(w:Webcomic{Name: {name} })
	return COUNT(p) as crewCount",["name"=>$ComicName]);
	$crewCount = $CrewList->getRecord();
	$TotalCrew = $crewCount->value('crewCount');
	return $TotalCrew;
}
function getAllMemberEvents()
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person) 
	WHERE e.Category IN ['Members','Public']
	AND e.StartTime > timestamp()
	AND e.Status = 'Approved'
	return e.Title as Title, 
	e.StartTime as Start_Time, 
	e.Category as Category, 
	e.Location as Location, 
	e.Type as Type, 
	e.DateCreated as DateCreated,
	e.Details as Details, 
	e.Status as Status, 
	p.Alias as Alias, 
	p.Email as Email ORDER BY e.StartTime ASC");
	return $EventList;	
}

function getAllPublicEvents()
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person) 
	WHERE e.Category = 'Public'
	AND e.StartTime > timestamp()
	AND e.Status = 'Approved'
	return e.Title as Title, 
	e.StartTime as Start_Time, 
	e.Category as Category, 
	e.Location as Location, 
	e.DateCreated as DateCreated,
	e.Type as Type, 
	e.Details as Details, 
	e.Status as Status, 
	p.Alias as Alias, 
	p.Email as Email ORDER BY e.StartTime ASC");
	return $EventList;	
}

function getAllMemberNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category IN ['Members','Public']
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

function getAllPublicNews()
{
	$session = buildCon();
	$query = "MATCH (n:News)-[:Was_Posted_By]->(p:Person) 
	WHERE n.DatePublished < timestamp() 
	and n.Category = 'Public'
	and n.Status = 'Approved'
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

function getAllUserEvents($Alias)
{
	$session = buildCon();
	$EventList = $session->run("MATCH (e:Event)<-[:Is_Organizer_Of]-(p:Person) 
	WHERE p.Alias = {alias}
	return e.Title as Title, 
	e.StartTime as Start_Time, 
	e.DateCreated as DateCreated,
	e.Category as Category, 
	e.Location as Location, 
	e.Type as Type, e.Details as Details, e.Status as Status, 
	p.Alias as Alias, p.Email as Email 
	ORDER BY e.StartTime ASC",["alias"=>$Alias]);
	return $EventList;
}

function getUserEditNews($Alias)
{
	$session = buildCon();
	$result = $session->run("MATCH (n:News)-[:Was_Posted_By]-(p:Person) 
	where p.Alias = {alias}
	return n.DateWritten as DateWritten, 
	n.Details as Details, n.Category as Category, 
	n.Title as Title, n.Status as Status, 
	p.First_Name as First, p.Last_Name as Last,	
	n.DatePublished as DatePublished 
	ORDER BY n.DatePublished",["alias"=>$Alias]);
	return $result;	
}

?>