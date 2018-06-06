<?php

require_once "dao/dao.php";
require_once "db/db-web.php";
require_once "parser.php";
require_once "process_traffic_matrix.php";

function cron() {
    // Nacteni dat z testovacich souboru.
    cron_procedure(new DateTime("20180605"));
}

// Funkce, ktera je volana bud cron() nebo year_cron() a ktera stahne data pro dany den.
function cron_procedure($date) {
    // Kvuli timeoutu.
    set_time_limit(0);
    
    // Objekt pro logovani.
    $logs = new Logging();
    
    // Vytvoreni objektu pro komunikaci s DB.
    $dbh = new DB_WEB($logs);
    $DAO = new DAO();
    $DAO->setDB($dbh);
    
    if ($date != NULL && $DAO->controlTrafficData($date->format("Y-m-d"))) {
        $logs->log(Logging::INFO, "Data k vybranemu dni (".$date->format("d.m.Y").") jeste v databazi nejsou.");
        $parser = new Parser($logs);
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
        $insertDate = array();
        $insertRTT = array();
        $insertRT = array();
        $insertOneDay = array();
        process_traffic_matrix($parser, $traffic, $trafficOneDay, $DAO->findFirstId("datum"), $DAO->findFirstId("zaznam_cas"), $DAO->findFirstId("zaznam"), $DAO->findFirstId("zaznam_prum_den"), $insertDate, $insertRTT, $insertRT, $insertOneDay, $date->format("Y-m-d"));
        $DAO->insertTrafficData($insertDate, $insertRTT, $insertRT, $insertOneDay);
    } else if ($date != NULL) {
        // Data pro vybrany den uz v databazi jsou.
        $logs->log(Logging::WARNING, "Pro vybrany den (".$date->format("d.m.Y").") jiz data v databazi jsou.");
    }
}

?>