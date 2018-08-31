<?php

require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");

// THE WEB SERVER MUST BE CONFIGURED TO ROUTE ALL ERRORS TO APP/INDEX.HTML
function entry ($requestData)
{
    $headerTitle = array ('title' => "POCKET_PHP ERROR");
    $engine = new TemplateEngine();
    $engine->renderHeader("", $headerTitle);
    $engine->renderPage("templates/navbar.html", configureNavbarStaticContent());
    // Load data
    $page_contents = array("error" => $requestData->errorMsg);
    $engine->renderPage(NOT_FOUND_404, $page_contents);
    $footer_contents = array("name" => "name");
    $engine->renderFooter(configureFooterStaticContent());

    exit();
}
