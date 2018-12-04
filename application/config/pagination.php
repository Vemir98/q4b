<?php defined('SYSPATH') OR die('No direct script access.');

return array(
    // Application defaults
    'default' => array(
        // source: "query_string" or "route"
        'current_page'      => array('source' => 'route', 'key'    => 'page'),
        'total_items'       => 0,
        'items_per_page'    => 10,
        'view'              => 'pagination/limited',
        'auto_hide'         => TRUE,
        'first_page_in_url' => FALSE,
        //if use limited template
        'max_left_pages'    => 5,
        'max_right_pages'   => 5,
    ),
);
