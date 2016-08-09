<?php

    $host = "localhost";    // db hostname
    $user = "user";         // db user
    $pass = "pass";         // db password
    $base = "heatmap";      // db database
    /***********************/
    $data = $_POST;

    $db = mysqli_connect($host, $user, $pass, $base);
    if(!$db) {
        exit("Verbindungsfehler: ".mysqli_connect_error());
    }

    //print_r($data);

    if(isset($data['getData'])) {
        $result = mysqli_query($db, "SELECT posx, posy FROM clicks");
        $buffer = array();
        while ($row = mysqli_fetch_array($result)) {
            $buffer[] = [$row['posx'],$row['posy']];
		}
        echo json_encode($buffer);
    } else {
        if($result = mysqli_query($db, "INSERT INTO clicks (posx, posy, location) VALUES ('".$data['pos']['x']."','".$data['pos']['y']."','".$data['loc']."')")) {
            echo "daten gespeichert('".$data['pos']['x']."','".$data['pos']['y']."','".$data['loc']."')";
        } else {
            echo "fehler beim speichern";
        }
    }
    
    mysqli_close($db);

?>
