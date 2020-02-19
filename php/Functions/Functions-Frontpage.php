<?php

function checkIfCreatorAliasExists($NameOrAlias)
{
	$session = buildCon();
	$exists = $session->run("MATCH (p:Person)
	WHERE toLower(p.First_Name) + " " + toLower(p.Last_Name) = toLower({nameoralias})
	OR toLower(p.Alias) = toLower({nameoralias})
	RETURN COUNT(distinct p) as Exists",
	["nameoralias"=>$NameOrAlias]);
	$CountExists = $exists->getRecord();
	return $CountExists->value("Exists");
}

function getHeraldCreatorComicadiaAlias($Creator)
{
	$session = buildCon();
	$Creator = $session->run("MATCH (p:Person)
	WHERE toLower(p.First_Name) + " " + toLower(p.Last_Name) = toLower({nameoralias})
	OR toLower(p.Alias) = toLower({nameoralias})
	RETURN distinct(p.Alias) as Alias, 
	p.ProfilePic as ProfilePic,
	p.UserType as UserType",
	["nameoralias"=>$NameOrAlias]);
	return $Creator->getRecord();
}
?>