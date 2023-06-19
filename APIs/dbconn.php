<?php
// Allow requests from any origin
header("Access-Control-Allow-Origin: *");

$servername = "localhost";
$username = "root";
$password = "Nut!19102535";
$dbname = "norapawsdb";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>