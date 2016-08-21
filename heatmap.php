<?php
    if(file_exists('credentials.php')) include "credentials.php";
    require_once 'class.heatmap.php';

    $data = $_POST;
    $hm = new heatmap($host, $user, $pass, $base);

    if(isset($data['getData'])) {
        echo $hm->getClicks($data['getData']);
    } else {
        $hm->saveData($data);
    }
