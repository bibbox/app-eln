<?php

/**
 * IARC L.Alteyrac 20170125
 * Disable the WP texturize process (autocorrect as double dash for example)
*/

remove_filter('the_content', 'wptexturize');

?>