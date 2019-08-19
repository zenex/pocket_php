//           _______  _        _______  _______  _______  _______  _______     _        _______ _________
// |\     /|(  ____ \( (    /|(  ____ \(  ____ )(  ___  )(  ____ \(  ____ \   ( (    /|(  ____ \\__   __/
// ( \   / )| (    \/|  \  ( || (    \/| (    )|| (   ) || (    \/| (    \/   |  \  ( || (    \/   ) (
//  \ (_) / | (__    |   \ | || (_____ | (____)|| (___) || |      | (__       |   \ | || (__       | |
//   ) _ (  |  __)   | (\ \) |(_____  )|  _____)|  ___  || |      |  __)      | (\ \) ||  __)      | |
//  / ( ) \ | (      | | \   |      ) || (      | (   ) || |      | (         | | \   || (         | |
// ( /   \ )| (____/\| )  \  |/\____) || )      | )   ( || (____/\| (____/\ _ | )  \  || (____/\   | |
// |/     \|(_______/|/    )_)\_______)|/       |/     \|(_______/(_______/(_)|/    )_)(_______/   )_(
// Author: AlexHG @ xenspace.net
// License: MIT. Use at your own risk.
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
// Homepage: https://xenspace.net/projects/?nav=pocket_php


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
    $headerTitle = array ('title' => "POCKET_PHP ABOUT");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("user_guide_link" => "project/?nav=user_guide",
                           "home_link" => "home");
    $engine->renderPage("about/about.html", $page_contents);
    $engine->renderFooter(configureFooterStaticContent());
}
