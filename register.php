<html>

<head>
<script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script type="text/javascript" src="./js/defaultLoad.js"></script>
<script type="text/javascript">

function registerNewUser()
{
 var FirstName = document.getElementById('RegisterFirstNameText').value;
 var Email = document.getElementById('RegisterEmailText').value;
 var Confirm = document.getElementById('ConfirmEmailText').value;
 var Alias = document.getElementById('RegisterAliasText').value;
 var LastName = document.getElementById('RegisterLastNameText').value;
 var Password = document.getElementById('RegisterPasswordText').value;
 xmlhttp = getxml();
 xmlhttp.onreadystatechange = function()
	{
		if(xmlhttp.readyState == XMLHttpRequest.DONE)
		{
			document.getElementById('RegisterUserERR').innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open("POST", "./php/actions.php?F=registerNewUser&FirstName="+FirstName+"&LastName="+LastName+"&Alias="+Alias+"&Confirm="+Confirm+"&Email="+Email+"&Password="+Password, true);
	xmlhttp.send();
}

</script>
<style>
.content-box {margin: 60px auto;width: 660px;}
.RegisterDIV   { margin: 5px; text-align: right;     width: 270px;}
</style>
</head>
<title>Register Comicadia User</title>
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="css/cpanel.css" rel="stylesheet" type="text/css" />


<body>
<center>
<div class="content-box">
<div id='RegistrationHeader'>
<img src='https://www.comicadia.com/media/registration_header.png' />
</div>
<div id='RegisterNewUser' >
 <div id='RegisterEmail' class='RegisterDIV'>
  Email: <input type='Email' id='RegisterEmailText' class='RegisterText'>
 </div>
 <div id='ConfirmEmail' class='RegisterDIV'>
  Confirm Email: <input type='Email' id='ConfirmEmailText' class='RegisterText'>
 </div>
 <div id='RegisterPassword' class='RegisterDIV'>
  Password: <input type='Password' id='RegisterPasswordText' class='RegisterText'>
 </div>
 <div id='RegisterAlias' class='RegisterDIV'>
  Alias: <input type='Email' id='RegisterAliasText' class='RegisterText'>
 </div>
 <div id='RegisterFirstName' class='RegisterDIV'>
  First Name: <input type='Text' id='RegisterFirstNameText' class='RegisterText'>
 </div>
 <div id='RegisterLastName' class='RegisterDIV'>
  Last Name: <input type='Text' id='RegisterLastNameText' class='RegisterText'>
 </div>
 <input type='button' id='RegisterNewUser' value='Create' onclick='registerNewUser();'>
 <div id='RegisterUserERR'></div>
</div>
</div>
</center>
</body>
</html>