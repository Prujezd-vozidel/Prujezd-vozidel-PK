-- skript prida sloupecky pro geosouradnice do tabulky ulice
alter table `prujezd_vozidel`.`ulice` add column `lng` double not null;
alter table `prujezd_vozidel`.`ulice` add column `lat` double not null;