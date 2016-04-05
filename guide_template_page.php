<!--
This is the template for the champion guide pages on www.lolchampion.de/champion-guides
The content comes from json files which were downloaded from riots servers and
from lolchampions own database. The User can switch between different Champion Roles.
See also guide_role_switcher.js and guide_role_switcher.php
-->
<script type="text/javascript" src="/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/guide.js"></script>

<?php
//Daten fuer ChampionId abfragen
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champtags/Champtags.json');
$champion_name= json_decode($inhalt, true);
foreach ($champion_name['data'] as $x) {
    if ($x[key] == $name) {
        $champ_id = $x[id];
        $champname = $x[key];
        $champnamekorrekt = $x[name];
        $champ_title = $x[title];
        break;
    }
}
$rolerate=1; //Start Rolle
//Verbindung DB
global $wpdb;
//Werbung
if (function_exists('adinserter')) {
    echo adinserter(4);
}
echo '<div id="Champion">';
echo '<div id="counterBGBild" style="background-image:url(http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/counter/MonkeyKing_Splash_0.jpg);">';
echo '<div id="champname" ><h2>'.$champnamekorrekt.' Guide</h2></div>';
echo '<div style="clear:left;"></div>';
echo '<div id="champtitle" >'.$champ_title.'</div>';
echo '<div style="clear:left;"></div>';
$sql = $wpdb->get_results($wpdb->prepare("SELECT Position,RoleRate FROM guide_daten WHERE ChampionName = %s", $champname));
$role_counter=0;
foreach ($sql as $x) {
    $role_counter++;
}
$role_counter=1;
    foreach ($sql as $x) {
        echo '<div onclick="UpdateRole(\''.$champname.'\',\''.$role_counter.'\')" class="role-selection role'.$role_counter.'">'.$x->Position.'</div>';
        $role_counter++;
    }
echo '</div>';
echo '<p>Detaillierter Leauge of Legends '.$champnamekorrekt.' Guide inklusive Skillreihenfolge, Beschw&ouml;rerzauber, den wichtigsten Items sowie Runen und Meisterschaften.</br></br>
Willkommen bei unserem '.$champnamekorrekt.' Guide. Hier findest du die wichtigsten Infos wie Skills, Items, Runen, Meisterschaften und Beschw&ouml;rerzauber zu '.$champnamekorrekt.'. Die Daten sind abgeleitet aus tausenden von Spielen, wo jeweils berechnet wurde, womit die Spieler am meisten Erfolg hatten. Falls ihr noch Anmerkungen zu unserem '.$champnamekorrekt.' Guide habt oder gerne einen detaillierten Guide verfassen wollt, den wir hier abdrucken, schreibt uns doch einfach eine Mail an <a href="mailto:info@lolchampion.de?subject=Guide">info@lolchampion.de</a> mit dem Betreff &quot;Guide&quot;.</p>';
//Link Block zur Champion-Seite und Counter-Seite
if ($champname != "MonkeyKing") {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champions/'.$champname.'"><div id="champion-link1">';
} else {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champions/Wukong"><div id="champion-link1">';
}
echo '<center>'.$champnamekorrekt.' Seite</center>';
echo '</div></a>';
if ($champname != "MonkeyKing") {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/counter/'.$champname.'"><div id="champion-link2">';
} else {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/counter/Wukong"><div id="champion-link2">';
}
echo '<center>'.$champnamekorrekt.' Counter';
echo '</div></a>';
//Ende Intro-Sektion
//Start-Items
echo '<div id="start-items">';
echo '<div id="champion-hl">';
echo '<h3>Start-Items</h3></div>';
echo '<div class="start-items-bereich">';
$sql = $wpdb->get_row($wpdb->prepare("SELECT Starter1Name, Starter2Name,Starter3Name,Starter4Name,Starter5Name,Starter6Name FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
$item_counter=1;
foreach ($sql as $item_name) {
    if ($item_name != "") {
        $item_id = $wpdb->get_row($wpdb->prepare("SELECT ItemID FROM item_daten WHERE Item_Deutsch = %s", $item_name));
        $item_daten_json = file_get_contents('http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/OfflineDaten/ItemsData/item_'.$item_id->ItemID.'.json');
        $item_daten = json_decode($item_daten_json, true);
        echo '<img class="startitem'.$item_counter.'" title="'.$item_name.'" alt="'.$item_name.'" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/item/'.$item_daten[id].'.png" />';
    }
    $item_counter++;
}
echo '</div>';
echo '</div>';
// Summoner-Spells
echo '<div id="summoner-spells">';
echo '<div id="champion-hl">';
echo '<h3> Beschw&ouml;rer-Zauber</h3></div>';
$sql = $wpdb->get_row($wpdb->prepare("SELECT SummonerSpell1, SummonerSpell2 FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
echo '<a style="margin-left: 35%;" href="http://www.lolchampion.de/lol-anfaenger-guide/beschwoerer-zauber#'.$sql->SummonerSpell1.'" >';
echo '<img class="summoner-spell1" title="'.$sql->SummonerSpell1.'" alt="'.$sql->SummonerSpell1.'" src="/_wordpress_dev716a/wp-content/bilder/summoner-spells/'.$sql->SummonerSpell1.'.jpg"></a>';
echo '<a href="http://www.lolchampion.de/lol-anfaenger-guide/beschwoerer-zauber#'.$sql->SummonerSpell2.'" >';
echo '<img class="summoner-spell2" title="'.$sql->SummonerSpell2.'" alt="'.$sql->SummonerSpell2.'" src="/_wordpress_dev716a/wp-content/bilder/summoner-spells/'.$sql->SummonerSpell2.'.jpg">';
echo '</a></div>';
echo '<div style="clear:both"></div>';
//Item-Build
echo '<div id="item-build">';
echo '<div id="champion-hl">';
echo' <h3>Item-Build</h3></div>';
$sql = $wpdb->get_row($wpdb->prepare("SELECT CoreItem1Name, CoreItem2Name,CoreItem3Name,CoreItem4Name,CoreItem5Name,CoreItem6Name FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
$i=1;
foreach ($sql as $item_name) {
    if ($item_name != "") {
        $item_id = $wpdb->get_row($wpdb->prepare("SELECT ItemID FROM item_daten WHERE Item_Deutsch = %s", $item_name));
        $item_daten_json = file_get_contents('http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/OfflineDaten/ItemsData/item_'.$item_id->ItemID.'.json');
        $item_daten = json_decode($item_daten_json, true);
        echo '<img class="guide-item'.$i.'" title="'.$item_name.'" alt="'.$item_name.'" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/item/'.$item_daten[id].'.png" />';
        if ($i<6) {
            echo '<div style="padding-top:20px;float:left;"> ></div>';
        }
        $i++;
    }
}
echo '</div>';
echo '<div style="clear:both"></div>';
//Werbung
if (function_exists('adinserter')) {
    echo adinserter(7);
}
//Skill-Order
echo '<div id="skill-order">';
echo '<div id="champion-hl">';
echo'<h3> F&auml;higkeiten Reihenfolge</h3></div>';
echo '<div id="skill-grid">';
echo '<div class="skill-grid-square-hl"></div>';
for ($i=1;$i<19;$i++) {
    echo '<div class="skill-grid-square-hl"><center>'.$i.'</center></div>';
}
echo '<div style="clear:both"></div>';
$inhalt = file_get_contents('http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/OfflineDaten/Champspells/spells_'.$champ_id.'.json');
$champion_spells= json_decode($inhalt, true);
$sql = $wpdb->get_row($wpdb->prepare("SELECT * FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
$spells=array("Q","W","E","R");
$j=0;
foreach ($champion_spells['spells'] as $img) {
    if ($j==4) {
        break;
    }
    $spellnameNr = $j+1;
    $spellname = "SkillName".$spellnameNr;
    echo '<div class="skill-grid-square">';
    echo '<img title="'.$sql->$spellname.'" alt="'.$champname.$spells[$j].'" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/spell/'.$img[image][full].'"/>';
    echo '</div>';
    for ($i=1;$i<19;$i++) {
        $skillnr = "Skill".$i;
        if ($sql->$skillnr == $spells[$j]) {
            echo '<div class="skill-grid-square zeile'.$j.'_spalte'.$i.' zeile'.$j.'">'.$sql->$skillnr.'</div>';
        } else {
            echo '<div class="skill-grid-square zeile'.$j.'_spalte'.$i.' zeile'.$j.'"></div>';
        }
    }
    $j++;
    echo '<div style="clear:both"></div>';
}
echo '<div style="clear:both"></div>';
echo '</div>';
echo '<div style="clear:both"></div>';
echo '</div>';
//Mastery-function einbinden
include(dirname(__FILE__).'/guide_masterys.php');
echo '<div id="mastery-page-guide-hl">';
echo '<h3>'.$champnamekorrekt.' Meisterschaft</h3></div>';
echo '<div id="mastery-page-guide">';
echo '<div style="clear:both"></div>';
$sql = $wpdb->get_row($wpdb->prepare("SELECT MasteryCode FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
//Funktion Aufrufen, siehe guide_masterys.php
masterys($sql->MasteryCode);
echo '</div>';
//Runen
echo '<div id="rune-page">';
echo '<div id="champion-hl">';
echo '<h3>'.$champnamekorrekt.' Runenseite</h3></div>';
echo '<div class="runenseiten">';
$sql1 = $wpdb->get_row($wpdb->prepare("SELECT RuneCode1,RuneCode2 FROM guide_daten WHERE ChampionName = %s AND RoleRate=%d", $champname, $rolerate));
if ($sql1->RuneCode1!="") {
    $sql2 = $wpdb->get_row($wpdb->prepare("SELECT * FROM rune_code WHERE RuneCode = %d ", $sql1->RuneCode1));
    if ($sql2->Code < 7) {
        echo "<p id=\"rune-page-hl\"><u>".$sql2->Name."</u></p>";
    }
    echo $sql2->Code;
}
echo '<div style="clear:both;margin:15px;"></div>';
if ($sql1->RuneCode2!="") {
    $sql2 = $wpdb->get_row($wpdb->prepare("SELECT * FROM rune_code WHERE RuneCode = %d ", $sql1->RuneCode2));
    if ($sql2->Code < 7) {
        echo "<p id=\"rune-page-hl\"><u>".$sql2->Name."</u></p>";
    }
    echo $sql2->Code;
}
echo '</div>';
echo '</div>';
echo '</div>';
?>
