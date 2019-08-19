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
require_once("utility.php");


// PRINTS AN HTML COMPLIANT RESPONSE
// TO REPLACE STRINGS WITH CUSTOM CONTENT WRAP THE KEY
// IN DOUBLE BRACKETS, LIKE THIS:
// {{title}}
// REPLACED BY:  array("title" => "PROTO PHP");
class TemplateEngine
{
    private $viewsDir, $staticDir, $templatesDir;
    private $headerFile, $footerFile;

    function __construct ()
    {
        // Main header file
        $this->headerFile = VIEWS."templates/header.html";
        // Main footer file
        $this->footerFile = VIEWS."templates/footer.html";
    }


    // RENDER THE TOPMOST SECTION OF THE HTML DOCUMENT, WILL APPEND
    // THE HEADER DATA SPECIFIED IN CONFIGURE.PHP -> configureHeaderStaticContent()
    public function renderHeader($headerData = NULL)
    {
        if (!ob_get_status()) // IF ob hasn't been started
            ob_start();

        // If the argument is valid and not empty, append both default
        // static array and the provided arguments to a single data
        // library
        $data = configureHeaderStaticContent();
        if (isset($headerData) && is_array($headerData) && !empty($headerData))
            $data = array_merge($headerData, $data);

        echo(replaceVariables($this->headerFile, $data));

    }

    // RENDER THE CLOSING HTML TAG
    public function renderFooter($footerData = NULL)
    {
        $data = configureFooterStaticContent();
        if (isset($footerData) && is_array($footerData) && !empty($footerData))
            $data = array_merge($footerData, $data);

        echo (replaceVariables($this->footerFile, $data));
        ob_flush();
    }

    // RENDER A CUSTOM PAGE
    public function renderPage($file, $pageData = NULL)
    {
        $file = VIEWS . $file;
        echo(replaceVariables($file, $pageData));
    }
}
