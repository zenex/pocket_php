<?php
// ███╗   ██╗███████╗ ██████╗ ██╗  ██╗███████╗██╗  ██╗   ██╗  ██╗██╗   ██╗███████╗
// ████╗  ██║██╔════╝██╔═══██╗██║  ██║██╔════╝╚██╗██╔╝   ╚██╗██╔╝╚██╗ ██╔╝╚══███╔╝
// ██╔██╗ ██║█████╗  ██║   ██║███████║█████╗   ╚███╔╝     ╚███╔╝  ╚████╔╝   ███╔╝
// ██║╚██╗██║██╔══╝  ██║   ██║██╔══██║██╔══╝   ██╔██╗     ██╔██╗   ╚██╔╝   ███╔╝
// ██║ ╚████║███████╗╚██████╔╝██║  ██║███████╗██╔╝ ██╗██╗██╔╝ ██╗   ██║   ███████╗
// ╚═╝  ╚═══╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═╝   ╚═╝   ╚══════╝
// Author:  AlexHG @ NEOHEX.XYZ
// License: MIT License
// Website: https://neohex.xyz

require_once(__DIR__."/../configure.php");

// EXTREMELY SIMPLE SQLITE3 WRAPPER
class SQLiteConnection
{
    protected static $db = NULL;

    // OPEN UP THE SQLITE DATABASE FILE
    // BOTH THE DATABSE FILE ADN THE FOLDER IT RESIDES IN MUST
    // ALLOW THE WEB SERVER RUNNING THE SCRIPT TO BOTH READ AND
    // WRITE
    public function __construct($dbFile = CORE_SQLITE_FILE)
    {
        try
        {
            if(self::$db == null)
            {
                self::$db = new PDO('sqlite:'.$dbFile,"","",array(PDO::ATTR_PERSISTENT => true));
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }

    private function __clone() {}
    private function __wakeup() {}


    public static function getDB() { return self::$db; }
}

class PostgreSQLConnection
{

    protected static $db = NULL;

    public function __construct()
    {
        $str = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
                       POSTGRESQL_HOST,
                       POSTGRESQL_PORT,
                       POSTGRESQL_DB,
                       POSTGRESQL_USER,
                       POSTGRESQL_PWD);


    }

}
