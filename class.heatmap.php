<?php

class heatmap
{
    private $dbLink;

    /**
     * Constructor connects to database
     *
     * @param $host database hostname
     * @param $user databse username
     * @param $pass password for user
     * @param $base database name
     *
     * @return boolean
     */
    function __construct($host, $user, $pass, $base)
    {
        $this->dbLink = mysqli_connect($host, $user, $pass, $base);
        if(!$this->dbLink) {
            exit("Verbindungsfehler: ".mysqli_connect_error());
        }
    }

    function __destruct()
    {
        mysqli_close($this->dbLink);
    }

    /**
     * get clicks from database
     *
     * @param $location the absolute path of the documents location
     *
     * @return array
     */
    public function getClicks($location)
    {
        $l = mysqli_query($this->dbLink, "SELECT * FROM locations WHERE location LIKE '".$location."'");
        $row = mysqli_fetch_array($l);
        $result = mysqli_query($this->dbLink, "SELECT posx, posy FROM clicks WHERE location LIKE '".$row['id']."'");
        $buffer = array();
        while ($row = mysqli_fetch_array($result)) {
            unset($row[0]);
            unset($row[1]);
            $buffer[] = $row;
		}

        return json_encode($buffer);
    }

    /**
     * save click to database
     *
     * @param $data the data array
     *
     */
    public function saveData($data)
    {
        $this->saveClick($data['dim']['screenHeight'], $data['dim']['screenWidth'],
                         $data['dim']['innerHeight'], $data['dim']['innerWidth'],
                         $data['loc'], $data['pos']['x'], $data['pos']['y']);
    }

    /**
     * save click to database
     *
     * @param $screenHeight height of users screen
     * @param $screenWidth width of users screen
     * @param $innerHeight height of users browserwindow
     * @param $innerWidth width of users browserwindow
     * @param $location the absolute path of the documents location
     * @param $posx the position of the x axis of the click
     * @param $posy the position of the y axis of the click
     *
     */
    public function saveClick($screenHeight, $screenWidth, $innerHeight, $innerWidth, $location, $posx, $posy)
    {
        $screenId = $this->_getScreenId($screenHeight, $screenWidth);
        if($screenId >= 0) {
            $locationId = $this->_getLocationId($location);
            if($locationId >= 0) {
                if($result = mysqli_query($this->dbLink, "INSERT INTO clicks (`idScreenResolution`, `posx`, `posy`, `innerWidth`, `innerHeight`, `location`)
                                                VALUES ('".$screenId."','".$posx."','".$posy."', '".$innerWidth."', '".$innerHeight."','".$locationId."')")) {

                    echo "Daten gespeichert S: ".$screenId.", X: ".$posx.", Y: ".$posy.", W: ".$innerWidth.", H: ".$innerHeight.", PATH: ".$locationId.":".$location;
                } else {
                    echo "Fehler beim speichern";
                }
            } else {
                echo "Fehler beim laden/anlegen des Pfades";
            }
        } else {
            echo "Fehler beim laden/anlegen der Auflösung";
        }
    }

    /**
     * get screenId if present or save the new resolution
     *
     * @param $screenHeight height of users screen
     * @param $screenWidth width of users screen
     *
     * @return $screenId or -1 on error
     */
    private function _getScreenId($screenHeight, $screenWidth)
    {
        $resolutions = mysqli_query($this->dbLink, "SELECT * FROM screenresolutions WHERE width='".$screenWidth."' AND height='".$screenHeight."'");
        if(mysqli_num_rows($resolutions) > 0) {
            $row = mysqli_fetch_array($resolutions);
            return $row['id'];
        } else {
            if($sr = mysqli_query($this->dbLink, "INSERT INTO screenresolutions (width, height) VALUES ('".$screenWidth."', '".$screenHeight."')")) {
                return mysqli_insert_id($this->dbLink);
            } else {
                return -1;
            }
        }
    }

    /**
     * get locationId if present or save the new location
     *
     * @param $location the absolute path of the documents location
     *
     * @return $locationId or -1 on error
     */
    private function _getLocationId($location)
    {
        $l = mysqli_query($this->dbLink, "SELECT * FROM locations WHERE location LIKE '".$location."'");
        if(mysqli_num_rows($l) > 0) {
            $row = mysqli_fetch_array($l);
            return $row['id'];
        } else {
            if($ln = mysqli_query($this->dbLink, "INSERT INTO locations (location) VALUES ('".$location."')")) {
                return mysqli_insert_id($this->dbLink);
            } else {
                return -1;
            }
        }
    }
}
