<?php
$host = "sql123.infinityfree.com"; 
$username = "if0_42557691";
$password = "kztWQ0xO6GMXir8";
$dbname = "if0_42557691_blog";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>