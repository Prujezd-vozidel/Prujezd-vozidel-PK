Sem p�ijde v�e co se t�k� backednu - tedy framework pro rest api + scripty pro stahov�n� dat (i kdy� by mo�n� mohly b�t ve zvl�tn� slo�ce).

Lumen:
-------
Vy�aduje verzi php >= 7.1.3, ale koukal jsem, �e jsou k dispozici i star� verze frameworku, kter� pracuj� s php 5.5.9.

M� to 20MB, mo�n� by �lo naj�t n�co �sporn�j��ho na m�sto, ale zase by ten framework m�l b�t rychl�...
index.php je ve slo�ce public, tak�e kdy� si obsah tohodle adres��e nakop�rujete do Apache (t�eba slo�ka aswi), k api p�isoup�te p�es: 
localhost/aswi/

Mapov�n� jednotliv�ch url je v souboru routes/web.php. Je tam p�r p��klad, tak se pod�vejte.
Tady je kdy�tak dokumentace: https://lumen.laravel.com/docs/5.6 . Je celkem stru�n� a p�ehledn�, n�s zaj�maj� hlavn� kapitoly Routing, Controllers a Database.
