# lolchampion
###**Note**: some comments in the files are in german. However in the header of each file i put a short description in English
Hello this is the file collection for my first webproject -  [lolchampion](http://www.lolchampion.de).

Its a wordpress site with a theme called Avada

The site is intentionally not responsive because of economic reasons (more adsense income). Although after the google-mobile-update there will be a mobile version in Q3 2016  - i'm already working on it).

Even though i used a theme for the site, i did a lot of changes and build a few **page templates** (see champion_, counter_, and guide_template_page) on my own.

I also wrote a few **jQuery-scripts** like a content-filter (overview-page-filter.js), a voting system with ip barrier(counter-vote.js) and a search form with the **typehead library** from twitter (search-form.js).

Furthermore i wrote some **ajax-scripts** for the counter and guide pages to update the content so the user does not have to reloads the site.  

**The latest files are the update_image_patch.php and the update_patchfiles.php. I wrote these files to automate a few functions like updating the pictures after a new patch from riot, crop and resize pictures and updating the champion data. A cronjob starts the files once a week.**

It was a great project and i learnt quite a lot in. I hope you like it. Feedback is always appreciated.

Cheers

Daniel






