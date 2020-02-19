<?php
	include "../../php/Connector.php";
	ini_set('display_errors', 1);
	error_reporting(E_ALL|E_STRICT);
	header("Content-Type: application/xml; charset=UTF-8");
	$rssString = '<?xml version="1.0" encoding="UTF-8"?>';
	$rssString .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
	$rssString .= '<channel>';
	$rssString .= '<atom:link href="https://www.comicadia.com/MAX/rss/" rel="self" type="application/rss+xml" />';
	$rssString .= '<title>Comicadia MAX RSS Feed</title>';
	$rssString .= '<link>https://www.comicadia.com/MAX/rss/</link>';
	$rssString .= "<description>A feed for all MAX rounds</description>";
	$rssString .= '<language>en-us</language>';
	$rssString .= '<copyright>Copyright (C) 2018 Comicadia</copyright>';
	
	$session = buildCon();
	$query = $session->run("MATCH (m:MAX)
	WHERE m.StartDate < timestamp()
	AND m.Status IN ['Completed', 'Active']
	return 
	m.Theme as Theme,
	m.StartDate as StartDate,
	m.SignUpEndDate as SignUps,
	m.EndDate as Deadline");
	foreach($query->getRecords() as $MAX)
	{
		$StartDate = $MAX->value("StartDate");
		$SignUpClose = $MAX->value("SignUps");
		$Deadline = $MAX->value("Deadline");
		$Theme = $MAX->value("Theme");
		
		
		$PubDate = $MAX->value("StartDate");
		$PubDate = date('D, d M Y H:i:s O',$PubDate /1000);
		$StartDate = date('F jS, Y', $StartDate/1000);
		$SignUpClose =  date('F jS, Y', $SignUpClose/1000);
		$Deadline = date('F jS, Y', $Deadline/1000);
		$rssString .= "<item>";
		$rssString .= "<title>MAX Round Starts: $StartDate</title>";
		$description = "MAX Round Starts: $StartDate. Sign Ups close: $SignUpClose. Deadline for submissions: $Deadline";
		if(trim($Theme) != '')
		{
			$description .= "Theme: ";
			$Theme = strip_tags($Theme);
			$Theme = htmlentities($Theme, null, 'utf-8');
			$Theme = str_replace("&nbsp;", "", $Theme);
			$Theme = str_replace("&ldquo;", "'", $Theme);
			$Theme = str_replace("&rdquo;", "'", $Theme);
					
			if(strlen($Theme) > 125)
			{
				$description .= substr($Theme,0,123);
				$description .= '...';
			}
			else
			{
				
				$description .= utf8_decode($Theme);
			}
		}
		$rssString .= "<description>$description</description>";
		$rssString .= "<link>https://www.comicadia.com/MAX/</link>";
		$rssString .= "<guid>https://www.comicadia.com/MAX/</guid>";
		$rssString .= "<pubDate>$PubDate</pubDate>";
		$rssString .= "</item>";
	}
	$rssString .= "</channel>";
	$rssString .= "</rss>";
	
	print($rssString);
?>