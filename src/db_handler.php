<?php 

function connect_db() {
    $server = 'localhost';
    $user = 'root';
    $pass = '';
    $database = 'sebuku_db';
    $connection = new mysqli($server, $user, $pass, $database);
    return $connection;
}