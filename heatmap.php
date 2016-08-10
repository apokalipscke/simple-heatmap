<?php
    if(file_exists('credentials.php'))
        include "credentials.php";

    /************************************************** fill with your data ***/
    //$host = "localhost";    // db hostname
    //$user = "user";         // db user
    //$pass = "pass";         // db password
    //$base = "heatmap";      // db database
    /*************************/
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
            unset($row[0]);
            unset($row[1]);
            //print_r($row);
            $buffer[] = $row;
		}
        //print_r($buffer[0]);
        echo json_encode($buffer);
    } else {
        if($result = mysqli_query($db, "INSERT INTO clicks (posx, posy, location) VALUES ('".$data['pos']['x']."','".$data['pos']['y']."','".$data['loc']."')")) {
            echo "daten gespeichert X: ".$data['pos']['x'].", Y: ".$data['pos']['y'].", PATH: ".$data['loc']."')";
        } else {
            echo "fehler beim speichern";
        }
    }

    mysqli_close($db);

?>
