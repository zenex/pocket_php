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
require_once("utility.php");

// PRINTS AN HTML COMPLIANT RESPONSE
// TO REPLACE STRINGS WITH CUSTOM CONTENT WRAP THE KEY
// IN DOUBLE BRACKETS, LIKE THIS:
// {{title}}
// REPLACED BY:  array("title" => "PROTO PHP");
class TemplateEngine
{
    private $contentStack;
    private $stackCounter;

    function __construct ()
    {
        $this->contentStack = array();
        $this->stackCounter = 0;
    }

    public function addFile($filename, $data = NULL)
    {
        $fileContents = file_get_contents(VIEWS_DIR . $filename);
        if (empty($fileContents))
            return;
        else
            $this->addString($fileContents, $data);
    }

    public function addString($string, $data = NULL)
    {
        if (empty($string))
            return;

        if ($data != NULL && is_array($data) || !$empty($data))
            $this->contentStack[$this->stackCounter] = replaceValues($string, '{{', '}}', $data);
        else
            $this->contentStack[$this->stackCounter] = $string;

        $this->stackCounter++;
    }

    public function render()
    {
        if (!ob_get_status()) // IF ob hasn't been started
            ob_start();

        foreach($this->contentStack as $section)
            echo($section);

        ob_flush();
    }
}
