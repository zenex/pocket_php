//  ___    ___ _______   ________   ___  ________      ________   _______  _________
// |\  \  /  /|\  ___ \ |\   ___  \|\  \|\   ___ \    |\   ___  \|\  ___ \|\___   ___\
// \ \  \/  / | \   __/|\ \  \\ \  \ \  \ \  \_|\ \   \ \  \\ \  \ \   __/\|___ \  \_|
//  \ \    / / \ \  \_|/_\ \  \\ \  \ \  \ \  \ \\ \   \ \  \\ \  \ \  \_|/__  \ \  \
//   /     \/   \ \  \_|\ \ \  \\ \  \ \  \ \  \_\\ \ __\ \  \\ \  \ \  \_|\ \  \ \  \
//  /  /\   \    \ \_______\ \__\\ \__\ \__\ \_______\\__\ \__\\ \__\ \_______\  \ \__\
// /__/ /\ __\    \|_______|\|__| \|__|\|__|\|_______\|__|\|__| \|__|\|_______|   \|__|
// |__|/ \|__|
// MIT Licensed. Use at your own risk.
// Visit xenid.net for more
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
// Homepage: https://xenid.net/projects/?nav=pocket_php
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
    $headerTitle = array ('title' => "POCKET_PHP USER GUIDE");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("nginx_conf" => "/static/text_files/nginx_config",
                           "gitlab_link" => "https://gitlab.com/Alex-HG/pocket_php");

    $engine->renderPage("project/user_guide.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}

function license($requestData = NULL)
{
    $headerTitle = array ('title' => "POCKET_PHP USER GUIDE");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $engine->renderPage("project/license.html");
    $footer_contents = array("name" => "name");
    $engine->renderFooter(configureFooterStaticContent());
}
