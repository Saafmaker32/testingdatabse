<?php
session_start();

if (!isset($_SESSION['validated']) || !$_SESSION['validated']) {
    // Controleer of de gebruiker gevalideerd is door antibot
    header('Location: antibot.php'); // Verwijs door naar antibot.php als niet gevalideerd
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Oeps! Pagina Niet Gevonden</title>
    <style>
        /* Algemene pagina stijlen */
        body {
            background-color: #f5f5f5;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            text-align: center;
        }
        
        h1 {
            font-size: 72px;
            color: #ff6347;
            margin: 0;
        }

        p {
            font-size: 20px;
            color: #555;
        }

        .game-container {
            margin-top: 30px;
            background-color: #eee;
            width: 400px;
            height: 200px;
            border: 2px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        #dino {
            position: absolute;
            bottom: 10px;
            left: 50px;
            width: 30px;
            height: 40px;
            background-color: green;
            border-radius: 5px;
        }

        #obstacle {
            position: absolute;
            bottom: 10px;
            right: 0;
            width: 30px;
            height: 30px;
            background-color: #333;
            border-radius: 5px;
            animation: moveObstacle 1.5s linear infinite;
        }

        @keyframes moveObstacle {
            100% {
                right: 100%;
            }
        }

        /* Stijlen voor het spel */
        .game-info {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 18px;
            color: #333;
        }

        .game-info span {
            font-weight: bold;
        }

        .hidden {
            display: none;
        }

        .btn-restart {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-restart:hover {
            background-color: #45a049;
        }

        /* Extra styling voor 404 */
        .error-container {
            text-align: center;
            color: #333;
            margin-top: 50px;
        }

        .error-container h2 {
            font-size: 24px;
            color: #ff6347;
        }

        .error-container p {
            font-size: 18px;
        }
    </style>
</head>
<body>

    <div class="error-container">
        <h1>404 - Oeps! Niet Gevonden</h1>
        <p>De pagina die je zoekt is helaas niet beschikbaar...</p>
        <p>Maak je geen zorgen, je kunt een klein spelletje spelen om de tijd te doden!</p>
    </div>

    <div class="game-container">
        <div class="game-info">
            <p><span id="score">Score: 0</span></p>
        </div>
        <div id="dino"></div>
        <div id="obstacle"></div>
    </div>

    <button class="btn-restart hidden" onclick="startGame()">Probeer het opnieuw</button>

    <script>
        // Game instellingen
        const dino = document.getElementById("dino");
        const obstacle = document.getElementById("obstacle");
        const scoreDisplay = document.getElementById("score");
        const restartBtn = document.querySelector(".btn-restart");

        let jumping = false;
        let score = 0;
        let gameInterval;
        let obstacleSpeed = 1.5;

        // Start het spel
        function startGame() {
            score = 0;
            scoreDisplay.innerHTML = `Score: ${score}`;
            obstacle.style.animation = `moveObstacle ${obstacleSpeed}s linear infinite`;
            obstacle.style.right = "0";
            jumping = false;
            dino.style.bottom = "10px";
            restartBtn.classList.add("hidden");
            gameInterval = setInterval(updateGame, 10);
        }

        // Update het spel
        function updateGame() {
            if (parseInt(obstacle.style.right) >= 400) {
                score++;
                scoreDisplay.innerHTML = `Score: ${score}`;
                obstacle.style.right = "0";
            }

            // Check of de dino en het obstakel in botsing komen
            if (parseInt(obstacle.style.right) >= 50 && parseInt(obstacle.style.right) <= 80 && !jumping) {
                gameOver();
            }
        }

        // Als de gebruiker springt
        document.addEventListener("keydown", (e) => {
            if (e.code === "Space" && !jumping) {
                jumping = true;
                dino.style.bottom = "80px"; // Spring omhoog
                setTimeout(() => {
                    dino.style.bottom = "10px"; // Val weer naar beneden
                    jumping = false;
                }, 300);
            }
        });

        // Game over functie
        function gameOver() {
            clearInterval(gameInterval);
            obstacle.style.animation = "none";
            restartBtn.classList.remove("hidden");
        }

        startGame(); // Start het spel zodra de pagina is geladen
    </script>

</body>
</html>
