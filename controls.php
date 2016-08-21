<?php

if(file_exists('credentials.php')) include "credentials.php";
require_once 'class.heatmap.php';

$data = $_GET;

$hm = new heatmap($config['db']['host'], $config['db']['user'], $config['db']['pass'], $config['db']['base']);

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="controls.css" media="all">
        <script type="text/javascript" src="jquery-3.1.0.min.js"></script>
        <script type="text/javascript" src="controls.js"></script>
        <title>Heatmap Control</title>
    </head>
    <body>
        <div class="controls">
            <pre><?= $data['loc'] ?></pre>
            <select name="locations" onchange="changeLocation(this.value)">
                <!-- <option value="1" selected><?= $data['loc'] ?></option> -->
                <?php foreach($hm->getLocations() as $location) {
                    if($location['path'] == $data['loc']) { ?>
                        <option value="<?= $location['path'] ?>" selected><?= $location['path'] ?></option>
                    <?php } else { ?>
                        <option value="<?= $location['path'] ?>"><?= $location['path'] ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
            <ul>
                <?php foreach($hm->getClicksByResolution($data['loc']) as $value) { ?>
                <li onclick="changeWidth(<?= $value['w'] ?>)">
                    <p class="resolution"><?= $value['w'] ?> x <?= $value['h'] ?>:<span><?= $value['c'] ?><br/><small><?= round($value['p']) ?>%</small></p>
                    <div class="pbar" style="width:<?= $value['p'] ?>%"></div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </body>
</html>
