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
require_once(CORE_DIR."HTTPRequest.php");
require_once(CORE_DIR."database.php");

function entry ($requestData)
{

    if (isset($requestData->GET["nav"]))
    {
        switch ($requestData->GET["nav"])
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
    $header = configureHeaderStaticContent();
    $header["title"] = "POCKET_PHP -- Home";
    $header["description"] = "POCKET_PHP: Blazing fast MVC implementation for PHP7+ ";

    $page_contents = array("about_link" => "about/",
                           "license_link" => "project/?nav=license",
                           "user_guide_link" => "project/?nav=user_guide",
                           "git_link" => PROJECT_GIT,
                           "author_link" => AUTHOR_LINK);

    $engine = new TemplateEngine();
    $engine->addFile("templates/header.html", $header);
    $engine->addFile("templates/navbar.html", configureNavbarStaticContent());
    $engine->addFile("home/home.html", $page_contents);
    $engine->addFile("templates/footer.html", configureFooterStaticContent());
    $engine->render();
}
