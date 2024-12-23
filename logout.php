<?php
session_start();
session_destroy(); // Ødelegger sesjonen
header("Location: login.php"); // Send brukeren tilbake til innlogging
exit;
?>
