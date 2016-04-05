<?php
/*This file is for the Counter-Voting on www.lolchampion.de/Counter
See also template_counter_page.php and counter-vote.js
counter-vote.js forwards the data via POST request
This function updates the database values in the table counter_schwach_gegen depending on the users vote.
After a vote the users ip is blocked for a short time.
If the user trys to vote again, he gets a notifcation.
*/
//Deklaration Variablen
$inhalt = file_get_contents(dirname(__FILE__).'/OfflineDaten/Champtags/Champtags.json');
$champion_name= json_decode($inhalt, true);
//Prüfung, ob übergebene Daten auch echt sind, Abgleich mit Tabelle aller Champion Namen
foreach ($champion_name['data'] as $x) {
    if ($x[key] == $_POST["champname"]) {
        $champname=$_POST["champname"];
    }
		if ($x[id] == $_POST["champ_id"]) {
				$counter_id=$_POST["champ_id"];
}
}
if ($_POST["vz"]=="plus" || $_POST["vz"]=="minus") {
    $vorzeichen=$_POST["vz"];
}
if ($_POST["tabelle"]=="counter_schwach_gegen") {
    $updatetabelle=$_POST["tabelle"];
}
$ipspeicherzeit = 3600;
$vote =0;
$_db_datenbank = "DATENBANK";
$_db_url = "URL";
$_db_usr =    "USER";
$_db_paswd = "PASSWORD";
//Mit Datenbank verbinden
$mysqli = new mysqli($_db_url, $_db_usr, $_db_paswd, $_db_datenbank);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//Ip-Adresse ermitteln
if (! isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['REMOTE_ADDR'];
} else {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
//Aktuelle Zeit ermitteln und Zeit ermitteln, wann wieder abgestimmt werden darf
$zeit = time();
$nichtmehrgueltig = $zeit-$ipspeicherzeit;
//Alle abgelaufenen IPs löschen (dürfen wieder abstimmen)
$stmt = $mysqli->prepare("DELETE FROM dg437tzU_Counter_Vote2 WHERE Time<= ?");
$stmt->bind_param("i", $nichtmehrgueltig);
$stmt->execute();
//Schauen ob IP schon abgestimmt hat
$stmt=$mysqli->prepare("SELECT IP FROM dg437tzU_Counter_Vote2 WHERE IP=?");
$stmt->bind_param("s", $ip);
$stmt->execute();
$res=$stmt->get_result();
$db_ip = $res->fetch_assoc();
$stmt->close();
//Falls nein, IP,Champion,Zeit und Counter speichern und Abstimmen ausführen
if ($db_ip[IP]=="") {
    $stmt = $mysqli->prepare("INSERT INTO dg437tzU_Counter_Vote2 (Champion,IP,$updatetabelle,Time) VALUES(?,?,?,?) ");
    $counter=$counter_id.",";
    $stmt->bind_param("sssi", $champname, $ip, $counter, $zeit);
    $stmt->execute();
    $stmt->close();
    $stmt = $mysqli->prepare("SELECT $champname FROM $updatetabelle WHERE ChampionID=?");
    $stmt->bind_param("s", $counter_id);
    $stmt->execute();
    $res=$stmt->get_result();
    $oldValue = $res->fetch_assoc();
    $stmt->close();
    if ($vorzeichen=="plus") {
        $newValue = $oldValue[$champname] +1;
    } else {
        $newValue = $oldValue[$champname] -1;
    }
    $stmt = $mysqli->prepare("UPDATE $updatetabelle SET $champname=$newValue WHERE ChampionID=?");
    $stmt->bind_param("s", $counter_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(array("a" => "$newValue", "b" => "$counter_id"));
}
//Falls IP bereits vorhanden, Prüfen ob für Champion bereits abgestimmt wurde
//Falls neuer Champion, Datenbank updaten;
else {
    $stmt=$mysqli->prepare("SELECT Champion FROM dg437tzU_Counter_Vote2 WHERE Champion=? AND IP=? ");
    $stmt->bind_param(ss, $champname, $ip);
    $stmt->execute();
    $res=$stmt->get_result();
    $db_champ = $res->fetch_assoc();
    $stmt->close();
    if ($db_champ[Champion]=="") {
        $stmt = $mysqli->prepare("INSERT INTO dg437tzU_Counter_Vote2 (Champion,IP,$updatetabelle,Time) VALUES(?,?,?,?) ");
        $counter=$counter_id.",";
        $stmt->bind_param("sssi", $champname, $ip, $counter, $zeit);
        $stmt->execute();
        $stmt->close();
        $stmt = $mysqli->prepare("SELECT $champname FROM $updatetabelle WHERE ChampionID=?");
        $stmt->bind_param("s", $counter_id);
        $stmt->execute();
        $res=$stmt->get_result();
        $oldValue = $res->fetch_assoc();
        $stmt->close();
				//Prüfen, ob Plus oder Minus gedrückt wurde
        if ($vorzeichen=="plus") {
            $newValue = $oldValue[$champname] +1;
        } else {
            $newValue = $oldValue[$champname] -1;
        }
        $stmt = $mysqli->prepare("UPDATE $updatetabelle SET $champname=$newValue WHERE ChampionID=?");
        $stmt->bind_param("s", $counter_id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(array("a" => "$newValue", "b" => "$counter_id"));
    } else {
        //Champ bereits vorhanden ->Prüfen, ob für den Counter Champion (Schwach/Stark) bereits abgestimmt wurde
        $stmt=$mysqli->prepare("SELECT $updatetabelle FROM dg437tzU_Counter_Vote2 WHERE Champion=? AND IP=? ");
        $stmt->bind_param(ss, $champname, $ip);
        $stmt->execute();
        $res=$stmt->get_result();
        $db_stark = $res->fetch_assoc();
        $stmt->close();
        $array= explode(",", $db_stark[$updatetabelle]);
        for ($i=0; $i < count($array); $i++) {
            if ($array[$i]==$counter_id) {
                $vote=1;
                break;
            }
        }
        //Wurde für den Champion abgestimmt, Meldung ausgeben
        if ($vote==1) {
            echo json_encode(array("c" => "Du hast bereits für diesen Champion abgestimmt! Du kannst morgen wieder für diesen Champion abstimmen!"));
            '';
        } else {
            $stmt=$mysqli->prepare("UPDATE dg437tzU_Counter_Vote2 SET $updatetabelle=CONCAT($updatetabelle,$counter_id,\",\") WHERE Champion=? AND IP=? ");
            $stmt->bind_param(ss, $champname, $ip);
            $stmt->execute();
            $stmt->close();
            $stmt = $mysqli->prepare("SELECT $champname FROM $updatetabelle WHERE ChampionID=?");
            $stmt->bind_param("s", $counter_id);
            $stmt->execute();
            $res=$stmt->get_result();
            $oldValue = $res->fetch_assoc();
            $stmt->close();
            if ($vorzeichen=="plus") {
                $newValue = $oldValue[$champname] +1;
            } else {
                $newValue = $oldValue[$champname] -1;
            }
            $stmt = $mysqli->prepare("UPDATE $updatetabelle SET $champname=$newValue WHERE ChampionID=?");
            $stmt->bind_param("s", $counter_id);
            $stmt->execute();
            $stmt->close();
            echo json_encode(array("a" => "$newValue", "b" => "$counter_id"));
        }
    }
}

?>
