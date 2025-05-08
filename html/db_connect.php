<?php
function getDBConnection() {
    $servername = getenv("MYSQLHOST") ?: "testingdatabse.railway.internal";
    $username = getenv("MYSQLUSER") ?: "root";
    $password = getenv("MYSQLPASSWORD") ?: "SLYTuCAeKQYXJwsRhtzsTKhhdRtkmjfY";
    $dbname = getenv("MYSQLDATABASE") ?: "railway";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>
