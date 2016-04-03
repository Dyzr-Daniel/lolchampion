<?php
/*
This file checks if the riot server provides new patch files.
If a new patch is available, all images from the riot server will be downloaded resized and/or croped
An Email is sent to the admin when no new patch is available or the riot servers are offline.
*/

//Datei um Patchvesion vom Riot-Server zu laden
include(dirname(__FILE__).'/patchdaten/patch.php');
//Prüfen welche Patchversion auf der Homepage derzeit aktiv ist
$cache_file = dirname(__FILE__).'/patchdaten/aktueller-patch-auf-lolchampion.json';
//Alte Patchversion auslesen
if (file_exists($cache_file)) {
  $fh = fopen($cache_file, 'r');
  $alte_patch_version = trim(fgets($fh));
  fclose($fh);
}
//Neue Patchversion auslesen
$datenpuffer_riot = new riotapi_patch('euw');
$neue_patch_version = $datenpuffer_riot->getPatch();
if ($neue_patch_version =="500" || $neue_patch_version =="503") {
  fehlermail("Verbindung zum Server bei Bilder");
}
//Vergleich der beiden Patchversionen
if ($alte_patch_version == $neue_patch_version[css]) {
  //Wenn Patchversionen gleich sind beende das Skrpit
  fehlermail("Kein neuer Patch verfügbar");
}
/*Wenn Patchversionen unterschiedlich, dann lade Inhalte neu herunter.
Anschließend speichere die neue Patchversion in der Datei: aktueller-patch-auf-lolchampion.json*/
else {
  if (isset($neue_patch_version[css])) {
  //Champion Square Bilder runterladen 120x120
    include(dirname(__FILE__).'/patchdaten/champTags.php');
    $datenpuffer_tags = new riotapi_abfrageTags('euw');
    $champ_namen =$datenpuffer_tags->getAllChampionInfos('tags');
    $champ_namen = json_decode($champ_namen, true);
    if (isset($champ_namen['data'])) {
      foreach ($champ_namen['data'] as $name) {
        $champion_square_url ='http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/img/champion/'.$name[key].'.png';
        $img_path = dirname(__FILE__).'/bilder/championssquare/'.$name[key].".png";
        $path_resized_50 =dirname(__FILE__).'/bilder/championssquare_rotation/'.$name[key].".png";
        $path_resized_30 =dirname(__FILE__).'/bilder/champs_angebote/'.$name[key].".png";
        //Bild 120x120 runterladen und abspeichern
        $img_data = file_get_contents($champion_square_url);
        file_put_contents($img_path, $img_data); //CHMOD 777 des Orderns wenn Bild nicht vorhanden, falls vorhanden muss Bild 666 haben sonst funktioniert es derzeit nicht!
        //Bild erneut laden und kleinere Versionen davon erstellen
        $source = imagecreatefrompng($img_path);
        //50x50 Bilder erstellen
        $thumb_50 = imagecreatetruecolor(50, 50);
        imagecopyresized($thumb_50, $source, 0, 0, 0, 0, 50, 50, 120, 120);
        imagepng($thumb_50, $path_resized_50, 9);
        //30x30 Bilder erstellen
        $thumb_30 = imagecreatetruecolor(30, 30);
        imagecopyresized($thumb_30, $source, 0, 0, 0, 0, 30, 30, 120, 120);
        imagepng($thumb_30, $path_resized_30, 9);
       }
       //Skins herunterladen
       $champ_namen =$datenpuffer_tags->getAllChampionInfos('skins');
       $champ_namen = json_decode($champ_namen, true);
       foreach ($champ_namen['data'] as $name) {
         foreach ($name['skins'] as $skins) {
           $skin_url ='http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$name[key].'_'.$skins[num].'.jpg';
           $skin_img_path = dirname(__FILE__).'/bilder/Skins/'.$name[key].'_Splash_'.$skins[num].'.jpg';
           $skin_thumb_path = dirname(__FILE__).'/bilder/tn_skins/tn_'.$name[key].'_Splash_'.$skins[num].'.jpg';
           $img_header_path = dirname(__FILE__).'/bilder/counter/'.$name[key].'_Splash_'.$skins[num].'.jpg';
           $skin_source = imagecreatefromjpeg($skin_url);
           imagejpeg($skin_source, $skin_img_path, 70);
           //Thumbnail erstellen
           list($width, $height) = getimagesize($skin_img_path);
           $thumb_skin = imagecreatetruecolor(339, 200);
           imagecopyresized($thumb_skin, $skin_source, 0, 0, 0, 0, 339, 200, $width, $height);
           imagejpeg($thumb_skin, $skin_thumb_path, 70);
           //Headerbild der Championseiten erstellen nur aus Standard-Skin
          if ($skins[num]=="0") {
            //Bild erst verkleinern, dann zusammenschneiden
            $thumb_crop = imagecreatetruecolor(960, 567);
            imagecopyresized($thumb_crop, $skin_source, 0, 0, 0, 0, 960, 567, $width, $height);
            $crop_area = array("x" => 0, "y" => 0, "width" => 960, "height" => 360);
            $crop_image = imagecrop($thumb_crop, $crop_area);
            imagejpeg($crop_image, $img_header_path, 70);
          }
         }
        }
        //Spells herunterladen
        $champ_namen =$datenpuffer_tags->getAllChampionInfos('spells');
        $champ_namen = json_decode($champ_namen, true);
        foreach ($champ_namen['data'] as $name) {
          foreach ($name['spells'] as $spells) {
            $spell_url ='http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/img/spell/'.$spells[image][full];
            $spell_img_path = dirname(__FILE__).'/bilder/spell/'.$spells[image][full];
            $spell_source = imagecreatefrompng($spell_url);
            imagepng($spell_source, $spell_img_path, 9);
            }
          }
        //Passive herunterladen
        $champ_namen =$datenpuffer_tags->getAllChampionInfos('passive');
        $champ_namen = json_decode($champ_namen, true);
        foreach ($champ_namen['data'] as $name) {
          $passive_url ='http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/img/passive/'.$name[passive][image][full];
          $passive_img_path = dirname(__FILE__).'/bilder/passive/'.$name[passive][image][full];
          $passive_source = imagecreatefrompng($passive_url);
          imagepng($passive_source, $passive_img_path, 9);
        }
       }
       //Items herunterladen
       include(dirname(__FILE__).'/patchdaten/getAllItems.php');
       $datenpuffer_item = new riotapi_abfrage_itemsAll('euw');
       $items =$datenpuffer_item->getAllItems('tags');
       $items = json_decode($items, true);
        if (isset($items['data'])) {
          foreach ($items['data'] as $item) {
            $item_url ='http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/img/item/'.$item[id].'.png';
            $item_img_path = dirname(__FILE__).'/bilder/item/'.$item[id].'.png';
            $item_source = imagecreatefrompng($item_url);
            imagepng($item_source, $item_img_path, 9);
          }
        }
      //Mastery Bilder laden
      include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/all_masteries.php');
      $datenpuffer_all_masteries = new riotapi_abfrage_mastery('euw');
      $inhalt_all_masteries = $datenpuffer_all_masteries->getAllMasteries('all');
      $all_masteries=json_decode($inhalt_all_masteries, true);
      if (isset($all_masteries['data'])) {
        foreach ($all_masteries['data'] as $mastery) {
          $mastery_url ='http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/img/mastery/'.$mastery[id].'.png';
          $mastery_img_path = dirname(__FILE__).'/bilder/masteries/'.$mastery[id].'.png';
          $mastery_source = imagecreatefrompng($mastery_url);
          imagepng($mastery_source, $mastery_img_path, 9);
          }
        }

    //Neue Patchversionen auf lolchampion abspeichern
    if (isset($neue_patch_version[css])) {
      $fh = fopen($cacheFile, 'w');
      fwrite($fh, $neue_patch_version[css]);
      fclose($fh);
    }
  }//ende if isset $neue_patch_version[css]
}//ende Else Patchnotizen
//Funktion schickt E-Mail,falls API nicht vorhanden ist
function fehlermail($grund)
{
    $empfaenger = 'info@lolchampion.de';
    $betreff = 'Fehler bei '.$grund.' Update';
    $nachricht = '';
    $header = 'From: info@lolchampion.de' . "\r\n" .
      'Reply-To: info@lolchampion.de' . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
    mail($empfaenger, $betreff, $nachricht, $header);
}
