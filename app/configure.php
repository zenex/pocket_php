<?php
// ███╗   ██╗███████╗ ██████╗ ██╗  ██╗███████╗██╗  ██╗   ██╗  ██╗██╗   ██╗███████╗
// ████╗  ██║██╔════╝██╔═══██╗██║  ██║██╔════╝╚██╗██╔╝   ╚██╗██╔╝╚██╗ ██╔╝╚══███╔╝
// ██╔██╗ ██║█████╗  ██║   ██║███████║█████╗   ╚███╔╝     ╚███╔╝  ╚████╔╝   ███╔╝
// ██║╚██╗██║██╔══╝  ██║   ██║██╔══██║██╔══╝   ██╔██╗     ██╔██╗   ╚██╔╝   ███╔╝
// ██║ ╚████║███████╗╚██████╔╝██║  ██║███████╗██╔╝ ██╗██╗██╔╝ ██╗   ██║   ███████╗
// ╚═╝  ╚═══╝╚══════╝ ╚═════╝ ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚═╝  ╚═╝   ╚═╝   ╚══════╝
// Author:  AlexHG @ NEOHEX.XYZ
// License: MIT License
// Website: https://neohex.xyz

// LOAD ALL RELEVANT CONFIGURATION TO THE CURRENT PHP CONTEXT
// R: void
configure();

// CONFIGURE THE POCKET_PHP BACKEND
// R: void
function configure() : void
{
    // ------- VERSION CONTROL -------

    // POCKET_PHP's version
    define("POCKET_PHP_VERSION", "1.3");
    // The rpoject's version
    define("PROJECT_VERSION", "0.1");

    // ------- POCKET_PHP SETTINGS -------

    // Enabling DEBUG has the following effects:
    // - More thorough error messages
    // - Internal data output to the client (sent before the HTTP header!)
    define("DEBUG", true);

    // Saves the client's data in the internal database
    define("TRACK_REQUESTS", false);

    // When ENFORCE_BANS is set to true all request with an IP in the
    // internal database ('banned' table) will always be redirected to
    // the banned page (specified below)
    define("ENFORCE_BANS", false);


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

    define("ROOT"        , __DIR__);
    define("CONFIG"      , ROOT . "/configure.php"); // Main configuration file (BEWARE OF COMMITING USERNAMES AND PASSWORDS)
    define("CORE"        , ROOT . "/core/");         // POCKET_PHP core files
    define("VIEWS"       , ROOT . "/views/");        // HTML files (to be served)
    define("STATIC"      , ROOT . "/static/");       // Files directly served by the web server (see the provided NGINX configuration file)
    define("CONTROLLERS" , ROOT . "/controllers/");  // Content logic
    define("MODELS"      , ROOT . "/models/");       // Database interface files

    // ------- URL ROUTING  -------

    // The default entry point for controllers
    define("CONTROLLER_ENTRY_FUNCTION", "entry");

    // The default controller to be called when the request is empty ("projectURL.com/")
    define("HOMEPAGE",  "home");

    // HTTPRequest.php will reroute all URL.com/robots.txt to URL.com/static/robots.txt
    define("ROBOTS_TXT", "static/text_files/robots.txt");

    // Many browsers request the favicon.ico by appending it immediately after the URL
    define("FAVICON_ICO", "static/images/internal/icons/favicon.ico");

    // SSL verification strings
    // These are used to validate SSL certificates, however, more configuration may be required for this to work.
    // Check out /core/HTTPRequests.php_check_syntax($filename) for more info

    // define("SSL_VER_1_TXT", "cR-7K-MrtkMjYJoknircDb-jugs8FfAxmRUXm5YsJWw.LfHgiDq45HRfQQDyruI6ucCKRG2Iqef1jBPgniAZ7As");
    // define("SSL_VER_2_TXT", "lvN5xeu63t-CxVEtDbUCuXNOT9kQIHud7ZPoFGFvImo.LfHgiDq45HRfQQDyruI6ucCKRG2Iqef1jBPgniAZ7As");

    // ------- SESSION SETTINGS  -------

    // This is the requested file that valid login attempts must call to be processed
    define("LOGIN_CONTROLLER", "login.php");
    // This is the requested file that clients target to cleanly log out
    define("LOGOUT_CONTROLLER", "logout.php");
    // How long a session will last (in seconds) before asking the client to log in again.
    // 0 = Won't expire
    // 60*30 = half hour
    define("SESSION_MAX_DURATION", 60*30);
    // Tolerance time span for client inactivity
    // 0 = Full tolerance
    // 60*30 = half hour of inactivity tolerance
    define("SESSION_INACTIVITY_TOLERANCE", 60*15);

    // ------- ERRORS AND EXCEPTIONS  -------

    // Error page for all general errors (PHP7 exceptions)
    define("HTTP_ERROR_PAGE", "error/http_error.html");

    // Page to be shown when a banned IP requests anything
    define("BANNED_IP_PAGE", "error/banned.html");

    // ------- DATABASE AND DATA LOGGING  -------
    // Location of the internal database
    define("CORE_SQLITE_FILE", "../tools/pocket_php.db");



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

    // CUSTOM CONSTANTS
    // Put all project related constants in here.

    // CUSTOM CONSTANTS
    define("ICONS_FOLDER", "/static/images/icons/");
    define("WEBMASTER_PROTONMAIL", "neohex.xyz@protonmail.com");
    define("WEBMASTER_GMAIL", "");
    // define("PHPMAILER_FOLDER", "/core/phpmailer/");

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
    $data["author"]                     = "NEOHEX.XYZ";
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
    $data["neohex_link"]  = "https://neohex.xyz";
    $data["git_link"]     = "https://gitlab.com/AlexHG/pocket_php";
    $data["author_link"]  = "https://neohex.xyz/about";
    $data["license_link"] = PROJECT_URL."project/?nav=license";

    return $data;
}

function configureNavbarStaticContent() : ?array
{
    $data                     = array();
    $data["home_link"]       = PROJECT_URL."home";
    $data["user_guide_link"] = PROJECT_URL."project?nav=user_guide";
    $data["about_link"]      = PROJECT_URL."about";
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
