<?php
/*This is the template for the champion pages on www.lolchampion.de/champions
The content comes from json files which were downloaded from riots servers or
from lolchampions own database.
*/
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
//Werbung
if (function_exists('adinserter')) {
    echo adinserter(3);
}
echo '<div id="Champion">';
echo '<div id="ChampionBG" style="height:360px;background-image:url(http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/counter/'.$x[key].'_Splash_0.jpg);margin-bottom:1em;position:relative;border:1px solid #000;color:#fff;">';
echo '<div id="champname"><h2>'.$champnamekorrekt.'</h2></div>';
echo '<div id="champtitle">'.$champ_title.'</div>';
echo '<div style="clear:left;"></div>';
echo'<ul id="champion-skills-header">';
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champspells/spells_'.$champ_id.'.json');
$champion_spells= json_decode($inhalt, true);
$spells=array("R","E","W","Q");
$j=0;
foreach (array_reverse($champion_spells['spells']) as $x) {
    if ($j>3) {
        break;
    }
    echo'<li>
  <img alt="'.$champname.$spells[$j].'" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/spell/'.$x[image][full].'"/>';
    echo '<div id="spell-hover">';
  //Platzhalter in json-Datei gegen Werte austauschen
  $countkeys= count($x[effectBurn]);
    for ($i=0;$i <= $countkeys;$i++) {
        $estr[$i]=" ".$x[effectBurn][$i+1]." ";
        $eindex=$i+1;
        $x[tooltip]=str_replace("{{ e".$eindex." }}", $estr[$i], $x[tooltip]);
        $x[resource]=str_replace("{{ e".$eindex." }}", $estr[$i], $x[resource]);
    }
    for ($i=0;$i < 6;$i++) {
        $fstr[$i]="";
        $findex=$i+1;
        $x[tooltip]=str_replace("({{ f".$findex." }})", $fstr[$i], $x[tooltip]);
    }
    for ($i=0;$i < 6;$i++) {
        $fstr[$i]="";
        $findex=$i+1;
        $x[tooltip]=str_replace("{{ f".$findex." }}", $fstr[$i], $x[tooltip]);
    }
    for ($i=0;$i < 6;$i++) {
        $fstr[$i]="";
        $findex=$i+1;
        $x[tooltip]=str_replace("(+)", $fstr[$i], $x[tooltip]);
    }
    $countkeys= count($x[vars]);
    for ($i=0;$i <= $countkeys;$i++) {
        $astr[$i]=$x[vars][$i][coeff][0]*100;
        $schadensart=$x[vars][$i][link];
        if ($schadensart=="bonusattackdamage") {
            $schadensart="zusätzlicher Angriffschaden";
        }
        if ($schadensart=="spelldamage") {
            $schadensart="Fähigkeitsstärke";
        }
        if ($schadensart=="attackdamage") {
            $schadensart="Angriffschaden";
        }
        if ($schadensart=="@dynamic.attackdamage") {
            $schadensart="Fähigkeitsstärke";
        }
        $astr[$i]=$astr[$i]." % ".$schadensart;
        $aindex=$i+1;
        $x[tooltip]=str_replace("{{ a".$aindex." }}", $astr[$i], $x[tooltip]);
    }
  //Ausgabe String mit Werten
  echo "<div id=\"spellname\">".$spells[$j].": ".$x[name]."</div>";
    $cost=$x[costBurn];
    $x[resource]=str_replace("{{ cost }}", $cost, $x[resource]);
    echo "<b>Kosten: </b>".$x[resource]."</br>";
    if ($x[rangeBurn]=="self") {
        $x[rangeBurn]="Selbst";
    };
    echo "<b>Reichweite: </b>".$x[rangeBurn]."</br></br>";
    echo $x[tooltip];
    echo '</br></br>';
    echo '</div>';
    echo'</li>';
    $j++;
}
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champpassive/passive_'.$champ_id.'.json');
$champion_passive= json_decode($inhalt, true);
echo '<li><img alt="'.$champname.'P" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/passive/'.$champion_passive[passive][image][full].'"/>';
echo '<div id="spell-hover">';
echo "<div id=\"spellname\">Passiv: ".$champion_passive[passive][name]."</div>";
echo $champion_passive[passive][description];
echo '</div>';
echo'</li>';
echo '</ul>';
echo '</div>';
/*Ende Header*/
//Intro Text
if ($champname !="MonkeyKing") {
    echo '<p>Alle Infos rund um '.$champnamekorrekt.' inklusive Videos und Übersicht seiner Fähigkeiten und Skins.</br></br>
Auf dieser Seite findet ihr alle Infos rund um '.$champnamekorrekt.'. Informiert euch ganz einfach über die Skins und Fähigkeiten von '.$champnamekorrekt.' und erhaltet im Champion Spotlight einen ersten Eindruck davon, wie ihr die Fähigkeiten von '.$champnamekorrekt.' am besten im Kampf einsetzt. Nebst den Basisinformationen findet ihr natürlich auch weiterführende Infos auf unserer <a href="http://www.lolchampion.de/counter/'.$champname.'/">Counterseite</a> und unserer Guideseite, wo ihr beispielsweise erfahrt gegen wen '.$champnamekorrekt.' besonders stark oder schwach ist und wie ihr '.$champnamekorrekt.' am effizientesten skillt, welche Beschwörerzauber man mitnimmt und welche Items man sich kaufen sollte.</p>';
} else {
    echo '<p>Alle Infos rund um '.$champnamekorrekt.' inklusive Videos und Übersicht seiner Fähigkeiten und Skins.</br></br>
Auf dieser Seite findet ihr alle Infos rund um '.$champnamekorrekt.'. Informiert euch ganz einfach über die Skins und Fähigkeiten von '.$champnamekorrekt.' und erhaltet im Champion Spotlight einen ersten Eindruck davon, wie ihr die Fähigkeiten von '.$champnamekorrekt.' am besten im Kampf einsetzt. Nebst den Basisinformationen findet ihr natürlich auch weiterführende Infos auf unserer <a href="http://www.lolchampion.de/counter/Wukong/">Counterseite</a> und unserer Guideseite, wo ihr beispielsweise erfahrt gegen wen '.$champnamekorrekt.' besonders stark oder schwach ist und wie ihr '.$champnamekorrekt.' am effizientesten skillt, welche Beschwörerzauber man mitnimmt und welche Items man sich kaufen sollte.</p>';
}
//Abschnitt Champion Werte
echo '<div id="champion-werte">';
echo '<div id="champion-hl">';
echo '<h3>'.$champnamekorrekt.' Werte</h3>';
echo '</div>';
echo '<img id="champions-image" alt="'.$champnamekorrekt.'_square" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/championssquare/'.$champname.'.png"/>';
echo '<div style="float:left">';
echo '<div id="champ-value-name">'.$champnamekorrekt.'</div>';
echo '<div id="champ-value-title">'.$champ_title.'</div>';
echo '</div>';
echo '<div style="clear:left;"></div>';
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champstats/stats_'.$champ_id.'.json');
$champion_stats= json_decode($inhalt, true);
if (array_key_exists('armor', $champion_stats['stats'])) {
    echo '<div id="champion-values"><div id="champion-values-optic" ><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/armor.png"/>';
    echo '   R&uuml;stung: ';
    echo $champion_stats['stats']['armor'].' (+'. $champion_stats['stats']['armorperlevel'].' pro Stufe)</div>';
}
if (array_key_exists('hpregen', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/healthreg.png"/>';
    echo '   Lebensregen: ';
    echo $champion_stats['stats']['hpregen'].' (+'. $champion_stats['stats']['hpregenperlevel'].' pro Stufe)</div>';
}
if (array_key_exists('mpregen', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/manareg.png"/>';
    echo '   Manaregen: ';
    echo $champion_stats['stats']['mpregen'].' (+'. $champion_stats['stats']['mpregenperlevel'].' pro Stufe)</div>';
}
if (array_key_exists('attackdamage', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/attackdmg.png"/>';
    echo '   Angriffsschaden: ';
    echo $champion_stats['stats']['attackdamage'].' (+'. $champion_stats['stats']['attackdamageperlevel'].' pro Stufe)</div>';
}
if (array_key_exists('attackspeedoffset', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/attackspeed.png"/>';
    echo '   Angriffstempo: ';
    echo round(0.625/(1+$champion_stats['stats']['attackspeedoffset']), 3).' (+'. $champion_stats['stats']['attackspeedperlevel'].'% pro Stufe)</div>';
}
if (array_key_exists('spellblock', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/magicresist.png"/>';
    echo '   Magieresistenz: ';
    echo $champion_stats['stats']['spellblock'].' (+'. $champion_stats['stats']['spellblockperlevel'].' pro Stufe)</div>';
}
echo '</div>';
if (array_key_exists('movespeed', $champion_stats['stats'])) {
    echo '<div id="champion-values"><div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/movementspeed.png"/>';
    echo '   Lauftempo: ';
    echo $champion_stats['stats']['movespeed'].'</div>';
}
if (array_key_exists('hpperlevel', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/health.png"/>';
    echo '   Leben: ';
    echo $champion_stats['stats']['hp'].'</div>';
}
if (array_key_exists('mp', $champion_stats['stats'])) {
    echo '<div id="champion-values-optic"><img src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tabellen_icons/mana.png"/>';
    echo '   Mana: ';
    echo $champion_stats['stats']['mp'].'</div></div>';
}
echo '</div>';
//Abschnitt Spotlight Video
echo '<div id="spotlight">';
echo '<div id="champion-hl">';
echo '<h3>'.$champnamekorrekt." Spotlight Video</h3>";
echo '</div>';
global $wpdb;
$sql = $wpdb->get_row("SELECT * FROM Spotlight_HREF WHERE Championname = '$champname'");
echo '<iframe width="100%" height="259" src="'.$sql->Link.'" frameborder="0" allowfullscreen></iframe>';
echo '</div>';
//Abschnitt Links zu Guide und Counter Seiten
if ($champname !="MonkeyKing") {
    echo '<a style="color:#fff;" href="/champion-guides/'.$champname.'-guide"><div style="margin-bottom:15px;" title="Zum '.$champnamekorrekt.' Guide!" id="champion-link1">';
    echo '<center>'.$champnamekorrekt.' Guide</center>';
    echo '</div></a>';
} else {
    echo '<a style="color:#fff;" href="/champion-guides/wukong-guide"><div style="margin-bottom:15px;" title="Zum '.$champnamekorrekt.' Guide!" id="champion-link1">';
    echo '<center>'.$champnamekorrekt.' Guide</center>';
    echo '</div></a>';
}
if ($champname !="MonkeyKing") {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/counter/'.$champname.'"><div id="champion-link2">';
    echo '<center>'.$champnamekorrekt.' Counter';
    echo '</div></a>';
} else {
    echo '<a style="color:#fff" href="http://www.lolchampion.de/counter/Wukong"><div id="champion-link2">';
    echo '<center>'.$champnamekorrekt.' Counter';
    echo '</div></a>';
}
//Werbung
if (function_exists('adinserter')) {
    echo adinserter(6);
}
//Abschnitt Skin Images
echo '<div id="champion-skins">';
echo '<div id="champion-hl-skins">';
echo '<h3>'.$champnamekorrekt." Skins</h3>";
echo '</div>';
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champskins/skins_'.$champ_id.'.json');
$champion_skins= json_decode($inhalt, true);
foreach ($champion_skins['skins'] as $x) {
    echo '<div id="champion-skins-elements">';
    echo '<a title="'.$x[name].'" href="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/Skins/'.$champname.'_Splash_'.$x[num].'.jpg" rel="prettyPhoto[gallery3] alt="'.$x[name].'" name="'.$x[name].'"><img alt="'.$x[name].'" src="http://www.lolchampion.de/_wordpress_dev716a/wp-content/bilder/tn_skins/tn_'.$champname.'_Splash_'.$x[num].'.jpg"/></a>';
    if ($x[name]=='default') {
        echo '<div id="champion-skin-name"><center>Standard-Skin</center></div>';
    } else {
        echo '<div id="champion-skin-name"><center>'.$x[name].'</center></div>';
    }
    echo '</div>';
}
echo '</div>';
//Abschnitt Champion Lore
echo '<div id="champion-lore">';
echo '<div id="champion-hl">';
echo '<a name="champion-geschichte"><h3>'.$champnamekorrekt." Geschichte</h3></a>";
echo '</div>';
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champlore/lore_'.$champ_id.'.json');
$champion_lore= json_decode($inhalt, true);
echo '<p>'.$champion_lore['lore'].'</p>';
echo '</div>';
echo '</div>';
