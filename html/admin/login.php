// Include antibot-beveiliging // Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging
// Include antibot-beveiliging



<?php
session_start();

// Include antibot-beveiliging

$error = '';

// Zet een admin-inlog voor jezelf (zorg ervoor dat je deze gebruikersnaam en wachtwoord geheim houdt)
$admin_username = 'zeweetnietwatheetis';  // Je speciale admingebruikersnaam
$admin_password = 'rooiekabel';  // Je speciale wachtwoord (bijvoorbeeld voor testdoeleinden)

// Als de huidige gebruiker het admin-account probeert in te loggen, omzeil dan de botdetectie
$is_admin_login = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Haal de gebruikersinvoer op en filter/sanitiseer
    $user = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Vervang FILTER_SANITIZE_STRING door FILTER_SANITIZE_FULL_SPECIAL_CHARS
    $pass = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Vervang FILTER_SANITIZE_STRING door FILTER_SANITIZE_FULL_SPECIAL_CHARS
    $honeypot = filter_input(INPUT_POST, 'hidden_field', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Honeypot veld ophalen

    // Controleer of het honeypot-veld ingevuld is (Als het ingevuld is, is het een bot)
    if (!empty($honeypot)) {
        die('Bot gedetecteerd via Honeypot!');
    }

    // Als het admin-account is, schakelt de botdetectie uit
    if ($user === $admin_username && $pass === $admin_password) {
        // Admin login gelukt, direct door naar dashboard
        session_regenerate_id(true);
        $_SESSION['logged_in'] = true;
        header("Location: dashboard.php");  // Redirect naar dashboard.php
        exit();
    }

    // Botcheck: Stop de uitvoering als een bot wordt gedetecteerd, behalve voor admin-inlog
    if ($user !== $admin_username) {
        // Voeg debugging toe om te achterhalen waarom je botgedetecteerd wordt
        echo "<pre>";
        echo "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
        echo "IP Adres: " . $_SERVER['REMOTE_ADDR'] . "\n";
        echo "Last Request Time: " . ($_SESSION['last_request'] ?? 'Not Set') . "\n";
        echo "</pre>";
    }

    // Als de invoer leeg is, stop dan de loginpoging
    if (empty($user) || empty($pass)) {
        $error = "Alle velden moeten ingevuld zijn.";
    } else {
        // Basis gebruikerscontrole (je kunt dit uitbreiden met een databasecontrole)
        // In een productieomgeving zou je nooit een wachtwoord in platte tekst opslaan!
        $stored_username = 'rootservice';
        $stored_password_hash = password_hash('', PASSWORD_DEFAULT); // Vergeet niet wachtwoorden te hashen

        // Controleer of de gebruikersnaam en het wachtwoord correct zijn
        if ($user === $stored_username && password_verify($pass, $stored_password_hash)) {
            // Start een nieuwe sessie en regenereer de session ID om session fixation te voorkomen
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            header("Location: dashboard.php"); // Redirect naar dashboard.php
            exit();
        } else {
            $error = "Ongeldige inloggegevens.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            background: #1e1e2f;
            color: #fff;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background: #2e2e3f;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
        }
        button {
            background: #4caf50;
            color: white;
            cursor: pointer;
        }
        .error {
            color: #ff4f4f;
            margin-top: 10px;
        }
        .hidden-field {
            display: none;  /* Honeypot: verborgen veld om bots te vangen */
        }
    </style>
</head>
<body>
    <form method="POST">
        <h2>Admin Login</h2>
        
        <!-- Honeypot veld voor bots -->
        <input type="text" name="hidden_field" class="hidden-field" value="">
        
        <!-- Gebruikersinvoer -->
        <input type="text" name="username" placeholder="Gebruikersnaam" required>
        <input type="password" name="password" placeholder="Wachtwoord" required>
        
        <button type="submit">Inloggen</button>
        
        <!-- Foutmelding bij ongeldige inloggegevens -->
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
