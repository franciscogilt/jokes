<?php
$host = 'localhost';
$dbname   = 'ijdb';
$user = 'ijdbuser';
$pass = 'ijdbpassword';
$port = "3306";
$charset = 'utf8';
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset;port=$port";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $options);
