<?php
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('terka',plugins_url('terka-content/site.css'));wp_enqueue_script('terka',plugins_url('terka-content/site.js'),[],null,true);});
