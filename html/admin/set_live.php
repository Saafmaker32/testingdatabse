<?php
session_start();

// Zorg ervoor dat de sessie-id opnieuw wordt gegenereerd om session fixation te voorkomen
if (session_status() == PHP_SESSION_ACTIVE) {
    session_regenerate_id(true); // voorkomt session fixation
}

// Controleer de HTTP methode
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Haal de platform waarde uit de POST en valideer deze
    $platform = filter_input(INPUT_POST, 'platform', FILTER_SANITIZE_STRING);

    // Controleer of het platform een geldige waarde heeft
    $validPlatforms = ['paypal', 'venmo', 'cashapp'];
    if (in_array($platform, $validPlatforms)) {
        // Zet de platform status in de sessie
        $_SESSION[$platform . '_live'] = true;
        $_SESSION[$platform . '_typing'] = true;

        // Optioneel: Reset na een tijdje
        $timeLimit = 60 * 10; // 10 minuten limiet
        $timestampKey = $platform . '_typing_timestamp';

        if (!isset($_SESSION[$timestampKey])) {
            $_SESSION[$timestampKey] = time();
        } else {
            // Check of de tijdslimiet is verstreken
            $elapsedTime = time() - $_SESSION[$timestampKey];
            if ($elapsedTime > $timeLimit) {
                // Als de tijdslimiet is verstreken, reset de status
                $_SESSION[$platform . '_live'] = false;
                $_SESSION[$platform . '_typing'] = false;
                unset($_SESSION[$timestampKey]); // Verwijder de timestamp
            }
        }
    } else {
        // Ongecontroleerde waarde, je kunt hier bijvoorbeeld een foutmelding loggen
        die('Ongevalideerde platformwaarde');
    }
}
?>
