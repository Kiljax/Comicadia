// Javascript Document

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
			document.getElementById("comicadia_rotator").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "http://www.comicadia.com/php/actions.php?F=loadRotator"+"&ComicID="+ComicID+"&Type="+"Rotator"+"&Count="+3, true);
	xmlhttp.send();
};

function ComicadiaRotateMobile(ComicID) 
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
			document.getElementById("comicadia_rotator").innerHTML = xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "http://www.comicadia.com/php/actions.php?F=loadMobileRotator"+"&ComicID="+ComicID+"&Type="+"Rotator"+"&Count="+3, true);
	xmlhttp.send();
};