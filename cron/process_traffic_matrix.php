<?php

function process_traffic_matrix($parser, $traffic, $idRecordTimeTable, $idRecordTable, &$insertRTT, &$insertRT, $date) {
    $times = array();
    
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
    
    foreach ($traffic as $device => $timeIntervals) {
        for ($t = 0; $t < $parser->HOW_MANY_INTERVALS; $t++) {
            if ($timeIntervals[$t] != NULL) {
                for ($d = 0; $d < 2; $d++) {
                    $dataExists = FALSE;
                    
                    for ($v = 0; $v < 11; $v++) {
                        if ($timeIntervals[$t][$d][$v][0] > 0) {
                            $dataExists = TRUE;
                            $insertRT[] = "('".$idRecordTable++."', '".$timeIntervals[$t][$d][$v][0]."', '".($timeIntervals[$t][$d][$v][1] / $timeIntervals[$t][$d][$v][0])."', '$v', '$idRecordTimeTable')";
                        }
                    }
                    
                    if ($dataExists) {
                        $insertRTT[] = "('".$idRecordTimeTable++."', '".$times[$t][0]->format("Y-m-d H:i:s.u")."', '".$times[$t][1]->format("Y-m-d H:i:s.u")."', '".($d + 1)."', '$device')";
                    }
                }
            }
        }
    }
}

?>
