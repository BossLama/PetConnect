<?php

namespace includes;
class ErrorLogger
{
    private static $log_file = "./storage/error.log";

    public static function log($message)
    {

        if(!file_exists(self::$log_file))
        {
            $file = fopen(self::$log_file, "w");
            fclose($file);
        }

        $date = date("Y-m-d H:i:s");
        $log_message = "[" . $date . "] " . $message . "\n";
        file_put_contents(self::$log_file, $log_message, FILE_APPEND);
    }
}

?>