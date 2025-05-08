<?php
session_start();
include '../../antibot.php';
include '../../db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    exit("Toegang geweigerd.");
}

$conn = getDBConnection();

if ($conn->query("DELETE FROM paypal_flow")) {
    echo "PayPal gegevens succesvol verwijderd.";
} else {
    echo "Fout bij verwijderen: " . $conn->error;
}
