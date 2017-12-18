<?php

include('DatabaseConnection.php');

if($argc < 2){
    echo ' No arguments passed'.PHP_EOL; die;
}
// short arguments p: is -p | long arguments ::drivers is --drivers="" | : = required :: = optional
$config = getopt('p:u:s:d:', ['driver::','filename::']);

$supported_drivers = ['pgsql', 'mysql'];

$server_name = $config['s'];
$username = $config['u'];
$password = $config['p'];
$database = $config['d'];
$charset = 'utf8mb4';

$filename = !empty($config['filename'])? $config['filename']: 'db.md';

$driver = !empty($config['driver'])? $config['driver']:'mysql'; // set driver to mysql if empty
if(!in_array($driver, $supported_drivers)){
    echo 'Unknown Driver '. $driver. PHP_EOL;
    echo 'Supported Drivers include: ';
    foreach ($supported_drivers as $supported_driver){
        echo $supported_driver.' ';
    }
    echo PHP_EOL;die;
}

$connection_args = compact('password', 'username', 'server_name', 'database');


$connection_types = [
    'pgsql' => new PGsqlConnection($connection_args),
    'mysql' => new MYsqlConnection($connection_args),
];

$connection = $connection_types[$driver];
$res = $connection->connect();

$connection->query();
$connection->writeToFile($filename);

