/*This is the script for the counter page on www.lolchampion.de
Its purpose is to update the "counter-value" after a user voted for a champion.
For Details see template_counter_page.php, update_counter_strong.php and update_counter_weak.php
*/

function UpdateValue(champion, id, vorzeichen, tbl) {
  $.ajax({
    type: "POST",
    url: "http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/ajax.php",
    data: {
      champname: champion,
      champ_id: id,
      vz: vorzeichen,
      tabelle: tbl
    },
    dataType: 'json',
    success: function(results) {
      var x = "counter-value" + results.b;
      $('.' + x).html(results.a);
      if (typeof results.c != 'undefined') {
        alert(results.c);
      }
    }
  }); // Ajax Call
}

function UpdateValue2(champion, id, vorzeichen, tbl) {
  $.ajax({
    type: "POST",
    url: "http://www.lolchampion.de/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/ajax2.php",
    data: {
      champname: champion,
      champ_id: id,
      vz: vorzeichen,
      tabelle: tbl
    },
    dataType: 'json',
    success: function(results2) {
        var x = "counter-valueZwei" + results2.b;
        $('.' + x).html(results2.a);
        if (typeof results2.c != 'undefined') {
          alert(results2.c);
        }
      }
      //dataType:"html",
  }); // Ajax Call
}
