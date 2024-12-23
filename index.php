

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JaroDocs - Hjem</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        .header {
            background-color: #002060; /* Mørk blå */
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header img {
            max-width: 150px;
            display: block;
            margin: 0 auto 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 100px); /* Juster for headerens høyde */
            padding: 20px;
        }

        .links ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .links li {
            text-align: center;
        }

        .links a {
            text-decoration: none;
            background-color: #0047ab;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-weight: bold;
            display: inline-block;
        }

        .links a:hover {
            background-color: #002060;
        }

        .logout-button {
            margin-top: 30px;
        }

        .logout-button button {
            background-color: #d9534f; /* Rød */
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-button button:hover {
            background-color: #c9302c;
        }

        .footer {
            background-color: #eaeaea;
            text-align: center;
            padding: 10px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="assets/logo.png" alt="JaroDocs Logo">
        <h1>Velkommen til JaroDocs</h1>
    </div>
    <div class="container">
        <div class="links">
            <ul>
                <li><a href="customers.php">Administrasjon</a></li>
                <li><a href="projects.php">Prosjekter</a></li>
                <li><a href="iso9001_documents.php">ISO 9001</a></li>
            </ul>
        </div>
        <div class="logout-button">
            <form method="POST" action="logout.php">
                <button type="submit">Logg ut</button>
            </form>
        </div>
    </div>
    <div class="footer">
        &copy; 2024 JaroDocs. Alle rettigheter reservert.
    </div>
</body>
</html>



