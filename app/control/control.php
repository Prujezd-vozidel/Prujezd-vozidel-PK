<?php

    require_once "../model/dao/dao.php";
    require_once "../model/db/db-web.php";
    require_once "../model/session.php";
    
    $dbh = new DB_WEB();
    $session = new Session();

    include "../view/header.php";
    include "../view/menu.php";
    
    if (!isSet($_GET["view"])) {
    
        include "../view/intro.php";
        
    } else {
        
        include "../view/intro.php";
        
    }

    include "../view/footer.php";

?>
