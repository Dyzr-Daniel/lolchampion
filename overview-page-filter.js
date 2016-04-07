/*Script for overview-page-filter.html where you can filter the champions by name or attribute*/
jQuery(document).ready(function() {
  /*Compares the input data from the input element with the champion names and shows only matching results*/
  jQuery('#box-champ').keyup(function() {
    jQuery("#checkbox-champion :checkbox:checked").removeAttr("checked");
    var valThis = jQuery(this).val().toLowerCase();
    if (valThis == "") {
      jQuery('.champions> li').show();
    } else {
      jQuery('.champions> li').each(function() {
        var text = jQuery(this).text().toLowerCase();
        (text.indexOf(valThis) >= 0) ? jQuery(this).show(): jQuery(this).hide();
      });
    };
  });

  /*Compares the input data from the checkboxes with the champion names and shows only matching results the data-champtype*/
  jQuery('input:checkbox').change(function() {
    jQuery('#box-champ').val("");
    $checked = jQuery('input:checked');
    if ($checked.length) {
      var selector = '';
      jQuery($checked).each(function(index, element) {
        selector += "[data-champtype~='" + element.id + "']";
      });
      jQuery('.champions > li').hide();
      jQuery('.champions > li').filter(selector).show();
    } else {
      jQuery('.champions > li').show();
    }
  });
});

/*Resets attributes*/
jQuery('input:button').click(function() {
  jQuery("#checkbox-champion :checkbox:checked").removeAttr("checked");
  jQuery('#box-champ').val("");
  jQuery('.champions > li').show();
});
