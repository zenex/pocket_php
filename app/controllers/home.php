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
    $page_contents = array("about_link" => "about/",
                           "license_link" => "project/?nav=license",
                           "user_guide_link" => "project/?nav=user_guide");
    $engine->renderPage("home/home.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}
