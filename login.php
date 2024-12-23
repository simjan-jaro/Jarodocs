<?php
require 'db_connect.php'; // Koble til databasen
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validering (forenklet)
    if (empty($username) || empty($password)) {
        echo "Alle felt må fylles ut!";
        exit;
    }

    // Hent bruker fra databasen
    $stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $password_hash);

    if ($stmt->fetch()) {
        // Verifiser passord
        if (password_verify($password, $password_hash)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit;
        } else {
            echo "Feil passord.";
        }
    } else {
        echo "Brukernavn ikke funnet.";
    }
    $stmt->close();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logg inn</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 30px 25px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        .login-container img {
            max-width: 120px;
            margin-bottom: 15px;
        }

        .login-container h1 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container p {
            margin-top: 15px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="login-container">
    <!-- Logo -->
    <img src="assets/logo.png" alt="JaroDocs Logo" style="width: 2000px; height: auto; margin-bottom: 20px;">
    <h1>Logg inn</h1>
    <form method="POST" action="login.php">
        <label for="username">Brukernavn:</label>
        <input type="text" id="username" name="username" placeholder="Skriv inn brukernavn" required>

        <label for="password">Passord:</label>
        <input type="password" id="password" name="password" placeholder="Skriv inn passord" required>

        <button type="submit">Logg inn</button>
    </form>
    <p>© 2024 JaroDocs</p>
</div>

</body>
</html>



