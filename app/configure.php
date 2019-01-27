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
    // configured accordingly if you choose to enable it (add the "https://" prefix)
    // Example: yourURL.com, testURL.xyz, localhost (for a local server)
    define("PROJECT_URL", "http://localhost/");

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
    $data["favicon"]   = PROJECT_URL."static/images/favicon.png";
    $data["css"]       = PROJECT_URL."static/css/style.css";
    $data["scanlines"] = PROJECT_URL."static/css/scanlines.css";
    $data["normalize"] = PROJECT_URL."static/css/normalize.css";
    $data["author"] = "AlexHG";
    $data["description"] = "welcome to my hub";
    return ($data);
}

function configureFooterStaticContent()
{
    $data = array();
    //JS files
    $data["gitlab"]  = "https://gitlab.com/Alex-HG/pocket_php";
    return $data;
}

function configureNavbarStaticContent()
{
    $data = array();
    // Navigation links (for absolute paths)
    $data["home"]       = PROJECT_URL."home";
    $data["about"]      = PROJECT_URL."home?nav=about";
    $data["projects"] = PROJECT_URL."projects";
    $data["gallery"] = PROJECT_URL."gallery";
    $data["blog"] = PROJECT_URL."blog";
    $data["links"] = PROJECT_URL."links";
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
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());

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
