<?php

require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");
require_once(CORE."database.php");

function entry ($requestData)
{
    if (isset($requestData->arguments["nav"]))
    {
        switch ($requestData->arguments["nav"])
        {
        case "home":
        {
            homepage($requestData);
            break;
        }
        case "about":
        {
            about($requestData);
            break;
        }
        default:
        {
            homepage($requestData);
            break;
        }
        }
    }
    else
        homepage($requestData);

    exit();
}

function homepage($requestData = NULL)
{
    $headerTitle = array ('title' => "POCKET_PHP WELCOME");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("about_link" => "home/?nav=about");
    $engine->renderPage("home/home.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}

function about($requestData = NULL)
{
    $headerTitle = array ('title' => "POCKET_PHP ABOUT");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("user_guide" => "project/?nav=user_guide");
    $engine->renderPage("home/about.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}
