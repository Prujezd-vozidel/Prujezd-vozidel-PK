<?php

class Logging {
    
    // Zpravy jsou pouze informativni.
    const INFO = 0;
    // Jedna se o mene zavazne stavy - treba data pro dany den jeste neexistuji.
    const WARNING = 1;
    // Doslo k padu cele procedury - napr. nepovedlo se vytvorit slozku pro extrahovana data.
    const ERROR = 2;
    
    // Soubor, do ktereho se budou zaznamy vkladat (defaultne ve slozce log).
    private $log_file;
    
    public function __construct() {
        $this->log_file = "log/cron.txt";
    }
    
    public function log($type, $message) {
        $type_str = "";
        switch ($type) {
            case self::INFO: $type_str = "INFO"; break;
            case self::WARNING: $type_str = "WARNING"; break;
            default: $type_str = "ERROR"; break;
        }
        
        $micro_date = microtime();
        $date_array = explode(" ", $micro_date);
        $date = sprintf("%s.%03d", date("d.m.Y H:i:s", $date_array[1]), (int) ($date_array[0] * 1000));
        
        file_put_contents($this->log_file, "$date\r\n--- $type_str ---\r\n$message\r\n\r\n", FILE_APPEND);
    }
    
}

?>
