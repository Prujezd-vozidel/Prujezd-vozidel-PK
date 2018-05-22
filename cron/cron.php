<?php

require_once "dao/dao.php";
require_once "db/db-web.php";
require_once "parser.php";
require_once "process_traffic_matrix.php";

function year_cron() {
    // Stazeni dat za posledni rok.
    for ($i = 365; $i > 0; $i--) {
        $date = new DateTime();
        $date->modify("-$i day");
        cron_procedure($date);
    }
}

function cron() {
    // Stazeni dat z minuleho dne.
    $date = new DateTime();
    $date->modify("-1 day");
    cron_procedure($date);
}

// Funkce, ktera je volana bud cron() nebo year_cron() a ktera stahne data pro dany den.
function cron_procedure($date) {
    // Kvuli timeoutu.
    set_time_limit(0);
    
    // Vytvoreni objektu pro komunikaci s DB.
    $dbh = new DB_WEB();
    $DAO = new DAO();
    $DAO->setDB($dbh);
    
    // Objekt pro logovani.
    $logs = new Logging();
    
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