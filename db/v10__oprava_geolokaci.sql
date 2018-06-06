-- Tento skript opravi geolokace u spatne zjistenych mest.

UPDATE ulice SET zem_sirka='49.3661949', zem_delka='13.1343705' WHERE nazev='Libkov';
UPDATE ulice SET zem_sirka='49.8893779', zem_delka='12.6067046' WHERE nazev='Broumov';
UPDATE ulice SET zem_sirka='49.3986612', zem_delka='12.8627931' WHERE nazev='Babylon';
