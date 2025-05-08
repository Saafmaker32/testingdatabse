<?php
// Start de sessie alleen als deze nog niet actief is
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Voeg je eigen IP-adres toe aan de whitelist om toegang te krijgen tot het adminpanel.
 */
function isWhitelistedIP() {
    $whitelist = [
        '2001:1c01:3bc4:1600:b505:949d:df51:8435',
        '84.107.191.216',
        '::1',
        '127.0.0.1',  // Voeg je eigen IP-adres hier toe
    ];

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, $whitelist);
}

/**
 * Detecteer bekende bots via de User-Agent.
 */
function isKnownBot() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $bots = [
        'bot', 'crawl', 'spider', 'slurp', 'fetch', 'wget', 'curl', 
        'python', 'scrapy', 'googlebot', 'bingbot', 'yandexbot', 'facebook', 'baidu'
    ];

    foreach ($bots as $bot) {
        if (strpos($userAgent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Detecteer bots op basis van te snelle verzoeken (rate limit).
 * Stel een minimum interval in tussen aanvragen om brute-force aanvallen te blokkeren.
 */
function isRateLimitBot($minInterval = 1) {
    if (!isset($_SESSION['last_request'])) {
        $_SESSION['last_request'] = time();
        return false;
    }

    $elapsed = time() - $_SESSION['last_request'];
    $_SESSION['last_request'] = time();

    return $elapsed < $minInterval;
}

/**
 * (Optioneel) Detecteer gebruikers zonder JavaScript (via ?nojs=1 in de URL).
 */
function isNoJSBot() {
    return isset($_GET['nojs']) && $_GET['nojs'] === '1';
}

/**
 * (Optioneel) IP blacklist � voeg handmatig verdachte IP's toe voor blokkeringsdoeleinden.
 */
function isBlacklistedIP() {
    $blacklist = [
        '192.168.0.1',    // voorbeeld IP
        '127.0.0.2'       // voeg eigen verdachte IP's toe
    ];

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, $blacklist);
}

/**
 * Controleer of de bezoeker een bot is door verschillende technieken toe te passen.
 */
function isBot() {
    // Als je IP op de whitelist staat, beschouw je het als geen bot
    if (isWhitelistedIP()) {
        return false;
    }

    // Combineer verschillende botdetectiemethoden
    if (isKnownBot()) {
        return true; // Bekende bot gedetecteerd via User-Agent
    }

    if (isRateLimitBot()) {
        return true; // Botgedrag gedetecteerd op basis van snelheid van verzoeken
    }

    if (isNoJSBot()) {
        return true; // Gebruiker zonder JavaScript, mogelijk een bot
    }

    if (isBlacklistedIP()) {
        return true; // Het IP van de gebruiker staat op de blacklist
    }

    return false; // Geen bot gedetecteerd
}

/**
 * Als een bot wordt gedetecteerd, blokkeer de toegang en stop het script.
 */
function blockBot() {
    if (isBot()) {
        // Optioneel: log de verdachte activiteit voor later onderzoek
        error_log("Bot gedetecteerd vanaf IP: " . $_SERVER['REMOTE_ADDR'] . " User-Agent: " . $_SERVER['HTTP_USER_AGENT']);

        // Hier kan een gebruikersvriendelijke foutmelding of redirect plaatsvinden
        die("Toegang geweigerd: Verdachte activiteit gedetecteerd.");
    }
}

// Roep de blockBot functie aan om bots te blokkeren
blockBot();
?>
