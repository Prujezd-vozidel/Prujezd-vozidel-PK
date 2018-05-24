-- tento skript prida tabulku s prumerem dnu
--
-- Struktura tabulky `zaznam_prum_den`
--

CREATE TABLE IF NOT EXISTS `zaznam_prum_den` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `vozidla_pocet` int(11) NOT NULL,
  `rychlost_prumer` double NOT NULL,
  `datum` date NOT NULL,
  `smer` int(11) NOT NULL,
  `zarizeni_id` varchar(20) NOT NULL,
  `vozidlo_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `zarizeni_id` (`zarizeni_id`),
  KEY `vozidlo_id` (`vozidlo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Omezení pro tabulku `zaznam_prum_den`
--
ALTER TABLE `zaznam_prum_den`
  ADD CONSTRAINT `zaznam_prum_den_ibfk_2` FOREIGN KEY (`vozidlo_id`) REFERENCES `vozidlo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `zaznam_prum_den_ibfk_1` FOREIGN KEY (`zarizeni_id`) REFERENCES `zarizeni` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
