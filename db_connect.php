<?php
$servername = "localhost";  // XAMPP kjører på localhost
$username = "root";         // Standard MySQL-bruker
$password = "";             // Standard MySQL-passord (tomt i XAMPP)
$dbname = "jarodocs";       // Databasen du opprettet tidligere

// Opprett tilkobling
$conn = new mysqli($servername, $username, $password, $dbname);

// Sjekk tilkobling
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
