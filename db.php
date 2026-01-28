<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "mini_store");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>