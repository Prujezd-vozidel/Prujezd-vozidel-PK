Sem pøijde vše co se týká backednu - tedy framework pro rest api + scripty pro stahování dat (i když by možná mohly být ve zvláštní složce).

Lumen:
-------
Vyžaduje verzi php >= 5.5.9, což by mìlo být ok se školním prostøedím.

Má to 20MB, možná by šlo najít nìco úspornìjšího na místo, ale zase by ten framework mìl být rychlý...
index.php je ve složce public, takže když si obsah tohodle adresáøe nakopírujete do Apache (tøeba složka aswi), k api pøisoupíte pøes: 
localhost/aswi/

Mapování jednotlivých url je v souboru app/Http/routes.php. Je tam pár pøíklad, tak se podívejte.
Tady je kdyžtak dokumentace: https://lumen.laravel.com/docs/5.2 . Je celkem struèná a pøehledná, nás zajímají hlavnì kapitoly Routing, Controllers a Database.
