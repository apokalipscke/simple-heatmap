<?php

if(file_exists('credentials.php')) include "credentials.php";
require_once 'class.heatmap.php';

$data = $_POST;
$hm = new heatmap($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['base']);

if(isset($data['getData'])) {
    echo $hm->getClicks($data['getData']);
} else {
    $hm->saveData($data);
}
