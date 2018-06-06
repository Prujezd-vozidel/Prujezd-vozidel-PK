-- tento skript nastavi ceska jmena geosouradnicim

ALTER TABLE `ulice` CHANGE COLUMN `lng` `zem_delka` double default -1;
ALTER TABLE `ulice` CHANGE COLUMN `lat` `zem_sirka` double default -1;