<?php
// ██████╗  ██████╗  ██████╗██╗  ██╗███████╗████████╗     ██████╗ ██╗  ██╗██████╗
// ██╔══██╗██╔═══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝     ██╔══██╗██║  ██║██╔══██╗
// ██████╔╝██║   ██║██║     █████╔╝ █████╗     ██║        ██████╔╝███████║██████╔╝
// ██╔═══╝ ██║   ██║██║     ██╔═██╗ ██╔══╝     ██║        ██╔═══╝ ██╔══██║██╔═══╝
// ██║     ╚██████╔╝╚██████╗██║  ██╗███████╗   ██║███████╗██║     ██║  ██║██║
// ╚═╝      ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝╚══════╝╚═╝     ╚═╝  ╚═╝╚═╝
// ─┐ ┬┌─┐┌┐┌┌─┐┌┐ ┬ ┬┌┬┐┌─┐ ─┐ ┬┬ ┬┌─┐
// ┌┴┬┘├┤ ││││ │├┴┐└┬┘ │ ├┤  ┌┴┬┘└┬┘┌─┘
// ┴ └─└─┘┘└┘└─┘└─┘ ┴  ┴ └─┘o┴ └─ ┴ └─┘
// Author:  SENEX @ XENOBYTE.XYZ
// License: MIT License
// Website: https://xenobyte.xyz/projects/?nav=pocket_php

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
    $header["title"] = "POCKET_PHP -- Home";
    $header["description"] = "POCKET_PHP: Blazing fast MVC implementation for PHP7+ ";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("about_link" => "about/",
                           "license_link" => "project/?nav=license",
                           "user_guide_link" => "project/?nav=user_guide",
                           "git_link" => PROJECT_GIT,
                           "author_link" => "https://xenobyte.xyz/about");
    $engine->renderPage("home/home.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}
