<?php
function getDBConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "my_base";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        // Log fout op server, toon geen gevoelige info aan gebruiker
        error_log("Database connection failed: " . $conn->connect_error);
        http_response_code(500);
        exit("Databasefout. Probeer het later opnieuw.");
    }

    return $conn;
}
?>
