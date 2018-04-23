-- prejmenovani tabulky vozidla
rename table `prujezd_vozidel`.`vozidla` to `prujezd_vozidel`.`vozidlo`;

-- update ciziho klice
alter table `prujezd_vozidel`.`zaznam` change column `vozidla_id` `vozidlo_id` bigint(20);