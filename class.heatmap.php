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
        $this->dbLink = new mysqli($host, $user, $pass, $base);

        // Fehler beim verbinden
        if($this->dbLink->connect_errno) {
            exit("Verbindungsfehler: ".$this->dbLink->connect_error);
        }
    }

    function __destruct()
    {
        // Datenbankverbindung trennen
        $this->dbLink->close();
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
        $sql = "SELECT * FROM locations
                WHERE location LIKE '" . $location . "'";

        $l = $this->dbLink->query($sql);
        $row = $l->fetch_assoc();

        $sql = "SELECT posx, posy FROM clicks
                WHERE location LIKE '" . $row['id'] . "'";

        $result = $this->dbLink->query($sql);
        $buffer = array();
        while ($row = $result->fetch_assoc()) {
            unset($row[0]);
            unset($row[1]);
            $buffer[] = $row;
		}
        $l->free();
        $result->free();
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
        $this->saveClick(   $data['dim']['screenHeight'], $data['dim']['screenWidth'],
                            $data['dim']['innerHeight'], $data['dim']['innerWidth'],
                            $data['loc'], $data['pos']['x'], $data['pos']['y']  );
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
    public function saveClick(  $screenHeight, $screenWidth,
                                $innerHeight, $innerWidth,
                                $location, $posx, $posy )
    {
        $screenId = $this->_getScreenId($screenHeight, $screenWidth);
        if($screenId >= 0) {
            // Auflösung gefunden/gespeichert

            $locationId = $this->_getLocationId($location);
            if($locationId >= 0) {
                // Dateipfad gefunden/gespeichert

                $sql = "INSERT INTO clicks (`idScreenResolution`, `posx`, `posy`, `innerWidth`, `innerHeight`, `location`)
                        VALUES ('".$screenId."','".$posx."','".$posy."', '".$innerWidth."', '".$innerHeight."','".$locationId."')";

                if($result = $this->dbLink->query($sql)) {
                    // Clickdaten erfolgreich gespeichert

                    echo "Daten gespeichert S: ".$screenId.", X: ".$posx.", Y: ".$posy.", W: ".$innerWidth.", H: ".$innerHeight.", PATH: ".$locationId.":".$location;
                } else {
                    // Fehler beim speichern der Clickdaten

                    echo "Fehler beim speichern";
                }
            } else {
                // Fehler beim laden/anlegen des Pfades

                echo "Fehler beim laden/anlegen des Pfades";
            }
        } else {
            // Fehler beim laden/anlegen der Auflösung

            echo "Fehler beim laden/anlegen der Auflösung";
        }
    }

    /**
     * get clicks by resolution for svg overlay
     *
     * @param $location the absolute path of the documents location
     *
     * @return $resolutions clicksbyresolution
     *
     */
    public function getClicksByResolution($location)
    {
        $sql = "SELECT * FROM clicksbyresolution
                WHERE location LIKE '" . $location . "'";

        $result = $this->dbLink->query($sql);
        $resolutions = array();
        $i = 0;
        $sumClicks = 0;
        while ($row = $result->fetch_assoc()) {
            $resolutions[$i]['w'] = $row['width'];
            $resolutions[$i]['h'] = $row['height'];
            $resolutions[$i]['c'] = $row['clicks'];
            $sumClicks += $row['clicks'];
            $i++;
        }
        $result->free();

        for($i = 0; $i < count($resolutions); $i++) {
            $resolutions[$i]['p'] = $resolutions[$i]['c']/$sumClicks*(100);
        }

        return $resolutions;
    }

    /**
     * get locations
     *
     * @return $locations all locations
     */
    public function getLocations()
    {
        $sql = "SELECT * FROM locations";

        $result = $this->dbLink->query($sql);
        $locations = array();
        while($row = $result->fetch_assoc()) {
            $locations[$row['id']] = array(
                'path' => $row['location']
            );
        }
        $result->free();

        return $locations;
    }

    /**
     * get location by locationId
     *
     * @param $id locationId
     *
     * @return $location for the id
     */
    public function getLocationById($id)
    {
        $sql = "SELECT * FROM locations
                WHERE id = '" . $id . "'";

        $result = $this->dbLink->query($sql);
        $row = $result->fetch_assoc();
        $result->free();

        return $row['location'];
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
        $sql = "SELECT * FROM screenresolutions
                WHERE width='" . $screenWidth . "' AND height='" . $screenHeight . "'";

        $resolutions = $this->dbLink->query($sql);
        if($resolutions->num_rows > 0) {
            // Auflösung gefunden, id holen

            $row = $resolutions->fetch_assoc();
            $screenId = $row['id'];
        } else {
            // Auflösung nicht vorhanden, speichern, id holen, bei Fehler -1

            $sql = "INSERT INTO screenresolutions (width, height)
                    VALUES ('" . $screenWidth . "', '" . $screenHeight . "')";

            if($sr = $this->dbLink->query($sql)) {
                $screenId = $this->dbLink->insert_id;
            } else {
                $screenId = -1;
            }
        }
        $resolutions->free();

        return $screenId;
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
        $sql = "SELECT * FROM locations
                WHERE location LIKE '" . $location . "'";

        $l = $this->dbLink->query($sql);
        if($l->num_rows > 0) {
            // Dateipfad gefunden, id holen

            $row = $l->fetch_assoc();
            $locationId = $row['id'];
        } else {
            // Dateipfad noch nicht vorhanden, speichern, id holen, bei Fehler -1

            $sql = "INSERT INTO locations (location)
                    VALUES ('" . $location . "')";

            if($ln = $this->dbLink->query($sql)) {
                $locationId = $this->dbLink->insert_id;
            } else {
                $locationId = -1;
            }
        }
        $l->free();

        return $locationId;
    }
}
