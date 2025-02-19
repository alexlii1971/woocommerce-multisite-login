<?php
add_action('set_logged_in_cookie', function($logged_in_cookie) {
    $_COOKIE[LOGGED_IN_COOKIE] = $logged_in_cookie;
});

add_filter('auth_cookie', function($cookie, $user_id, $expiration) {
    $cookie['path'] = '/';
    $cookie['domain'] = COOKIE_DOMAIN;
    return $cookie;
}, 10, 3);