<?php
?>

<html>
<head>

</head>
<title>Comicadia - Rotating banner example</title>
<body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script>
/*
function ComicadiaRotate(ComicID) 
{
	 if (window.XMLHttpRequest) 
	{
		xmlhttp = new XMLHttpRequest();
	} 
	else 
	{
  		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}   
	xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById("stuff").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "php/actions.php?F=loadRotator"+"&ComicID="+ComicID+"&Type="+"Rotator"+"&Count="+4, true);
	xmlhttp.send();
};
*/
</script>

<link href="http://www.comicadia.com/demo/css/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://www.comicadia.com/demo/php/rotate.php"></script>
<div id="Comicadia_Internal_Ads" style='height:60px; width:900px; background: blue;'></div>
<!-- <input type='button' id='testbutton' onclick='ComicadiaRotate(5121513884814165);' value='test'>
<div id='stuff' style='height:400px; width:400px; background: blue;'></div> -->

<script>

	function loadComicadiaRotate() {
    	ComicadiaRotate(5121513884814165);
	}
	
	if(typeof jQuery=='undefined') {
		var headTag = document.getElementsByTagName("head")[0];
		var jqTag = document.createElement('script');
		jqTag.type = 'text/javascript';
		jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
		jqTag.onload = loadComicadiaRotate;
		headTag.appendChild(jqTag);
	} else {
		 loadComicadiaRotate();
	}

</script>

<?php 
print("&lt;script&gt;

	function loadComicadiaRotate() {
    	ComicadiaRotate(5121513884814165);
	}
	
	if(typeof jQuery=='undefined') {
		var headTag = document.getElementsByTagName(\"head\")[0];
		var jqTag = document.createElement('script');
		jqTag.type = 'text/javascript';
		jqTag.src = 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js';
		jqTag.onload = loadComicadiaRotate;
		headTag.appendChild(jqTag);
	} else {
		 loadComicadiaRotate();
	}
	
&lt;/script&gt;");
?>	

</body>
</html>





