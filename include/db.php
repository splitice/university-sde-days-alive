<?php
//Establish connection
$connection = parse_url($_ENV["DATABASE_URL"]);
$pgcon = pg_connect('user='.$connection['user'].' password='.$connection['pass'].' host='.$connection['host'].' dbname='.substr($connection['path'],1));
if(!$pgcon){
    die('An internal error occurred.');
}
unset($connection);