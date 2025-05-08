<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../db_connect.php';
$conn = getDBConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Admin Panel</title>
    <style>
        body {
            background: #121212;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 220px;
            background-color: #2e2e2e;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar .top, .sidebar .bottom {
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 22px;
        }
        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: #ccc;
            text-decoration: none;
            font-size: 18px;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #444;
            color: white;
        }
        .main {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }
        h1 {
            text-align: center;
            padding: 20px 0;
        }
        .tabs {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .tab-btn {
            background: #2e2e2e;
            border: none;
            color: white;
            padding: 15px 30px;
            cursor: pointer;
            margin: 0 10px;
            border-radius: 10px;
            font-size: 16px;
            transition: background 0.3s, color 0.3s;
        }
        .tab-btn.active {
            background: #444;
        }
        .paypal-color {
            background-color: red !important;
            color: white !important;
        }
        .venmo-color {
            background-color: blue !important;
            color: white !important;
        }
        .cashapp-color {
            background-color: green !important;
            color: white !important;
        }
        .tab-content {
            display: none;
            padding: 20px;
            text-align: center;
        }
        .tab-content.active {
            display: block;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #555;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #333;
        }
        .logout-button {
            position: fixed;
            top: 10px;
            right: 20px;
            background: #444;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="top">
        <h2>OZ Panel</h2>
        <a href="#" class="active" onclick="showTab('overview')">Voorpagina</a>
        <a href="#" onclick="showTab('paypal')">PAYPAL</a>
        <a href="#" onclick="showTab('venmo')">VENMO</a>
        <a href="#" onclick="showTab('cashapp')">CASHAPP</a>
    </div>
    <div class="bottom">
        <a href="#" onclick="clearTable('paypal')">Leeg PayPal</a>
        <a href="#" onclick="clearTable('venmo')">Leeg Venmo</a>
        <a href="#" onclick="clearTable('cashapp')">Leeg CashApp</a>
        <a href="#" onclick="clearAllData()">Verwijder alle gegevens</a>
    </div>
</div>

<a href="logout.php" class="logout-button">Logout</a>

<div class="main">
    <h1>Live Admin Panel</h1>

    <div class="tabs">
        <button class="tab-btn" onclick="showTab('overview')">Voorpagina</button>
        <button class="tab-btn" onclick="showTab('paypal')">PAYPAL</button>
        <button class="tab-btn" onclick="showTab('venmo')">VENMO</button>
        <button class="tab-btn" onclick="showTab('cashapp')">CASHAPP</button>
    </div>

    <div id="overview" class="tab-content active">
        <h2>Overzicht alle invoer</h2>
        <div id="overview-data">Laden...</div>
    </div>

    <div id="paypal" class="tab-content">
        <h2>PayPal Invoer</h2>
        <div id="paypal-data">Laden...</div>
    </div>

    <div id="venmo" class="tab-content">
        <h2>Venmo Invoer</h2>
        <div id="venmo-data">Laden...</div>
    </div>

    <div id="cashapp" class="tab-content">
        <h2>CashApp Invoer</h2>
        <div id="cashapp-data">Laden...</div>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tab).classList.add('active');
    event.target.classList.add('active');
}

function loadData() {
    fetch('fetch_data.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('paypal-data').innerHTML = data.paypal;
            document.getElementById('venmo-data').innerHTML = data.venmo;
            document.getElementById('cashapp-data').innerHTML = data.cashapp;
            document.getElementById('overview-data').innerHTML = `
                <h3>PayPal</h3>${data.paypal}
                <h3>Venmo</h3>${data.venmo}
                <h3>CashApp</h3>${data.cashapp}
            `;

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(btn => {
                btn.classList.remove('paypal-color', 'venmo-color', 'cashapp-color');
            });

            if (data.status.paypal === 'typing') {
                buttons[1].classList.add('paypal-color');
            }

            if (data.status.venmo === 'typing') {
                buttons[2].classList.add('venmo-color');
            }

            if (data.status.cashapp === 'typing') {
                buttons[3].classList.add('cashapp-color');
            }
        });
}

function clearTable(table) {
    if (confirm(`Weet je zeker dat je de gegevens van ${table} wilt verwijderen?`)) {
        fetch(`clear_data/clear_${table}.php`)
            .then(res => res.text())
            .then(msg => {
                alert(msg);
                loadData();
            });
    }
}

function clearAllData() {
    if (confirm('Weet je zeker dat je alle gegevens wilt verwijderen?')) {
        fetch('clear_data/clear_all.php')
            .then(response => response.text())
            .then(message => {
                alert(message);
                loadData();
            })
            .catch(err => console.log("Fout bij verwijderen:", err));
    }
}

setInterval(loadData, 3000);
window.onload = () => {
    showTab('overview');
    loadData();
};
</script>

</body>
</html>
