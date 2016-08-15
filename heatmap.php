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
        $result = mysqli_query($db, "SELECT posx, posy FROM clicks WHERE location LIKE '".$data['getData']."'");
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
        $sw = $data['dim']['screenWidth'];
        $sh = $data['dim']['screenHeight'];
        $resolutions = mysqli_query($db, "SELECT * FROM screenresolutions WHERE width='".$sw."' AND height='".$sh."'");
        if(mysqli_num_rows($resolutions) > 0) {
            $row = mysqli_fetch_array($resolutions);
            $screenId = $row['id'];
            echo $screenId;
        } else {
            if($sr = mysqli_query($db, "INSERT INTO screenresolutions (width, height) VALUES ('".$sw."', '".$sh."')")) {
                $screenId = mysqli_insert_id($db);
                echo "SID: ".$screenId." ";
            } else {
                echo "fehler beim anlegen der screenresolution $sw $sh ";
            }
        }
        if($result = mysqli_query($db, "INSERT INTO clicks (`id-screenResolution`, `posx`, `posy`, `innerWidth`, `innerHeight`, `location`)
                                        VALUES ('".$screenId."','".$data['pos']['x']."','".$data['pos']['y']."', '".$data['dim']['innerWidth']."', '".$data['dim']['innerHeight']."','".$data['loc']."')")) {

            echo "daten gespeichert S: ".$screenId.", X: ".$data['pos']['x'].", Y: ".$data['pos']['y'].", W: ".$data['dim']['innerWidth'].", H: ".$data['dim']['innerHeight'].", PATH: ".$data['loc'];
        } else {
            echo "fehler beim speichern";
        }

    }

    mysqli_close($db);

?>
