<?php
/*This file purpose is to load the new data after the role witch button was presssed
and return it to guide_role_switcher.js to update the site content (see also guide_template_page.php)
guide_role_switcher.js forwards via POST request  which champion and role the user wants to see
*/
//declare vars
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champtags/Champtags.json');
$champion_name= json_decode($inhalt,true);
//verify input
foreach($champion_name['data'] as $x) {
	if ($x[key] == $_POST["champname"]) {
		$champname=$_POST["champname"];
	}
}
if ($_POST["champ_role"]=="1" || $_POST["champ_role"]=="2" || $_POST["champ_role"]=="3"){
	$role=$_POST["champ_role"];
}

$_db_datenbank = "DATENBANK";
$_db_url = "URL";
$_db_usr =    "USER";
$_db_paswd = "PASSWORD";
//connect to db
$mysqli = new mysqli($_db_url, $_db_usr, $_db_paswd, $_db_datenbank);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//Start-Items
$stmt=$mysqli->prepare("SELECT Starter1Name, Starter2Name,Starter3Name,Starter4Name,Starter5Name,Starter6Name FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$item = $res->fetch_assoc();
$stmt->close();
$i=0;
foreach($item as $item_name){
	if ($item_name != ""){
		$stmt=$mysqli->prepare("SELECT ItemID FROM item_daten WHERE Item_Deutsch = ?");
		$stmt->bind_param("s",$item_name);
		$stmt->execute();
		$res=$stmt->get_result();
		$item_id = $res->fetch_assoc();
		$stmt->close();
		$item_array["startitem"][$i]= array($item_id[ItemID],$item_name);
		$i++;
	}
}
// Summoner Spells
$stmt=$mysqli->prepare("SELECT SummonerSpell1, SummonerSpell2 FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$summonerspells = $res->fetch_assoc();
$stmt->close();
$summoner_array["summonerspells"]= array($summonerspells[SummonerSpell1],$summonerspells[SummonerSpell2]);
//Item Build
$stmt=$mysqli->prepare("SELECT CoreItem1Name, CoreItem2Name,CoreItem3Name,CoreItem4Name,CoreItem5Name,CoreItem6Name FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$guide_item = $res->fetch_assoc();
$stmt->close();
$i=0;
foreach($guide_item as $guide_item_name){
	if (guide_item_name != ""){
		$stmt=$mysqli->prepare("SELECT ItemID FROM item_daten WHERE Item_Deutsch = ?");
		$stmt->bind_param("s",$guide_item_name);
		$stmt->execute();
		$res=$stmt->get_result();
		$item_id = $res->fetch_assoc();
		$stmt->close();
		$itembuild_array["itembuild"][$i]= array($item_id[ItemID],$guide_item_name);
		$i++;
	}
}
//Skill-Order
$stmt=$mysqli->prepare("SELECT * FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$skills = $res->fetch_assoc();
$stmt->close();
$spells=array("Q","W","E","R");
$j=0;
foreach($spells as $spell_value){
	for ($i=1;$i<19;$i++){
		$skillnr = "Skill".$i;
		if ($skills[$skillnr] == $spell_value){
			$spell_array["spellorder"][$j][$i]= array("zeile".$j."_spalte".$i."",$skills[$skillnr]);
		}else{
			$spell_array["spellorder"][$j][$i]= array("zeile".$j."_spalte".$i."","");
			}
	}
$j++;
}
//Masteries
include(dirname(__FILE__).'/guide_update_masterys.php');
$stmt=$mysqli->prepare("SELECT MasteryCode FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$mastery = $res->fetch_assoc();
$stmt->close();
//call mastery function
$masterytext=utf8_encode(masterys($mastery[MasteryCode]));
$mastery_array["mastery"] =array($masterytext);
//Runen
$stmt=$mysqli->prepare("SELECT RuneCode1,RuneCode2 FROM guide_daten WHERE ChampionName = ? AND RoleRate=?");
$stmt->bind_param("si",$champname,$role);
$stmt->execute();
$res=$stmt->get_result();
$runen = $res->fetch_assoc();
$stmt->close();
$runentext="";
if ($runen[RuneCode1]!=""){
	$stmt=$mysqli->prepare("SELECT * FROM rune_code WHERE RuneCode = ? ");
	$stmt->bind_param("i",$runen[RuneCode1]);
	$stmt->execute();
	$res=$stmt->get_result();
	$runen1 = $res->fetch_assoc();
	$stmt->close();
	if ($runen1[RuneCode] < 7){
	$runentext= "<p id=\"rune-page-hl\"><u>".$runen1[Name]."</u></p>";
	}
	$runentext= $runentext.$runen1[Code];
}
$runentext= $runentext.'<div style="clear:both;margin:15px;"></div>';
if ($runen[RuneCode2]!=""){
	$stmt=$mysqli->prepare("SELECT * FROM rune_code WHERE RuneCode = ? ");
	$stmt->bind_param("i",$runen[RuneCode2]);
	$stmt->execute();
	$res=$stmt->get_result();
	$runen2 = $res->fetch_assoc();
	$stmt->close();
	if ($runen2[RuneCode] < 7){
	$runentext= $runentext."<p id=\"rune-page-hl\"><u>".$runen1[Name]."</u></p>";
	}
	$runentext= $runentext.$runen2[Code];
}
$runentext=utf8_encode($runentext);
$runen_array["runen"]= array($runentext);
//merge arrays and return to guide_role_switcher.js
$result = array_merge($item_array, $summoner_array, $itembuild_array, $spell_array,$mastery_array,$runen_array);
echo json_encode($result);
?>
