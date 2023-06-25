<?php
$servername = "localhost";
$username = "root";
$password = "Nut!19102535";
$dbname = "norapawsdb";

// $servername = "localhost";
// $username = "id20932709_nutchyleo";
// $password = "Nut!19102535";
// $dbname = "id20932709_norapawsdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>