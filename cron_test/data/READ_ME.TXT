// Obsah souboru "test_Ymd_doprava.csv"
idDetektor - z tohoto udaje "10<ID_ZARIZENI>10<SMER>" bereme oznaceni zarizeni (detektoru) a smer, ve kterem snima (1 = ve smeru, 2 = v proti smeru).
DatumCas - datum a cas zaznamu.
Intenzita - NEPOTREBNE, NAHRAZENO PISMENEM "X".
IntenzitaN - NEPOTREBNE, NAHRAZENO PISMENEM "X".
Obsazenost - NEPOTREBNE, NAHRAZENO PISMENEM "X".
Rychlost - namerena rychlost (musi se brat v potaz i zaznamy s rychlosti 0.0 = rychlost nesla urcit).
Stav - jestli je zarizeni funkcni (=1) nebo nefunkcni (<0). V aktualni verzi se s timto udajem nepracuje (pokud nam zarizeni da rozumny udaj, je nam jedno, jestli je funkcni nebo ne - do budoucna by chtelo opravit udaj v DB).
TypVozidla - NEPOTREBNE, NAHRAZENO PISMENEM "X".
Trvani100 - NEPOTREBNE, NAHRAZENO PISMENEM "X".
RychlostHistorie - NEPOTREBNE, NAHRAZENO PISMENEM "X".
TypVozidla10 - konkretni typ vozidla (s obecnym oznacenim, 4 skupiny, nepracujeme).



// Obsah souboru "test_Ymd_lokace.csv"
Name - popis ulice (napr. z jakeho smeru zarizeni snima).
Town - nazev mesta.
Street - nazev ulice.
IdDevice - KP<ID_ZARIZENI>, bereme oznaceni detektoru.
idArea - NEPOTREBNE, NAHRAZENO PISMENEM "X".



Jednotlive sloupce v obou souborech jsou oddeleny svislitkem.