<?php
// Includen van antibot script
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basis antibot check
    if (empty($_POST['antibot_check']) || $_POST['antibot_check'] !== 'valid') {
        die('Bot activiteit gedetecteerd.');
    }
    
    // Sessies starten en vernietigen
    session_start();
    $_SESSION = []; // Vernietig alle sessiegegevens
    session_destroy(); // Vernietig de sessie
    header("Location: login.php"); // Redirect naar de login pagina na uitloggen
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uitloggen</title>
    <style>
        /* style.css in het bestand zelf */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .logout-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        button, .btn-cancel {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-logout {
            background-color: #d9534f;
            color: white;
            font-size: 16px;
        }

        .btn-cancel {
            background-color: #5bc0de;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .btn-cancel:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-box">
            <h1>Uitloggen</h1>
            <p>Weet je zeker dat je wilt uitloggen?</p>
            <form action="logout.php" method="POST">
                <input type="hidden" name="antibot_check" value="valid">
                <button type="submit" class="btn-logout">Ja, uitloggen</button>
                <a href="index.php" class="btn-cancel">Annuleren</a>
            </form>
        </div>
    </div>
</body>
</html>
