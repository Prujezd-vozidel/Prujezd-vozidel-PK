Dopravni data, poskytovana v ramci projektu Plzenskeho kraje.
Data jsou ze zarizeni SYDO Traffic Zeus umistenych ve mestech a obcich zarazenych do projektu.

Vice o projektu: doprava.plzensky-kraj.cz
Vice o zarizeni: www.gemos.cz

This traffic data are made available under the Open Database License: http://opendatacommons.org/licenses/odbl/1.0/.
Any rights in individual contents of the database are licensed under the Database Contents 
License: http://opendatacommons.org/licenses/dbcl/1.0/

Struktura csv souboru:
	DOPR_D_RRRRMMDD.csv
		IdDetektor|DatumCas|Intenzita|IntenzitaN|Obsazenost|Rychlost|Stav|TypVozidla|Trvani100|RychlostHistorie|TypVozidla10
		Jednoznacný identifikátor záznamu je kombinace IdDetektor a DatumCas

	Location.csv
		Name|Town|Street|IdDevice|IdArea
		Jednoznacný identifikátor záznamu je kombinace IdDevice

Parovani mezi tabulkami:
IdDevice je konstruovan jako <KP><IdZarizeni>
IdDetektoru je konstruovano jako <10><IdZarizeni><IdDetektoru>
IdDetektoru v tomto pripade je slozeno z <10><smer>

Smer:
1 - ve smeru
2 - v protismeru

IdZarizeni je vzdy 3 mistne cislo.
Vice informaci o jednotlivych sloupci lze nalezt na webu doprava.plzensky-kraj.cz