<?php

return [
    '' => 'App\Controllers\IndexController@indexAction',
    'subpage/(:any)' => 'App\Controllers\IndexController@subPageAction',
];
