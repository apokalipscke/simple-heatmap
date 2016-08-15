CREATE TABLE `clicks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `posx` int(11) NOT NULL,
  `posy` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY (id)
)

DELIMITER $$
--
-- Prozeduren
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `filldemodata` (IN `irgendwas` INT)  BEGIN
    DECLARE irgendwas2 INT;
    SET irgendwas2 = irgendwas;
    SET irgendwas = 0;
    WHILE irgendwas <= irgendwas2 DO
        INSERT INTO clicks (posx, posy, location) VALUES (round(rand()*1898), round(rand()*2114), '/testing');
        SET irgendwas = irgendwas + 1;
    END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `clicks`
--

ALTER TABLE `clicks`
  ADD `id-screenResolution` INT NOT NULL,
  ADD `innerWidth` INT NOT NULL,
  ADD `innerHeight` INT NOT NULL

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `screenresolutions`
--

CREATE TABLE `screenresolutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (id)
)
