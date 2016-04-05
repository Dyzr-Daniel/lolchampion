<?php
/*This function provides the mastery container for the guide_template_page.php
The content comes from json files which were downloaded from riots servers 
*/
function masterys($skillung)
{
    $inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/masterys/mastery_tree.json');
    $masterys= json_decode($inhalt, true);
    $inhalt_all_masteries = file_get_contents(dirname(__FILE__).'/OfflineDaten/masterys/all_masteries.json');
    $all_masteries= json_decode($inhalt_all_masteries, true);
    $skill_length= strlen($skillung);
    for ($i=0;$i<=$skill_length;$i++) {
        $mastery_skillung[$i] = substr($skillung, $i, 1);
    }
    $skill_array_nr=0;
    $baum_namen = [ "Wildheit", "Gerissenheit","Entschlossenheit"];
    $k=0; //Laufindex für $baum_namen
        foreach ($masterys['tree'] as $tree) {
            //Pfad Tiefe bestimmen
        $tree_depth=count($tree);
        //Für jedes Tier auslesen, wie viele Elemente vorhanden sind und in Array speichern
        for ($i=0;$i<$tree_depth;$i++) {
            $tree_tier[$i]=count($tree[$i]);
        }
        //Baum erstellen
        echo '<div class="mastery">'; //Baum öffnen
        echo '<div class="mastery-tree" id="'.strtolower($baum_namen[$k]).'">'; //Baum öffnen
        for ($i=0;$i<$tree_depth;$i++) {
            echo '<div class="tier tier'.$i.'"">'; //Tier div öffnen
            for ($j=0;$j<$tree_tier[$i];$j++) {
                if (isset($tree[$i][$j][masteryId])) {
                    echo '<span class="mastery-tier-img">';
                    if ($mastery_skillung[$skill_array_nr] <> "0") {
                        echo "<div class='mastery-tier-img-color' style='background:url(\"/_wordpress_dev716a/wp-content/bilder/masteries/".$tree[$i][$j][masteryId].".png\")'></div>";
                        echo "<div class='mastery-points'>".$mastery_skillung[$skill_array_nr]."/".$all_masteries['data'][$tree[$i][$j][masteryId]][ranks]."</div>";
                    } else {
                        echo "<div class='mastery-tier-img-grey'style='background:url(\"/_wordpress_dev716a/wp-content/bilder/masteries/gray_".$tree[$i][$j][masteryId].".png\")'></div>";
                        echo "<div class='mastery-points-grey'>".$mastery_skillung[$skill_array_nr]."/".$all_masteries['data'][$tree[$i][$j][masteryId]][ranks]."</div>";
                    }
                    echo '</span>';
                    $skill_array_nr++;
                } else {
                    echo '<span class="mastery-tier-img">';
                    echo '</span>';
                }
            }
            echo '</div>'; //Tier div schließen
        }
            echo "<div class='tree-name'>".$baum_namen[$k]."</div>";
            $k++;
            echo '</div></div>'; // Baum schließen
        }//Ende foreach
}//Ende function
