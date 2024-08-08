<?php
$conn = new mysqli('localhost', 'root', '', 'help');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
