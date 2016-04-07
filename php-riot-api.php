<?php
/*This is the Riot Api i used to download the content form riot servers.
its based on the PHP Riot API from Kevin Ohashi (http://kevinohashi.com) http://github.com/kevinohashi/php-riot-api
Although i did quite a few changes over the year.
*/

if(!class_exists('riotapi')){
class riotapi {
const API_URL_1   = 'https://global.api.pvp.net/api/lol/static-data/{region}/v1.2/';
const API_URL_1_1 = 'http://euw.api.pvp.net/api/lol/{region}/v1.1/';
const API_URL_1_2 = 'https://euw.api.pvp.net/api/lol/{region}/v1.2/';
const API_URL_1_3 = 'https://euw.api.pvp.net/api/lol/{region}/v1.3/';
const API_URL_2_2 = 'https://euw.api.pvp.net/api/lol/{region}/v2.2/';
const API_URL_2_3 = 'https://euw.api.pvp.net/api/lol/{region}/v2.3/';
const API_KEY = 'API_KEY';
const RATE_LIMIT_MINUTES = 500;
const RATE_LIMIT_SECONDS = 10;
const CACHE_LIFETIME_MINUTES = 60;
const CACHE_ENABLED = true;
private $REGION;
public function __construct($region)
{
$this->REGION = $region;
}
public function getRotation(){
$call = 'champion?freeToPlay=true&';
//add API URL to the call
$call = self::API_URL_1_2 . $call;
return $this->request($call);
}
public function getPatch(){
$call = 'realm?';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getChampionIDs() {
$call = 'champion?';
//add API URL to the call
$call = self::API_URL_1_2 . $call;
return $this->request($call);
}
public function getSummoner($id,$option=null){
$call = 'summoner/';// . $id .'?';
switch ($option) {
case '':
$call .= $id.'?';
break;
case 'masteries':
$call .= $id.'/masteries?';
break;
case 'runes':
$call .= $id.'/runes?';
break;
case 'name':
$call .= $id.'/name?';
break;
default:
//do nothing
break;
}
//add API URL to the call
$call = self::API_URL_1_3 . $call;
return $this->request($call);
}
public function getSummonerByName($name){
//sanitize name a bit - this will break weird characters
$name = preg_replace("/[^a-zA-Z0-9 ]+/", "", $name);
$call = 'summoner/by-name/' . $name .'?';
//add API URL to the call
$call = self::API_URL_1_3 . $call;
return $this->request($call);
}
public function getTeam($id){
$call = 'team/by-summoner/' . $id .'?';
//add API URL to the call
$call = self::API_URL_2_2 . $call;
return $this->request($call);
}
public function getAllChampionInfos($option){
$call = 'champion?locale=de_DE&champData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getChampionInfos($id,$option){
$call = 'champion/' .$id .'?locale=de_DE&champData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}

public function getAllItems($option){
$call = 'item?locale=de_DE&itemListData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}

public function getItem($id,$option){
$call = 'item/' .$id .'?locale=de_DE&itemData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getAllMasteries($option){
$call = 'mastery?locale=de_DE&masteryListData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getMastery($id,$option){
$call = 'mastery/' .$id .'?locale=de_DE&masteryListData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getAllRunes($option){
$call = 'rune?locale=de_DE&runeListData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
public function getRune($id,$option){
$call = 'rune/' .$id.'?locale=de_DE&runeListData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}

public function getAllSummonorSpells($option){
$call = 'summoner-spell?locale=de_DE&spellData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}

public function getSummonorSpell($id,$option){
$call = 'summoner-spell/' .$id.'?locale=de_DE&spellData='.$option .'&';
//add API URL to the call
$call = self::API_URL_1 . $call;
return $this->request($call);
}
private function request($call){
//format the full URL
$url = $this->format_url($call);
//caching
if(self::CACHE_ENABLED){
$cacheFile = dirname(__FILE__).'/cache/' . md5($url);

if (file_exists($cacheFile)) {
$fh = fopen($cacheFile, 'r');
$cacheTime = trim(fgets($fh));
// if data was cached recently, return cached data
if ($cacheTime > strtotime('-'. self::CACHE_LIFETIME_MINUTES . ' minutes')) {
  return fread($fh,filesize($cacheFile));
}
// else delete cache file
fclose($fh);
unlink($cacheFile);
}
}
//call the API and return the result
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
if(self::CACHE_ENABLED){
  //create cache file
  $fh = fopen($cacheFile, 'w');
  fwrite($fh, time() . "\n");
  fwrite($fh, $result);
  fclose($fh);
}
return $result;
}
//creates a full URL you can query on the API
private function format_url($call){
return str_replace('{region}', $this->REGION, $call) . 'api_key=' . self::API_KEY;
}
}
}


?>
