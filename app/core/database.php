//  ___    ___ _______   ________   ___  ________      ________   _______  _________
// |\  \  /  /|\  ___ \ |\   ___  \|\  \|\   ___ \    |\   ___  \|\  ___ \|\___   ___\
// \ \  \/  / | \   __/|\ \  \\ \  \ \  \ \  \_|\ \   \ \  \\ \  \ \   __/\|___ \  \_|
//  \ \    / / \ \  \_|/_\ \  \\ \  \ \  \ \  \ \\ \   \ \  \\ \  \ \  \_|/__  \ \  \
//   /     \/   \ \  \_|\ \ \  \\ \  \ \  \ \  \_\\ \ __\ \  \\ \  \ \  \_|\ \  \ \  \
//  /  /\   \    \ \_______\ \__\\ \__\ \__\ \_______\\__\ \__\\ \__\ \_______\  \ \__\
// /__/ /\ __\    \|_______|\|__| \|__|\|__|\|_______\|__|\|__| \|__|\|_______|   \|__|
// |__|/ \|__|
// MIT Licensed. Use at your own risk.
// Visit xenid.net for more
//      ___         ___           ___           ___           ___                       ___         ___           ___
//     /  /\       /  /\         /  /\         /__/|         /  /\          ___        /  /\       /__/\         /  /\
//    /  /::\     /  /::\       /  /:/        |  |:|        /  /:/_        /  /\      /  /::\      \  \:\       /  /::\
//   /  /:/\:\   /  /:/\:\     /  /:/         |  |:|       /  /:/ /\      /  /:/     /  /:/\:\      \__\:\     /  /:/\:\
//  /  /:/~/:/  /  /:/  \:\   /  /:/  ___   __|  |:|      /  /:/ /:/_    /  /:/     /  /:/~/:/  ___ /  /::\   /  /:/~/:/
// /__/:/ /:/  /__/:/ \__\:\ /__/:/  /  /\ /__/\_|:|____ /__/:/ /:/ /\  /  /::\    /__/:/ /:/  /__/\  /:/\:\ /__/:/ /:/
// \  \:\/:/   \  \:\ /  /:/ \  \:\ /  /:/ \  \:\/:::::/ \  \:\/:/ /:/ /__/:/\:\   \  \:\/:/   \  \:\/:/__\/ \  \:\/:/
//  \  \::/     \  \:\  /:/   \  \:\  /:/   \  \::/~~~~   \  \::/ /:/  \__\/  \:\   \  \::/     \  \::/       \  \::/
//   \  \:\      \  \:\/:/     \  \:\/:/     \  \:\        \  \:\/:/        \  \:\   \  \:\      \  \:\        \  \:\
//    \  \:\      \  \::/       \  \::/       \  \:\        \  \::/          \__\/    \  \:\      \  \:\        \  \:\
//     \__\/       \__\/         \__\/         \__\/         \__\/                     \__\/       \__\/         \__\/
//
// Blazing fast MVC implementation for PHP7+
// Homepage: https://xenid.net/projects/?nav=pocket_php

<?php
require_once(__DIR__."/../configure.php");

// EXTREMELY SIMPLE SQLITE3 WRAPPER
class SQLiteConnection
{
    public $db = NULL;

    // OPEN UP THE SQLITE DATABASE FILE
    // BOTH THE DATABSE FILE ADN THE FOLDER IT RESIDES IN MUST
    // ALLOW THE WEB SERVER RUNNING THE SCRIPT TO BOTH READ AND
    // WRITE
    public function __construct($dbFile = CORE_SQLITE_FILE)
    {
        try
        {
            if($this->db == null)
            {
                $this->db = new PDO('sqlite:'.$dbFile,"","",array(PDO::ATTR_PERSISTENT => true));
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
}
