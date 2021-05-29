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
    public function renderHeader($headerData = NULL) : void
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
    public function renderFooter($footerData = NULL) : void
    {
        $data = configureFooterStaticContent();
        if (isset($footerData) && is_array($footerData) && !empty($footerData))
            $data = array_merge($footerData, $data);

        echo (replaceVariables($this->footerFile, $data));
        ob_flush();
    }

    // RENDER A CUSTOM PAGE
    public function renderPage($file, $pageData = NULL) : void
    {
        $file = VIEWS . $file;
        echo(replaceVariables($file, $pageData));
    }
}
