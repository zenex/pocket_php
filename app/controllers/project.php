<?php
// Author:  AlexHG @ XENOBYTE.XYZ
// License: MIT License
// Website: https://XENOBYTE.XYZ

require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");
require_once(CORE."database.php");
require_once(__DIR__."/../models/devlog_model.php");

function entry ($requestData)
{
    if (isset($requestData->arguments["nav"]))
    {
        switch ($requestData->arguments["nav"])
        {
        case "home":
        {
            user_guide($requestData);
            break;
        }
        case "user_guide":
        {
            user_guide($requestData);
            break;
        }
        case "license":
        {
            license($requestData);
            break;
        }
        case "changelog":
        {
            changelog($requestData);
            break;
        }
        default:
        {
            user_guide($requestData);
            break;
        }
        }
    }
    else
        user_guide($requestData);

    exit();
}


function user_guide($requestData = NULL)
{
    $header["title"] = "POCKET_PHP -- User Guide";
    $header["description"] = "Pocket_PHP documentation, installation and development log";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("nginx_conf" => "/static/text_files/nginx_config",
                           "nginx_vbs_file" => PROJECT_URL."static/text_files/pocket_php_nginx_vbs",
                           "git_link" => "https://github.com/zenex/pocket_php",
                           "devlog_entries" => getDevlogEntries());

    $engine->renderPage("project/user_guide.html", $page_contents);
    $engine->renderFooter();
}

function license($requestData = NULL)
{
    $header["title"] = "POCKET_PHP -- License";
    $header["description"] = "tldr: It's MIT Licensed";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $engine->renderPage("project/license.html");
    $footer_contents = array("name" => "name");
    $engine->renderFooter();
}
