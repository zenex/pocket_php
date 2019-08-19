//           _______  _        _______  _______  _______  _______  _______     _        _______ _________
// |\     /|(  ____ \( (    /|(  ____ \(  ____ )(  ___  )(  ____ \(  ____ \   ( (    /|(  ____ \\__   __/
// ( \   / )| (    \/|  \  ( || (    \/| (    )|| (   ) || (    \/| (    \/   |  \  ( || (    \/   ) (
//  \ (_) / | (__    |   \ | || (_____ | (____)|| (___) || |      | (__       |   \ | || (__       | |
//   ) _ (  |  __)   | (\ \) |(_____  )|  _____)|  ___  || |      |  __)      | (\ \) ||  __)      | |
//  / ( ) \ | (      | | \   |      ) || (      | (   ) || |      | (         | | \   || (         | |
// ( /   \ )| (____/\| )  \  |/\____) || )      | )   ( || (____/\| (____/\ _ | )  \  || (____/\   | |
// |/     \|(_______/|/    )_)\_______)|/       |/     \|(_______/(_______/(_)|/    )_)(_______/   )_(
// Author: AlexHG @ xenspace.net
// License: MIT. Use at your own risk.
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
// Homepage: https://xenspace.net/projects/?nav=pocket_php



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
