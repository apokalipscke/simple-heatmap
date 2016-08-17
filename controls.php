<?php

    if(file_exists('credentials.php'))
        include "credentials.php";

    $data = $_GET;
    //print_r($data);

    $db = mysqli_connect($host, $user, $pass, $base);
    if(!$db) {
        exit("Verbindungsfehler: ".mysqli_connect_error());
    }

    $result = mysqli_query($db, "SELECT * FROM clicksbyresolution WHERE location LIKE '".$data['loc']."'");
    $resolutions = array();
    $i = 0;
    $sumClicks = 0;
    while ($row = mysqli_fetch_array($result)) {
        $resolutions[$i]['w'] = $row['width'];
        $resolutions[$i]['h'] = $row['height'];
        $resolutions[$i]['c'] = $row['clicks'];
        $sumClicks += $row['clicks'];
        $i++;
    }

    /* TODO *******************************************************************/
    /* - get clicks per resolution                                            */
    /**************************************************************************/

?>

<script>
    window.onunload = refreshParent;
    //window.onclick = refreshParent;
    function refreshParent() {
        window.opener.document.getElementById('drawing').style.display = 'none';
    }
</script>
<link rel="stylesheet" type="text/css" href="controls.css" media="all">
<div class="controls">
    <h3>Heatmap Controls</h3>
    <p><?php echo $data['loc'] ?></p>
    <strong>Auflösungen:</strong>
    <ul>
        <?php
        foreach($resolutions as $value) {
            echo "<li>";
            echo "<p>".$value['w'] . ' x ' . $value['h'] . ': ' . $value['c']."</p>";
            echo "<div class='pbar' style='width:".$value['c']/$sumClicks*(100)."%'></div>";
            echo "</li>";
        }
        ?>
    </ul>
</div>
