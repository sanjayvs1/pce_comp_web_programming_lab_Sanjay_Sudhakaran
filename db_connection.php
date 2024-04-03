<?php
$servername = "localhost";
// $username = "if0_36291810";
// $password = "6m608uwYh5qIv2";
$username = "root";
$password = "";
$database = "circlefit";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
