<?php

function process_traffic_matrix($parser, $traffic, $trafficOneDay, $idDateTable, $idRecordTimeTable, $idRecordTable, $idRecordOneDayTable, &$insertDate, &$insertRTT, &$insertRT, &$insertOneDay, $date) {
    $times = array();
    
    // Rekonstrukce casovych intervalu.
    for ($i = 0; $i < $parser->HOW_MANY_INTERVALS; $i++) {
        $times[$i] = array();
        $times[$i][0] = new DateTime($date);
        $times[$i][1] = new DateTime($date);
        
        $fromSec = (int) (($i * $parser->intervalMilli) / 1000);
        $toSec = (int) (($i + 1) * $parser->intervalMilli / 1000);
        
        $fromHours = (int) ($fromSec / 3600);
        $fromMinutes = (int) (($fromSec - $fromHours * 3600) / 60);
        $fromSeconds = (int) ($fromSec - $fromHours * 3600 - $fromMinutes * 60);
        
        $toHours = (int) ($toSec / 3600);
        $toMinutes = (int) (($toSec - $toHours * 3600) / 60);
        $toSeconds = (int) ($toSec - $toHours * 3600 - $toMinutes * 60);
        
        $times[$i][0]->setTime($fromHours, $fromMinutes, $fromSeconds);
        $times[$i][1]->setTime($toHours, $toMinutes, $toSeconds);
    }
    
    // Priprava dat pro naplneni tabulek zaznam a zaznam_cas.
    foreach ($traffic as $device => $timeIntervals) {
        for ($t = 0; $t < $parser->HOW_MANY_INTERVALS; $t++) {
            if ($timeIntervals[$t] != NULL) {
                for ($d = 0; $d < 2; $d++) {
                    $dataExists = FALSE;
                    
                    for ($v = 0; $v < 11; $v++) {
                        $count_ = $timeIntervals[$t][$d][$v][0] + $timeIntervals[$t][$d][$v][2];
                        if ($count_ > 0) {
                            $dataExists = TRUE;
                            $speed_ = -1.0;
                            
                            if ($timeIntervals[$t][$d][$v][0] > 0) {
                                $speed_ = $timeIntervals[$t][$d][$v][1] / (double) $timeIntervals[$t][$d][$v][0];
                            }
                            
                            $insertRT[] = "('".$idRecordTable++."', '$count_', '$speed_', '$v', '$idRecordTimeTable')";
                        }
                    }
                    
                    if ($dataExists) {
                        $insertRTT[] = "('".$idRecordTimeTable++."', '".($d + 1)."', '$device', '".($idDateTable + $t)."')";
                    }
                }
            }
        }
    }
    
    // Priprava dat pro naplneni tabulky datum. Budou vlozeny veskere casove intervaly. Je nemozne, aby neexistovalo alespon jedno zarizeni,
    // ktere v dany casovy interval detekuje alespon jeden dopravni prostredek. I kdyby takova situace nastala, dane intervaly take mohou
    // slouzit pro pouziti ve statistikach.
    for ($i = 0; $i < $parser->HOW_MANY_INTERVALS; $i++) {
        $insertDate[] = "('".$idDateTable++."', '".$times[$i][0]->format("Y-m-d H:i:s")."', '".$times[$i][1]->format("Y-m-d H:i:s")."')";
    }
    
    // Priprava dat pro naplneni tabulky zaznam_prum_den.
    foreach ($trafficOneDay as $device => $direction) {
        for ($d = 0; $d < 2; $d++) {
            for ($v = 0; $v < 11; $v++) {
                $count_ = $direction[$d][$v][0] + $direction[$d][$v][2];
                if ($count_ > 0) {
                    $speed_ = -1.0;
                    if ($direction[$d][$v][0] > 0) {
                        $speed_ = $direction[$d][$v][1] / (double) $direction[$d][$v][0];
                    }
                    $insertOneDay[] = "('".$idRecordOneDayTable++."', '$count_', '$speed_', '".($d + 1)."', '$device', '$v', '$idDateTable')";
                }
            }
        }
    }
    
    // Priprava posledniho zaznamu ke vlozeni do tabulky datum (cely den - vzdy bude alespon jeden udaj).
    $timeFrom = new DateTime($date);
    $timeFrom->setTime(0, 0, 0);
    $timeTo = new DateTime($date);
    $timeTo->setTime(0, 0, 0);
    $timeTo->modify("+1 day");
    $insertDate[] = "('$idDateTable', '".$timeFrom->format("Y-m-d H:i:s")."', '".$timeTo->format("Y-m-d H:i:s")."')";
    
}

?>
