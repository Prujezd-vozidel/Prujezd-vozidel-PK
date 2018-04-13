<?php

    require_once "../model/dao/dao.php";
    require_once "../model/db/db-web.php";
    require_once "../model/parser.php";
    require_once "../model/session.php";
    
    $dbh = new DB_WEB();
    $session = new Session();
    
    $traffic = NULL;
    $locations = NULL;
    if (isSet($_POST["date"])) {
        $parser = new Parser();
        $parser->doWork(date_format(new DateTime($_POST["date"]), "Ymd"));
        $traffic = $parser->getTraffic();
        $locations = $parser->getLocations();
    }
    
    include "../view/header.php";
    include "../view/menu.php";
    
    if (!isSet($_GET["view"])) {
        include "../view/intro.php";
    } else {
        include "../view/intro.php"; // TODO
    }

    include "../view/footer.php";

?>
