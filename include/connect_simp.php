<?php

$hostname = 'db5000303641.hosting-data.io';
$username = 'dbu526577';
$password = '4bWm8Z/(';
$dbname = 'dbs296628';

try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    }
catch(PDOException $e)
    {
    echo $e->getMessage();
    }

?>