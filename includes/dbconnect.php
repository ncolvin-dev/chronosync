<?php

$host = "3.142.254.132";
$port = "5432";
$dbname = "chronosync";
$user = "sara";
$password = "mockup-dispense-popcorn";

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    die("Database connection failed.");
} else {
    echo "Connected to PostgreSQL!";
}
?>