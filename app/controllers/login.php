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
require_once(CONFIG);
require_once(CORE."HTTPRequest.php");

// AS SPECIFIED IN THE CONFIGURATION.PHP FILE, ALL CONTROLLER
// FILES. IN ADDITION TO BEING NAMED AFTER THE DESIRED FILE TO
// BE SERVED, MUST INCLUDE A FUNCTION NAMED 'ENTRY( HTTPOBJECT )'
// TO BE CONSIDERED A VALID POCKET_PHP CONTROLLER
function entry ($requestData)
{
    // Is the client already logged in?
    if ($requestData->sessionStatus == SESSION_STATUS::VALID_SESSION)
    {
        loginHomepage($requestData);
        exit();
    }
    // Do we have an internal error to report?
    if ($requestData->errorMsg != NULL)
    {
        processError($requestData);
        exit();
    }
    // Empty or incomplete form
    else if (!isset($requestData->arguments["email"]) || !isset($requestData->arguments["password"]) ||
             empty($requestData->arguments["email"]) || empty($requestData->arguments["password"]))
    {
        loginRequest($requestData);
        exit();
    }
    // Just in case
    throw new Exception("CRITICAL ERROR! Please try again in five minutes!");
}

function loginHomepage($requestData= NULL)
{
    $headerTitle = array ('title' => "POCKET_PHP members area");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $engine->renderPage("login/login_homepage.html");
    $engine->renderFooter();
}

function loginRequest($requestData)
{
    $headerTitle = array ('title' => "POCKET_PHP - Login");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("data" => $requestData->getHTMLString());
    $engine->renderPage("login/login.html", $page_contents);
    $engine->renderFooter();
}

function processError($requestData)
{
    $headerTitle = array ('title' => "POCKET_PHP - Login error");
    $engine = new TemplateEngine();
    $engine->renderHeader($headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    // Load data
    $page_contents = array("error" => $requestData->errorMsg);
    $engine->renderPage("login/login.html", $page_contents);
    $engine->renderFooter();
}
