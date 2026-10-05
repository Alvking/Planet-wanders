<?php
// C:\xampp\htdocs\Clients\planetwanders\db\db.php

$host   = '127.0.0.1';
$user   = 'root';
$pass   = ''; // your MySQL password (blank by default on XAMPP)
$dbname = 'planet_wanders_db';

// Enable mysqli error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Database connection failed: " . $e->getMessage());
}


?>