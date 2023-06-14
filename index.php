<?php

use App\Connection\Connection;

require_once __DIR__ . '/vendor/autoload.php';


$con = Connection::getInstance('./app/Connection/config.ini');

var_dump($con);
