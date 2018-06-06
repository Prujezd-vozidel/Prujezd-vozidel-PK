<?php

require_once "cron.php";
require_once "logging.php";

echo "<h1>Zkouška cronu</h1>";
echo "<p>Spouštím script:</p>";

cron();

echo "<p>KONEC</p>";

?>