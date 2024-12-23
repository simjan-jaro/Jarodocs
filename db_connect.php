<?php
// Hent databaseinformasjon fra miljøvariabler
$host = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');
$database = getenv('DB_NAME');

// Opprett en ny databaseforbindelse
$conn = new mysqli($host, $username, $password, $database);

// Sjekk om forbindelsen feilet
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

