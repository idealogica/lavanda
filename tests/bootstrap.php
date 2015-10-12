<?php

include($_ENV['AUTOLOAD_PATH']);

$connection = new PDO(
    env('DB_DRIVER').":host=".env('DB_HOST'),
    env('DB_USERNAME'),
    env('DB_PASSWORD'));
$connection->query("DROP DATABASE IF EXISTS ".env('DB_DATABASE'));
$connection->query("CREATE DATABASE ".env('DB_DATABASE'));
