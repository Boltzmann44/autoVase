<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <title>AutoVase</title>
</head>
<body>
    <?php include 'script2.php'?>
    <div class="container">
        <div class="topbar">
            <div class="logo">
                <i class="fa fa-leaf"></i>
                <h2>AutoVase.</h2>
            </div>
            <div class="database">
                <h4>
                <?php
                    if ($mysqli->connect_error) {
                        die("Connessione fallita");
                    }
                    else{
                        echo "Database connesso";
                    }
                ?>
                </h4>
            </div>
        </div>
        <div class="sidebar">
            <div class="dashboard-button">
                <a href="index.php">
                    <i class="fa fa-chart-line"></i>
                    <div>Dashboard</div>
                </a>
            </div>
        </div>
        <div class="main">
            <div class="cards">
                <button class="card" id="temp-card">
                        <div class="card-content">
                            <div class="number" id="temp-num">
                                <?php 
                                    $result =  $mysqli->query("SELECT temperature FROM autovase ORDER BY id DESC LIMIT 1");
                                    $row = mysqli_fetch_assoc($result);
                                    $temperature = $row["temperature"];
                                    echo $temperature . '°';
                                ?>
                            </div>
                            <h3 class="card-name">Temperatura</h3>
                        </div>
                        <div class="icon-box">
                            <i class="fa fa-temperature-three-quarters"></i>
                        </div>
                </button>
                <button class="card" id="light-card">
                    <div class="card-content">
                        <div class="number" id="light-num">
                        <?php 
                            $result = $mysqli->query("SELECT light FROM autovase ORDER BY id DESC LIMIT 1");
                            $row = mysqli_fetch_assoc($result);
                            $light = $row["light"];
                            echo $light . '%';
                        ?>
                        </div>
                        <h3 class="card-name">Luce</h3>
                    </div>
                    <div class="icon-box">
                        <i class="fa fa-sun"></i>
                    </div>     
                </button>
                <div class="card" id="water-card">
                    <div class="card-content">
                        <div class="number">
                            <?php 
                                $result =  $mysqli->query("SELECT water_level FROM autovase ORDER BY id DESC LIMIT 1");
                                $row = mysqli_fetch_assoc($result);
                                $w_level = $row["water_level"];
                                if($w_level<= 40){
                                    echo 'Basso';
                                }
                                else if($w_level <= 70 && $w_level>40){
                                    echo 'Medio';
                                }
                                else{
                                    echo 'Alto';
                                }
                            ?>
                        </div>
                        <h4 class="card-name">Livello acqua</h4>
                    </div>
                    <div class="icon-box">
                        <i class="fa fa-glass-water"></i>
                    </div>
                </div>
                <button class="card" id="hum-card">
                    <div class="card-content">
                        <div class="number">
                        <?php 
                            $result =  $mysqli->query("SELECT humidity FROM autovase ORDER BY id DESC LIMIT 1");
                            $row = mysqli_fetch_assoc($result);
                            $hum = $row["humidity"];
                            echo $hum . '%';
                        ?>
                        </div>
                        <h3 class="card-name">Umidità terreno</h3>
                    </div>
                    <div class="icon-box">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                </button>
            </div>
            <div class="popup-container">
                    <div class="popup-box" id="popup-temp">
                        <h2 class="popup-name">Temperatura:</h2>
                        <p class="popup-p">Indica la temperatura critica per la pianta.</p>
                        <input type="text" id="input-temp" name="tempname" value="">
                        <button class="sendButton" id="btn-temp">Invia</button>
                        <h4 class="popup-level">Livello critico: <?php echo $tempMax?>°</h4>
                    </div>
                    <div class="popup-box" id="popup-light">
                        <h2 class="popup-name">Luminosità:</h2>
                        <p class="popup-p">Indica la luminosità critica per la pianta.</p>
                        <input type="text" id="input-light" name="lightname" value="" required>
                        <button class="sendButton" id="btn-light">Invia</button>
                        <h4 class="popup-level">Livello critico: <?php echo $lightMax?>%</h4>
                    </div>
                    <div class="popup-box" id="popup-water"></div>
                    <div class="popup-box" id="popup-hum">
                        <h2 class="popup-name">Umidità:</h2>
                        <p class="popup-p">Indica il range di umidità adatto</p>
                        <select name="Livelli" id="input-hum">
                            <option value="basso">Basso</option>
                            <option value="medio">Medio</option>
                            <option value="alto">Alto</option>
                        </select>
                        <button class="sendButton" id="btn-hum">Invia</button>
                        <h4 style="margin-top: 5px;">Range attuale:</h4>
                            <?php 
                                switch($humMax){
                                    case 1:
                                        echo ' Basso (0-30%)';
                                        break;
                                    case 2:
                                        echo ' Medio (30%-70%)';
                                        break;
                                    case 3:
                                        echo ' Alto (70%-100%)';
                                        break;
                                }
                            ?>
                    </div>
            </div>
            <div class="charts">
                <div class="chart">
                    <h2>Temperatura:</h2>
                    <canvas id="tempChart"></canvas>
                </div>
                <div class="chart">
                    <h2>Livello acqua:</h2>
                    <canvas id="waterChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <script>
        /*passaggi di variabili da php a js*/
        var temps = <?php echo $temps_json; ?>;
        var temp = <?php echo $temperature; ?>;
        var light = <?php echo $light; ?>;
        var waterLevel = <?php echo $w_level; ?>;
        var hum = <?php echo $hum; ?>;     

        var tempMax = <?php echo $tempMax; ?>;
        var lightMax = <?php echo $lightMax; ?>;
        var humMax = <?php echo $humMax; ?>;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="grafico1.js"></script>
    <script src="grafico2.js"></script>
    <script src="script1.js"></script>
</body>
</html>
