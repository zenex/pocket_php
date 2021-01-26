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
                           "git_link"        => PROJECT_GIT,
                           "btc_link"        => BTC_ADDRESS,
                           "monero_link"     => MONERO_ADDRESS,
                           "author_link"     => "https://xenobyte.xyz/about",
                           "author"          => AUTHOR);
    $engine->renderPage("about/about.html", $page_contents);
    $engine->renderFooter();
}
