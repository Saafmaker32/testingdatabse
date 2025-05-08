<?php
session_start();
include '../../antibot.php';
include '../../db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit("Toegang geweigerd.");
}

$conn = getDBConnection();

$queries = [
    "DELETE FROM paypal_flow",
    "DELETE FROM user_flow",
    "DELETE FROM cshpp_data"
];

$success = true;
foreach ($queries as $q) {
    if (!$conn->query($q)) {
        $success = false;
        echo "Fout bij verwijderen: " . $conn->error;
        break;
    }
}

if ($success) echo "Alle gegevens succesvol verwijderd.";
