<?php

function getItemDetails($ItemID)
{
	$session = buildCon();
	$MerchList = $session->run("MATCH (m:Merch)
	WHERE toInt(m.ItemID) = toInt({itemid})
	WITH m
	OPTIONAL MATCH (w:Webcomic)<-[:Is_Merch_Of]-(m)-[:Was_Designed_By]->(p:Person)
	WITH w,m,p
	MATCH (m)-[:Is_Of_Merch_Type]->(mt:MerchType)
	return  m.ItemID as ItemID, 
	m.URL as URL,
	mt.Name as Type,
	w.ComicID as ComicID,
	m.Desc as Desc, 
	m.ImgURL as ImgURL, 
	p.Alias as Alias, 
	m.Title as Title",
	["itemid"=>$ItemID]);
	return $MerchList;
}

function getAllMerchByPaginationSortedByNew($startArticle,$articlesPerPage)
{
	$session = buildCon();
	$MerchList = $session->run("MATCH (m:Merch)
	WITH m
	OPTIONAL MATCH (w:Webcomic)<-[:Is_Merch_Of]-(m)-[:Was_Designed_By]->(p:Person)
	WITH w,m,p
	MATCH (m)-[:Is_Of_Merch_Type]->(mt:MerchType)
	return  m.ItemID as ItemID, 
	m.URL as URL,
	mt.Name as Type,
	w.ComicID as ComicID,
	m.Desc as Desc, 
	m.ImgURL as ImgURL, 
	p.Alias as Alias, 
	m.Title as Title
	ORDER BY m.ItemID DESC 
	SKIP {start} LIMIT {articlesperpage}",
	["start"=>$startArticle,"articlesperpage"=>$articlesPerPage]);
	return $MerchList;
}


function getAllMerchForComic($ComicID,$startArticle,$articlesPerPage)
{
	$session = buildCon();
	$MerchList = $session->run("MATCH (w:Webcomic)
	WHERE toInt(w.ComicID) = toInt({comicid})
	WITH w
	OPTIONAL MATCH (w)<-[:Is_Merch_Of]-(m:Merch)-[:Was_Designed_By]->(p:Person)
	WITH w,m,p
	MATCH (m)-[:Is_Of_Merch_Type]->(mt:MerchType)
	return  m.ItemID as ItemID, 
	m.URL as URL,
	mt.Name as Type,
	w.ComicID as ComicID,
	m.Desc as Desc, 
	m.ImgURL as ImgURL, 
	p.Alias as Alias, 
	m.Title as Title
	ORDER BY m.ItemID DESC 
	SKIP {start} LIMIT {articlesperpage}",
	["comicid"=>$ComicID, "start"=>$startArticle,"articlesperpage"=>$articlesPerPage]);
	return $MerchList;
}

function getAllMerchAvailableToPerson($Alias)
{
	print("Coming soon!");
}
/*
function getAllMerchByPerson($Alias)
{
	$session = buildCon();
	$MerchList = $session->run("MATCH (p:Person)
	WHERE toLower(p.Alias) = toLower({alias})
	WITH p
	OPTIONAL MATCH (p)-[r:Works_On]->(w:Webcomic)<-[:Is_Merch_Of]-(m:Merch)
	with p, m, w
	OPTIONAL MATCH (p)<-[:Was_Designed_By]-(m:Merch)
	
	OPTIONAL MATCH (w)<-[:Is_Merch_Of]-(m:Merch)-[:Was_Designed_By]->(p:Person)
	WITH w,m,p
	MATCH (m)-[:Is_Of_Merch_Type]->(mt:MerchType)
	return  m.ItemID as ItemID, 
	m.URL as URL,
	mt.Name as Type,
	w.ComicID as ComicID,
	m.Desc as Desc, 
	m.ImgURL as ImgURL, 
	p.Alias as Alias, 
	m.Title as Title
	ORDER BY m.ItemID DESC 
	SKIP {start} LIMIT {articlesperpage}",
	["comicid"=>$ComicID, "start"=>$startArticle,"articlesperpage"=>$articlesPerPage]);
	return $MerchList;
}
*/
function getCountOfAllGenericMerch()
{
	$session = buildCon();
	$MerchCount = $session->run("OPTIONAL MATCH (m:Merch)
	RETURN COUNT(m) as MerchCount");
	$MerchCount = $MerchCount->getRecord();
	return $MerchCount->value("MerchCount");
}

function getCountOfAllMerchForComic($ComicID)
{
	$session = buildCon();
	$MerchCount = $session->run("MATCH (w:Webcomic)
	WHERE toInt(w.ComicID) = toInt({comicid})
	WITH w
	OPTIONAL MATCH (w)<-[:Is_Merch_Of]-(m:Merch)
	RETURN COUNT(m) as MerchCount",
	["comicid"=>$ComicID]);
	$MerchCount = $MerchCount->getRecord();
	return $MerchCount->value("MerchCount");
}

function getCountOfAllMerchByType($Type)
{
	$session = buildCon();
	$MerchCount = $session->run("MATCH (m:Merch)-[:Is_Of_Merch_Type]->(mt:MerchType)
	WHERE toLower(mt.Name) = toLower({type})
	RETURN COUNT(DISTINCT (m)) as MerchCount",
	["type"=>$Type]);
	$MerchCount = $MerchCount->getRecord();
	return $MerchCount->value("MerchCount"); 
}

function getAllMerchByType($Type,$startArticle,$articlesPerPage)
{
	$session = buildCon();
	$MerchList = $session->run("MATCH (m:Merch)-[:Is_Of_Merch_Type]->(mt:MerchType)
	WHERE toLower(mt.Type) = toLower({type}) 
	WITH m
	OPTIONAL MATCH (w:Webcomic)<-[:Is_Merch_Of]-(m)-[:Was_Designed_By]->(p:Person)
	return  m.ItemID as ItemID, 
	m.URL as URL,
	mt.Name as Type,
	w.ComicID as ComicID,
	m.Desc as Desc, 
	m.ImgURL as ImgURL, 
	p.Alias as Alias, 
	m.Title as Title
	ORDER BY m.ItemID DESC
	SKIP {start} LIMIT {articlesperpage}",
	["type"=>$Type,"start"=>$startArticle,"articlesperpage"=>$articlesPerPage]);
	return $MerchList;
}

function addMerchToComic($ComicID, $URL, $Type, $Name, $Desc, $ImgURL, $Alias, $Title)
{
	$session = buildCon();
	$Merch = $session->run("OPTIONAL MATCH (p:Person), (w:Webcomic)
	WHERE toLower(p.Alias) = toLower({alias})
	AND toInt(w.ComicID) = toInt({comicid})
	WITH p, w
	CREATE (w)<-[:Is_Merch_Of]-(m:Merch{ItemID: timestamp(), URL: {url}, Name: {name}, Desc: {desc}, ImgURL: {imgurl}, Title: {title})-[:Was_Designed_By]->(p)
	WITH m
	OPTIONAL MATCH (mt:MerchType)
	WHERE toLower(mt.Name) = toLower({type})
	CREATE (m)-[:Is_Of_Merch_Type]->(mt)",
	["comicid"=>$ComicID, "alias"=>$Alias,"url"=>$URL,"name"=>$Name,"desc"=>$Desc,"imgurl"=>$ImgURL,"alias"=>$Alias,"title"=>$Title,"type"=>$Type]);
}

function addMerchToPerson($URL, $Type, $Name, $Desc, $ImgURL, $Alias, $Title)
{
	$session = buildCon();
	$Merch = $session->run("MATCH (p:Person)
	WHERE toLower(p.Alias) = toLower({alias})
	WITH p
	CREATE (m:Merch{ItemID: timestamp(), URL: {url}, Name: {name}, Desc: {desc}, ImgURL: {imgurl}, Title: {title})-[:Was_Designed_By]->(p)
	WITH m
	OPTIONAL MATCH (mt:MerchType)
	WHERE toLower(mt.Name) = toLower({type})
	CREATE (m)-[:Is_Of_Merch_Type]->(mt)",
	["alias"=>$Alias,"url"=>$URL,"name"=>$Name,"desc"=>$Desc,"imgurl"=>$ImgURL,"alias"=>$Alias,"title"=>$Title,"type"=>$Type]);
}

?>