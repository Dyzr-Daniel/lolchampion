/*
script for search-form for www.lolchampion.de. See also search_form.html, jquery.typeahead.min.css
and jquery.typeahead.min. Data from search-form-data.json
*/
jQuery('#champion-search').typeahead({
    order: "desc",
    minLength: 1,
    maxItem: 9,
    group: [true, "{{group}}"],
    maxItemPerGroup: 3,
    display: 'champion',
    template: "<img src='/_wordpress_dev716a/wp-content/bilder/champs_angebote/{{key}}.png'> <span class='search-form-text'>{{champion}} {{group}}</span> ",
    source: {
        Counter: {url:["/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/search-data.json","counter"],href:"/counter/{{key}}"},
        Guides:{ url:["/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/search-data.json","guides"],href:"/champion-guides/{{key}}-guide"},
        Champion:{ url:["/_wordpress_dev716a/wp-content/themes/Avada-Child-Theme/search-data.json","champions"],href:"/champions/{{key}}"}
    },
    callback: {
      onClickAfter: function (node, a, item, event) {
        window.location.href=item.href;
      }
    }
});
