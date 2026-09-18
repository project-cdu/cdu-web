<?php

class Connection
{
    private static $pdo;

    public function conect()
    {
        if (is_null(self::$pdo)) {
  
            $dbname = 'postgres'; 
            $dbuser = '';
            $dbpass = '';
            $host = 'localhost';
            self::$pdo = new PDO("pgsql:dbname=$dbname;host=$host", $dbuser, $dbpass);


    
    }

        if(is_null(self::$pdo)) throw new Exception('Database connection failed.');
        else return self::$pdo;
    }
}