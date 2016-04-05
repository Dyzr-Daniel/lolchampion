<!--
This is the template for the counter pages on www.lolchampion.de/counter
The content comes from json files which were downloaded from riots servers and
from lolchampions own database.The User can also vote for different Champions. See counter-vote.js,
update_counter_strong.php and update_counter_weak.php
-->
<script type="text/javascript" src="/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/counter-vote.min.js"></script>

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
//Verbindung DB
global $wpdb;
//Werbung
if (function_exists('adinserter')) {
    echo adinserter(5);
}
    echo '<div id="COUNTER">';
    if ($champname != "MonkeyKing") {
        echo '<a title="Zur Champion-Seite" href="http://www.lolchampion.de/champions/'.$x[key].'/">';
    } else {
        echo '<a title="Zur Champion-Seite" href="http://www.lolchampion.de/champions/Wukong/">';
    }
    echo '<div id="counterBGBild" style="background-image:url(http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/counter/'.$x[key].'_Splash_0.jpg);">';
    echo '<div id="champname" ><h2>'.$champnamekorrekt.' Counter</h2></div>';
    echo '<div style="clear:left;"></div>';
    echo '<div id="champtitle" >'.$champ_title.'</div>';
    echo '<div style="clear:left;"></div>';
    echo '</div></a>';
    echo '<p>Hier findest du hilfreiche Tipps und Tricks zu Countern von '.$champnamekorrekt.'.
	Außerdem bestimmst du selber mit, gegen welche anderen Champions '.$champnamekorrekt.' stark und schlecht ist und
	hilfst so mit, die Seite immer aktuell zu halten. Falls du zudem Ideen oder Tippvorschläge zu Countern von '.$champnamekorrekt.' hast,
	schreib uns doch einfach eine <a href="mailto:info@lolchampion.de">E-Mail</a>.</p>';
    //Quarter Tipps
    echo '<div id="counter2">';
    echo '<div id="Quarter_2_hl" >Counter-Tipps:</div>';
    echo '<div id="Quarter_2">';
    $sql = $wpdb->get_row($wpdb->prepare("SELECT * FROM Counter_Tips WHERE Champion=%s", $champname));
    echo '<ul id="counter-tips" style="margin-top:0px;">';
    echo '<li>';
    echo $sql->Tip_1;
    echo '</li>';
    echo '<li>';
    echo $sql->Tip_2;
    echo '</li>';
    if ($sql->Tip_3 <>'') {
        echo '<li>';
        echo $sql->Tip_3;
        echo '</li>';
    }
    if ($sql->Tip_4 <>'') {
        echo '<li>';
        echo $sql->Tip_4;
        echo '</li>';
    }
    echo'</ul>';
    echo '</div>';
    echo '</div>';
    //Quarter Stark
    echo '<div id="counter2" >';
    echo '<div id="Quarter_1_hl">'.$champnamekorrekt.' ist stark gegen:</div>';
    echo'<div id="Quarter_1">';
    $counterquery = $wpdb->get_results("SELECT * FROM counter_stark_gegen WHERE $champname ORDER BY $champname DESC LIMIT 0,5", ARRAY_A);
    $summe = $wpdb->get_var("SELECT SUM($champname) FROM (SELECT * FROM counter_stark_gegen WHERE $champname ORDER BY $champname DESC LIMIT 0,5) AS subquery");
    echo '<div style="height:10px;"></div>';
    foreach ($counterquery as $row) {
        $champ1 = $row['Champion'];
        $counter_id = $row['ChampionID'];
        $champcalled = $row['Called'];
        if ($champ1 =="MonkeyKing") {
            echo '<div id="counter-div"><div style="float:left">
			<input class="plus_button" onclick="UpdateValue(\''.$champname.'\',\''.$counter_id.'\',\'plus\',\'counter_stark_gegen\')" type="button" name="Plus1" value="+"></br>
			<input class="minus_button" onclick="UpdateValue(\''.$champname.'\',\''.$counter_id.'\',\'minus\',\'counter_stark_gegen\')" type="button" name="Minus1" value="-"></div>
			<div id="counter-bild" style="float:left;">
			<a target="_blank" href="/champions/Wukong" title="'.$champcalled.'" alt="'.$champcalled.'"><img title="'.$champcalled.'" alt="'.$champcalled.'_Square"
			src="/_wordpress_dev716a/wp-content/bilder/championssquare_rotation/'.$champ1.'.png" /></a></div>';
        } else {
            echo '<div id="counter-div">
			<div style="float:left">
			<input class="plus_button" onclick="UpdateValue(\''.$champname.'\',\''.$counter_id.'\',\'plus\',\'counter_stark_gegen\')" type="button" name="Plus1" value="+"></br>
			<input class="minus_button" onclick="UpdateValue(\''.$champname.'\',\''.$counter_id.'\',\'minus\',\'counter_stark_gegen\')" type="button" name="Minus1" value="-"></div>
			<div id="counter-bild" style="float:left;">
			<a target="_blank" href="/champions/'.$champ1.'" title="'.$champcalled.'" alt="'.$champcalled.'">
			<img title="'.$champcalled.'" alt="'.$champcalled.'_Square" src="/_wordpress_dev716a/wp-content/bilder/championssquare_rotation/'.$champ1.'.png" /></a></div>';
        }
        echo '<div class="bar green" style="float:left;width:160px;">';
        echo '<div id="counter-name">';
        echo $champcalled;
        echo '</div>';
        echo '<div class="counter-value counter-value'.$counter_id.'" stlye="padding-top:15px">';
        echo $row[$champname];
        echo '</div>';
        echo '</div>';
        echo '<div class="bar green" style="float:left;border-radius:0px 5px 5px 0px;background-color:green;max-width:30%;width:';
        $balken_breite=($row[$champname]/$summe)*100;
        if ($balken_breite <> 0) {
            echo $balken_breite;
        } else {
            echo'20';
        }
        echo  '%;"></div>';
        echo '</div>';
        echo '<div style="margin:10px"></div>';
    }    //Ende foreach
echo '</div>';
echo '</div>';
//Link Block
if ($champname != "MonkeyKing") {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champions/'.$champname.'"><div id="champion-link1">';
} else {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champions/Wukong"><div id="champion-link1">';
}
echo '<center>'.$champnamekorrekt.' Seite</center>';
echo '</div></a>';
if ($champname != "MonkeyKing") {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champion-guides/'.$champname.'-guide"><div id="champion-link2">';
} else {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/champion-guides/Wukong-guide"><div id="champion-link2">';
}
echo '<center>'.$champnamekorrekt.' Guide';
echo '</div></a>';
//Quarter schwach
echo '<div id="counter2" style="margin-top: 30px;">';
echo '<div id="Quarter_3_hl">'.$champnamekorrekt.' ist schwach gegen:</div>';
echo '<div id="Quarter_3">';
echo '<div style="margin:10px";></div>';
$counterquery2 = $wpdb->get_results("SELECT * FROM counter_schwach_gegen WHERE $champname ORDER BY $champname LIMIT 124,130", ARRAY_A);
$summe = $wpdb->get_var("SELECT SUM($champname) FROM (SELECT * FROM counter_schwach_gegen WHERE $champname ORDER BY $champname LIMIT 124,130) AS subquery");
foreach ($counterquery2 as $row) {
    $champ1 = $row['Champion'];
    $counter_id = $row['ChampionID'];
    $champcalled = $row['Called'];
    if ($champ1 =="MonkeyKing") {
        echo '<div id="counter-div">
		<div id="counter-bild" style="float:right;">
		<a target="_blank" href="http://www.lolchampion.de/champions/Wukong" title="'.$champcalled.'" alt="'.$champcalled.'">
		<img title="'.$champcalled.'" alt="'.$champcalled.'_Square" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/championssquare_rotation/'.$champ1.'.png" />
		</a></div>
		<div style="float:right">
		<input class="plus_button" onclick="UpdateValue2(\''.$champname.'\',\''.$counter_id.'\',\'plus\',\'counter_schwach_gegen\')" type="button" name="Plus1" value="+"></br>
		<input class="minus_button" onclick="UpdateValue2(\''.$champname.'\',\''.$counter_id.'\',\'minus\',\'counter_schwach_gegen\')" type="button" name="Minus1" value="-"></div>';
    } else {
        echo '<div id="counter-div">
		<div id="counter-bild" style="float:right;">
		<a target="_blank" href="http://www.lolchampion.de/champions/'.$champ1.'" title="'.$champcalled.'" alt="'.$champcalled.'">
		<img title="'.$champcalled.'" alt="'.$champcalled.'_Square" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/championssquare_rotation/'.$champ1.'.png" />
		</a></div>
		<div style="float:right;">
		<input class="plus_button" onclick="UpdateValue2(\''.$champname.'\',\''.$counter_id.'\',\'plus\',\'counter_schwach_gegen\')" type="button" name="Plus1" value="+"></br>
		<input class="minus_button" onclick="UpdateValue2(\''.$champname.'\',\''.$counter_id.'\',\'minus\',\'counter_schwach_gegen\')" type="button" name="Minus1" value="-">
 		</div>';
    }
    echo '<div class="bar red" style="float:right;width:160px;">';
    echo '<div id="counter-name2">';
    echo $champcalled;
    echo '</div>';
    echo '<div class="counter-valueZwei counter-valueZwei'.$counter_id.'" stlye="padding-top:15px">';
    echo $row[$champname];
    echo '</div>';
    echo '</div>';
    echo '<div class="bar red" style="border-radius:5px 0px 0px 5px;float:right;background-color:red;max-width:30%;width:';
    $balken_breite=($row[$champname]/$summe)*100;
    if ($balken_breite <> 0) {
        echo $balken_breite;
    } else {
        echo'20';
    }
    echo  '%;"></div>';
    echo '</div>';
    echo '<div style="margin:10px"></div>';
}//Ende foreach
echo '</div>';
echo '</div>';
//Quarter Werbung
echo '<div id="counter2">';
echo '<div id="Quarter_4">';
echo 'Anzeige</br>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Banner Counter -->
	<ins class="adsbygoogle"
  style="display:inline-block;width:336px;height:280px"
  data-ad-client="ca-pub-3510457403228846"
  data-ad-slot="9076921018"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	<script type="text/javascript" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/advert.js"></script>
	<script type="text/javascript">
	if (document.getElementById("tester") == undefined){
		document.write(\' <a href="http://www.lolchampion.de/adblocker-deaktiveren/"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/uploads/2014/12/antiadblock_Amumu.jpg"/></a>\');
	}
	</script></div>';
echo '</div>';
echo '</div>';
?>
