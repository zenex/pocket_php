<?php
// Author:  AlexHG @ XENOBYTE.XYZ
// License: MIT License
// Website: https://XENOBYTE.XYZ


require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");
require_once(CORE."database.php");

function entry ($requestData)
{
    if (isset($requestData->arguments["nav"]))
    {
        switch ($requestData->arguments["nav"])
        {
        case "about":
        {
            about($requestData);
            break;
        }
        default:
        {
            about($requestData);
            break;
        }
        }
    }
    else
        about($requestData);

    exit();
}

function about($requestData = NULL)
{
    $header["title"] = "POCKET_PHP -- About the project";
    $header["description"] = "More information about the pocket_php project";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("user_guide_link" => "project/?nav=user_guide",
                           "home_link"       => "home",
                           "git_link"        => "https://github.com/zenex/pocket_php",
                           "btc_link"        => "19LCuwFDAFam19TPKoH3LgMSJR2jWUGxmx",
                           "monero_link"     => "42u5eF96XKvJG2JDyWQhHD8Gjoxf218u6RbMhAzEzyGQdxkAu7stCj3dburBhrnU3aDaJHoH1HveAAdUfArA9MAuFCbnncc",
                           "author_link"     => "https://xenobyte.xyz/about");
    $engine->renderPage("about/about.html", $page_contents);
    $engine->renderFooter();
}
