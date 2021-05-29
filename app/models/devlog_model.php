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

require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");
require_once(CORE."database.php");

function getDevlogEntries()
{
    $db = (new SQLiteConnection())->getDB();
    if ($db != NULL)
    {
        $query = $db->prepare("SELECT * FROM devlog ORDER BY id DESC");
        $query->execute();
        $result = $query->fetchAll();
        if ($result)
            return formatDevlogPost($result);
        else
            return NULL;
    }
    return NULL;
}


function formatDevlogPost($journalData)
{
    $string = "";
    $titleColor = "";
    $textColor = "";
    $linkToCategory= "";
    foreach ($journalData as $row)
    {
        $titleColor = "greentext";
        $textColor = "neon-swamp";
        $linkToCategory = PROJECT_URL."journal?category=life";

        // Post opener
        $string .= "<div class='post-content' style=''>";
        $string .= "<div class='green'> &#9632; #". $row["id"]." [". $row["date"]."] -- ". $row["title"]."</a></div>";
        $string .= "<div class=' ".$textColor."' style='padding-bottom: 10px;'>".$row["content"]."</div>";
        $string .= "</div>";
    }
    return $string;
}
