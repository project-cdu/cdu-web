<?php

class Connection
{
    private static $pdo;

    public function conect()
    {
        if (is_null(self::$pdo)) {
  
            $dbname = env('DB_DATABASE');
            $dbuser = env('DB_USERNAME');
            $dbpass = env('DB_PASSWORD');
            $host = env('DB_HOST');
            self::$pdo = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass);


    
    }

        if(is_null(self::$pdo)) throw new Exception('Database connection failed.');
        else return self::$pdo;
    }
}