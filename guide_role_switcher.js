/*This is the ajax script for the champion guide pages on www.lolchampion.de/champion-guides
Its purpose is to load the new content after the role witch button was presssed
For Details see guide_template_page.php and guide_role_switcher.php
*/
function UpdateRole(champion, role) {
  $.ajax({
    type: "POST",
    url: "http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/guide_role_switcher.php",
    data: {
      champname: champion,
      champ_role: role
    },
    dataType: 'json',
    success: function(results) {
        console.log(results);
        $.each(results, function(gruppe, array) {
          if (gruppe == "startitem") {
            var text = "";
            for (i = 0; i < 6; i++) {
              if (typeof array[i] != "undefined") {
                var StartItemName = array[i][1];
                var StartItemName = StartItemName.replace("&auml;", "ä").replace("&ouml;", "ö").replace("&uuml;", "ü").replace("&szlig;", "ß");
                var img = "/_wordpress_dev716a/wp-content/bilder/item/" + array[i][0] + ".png"
                text = text + "<img src=\"" + img + " \"/>";
              }
              if (i == 5) {
                $('.start-items-bereich').html(text);
              }
            }
          }
          /*Load new summonerspells*/
          if (gruppe == "summonerspells") {
            var img1 = "/_wordpress_dev716a/wp-content/bilder/summoner-spells/" + array[0] + ".jpg"
            var img2 = "/_wordpress_dev716a/wp-content/bilder/summoner-spells/" + array[1] + ".jpg"
            $('.summoner-spell1').attr('src', img1).attr('title', array[0]).attr('alt', array[0]);;
            $('.summoner-spell2').attr('src', img2).attr('title', array[1]).attr('alt', array[1]);;
          }
          /*Load new itembuild*/
          if (gruppe == "itembuild") {
            for (i = 0; i < 6; i++) {
              if (typeof array[i] != "undefined") {
                var BuildItemName = array[i][1];
                var BuildItemName = BuildItemName.replace("&auml;", "ä").replace("&ouml;", "ö").replace("&uuml;", "ü").replace("&szlig;", "ß");
                var img = "/_wordpress_dev716a/wp-content/bilder/item/" + array[i][0] + ".png"
                $('.guide-item' + (i + 1)).attr('src', img).attr('title', BuildItemName).attr('alt', BuildItemName);
              }else {
                $('.guide-item' + (i + 1)).remove();
              }
            }
          }
          /*Load new spellorder*/
          if (gruppe == "spellorder") {
            $.each(array, function(spellnr, spellarray) { //sepllarray[spalte][0=klasse, 1=Wert]
              for (i = 1; i < 19; i++) {
                $('.' + spellarray[i][0]).text(spellarray[i][1]);
              }
            });
          }
          /*Load new masterys*/
          if (gruppe == "mastery") {
            $('#mastery-page-guide').html(array);
          }
          /*Load new runes*/
          if (gruppe == "runen") {
            $('.runenseiten').html(array);
          }
        });
      } //Ende success
  }); // Ende Ajax Call
}
