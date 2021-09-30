<?php
// ___  ____ ____ _  _ ____ ___     ___  _  _ ___
// |__] |  | |    |_/  |___  |      |__] |__| |__]
// |    |__| |___ | \_ |___  |  ___ |    |  | |
// -----------------------------------------------
// ─┐ ┬┌─┐┌┐┌┌─┐┌┐ ┬ ┬┌┬┐┌─┐ ─┐ ┬┬ ┬┌─┐
// ┌┴┬┘├┤ ││││ │├┴┐└┬┘ │ ├┤  ┌┴┬┘└┬┘┌─┘
// ┴ └─└─┘┘└┘└─┘└─┘ ┴  ┴ └─┘o┴ └─ ┴ └─┘
// Author:  SENEX @ XENOBYTE.XYZ
// License: MIT License
// Website: https://xenobyte.xyz/projects/?nav=pocket_php

require_once(CONFIGURATION_FILE);

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
    public function __wakeup() {}


    public static function getDB() { return self::$db; }
}
