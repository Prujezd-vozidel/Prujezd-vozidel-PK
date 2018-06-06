-- prejmenovani tabulky vozidla
rename table `vozidla` to `vozidlo`;

-- update ciziho klice
alter table `zaznam` change column `vozidla_id` `vozidlo_id` bigint(20);