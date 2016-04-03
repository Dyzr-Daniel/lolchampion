<?php
/*
This file checks if the riot server provides new patch files.
If a new patch is available, all patchfiles from the riot server will be downloaded.
An email is sent to the admin when no new patch is available or the riot servers are offline.
*/

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
//Vergleich der beiden Patchversionen
if ($alte_patch_version == $neue_patch_version[css]) {
    //Wenn Patchversionen gleich sind beende das Skrpit
  fehlermail("Kein neuer Patch verfügbar");
} else {
    include(dirname(__FILE__).'/themes/Avada-Child-Theme/php-riot-api.php');
    $datenpuffer_riot = new riotapi('euw');
  //ChampionIDs über Riot-Api abfragen, um weiter unten für jeden Champion die Daten zu speichern
  $inhalt_riot = $datenpuffer_riot->getChampionIDs();
    $champ_ids = json_decode($inhalt_riot, true);
  //Champion Namen speichern
  include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/champTags.php');
    $datenpuffer_tags = new riotapi_abfrageTags('euw');
    $inhalt =$datenpuffer_tags->getAllChampionInfos('tags');
  //Daten für Champion Seiten speichern
  include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/champDaten.php');
    $datenpuffer = new riotapi_abfrage('euw');
    foreach ($champ_ids['champions'] as $api_id) {
        $champ_id =$api_id[id];
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'stats');
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'blurb');
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'spells');
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'passive');
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'skins');
        $inhalt =$datenpuffer->getChampionInfos($champ_id, 'lore');
    }
  //Daten über alle Items speichern
  include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/getAllItems.php');
    $datenpuffer_items_all = new riotapi_abfrage_itemsAll('euw');
    $inhalt_items_all = $datenpuffer_items_all->getAllItems('all');
  //Alle Item IDs speichern, um für jedes Item die einzelnen Daten weiter unten speichern zu können
  $inhalt_item_ids=$datenpuffer_riot->getAllItems('tags');
    $item_ids= json_decode($inhalt_item_ids, true);
  //Speichern der einzelnen Items
  include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/itemsDaten.php');
    $datenpuffer_items =new riotapi_abfrage_items('euw');
    foreach ($item_ids['data'] as $api_id) {
        $item_id=$api_id[id];
        $inhalt =$datenpuffer_items->getItem($item_id, 'all');
    }
  //MasteryTree Datei runterladen
  $mastery_url = 'http://ddragon.leagueoflegends.com/cdn/'.$neue_patch_version[css].'/data/en_US/mastery.json';
    $mastery_path = dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/masterys/mastery_tree.json';
    $mastery_data = file_get_contents($mastery_url);
    file_put_contents($mastery_path, $mastery_data);
  //All Masteries runterladen und dann alle Masteries
  include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/all_masteries.php');
    include(dirname(__FILE__).'/themes/Avada-Child-Theme/OfflineDaten/Mastery_Id.php');
    $datenpuffer_all_masteries = new riotapi_abfrage_mastery('euw');
    $inhalt_all_masteries = $datenpuffer_all_masteries->getAllMasteries('all');
    $all_masteries=json_decode($inhalt_all_masteries, true);
    $datenpuffer_masteries_id = new riotapi_mastery('euw');
    foreach ($all_masteries['data'] as $mastery) {
        $mastery_id = $mastery[id];
        $inhalt = $datenpuffer_masteries_id->getMastery($mastery_id, 'all');
    }
}//ende Else
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
