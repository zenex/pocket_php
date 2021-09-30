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
        case "settings":
        {
            settings($requestData);
            break;
        }
        default:
        {
            settings($requestData);
            break;
        }
        }
    }
    else
        settings($requestData);

    exit();
}

function settings($requestData = NULL)
{
    $header = configureHeaderStaticContent();
    $header["title"] = "POCKET_PHP -- Settings the project";
    $header["description"] = "More information settings the pocket_php project";

    $data["pocket_php_ver"] = POCKET_PHP_VERSION;
    $data["project_ver"] = PROJECT_VERSION;

    $data["debug"] = DEBUG;
    $data["track_requests"] = TRACK_REQUESTS;
    $data["enforce_bans"] = ENFORCE_BANS;

    $data["project_url"] = PROJECT_URL;
    $data["force_https"] = FORCE_HTTPS;
    $data["root_dir"] = ROOT_DIR;
    $data["configuration_file"] = CONFIGURATION_FILE;
    $data["core_dir"] = CORE_DIR;
    $data["static_dir"] = STATIC_DIR;
    $data["views_dir"] = VIEWS_DIR;
    $data["controllers_dir"] = CONTROLLERS_DIR;
    $data["models_dir"] = MODELS_DIR;

    $data["controller_entry"] = CONTROLLER_ENTRY_FUNCTION;
    $data["homepage"] = HOMEPAGE;
    $data["robots_txt"] = ROBOTS_TXT;
    $data["favicon_ico"] = FAVICON_ICO;
    $data["http_error_page"] = HTTP_ERROR_PAGE;
    $data["banned_page"] = BANNED_IP_PAGE;
    $data["core_sqlite"] = CORE_SQLITE_FILE;

    $data["sessions_enabled"] = SESSIONS_ENABLED;
    $data["session_save_path"] = SESSIONS_SAVE_PATH;
    $data["session_max_duration"] = SESSION_MAX_DURATION;
    $data["session_inactivity_tolerance"] = SESSION_INACTIVITY_TOLERANCE;
    $data["session_cookie_duration"] = SESSION_COOKIE_DURATION;
    $data["login_captcha"] = ENFORCE_LOGIN_CAPTCHA;
    $data["captcha_length"] = LOGIN_CAPTCHA_LENGTH;
    $data["captcha_font_1"] = CAPTCHA_FONT_1;
    $data["captcha_font_2"] = CAPTCHA_FONT_2;
    $data["captcha_font_3"] = CAPTCHA_FONT_3;
    $data["captcha_font_4"] = CAPTCHA_FONT_4;

    $data["ini_file_uploads"] = ini_get("file_uploads");
    $data["ini_max_file_uploads"] = ini_get("max_file_uploads");
    $data["ini_upload_max_filesize"] = ini_get("upload_max_filesize");

    $data["login_page"] = LOGIN_CONTROLLER;
    $data["logout_page"] = LOGOUT_CONTROLLER;

    $engine = new TemplateEngine();
    $engine->addFile("templates/header.html", $header);
    $engine->addFile("templates/navbar.html", configureNavbarStaticContent());
    $engine->addFile("home/home.html", $data);
    $engine->addFile("templates/footer.html", configureFooterStaticContent());
    $engine->render();

}
