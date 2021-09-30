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

function entry ($requestData)
{
    if (isset($requestData->GET["nav"]))
    {
        switch ($requestData->GET["nav"])
        {
        case "license":
        {
            license($requestData);
            break;
        }
        }
    }
    else
        license($requestData);

    exit();
}


function license($requestData = NULL)
{
    $header = configureHeaderStaticContent();
    $header["title"] = "POCKET_PHP -- License";
    $header["description"] = "tldr: It's MIT Licensed";
    $contents["author_link"] = "https://xenobyte.xyz/about";
    $contents["author"] = AUTHOR;

    $engine = new TemplateEngine();
    $engine->addFile("templates/header.html", $header);
    $engine->addFile("templates/navbar.html", configureNavbarStaticContent());
    $engine->addFile("project/license.html", $contents);
    $engine->addFile("templates/footer.html", configureFooterStaticContent());
    $engine->render();

}
