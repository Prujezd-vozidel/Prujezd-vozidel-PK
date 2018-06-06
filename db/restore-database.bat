SET DB_USR=sem-si-dejte-vaseho-db-uzivatele
SET DB_PW=sem-si-dejte-vase-heslo-do-db
SET DB_NAME=sem-si-dejte-jmeno-vasi-databaze

mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < drop-all-tables.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v01__base_schema.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v02__data_10_04_2018.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v03__vozidlo_rename.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v04__geosouradnice.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v05__geosouradnice_update.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v06__zaznam_prum_den.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v07__data_20_05_2018.sql
mysql -u %DB_USR% --password=%DB_PW% %DB_NAME% < v08__geosouradnice_cesky.sql