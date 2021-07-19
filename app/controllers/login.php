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
        // header("Location: ".PROJECT_URL);
        exit();
    }
    // Do we have an internal error to report?
    if ($requestData->errorMsg != NULL)
    {
        processError($requestData);
        exit();
    }
    // Empty or incomplete form
    else if (!isset($requestData->POST["email"]) || !isset($requestData->POST["password"]) ||
             empty($requestData->POST["email"]) || empty($requestData->POST["password"]))
    {
        loginRequest($requestData);
        exit();
    }
    // Just in case
    throw new Exception("CRITICAL ERROR! Please try again in five minutes!");
}

function loginHomepage($requestData= NULL)
{
    $header["title"] = "POCKET_PHP -- Login homepage";
    $header["description"] = "Login succesful";
    $pageContents["ID"] = $requestData->accountID;
    $pageContents["accountType"] = $requestData->accountType;
    $pageContents["last_login"] = $requestData->accountLastLogin;
    $pageContents["email"] = $requestData->accountEmail;
    $pageContents["login_time"] = $requestData->accountSessionTime;
    $pageContents["ip"] = $requestData->accountLoginIP;
    $pageContents["captcha"] = $_SESSION["solved_captcha"];
    $pageContents["sid"] = session_id();
    $pageContents["account_session_id"] = $requestData->accountSessionID;
    $pageContents["cookie_session_id"] = $_COOKIE["SID"];

    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $engine->renderPage("login/login_homepage.html", $pageContents);
    $engine->renderFooter();
}

function loginRequest($requestData)
{
    $header["title"] = "POCKET_PHP -- Login";
    $header["description"] = "Login to proceed";
    $pageContents["proto_ver"] = "ver. ".PROJECT_VERSION;
    $pageContents["login_css"] = PROJECT_URL."static/css/login.css";
    $pageContents["login_img"] = PROJECT_URL."static/images/warning.png";
    $pageContents["error"] = $requestData->errorMsg;
    $pageContents["login_css"] = PROJECT_URL."static/css/login.css";

    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $engine->renderPage("login/login.html", $pageContents);
    $engine->renderFooter();
}

function processError($requestData)
{
    $header["title"] = "POCKET_PHP -- Login error";
    $header["description"] = "Login error";
    $pageContents["proto_ver"] = "ver. ".PROJECT_VERSION;
    $pageContents["login_css"] = PROJECT_URL."static/css/login.css";
    $pageContents["login_img"] = PROJECT_URL."static/images/warning.png";

    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $pageContents["error"] = $requestData->errorMsg;
    $pageContents["login_css"] = PROJECT_URL."static/css/login.css";
    $engine->renderPage("login/login.html", $pageContents);
    $engine->renderFooter();
}
