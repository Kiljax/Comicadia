<?php

include 'php/GUI.php';

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

<script type="text/javascript">

function hasName(SearchBy)
{
	return SearchBy == 'Name';
}

function searchComicadiaComics(SearchBy)
{

	//	var ThemeList[];
	//for(i=0 ;i<ThemArray.length;++i)
	//{
		//ThemeList.push(ThemeArray[i].value);
	//}
	if(SearchBy == '')
	{
		document.getElementById('searchMSG').innerHTML = "You must select one search parameter.";
	}
	else
	{
		var Error = 'Search not executed:';
		var Success = true;
		if(SearchBy =='Name')
		{
			var ComicName = document.getElementById("searchComicNameText").value;
			if(ComicName == '')
			{
				Error = Error + "<br>Text is required to search for a comic by its name.";
				Success = false;
			}
			else
			{
				var functionString = '?searchForComicsBy=Name&ComicName='+ComicName;
			}
		}
		else if(SearchBy =="Genres")
		{
			var GenreArray = getCheckedBoxes("genreCheckbox");
			if(GenreArray.length > 0)
			{
				document.getElementById('testMSG').innerHTML = 'Genre found';
				var GenreString = '';
				for(j=0;j<GenreArray.length;++j)
				{
					GenreString = GenreString + GenreArray[j] +",";
				}				
				var functionString = '?searchForComicsBy=Genres&Genres='+GenreString;
			}
			else
			{
				Error = Error + "<br>When searching by Genre, you must select at least one Genre.";
				Success = false;
			}
		}
		else if(SearchBy== 'Themes')
		{
			var ThemeArray = getCheckedBoxes("themeCheckbox");
			if(ThemeArray.length > 0)
			{	
				var ThemeString = '';
				for(i = 0; i< ThemeArray.length;++i)
				{
					ThemeString = ThemeString + ThemeArray[i] + ",";
				}			
				var functionString = '?searchForComicsBy=Themes&Themes='+ThemeString;
			}
			else
			{	
				Error = Error + '<br>When searching by Theme, you must select at least one Theme';
				Success = false;
			}	
		}
		else
		{
			document.getElementById('searchMSG').innerHTML = "What are you searching for, exactly?";
		}
		if(Success == true)
		{
			window.location = "comics.php" + functionString;
		}
		else
		{
			document.getElementById('searchMSG').innerHTML = Error;
		}
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

</script>

<meta name="description" content="Read these amazing comics, brought to you by Comicadia. Ranging a vast expanse of themes and genres, Comicadia is diverse in its artistic styles, content and settings." />

</head>
<title>Comicadia - Comics</title>
<body>
<div id="MainContent">
	<?php 
		buildComicadiaRandomSpotlightHeader();
		?>
	<div id="BodyMain">
		<?php 
		if(isset($_REQUEST['searchForComicsBy']))
		{
		
			$Method = $_REQUEST['searchForComicsBy'];
			print("<div id='ComicSearch'>");
			buildComicSearch();
			if($Method == 'Name')
			{
				$ComicName = $_REQUEST['ComicName'];
				$ComicList = searchComicByName($ComicName);
			}
			elseif($Method == 'Genres')
			{
				$GenreList = $_REQUEST["Genres"];
				$GenreList = rtrim(trim($GenreList),',');
				$GenreList = explode(',',$GenreList);
				$ComicList = searchComicByGenre($GenreList);
			}
			elseif($Method == 'Theme')
			{
				$ThemeList = $_REQUEST["Themes"];
				$ThemeList = rtrim(trim($ThemeList),',');
				$ThemeList = explode(',',$ThemeList);
				$ComicList = searchComicByTheme($ThemeList);
			}
			else
			{
				$ComicList = false;
			}
			if($ComicList)
			{
				foreach($ComicList->getRecords() as $Comic)
				{
					$Name = $Comic->value("ComicName");
					buildComicSquare($Name);
				}
			}
			else
			{
				print("No active comics match that keyword");
			}
			print("</div>");
		}
		elseif(isset($_REQUEST['ComicID']))
		{
			$ComicID = $_REQUEST['ComicID'];
			buildComicProfile($ComicID);
		}
		else
		{
			print("<div id='ComicSearch'>");
			buildComicSearch();
			buildAllComicsSquares();
			print("</div>");
		}
		?>
		<div class="clear"></div>
	</div>
	<?php
	buildFooter();
	?>
</body>
</html>