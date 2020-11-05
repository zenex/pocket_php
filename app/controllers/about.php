<?php
// Author:  AlexHG @ ZEN3X.COM
// License: MIT License
// Website: https://ZEN3X.COM


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
    $header["title"] = "POCKET_PHP -- About the project";
    $header["description"] = "More information about the pocket_php project";
    $engine = new TemplateEngine();
    $engine->renderHeader($header);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    $page_contents = array("user_guide_link" => "project/?nav=user_guide",
                           "home_link"       => "home",
                           "git_link"        => "https://github.com/zenex/pocket_php",
                           "btc_link"        => "bc1qe62yenljh8hjlapnp8pw32ex625pxz25cpdsyp",
                           "monero_link"     => "45pFh79LjM9VNT2S3TA28VFG3DqXLYSbvJ7DQ98YZG9VPB5ZPiaj6SsRQggpUUzQdV9JUV4mf",
                           "author_link"     => "https://zen3x.com/about");
    $engine->renderPage("about/about.html", $page_contents);
    $engine->renderFooter();
}
