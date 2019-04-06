<?php

// LOAD ALL RELEVANT CONFIGURATION TO THE CURRENT PHP CONTEXT
// R: void
configure();

// CONFIGURE THE POCKET_PHP BACKEND
// R: void
function configure()
{
    // ------- VERSION CONTROL -------

    // POCKET_PHP's version
    define("VERSION", 1.0);

    // ------- POCKET_PHP SETTINGS -------

    // Enabling DEBUG has the following effects:
    // - More thorough error messages
    // - Internal data output to the client (sent before the HTTP header!)
    define("DEBUG", true);

    // Saves the client's data in the internal database
    define("TRACK_REQUESTS", true);

    // When ENFORCE_BANS is set to true all request with an IP in the
    // internal database ('banned' table) will always be redirected to
    // the banned page (specified below)
    define("ENFORCE_BANS", true);


    // ------- INTERNAL FOLDER STRUCTURE -------

    // The server's URL / IP, note that this address will be used to build all
    // absolute paths used by the template engine module and that HTTPS must be
    // configured accordingly (it is strongly adviced to turn HTTPS on).
    // Example: http://localhost.com and https://localhost (for a local server)
    if ((!empty($_SERVER['HTTPS'])) && $_SERVER["HTTPS"] != 'off')
        define("PROJECT_URL", "https://localhost/");
    else
        define("PROJECT_URL", "http://localhost/");

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

    // ------- SESSION SETTINGS  -------

    // This is the requested file that valid login attempts must call to be processed
    define("LOGIN_CONTROLLER", "login.php");
    // This is the requested file that clients target to cleanly log out
    define("LOGOUT_CONTROLLER", "logout.php");

    // ------- ERRORS AND EXCEPTIONS  -------

    // Error page for all general errors (PHP7 exceptions)
    define("HTTP_ERROR_PAGE", "error/http_error.html");

    // Page to be shown when a banned IP requests anything
    define("BANNED_IP_PAGE", "error/banned.html");

    // ------- DATABASE AND DATA LOGGING  -------
    // Location of the internal database
    define("CORE_SQLITE_FILE", CORE ."pocket_php.db");



    // ------- PHP CONFIGURATION  -------
    date_default_timezone_set('UTC');
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
function configureHeaderStaticContent()
{
    $data = array();
    // CSS files
    $data["favicon"]     = PROJECT_URL."static/images/favicon.png";
    $data["css"]         = PROJECT_URL."static/css/style.css";
    $data["scanlines"]   = PROJECT_URL."static/css/scanlines.css";
    $data["normalize"]   = PROJECT_URL."static/css/normalize.css";
    $data["author"]      = "AlexHG";
    $data["description"] = "welcome to my hub";
    return ($data);
}

function configureFooterStaticContent()
{
    $data = array();
    //JS files
    $data["gitlab_link"]  = "https://gitlab.com/AlexHG/pocket_php";
    $data["about_link"]   = PROJECT_URL."about";
    $data["license_link"] = PROJECT_URL."project/?nav=license";
    return $data;
}

function configureNavbarStaticContent()
{
    $data                    = array();
    // Navigation links (for absolute paths)
    $data["home_link"]       = PROJECT_URL."home";
    $data["user_guide_link"] = PROJECT_URL."project?nav=user_guide";
    $data["about_link"]      = PROJECT_URL."about";
    $data["login_link"]      = PROJECT_URL."login";
    $data["logout_link"]     = PROJECT_URL."logout";
    return $data;
}


// ------- EXCEPTION HANDLER -------

// THIS IS THE EXCEPTION HANDLER FUNCTION THAT ALL THROWN EXCEPTIONS WILL END UP
// BEING PROCESSED BY, THE CODE DEFINES THE ERROR CATEGORY
function handleException ($e) // Throwable $e (PHP 7+)
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

    $engine = new TemplateEngine();
    $headerTitle = array ('title' => "POCKET_PHP - ERROR");
    $engine->renderHeader($headerTitle);
    $navbarData = configureNavbarStaticContent();
    $navbarData["submenu"] = "404 - NOT FOUND";
    $navbarData["submenu_color"] = "neon-orange";
    $engine->renderPage("templates/navbar.html", $navbarData);

    // Load data
    $page_contents["warning_logo"] = PROJECT_URL."static/images/error_icon.png";

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
    $engine->renderFooter();

    exit();
}
