-- skript prida sloupecky pro geosouradnice do tabulky ulice
alter table `ulice` add column `lng` double default 0;
alter table `ulice` add column `lat` double default 0;