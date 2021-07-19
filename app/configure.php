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


// LOAD ALL RELEVANT CONFIGURATION TO THE CURRENT PHP CONTEXT
// R: void
configure();

// CONFIGURE THE POCKET_PHP BACKEND
// R: void
function configure() : void
{
    // ------- VERSION CONTROL -------

    // POCKET_PHP's version
    define("POCKET_PHP_VERSION", "2.1");
    // The rpoject's version
    define("PROJECT_VERSION", "2.1");

    // ------- POCKET_PHP SETTINGS -------

    // Enabling DEBUG has the following effects:
    // - More thorough error messages
    // - Internal data output to the client (sent before the HTTP header!)
    define("DEBUG", true);

    // Saves the client's data in the internal database
    define("TRACK_REQUESTS", 0);

    // When ENFORCE_BANS is set to true all request with an IP in the
    // internal database ('banned' table) will always be redirected to
    // the banned page (specified below)
    define("ENFORCE_BANS", 0);

    // Toggles sessions, note that the HTTPRequest object
    define("SESSIONS_ENABLED", true);



    // ------- INTERNAL FOLDER STRUCTURE -------

    // The server's URL / IP, note that this address will be used to build all
    // absolute paths used by the template engine module and that HTTPS must be
    // configured accordingly (it is strongly adviced to turn HTTPS on).
    // Example: http://localhost.com and https://localhost (for a local server)
    if ((!empty($_SERVER['HTTPS'])) && $_SERVER["HTTPS"] != 'off')
        define("PROJECT_URL", "https://pocket_php.localhost/");
    else
        define("PROJECT_URL", "http://pocket_php.localhost/");

    // Forces all non HTTPS requests to the HTTPS version of the site
    // HTTPS must be configured a priori and PROJECT_URL must be set
    // to the desired HTTPS enabled URL
    define("FORCE_HTTPS", true);
    define("ROOT_DIR"        , __DIR__);
    define("CONFIGURATION_FILE"      , ROOT_DIR . "/configure.php"); // Main configuration file (BEWARE OF COMMITING USERNAMES AND PASSWORDS)
    define("CORE_DIR"        , ROOT_DIR . "/core/");         // POCKET_PHP core files
    define("VIEWS_DIR"       , ROOT_DIR . "/views/");        // HTML files (to be served)
    define("STATIC_DIR"      , ROOT_DIR . "/static/");       // Files directly served by the web server (see the provided NGINX configuration file)
    define("CONTROLLERS_DIR" , ROOT_DIR . "/controllers/");  // Content logic
    define("MODELS_DIR"      , ROOT_DIR . "/models/");       // Database interface files

    // ------- URL ROUTING  -------

    // The default entry point for controllers
    define("CONTROLLER_ENTRY_FUNCTION", "entry");

    // The default controller to be called when the request is empty ("projectURL.com/")
    define("HOMEPAGE",  "home");

    // HTTPRequest.php will reroute all URL.com/robots.txt to URL.com/static/robots.txt
    define("ROBOTS_TXT", "static/text_files/robots.txt");

    // Many browsers request the favicon.ico by appending it immediately after the URL
    define("FAVICON_ICO", "static/images/internal/icons/favicon.ico");



    // ------- ERRORS AND EXCEPTIONS  -------

    // Error page for all general errors (PHP7 exceptions)
    define("HTTP_ERROR_PAGE", "error/http_error.html");

    // Page to be shown when a banned IP requests anything
    define("BANNED_IP_PAGE", "error/banned.html");


    // ------- DATABASE AND DATA LOGGING  -------

    // Location of the internal database
    define("CORE_SQLITE_FILE", "../tools/pocket_php.db");


    // ------- SESSION SETTINGS  -------

    // This prevents session file clashing when running multiple sites on the same server (defaults to /tmp)
    define("SESSIONS_SAVE_PATH", "../tools/sessions");
    // This is the requested file that valid login attempts must call to be processed
    define("LOGIN_CONTROLLER", "login.php");
    // This is the requested file that clients target to cleanly log out
    define("LOGOUT_CONTROLLER", "logout.php");

    // How long a session will last (in seconds) before asking the client to log in again.
    // 0 = Won't expire
    // 60*30 = half hour
    // define("SESSION_MAX_DURATION", 60*30);
    define("SESSION_MAX_DURATION", 60*5);
    // Tolerance time span for client inactivity
    // 0 = Full tolerance
    // 60*30 = half hour of inactivity tolerance
    // define("SESSION_INACTIVITY_TOLERANCE", 60*15);
    define("SESSION_INACTIVITY_TOLERANCE", 60*2);
    // How long will the user SID cookie be valid for
    // NOTE: If the cookie expires before the SESSION_MAX_DURATION
    define("SESSION_COOKIE_DURATION", 60*10);
    // PHP garbage collector trigger probability
    define("SESSION_GC_PROBABILITY", 1);
    define("SESSION_GC_DIVISOR", 100);;

    // ------- LOGIN CAPTCHA SETTINGS  -------

    define("ENFORCE_LOGIN_CAPTCHA", true);
    define("LOGIN_CAPTCHA_LENGTH", 6);
    define("CAPTCHA_FONT_1", "static/fonts/ro.ttf");
    define("CAPTCHA_FONT_2", "static/fonts/heading.woff");
    define("CAPTCHA_FONT_3", "static/fonts/libsans.ttf");
    define("CAPTCHA_FONT_4", "static/fonts/cantarell.otf");


    // ------- PHP CONFIGURATION  -------
    // Note that the PHP locales are the same as the ones enabled on the host system.
    // Use "locale -a" to inspect the enabled locales and uncomment the desired
    // locales in /etc/locale.gen then run locale-gen to enable new languages.
    // date_default_timezone_set('UTC');
    date_default_timezone_set('America/Monterrey');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    if (DEBUG)
    {
        error_reporting(-1);
        ini_set('display_errors', 1);
    }
    else
    {
        error_reporting(0);
        ini_set('display_errors', 0);
    }
    // This function will deal with all thrown exceptions making it a great
    // exit in case of errors
    set_exception_handler("handleException");

    // Session file path, must be writeable by the PHP-FPM daemon
    ini_set("session.save_path", SESSIONS_SAVE_PATH);
    ini_set("session.save_handler", "files");
    ini_set('session.gc_probability', SESSION_GC_PROBABILITY);
    ini_set('session.gc_divisor', SESSION_GC_DIVISOR);
    // Set session duration (maxlifetime) to 1 hour - 60 x 60 x 1
    ini_set('session.gc_maxlifetime', SESSION_MAX_DURATION);
     // Set the session cookie to expire at the same time as the server side session id file
    session_set_cookie_params(SESSION_COOKIE_DURATION, "/");

    // File uploads settings that MUST BE MANUALLY SET IN PHP.INI
    //ini_set("file_uploads", 1);
    //ini_set("max_file_uploads", 20);
    //ini_set("upload_max_filesize", "2M");

    // ------- CUSTOM CONSTANTS  -------
    // Put all project related constants in here.

    // CUSTOM CONSTANTS
    define("AUTHOR", "SENEX @ XENOBYTE.XYZ");
    define("AUTHOR_LINK", "https://xenobyte.xyz/about");
    define("BTC_ADDRESS", "19LCuwFDAFam19TPKoH3LgMSJR2jWUGxmx");
    define("MONERO_ADDRESS", "42u5eF96XKvJG2JDyWQhHD8Gjoxf218u6RbMhAzEzyGQdxkAu7stCj3dburBhrnU3aDaJHoH1HveAAdUfArA9MAuFCbnncc");
    define("ICONS_FOLDER", "/static/images/icons/");
    define("WEBMASTER_PROTONMAIL", "xenobyte.xyz@protonmail.com");
    define("WEBMASTER_GMAIL", "");
    define("PROJECT_GIT", "https://git.xenobyte.xyz/XENOBYTE/pocket_php");
    // define("PHPMAILER_FOLDER", "/core/phpmailer/");

    // Set the session status
    if (SESSIONS_ENABLED)
    {
        if (session_id() == "")
            session_start();
    }
}

// ------- TEMPLATE ENGINE SETTINGS -------

// THE TEMPLATE ENGINE MODULE USES THIS INFORMATION TO
// PROPERLY INSERT THE STATIC FILE CALLS TO THE MAIN
// HTML HEADER, FOOTER (AND ANY OTHER) FILES, MUST RETURN
// A PROPER ARRAY AND THE HTML FILE ITSLEF MUST CALL THE {{SETTING}}
// EXAMPLE FOR A LIBRARY CONTAINING THE KEY "TITLE" => POCKET_PHP
// <TITLE>{{TITLE}}</TITLE>
//
// R: Associative Array or NULL
function configureHeaderStaticContent() : ?array
{
    $data                               = array();
    // CSS files
    $data["msapp_xml"]                  = PROJECT_URL."static/text_files/msapps.xml";
    $data["manifest_json"]              = PROJECT_URL."static/text_files/manifest.json";
    $data["style_css"]                  = PROJECT_URL."static/css/style.css";
    $data["scanlines_css"]              = PROJECT_URL."static/css/scanlines.css";
    $data["normalize"]                  = PROJECT_URL."static/css/normalize.css";
    $data["author"]                     = AUTHOR;
    $data["apple_mobile_web_app_title"] = "pocket_php";
    $data["application_name"]           = "pocket_php";
    $data["robots_inline"]              = "all";

    $data["favicon_img"]                = ICONS_FOLDER."favicon.png";
    $data["favicon_ico"]                = ICONS_FOLDER."favicon.ico";
    $data["apple_touch_icon_img"]       = ICONS_FOLDER."apple-touch-icon.png";
    $data["favicon_16x16_img"]          = ICONS_FOLDER."favicon-16x16.png";
    $data["favicon_32x32_img"]          = ICONS_FOLDER."favicon-32x32.png";
    $data["safari_pinned_tab_svg"]      = ICONS_FOLDER."safari-pinned-tab.svg";
    return ($data);
}

function configureFooterStaticContent() : ?array
{
    $data                 = array();
    $data["site_link"]  = "https://xenobyte.xyz";
    $data["project_home_link"]  = "https://xenobyte.xyz/projects/?nav=pocket_php";
    $data["git_link"]     = PROJECT_GIT;
    $data["author_link"]  = "https://xenobyte.xyz/about";
    $data["author"]  = AUTHOR;
    $data["license_link"] = PROJECT_URL."project/?nav=license";
    $data["btc_link"]  = BTC_ADDRESS;
    $data["monero_link"] = MONERO_ADDRESS;

    return $data;
}

function configureNavbarStaticContent() : ?array
{
    $data                     = array();
    $data["author_link"]  = "https://xenobyte.xyz";
    $data["project_home_link"]  = "https://xenobyte.xyz/projects/?nav=pocket_php";
    $data["home_link"]       = PROJECT_URL."home";
    $data["settings_link"]      = PROJECT_URL."settings";
    $data["login_link"]      = PROJECT_URL."login";
    $data["logout_link"]     = PROJECT_URL."logout";

    $data["pocket_php_ver"]     = POCKET_PHP_VERSION;
    return $data;
}


// ------- EXCEPTION HANDLER -------

// THIS IS THE EXCEPTION HANDLER FUNCTION THAT ALL THROWN EXCEPTIONS WILL END UP
// BEING PROCESSED BY, THE CODE DEFINES THE ERROR CATEGORY
function handleException ($e) : void // Throwable $e (PHP 7+)
{
    // include(CORE."templateEngine.php");
    if (DEBUG)
    { //
        $errorMsg = $e->getMessage() ."<br><br>". $e->getTraceAsString();
        $page_contents = array("error" => "POCKET_PHP Exception! <br> Code: ". $e->getCode() ."<br> Message: ". $errorMsg);
    }
    else // Set to any generic error message
    {
        $page_contents = array("error" => "An error has occurred, please try again in a few moments.");
    }



    // HTML HEADERS
    $header["title"] = "POCKET_PHP | 404 - NOT FOUND";
    $header["description"] = "404 - Content not found.";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);

    // SITE NAVIGATION
    $navbarData = configureNavbarStaticContent();
    $engine->renderPage("templates/navbar.html", $navbarData);

    // Load data
    $page_contents["error_gif"] = PROJECT_URL."static/images/error.gif";
    $page_contents["back_link"] = PROJECT_URL."home";

    // var_dump(debug_backtrace());
    switch ($e->getCode())
    {
    case 404: // Not found
    {
        $engine->renderPage(HTTP_ERROR_PAGE, $page_contents);
        break;
    }
    case 666: // Banned error code
    {
        $page_contents["error"] = $e->getMessage();
        $engine->renderPage("error/banned.html", $page_contents);
        break;
    }
    default:
    {
        $engine->renderPage(HTTP_ERROR_PAGE, $page_contents);
        break;
    }
    }
    // FOOTER
    $engine->renderFooter(configureFooterStaticContent());

    exit();
}
