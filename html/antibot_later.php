<?php
// antibot.php

session_start();

/**
 * Whitelist: laat bekende IP's toe, zoals je eigen admin-IP
 */
function isWhitelistedIP() {
    $whitelist = [
        '2001:1c01:3bc4:1600:b505:949d:df51:8435',
        '84.107.191.216',
         // Jouw IP-adres
    ];

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, $whitelist);
}

/**
 * Detecteer bekende bots via de User-Agent
 */
function isKnownBot() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $bots = ['bot', 'crawl', 'spider', 'slurp', 'fetch', 'wget', 'curl', 'python', 'scrapy'];

    foreach ($bots as $bot) {
        if (strpos($userAgent, $bot) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Detecteer bots op basis van te snelle verzoeken (rate limit)
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
 * (Optioneel) Detecteer gebruikers zonder JavaScript
 */
function isNoJSBot() {
    return isset($_GET['nojs']) && $_GET['nojs'] === '1';
}

/**
 * (Optioneel) IP blacklist – voeg handmatig verdachte IP's toe
 */
function isBlacklistedIP() {
    $blacklist = [
        '192.168.0.1',     // voorbeeld
        '127.0.0.2'        // vul aan
    ];

    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, $blacklist);
}

/**
 * Hoofdfunctie: bepaal of de bezoeker een bot is
 */
function isBot() {
    if (isWhitelistedIP()) {
        return false;
    }

    return isKnownBot() || isRateLimitBot() || isNoJSBot() || isBlacklistedIP();
}

/**
 * Als een bot wordt gedetecteerd, blokkeer de toegang
 */
function blockBot() {
    if (isBot()) {
        error_log("Bot gedetecteerd vanaf IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'onbekend') . " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'onbekend'));
        die("Toegang geweigerd: Verdachte activiteit gedetecteerd.");
    }
}

// Roep blockBot aan om bots te blokkeren
blockBot();
?>
