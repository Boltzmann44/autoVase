<?php
    /*Collegamento al database*/
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "autovaseDatabase";
    
    @$mysqli= new mysqli($hostname, $username, $password, $database);   
    
    /*Query che ottiene gli ultimi 10 valori di temperatura*/
    $result1 = $mysqli->query("SELECT temperature FROM autovase ORDER BY id DESC LIMIT 10");

    while ($row = $result1->fetch_assoc()) {
        $temps[] = $row['temperature'];
    }
    $temps_json = json_encode(array_reverse($temps));
    
    /*Gestione della configurazione mediante un file csv*/
    $filename = 'config.csv';
    $tempMax=0;
    $lightMax=0;
    $humMax=0;

    loadCsv($filename,$tempMax,$lightMax,$humMax);

    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);

    if (isset($data['temp'])){
        $tempMax = $data['temp'];
        printCsv($filename,$tempMax,$lightMax,$humMax);
    }
    if (isset($data['light'])){
        $lightMax = $data['light'];
        printCsv($filename,$tempMax,$lightMax,$humMax);
    }
    if (isset($data['hum'])){
        $humMax = $data['hum'];
        printCsv($filename,$tempMax,$lightMax,$humMax);
    }

    function printCsv($filename,$tempMax,$lightMax,$humMax){
        $handler = fopen($filename,'w');
        fputcsv($handler,array($tempMax,$lightMax,$humMax));
        fclose($handler);
    }
    function loadCsv($filename,&$tempMax,&$lightMax,&$humMax){
        $handler = fopen($filename,'r');
        if($handler){
                $data = fgetcsv($handler);
                $tempMax = $data[0];
                $lightMax = $data[1];
                $humMax = $data[2];
        }
    }

    /*Raccolta valori da ESP32*/
    if(isset($_POST['temperature']) && isset($_POST['light']) && isset($_POST['water'])){
        $t = $_POST['temperature'];
        $l = $_POST['light'];
        $w = $_POST['water'];
        $h = $_POST["humidity"];
        
        //$sql = ("INSERT INTO autovase (temperature,light,water) VALUES (".$t.",".$l.",".$w.")");
        $sql = $mysqli->query("INSERT INTO autovase (temperature) VALUES (".$t.")");
    }
?>