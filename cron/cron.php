<?php

require_once "dao/dao.php";
require_once "db/db-web.php";
require_once "parser.php";
require_once "process_traffic_matrix.php";

function cron() {
    $dbh = new DB_WEB();
    $DAO = new DAO();
    $DAO->setDB($dbh);
    
    $date = new DateTime();
    $date->modify("-1 day");
    
    if ($DAO->controlTrafficData($date->format("Y-m-d"))) {
        $parser = new Parser();
        $parser->doWork($date->format("Ymd"));
        
        $traffic = $parser->getTraffic();
        $trafficOneDay = $parser->getTrafficOneDay();
        $locations = $parser->getLocations();
        
        $DAO->insertVehicles(); // Pokud nejsou typy vozidel v tabulce - pridat.
        
        // Pridat udaje o novych zarizenich.
        foreach ($locations as $l) {
            $DAO->insertLocationData($l);
        }
        
        // Pridat zaznamy z vybraneho dne.
        $insertRTT = array();
        $insertRT = array();
        $insertOneDay = array();
        process_traffic_matrix($parser, $traffic, $trafficOneDay, $DAO->findFirstId("zaznam_cas"), $DAO->findFirstId("zaznam"), $DAO->findFirstId("zaznam_prum_den"), $insertRTT, $insertRT, $insertOneDay, $date->format("Y-m-d"));
        $DAO->insertTrafficData($insertRTT, $insertRT, $insertOneDay);
    }
}

?>