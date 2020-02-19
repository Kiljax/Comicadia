jQuery(document).ready(function($)
{
	
	//portfolio - show link
	$('.squaregrid').hover(
		function () {
			$(this).animate({opacity:'1'});
		},
		function () {
			$(this).animate({opacity:'0'});
		}
	);	
		$('.horizgrid').hover(
		function () {
			$(this).animate({opacity:'1'});
		},
		function () {
			$(this).animate({opacity:'0'});
		}
	);	
	$('.toggle-nav').click(function(e) 
	{
		$(this).toggleClass('active');
		$('#TopNav ul').toggleClass('active');
		e.preventDefault();
	});
	$('#login-trigger').click(function()
	{
		$(this).next('#login-content').slideToggle();
		$(this).toggleClass('active');          
	
		if ($(this).hasClass('active')) $(this).find('span').html('&#x25B2;')
		else $(this).find('span').html('&#x25BC;')
	});
});

function goHome()
	{
		location.href = './index.php';	
	}

	function AttemptLogin()
	{	
		var Email = document.getElementById('username').value;
		var Pass = document.getElementById('password').value;
		
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
				if(xmlhttp.responseText.trim() != 'Success')
				{
					document.getElementById('PassMSG').innerHTML=xmlhttp.responseText;
				}
				else 
				{
					location.reload();				
				}
			}
		}
		xmlhttp.open("POST", "php/actions.php?F=Login&Email="+Email+"&Password="+Pass, true);
		xmlhttp.send();
	}

function getxml()
{
	if (window.XMLHttpRequest) 
	{
		xmlhttp = new XMLHttpRequest();
	} 
	else 
	{
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;	
}