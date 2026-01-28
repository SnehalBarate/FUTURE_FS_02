<?php
session_start();
session_unset(); // Saare variables clear karein
session_destroy(); // Session khatam karein

// Browser cache ko clear karne ke liye headers
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

header("Location: login.php");
exit();
?>