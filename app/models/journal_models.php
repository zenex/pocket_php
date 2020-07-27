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

require_once(__DIR__."/../configure.php");
require_once(CORE."HTTPRequest.php");
require_once(CORE."database.php");

class JournalPost
{
    public $id = NULL;
    public $title = NULL;
    public $author = NULL;
    public $date = NULL;
    public $category = NULL;
    public $content = NULL;
    public $titleColor = NULL;
    public $textColor = NULL;
    public $htmlStr = NULL;
    public $tags = NULL;
}



function getJournalArchive($category = NULL)
{
    $db = new SQLiteConnection();
    $db = $db->db;
    if ($db != NULL)
    {
        $query = $db->prepare("SELECT * FROM journal ORDER BY id DESC");
        if ($category != NULL)
        {
            $category = $requestData->arguments["category"];
            $query = $db->prepare("SELECT * FROM journal where category=:category ORDER BY id DESC");
            $query->bindParam(":category", $category);
        }
        $query->execute();
        $result = $query->fetchAll();
        if ($result)
            return $result;
        else
            return NULL;
    }
    return NULL;
}

function getDevlogEntries()
{
    $db = new SQLiteConnection();
    $db = $db->db;
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


function getJournalEntries($category = NULL, $amount = 30)
{
    $db = new SQLiteConnection();
    $db = $db->db;
    if ($db != NULL)
    {
        $query = $db->prepare("SELECT * FROM journal ORDER BY id DESC LIMIT ".$amount);
        if ($category != NULL)
        {
            $query = $db->prepare("SELECT * FROM journal where category=:category ORDER BY id DESC LIMIT ".$amount);
            $query->bindParam(":category", $category);
        }
        $query->execute();
        $result = $query->fetchAll();
        if ($result)
            return generateJournalPost($result);
        else
            return NULL;
    }
    return NULL;
}


function getJournalPost($postID)
{
    $journalPost = new JournalPost();
    $db = new SQLiteConnection();
    $db = $db->db;
    if ($db != NULL)
    {
        $query = $db->prepare("SELECT * FROM journal WHERE id=:id LIMIT 1");
        $query->bindparam(":id", $postID);
        $query->execute();
        $result = $query->fetchAll();
        if ($result)
        {
            $journalPost->id = $result[0]["id"];
            $journalPost->content = $result[0]["content"];
            $journalPost->author = $result[0]["author"];
            $journalPost->date = $result[0]["date"];
            $journalPost->title = $result[0]["title"];
            $journalPost->category = $result[0]["category"];

            switch ($journalPost->category)
            {
            case "life":
            {
                $journalPost->titleColor = "greentext";
                $journalPost->textColor = "neon-swamp";
                break;
            }
            case "misc":
            {
                $journalPost->titleColor = "yellow";
                $journalPost->textColor = "gray";
                break;
            }
            case "dream_journal":
            {
                $journalPost->titleColor = "neon-blue";
                $journalPost->textColor = "magenta";
                break;
            }
            case "tech":
            {
                $journalPost->titleColor = "cyan";
                $journalPost->textColor = "blue";
                break;
            }
            case "sgl_devlog":
            {
                $journalPost->titleColor = "magenta";
                $journalPost->textColor = "neon-red";
                break;
            }
            case "pocketphp_devlog":
            {
                $journalPost->titleColor = "white";
                $journalPost->textColor = "white";
                break;
            }
            }

        }
        return $journalPost;
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
        // Post header
        $string .= "<div class='journal-title red-underline'>";
        $string .= "<div class='green'> &#9632; ". $row["title"]."</a>";
        $string .= "</div>";
        $string .= "<div class='journal-date'>";
        $string .= "<span class='glow-white'>[#".$row["id"]."]</span> <span class='glow-red'>[".$row["date"]."]</span>";
        $string .= "</div>";
        // $string .= "<span style='padding-bottom: 10px;' class='magenta'> [ ".$row["author"]." @ ".$row["date"]." ]</span>";
        $string .= "<div class='journal-content ".$textColor."' style='padding-bottom: 10px;'>".$row["content"]."</div>";
        // With author
        // $string .= "<p class='white' style='text-align:right; margin-bottom: 10px;'>".$row["author"]." @ ".$row["date"]."</p>";
        // Without author
        $string .= "<div class='journal-footer white-overline' style='text-align:right; margin-bottom: 10px;'> ".$tags."</div>";

        // $string .= "<p class='crying-pink' style='text-align:right; margin-bottom: 10px;'><br><span class='magenta'>[ #".$row["id"]." ]</span> [ ".$row["date"]." ]</p>";
        $string .= "</div>"; // post-title
    }
    return $string;
}

function getJournalEntry($category, $ID)
{
    $journalPost = new JournalPost();
    $db = new SQLiteConnection();
    $db = $db->db;
    if ($db != NULL)
    {
        $query = $db->prepare("SELECT * FROM journal WHERE category=:category AND id=:id LIMIT 1");
        $query->bindparam(":category", $category);
        $query->bindparam(":id", $ID);
        $query->execute();
        $result = $query->fetchAll();
        if ($result)
        {
            $journalPost->id = $result[0]["id"];
            $journalPost->content = $result[0]["content"];
            $journalPost->author = $result[0]["author"];
            $journalPost->date = $result[0]["date"];
            $journalPost->title = $result[0]["title"];
            $journalPost->category = $result[0]["category"];
            $journalPost->tags = tagString($result[0]["tags"]);
            $linkToCategory= "";
            switch ($journalPost->category)
            {
            case "life":
            {
                $journalPost->titleColor = "greentext";
                $journalPost->textColor = "clover";
                $linkToCategory = PROJECT_URL."journal?category=life";
                break;
            }
            case "misc":
            {
                $journalPost->titleColor = "yellow";
                $journalPost->textColor = "gray";
                $linkToCategory = PROJECT_URL."journal?category=misc";
                break;
            }
            case "dream_journal":
            {
                $journalPost->titleColor = "neon-blue";
                $journalPost->textColor = "magenta";
                $linkToCategory = PROJECT_URL."journal?category=dream_journal";
                break;
            }
            case "tech":
            {
                $journalPost->titleColor = "neon-blue";
                $journalPost->textColor = "blue";
                $linkToCategory = PROJECT_URL."journal?category=tech";
                break;
            }
            case "sgl_devlog":
            {
                $journalPost->titleColor = "magenta";
                $journalPost->textColor = "neon-red";
                $linkToCategory = PROJECT_URL."journal?category=sgl_devlog";
                break;
            }
            case "pocketphp_devlog":
            {
                $journalPost->titleColor = "white";
                $journalPost->textColor = "white";
                $linkToCategory = PROJECT_URL."journal?category=pocketphp_devlog";
                break;
            }
            }

            // Get the next and previous posts for the navigation menu
            $previousEntryStr = "";
            $previousID = (int)$ID-1;
            $query = $db->prepare("SELECT * FROM journal WHERE id=:id LIMIT 1");
            $query->bindparam(":id", $previousID);
            $query->execute();
            $result = $query->fetchAll();
            if ($result)
            {
                $previousEntryStr = "<a title='previous' class='neon-orange' href='".PROJECT_URL."journal/?category=".$result[0]["category"]."&id=".$result[0]["id"]."'><--- previous</a>";
            }

            $nextEntryStr = "";
            $nextID = (int)$ID+1;
            $query = $db->prepare("SELECT * FROM journal WHERE id=:id LIMIT 1");
            $query->bindparam(":id", $nextID);
            $query->execute();
            $result = $query->fetchAll();
            if ($result)
            {
                $nextEntryStr = "<a title='next' class='neon-orange' href='".PROJECT_URL."journal/?category=".$result[0]["category"]."&id=".$result[0]["id"]."'>next ---></a>";
            }

            // Generate the final HTML output

            // Post opener
            $journalPost->htmlStr .= "<div class='post-content shadow-post' style='margin-bottom: 30px;'>";

            $journalPost->htmlStr .= "<div class='journal-title white-underline'>";
            $journalPost->htmlStr .= "<a href='".PROJECT_URL."journal/?category=".$journalPost->category."&id=".$journalPost->id."' class='glow-".$journalPost->titleColor." journal-entry-title'> &#9632; ". $journalPost->title."</a>";
            $journalPost->htmlStr .= "</div>";
            $journalPost->htmlStr .= "<div class='journal-date'>";
            $journalPost->htmlStr .= "<span class='glow-white'>[#".$journalPost->id."]</span> <span class='glow-red'>[".$journalPost->date."]</span>";
            $journalPost->htmlStr .= "<span class='glow-yellow'> [".$journalPost->author."]</span> <a href='".$linkToCategory."' class='glow-".$journalPost->titleColor."'>[".$journalPost->category."]</a>";
            $journalPost->htmlStr .= "</div>";

            $journalPost->htmlStr .= "<div class='journal-content ".$journalPost->textColor."' style='padding-bottom: 10px;'>".$journalPost->content."</div>";
            // With author
            // $journalPost->htmlStr = "<p class='white' style='text-align:right; margin-bottom: 10px;'>".$journalPost->author"]." @ ".$journalPost->date"]."</p>";
            // Without author
            $journalPost->htmlStr .= "<p class='journal-footer white-overline' style='text-align:right; margin-bottom: 10px;'>";

            $journalPost->htmlStr .= "<span> ".$journalPost->tags."</span>";
            $journalPost->htmlStr .= "</p>";
            $journalPost->htmlStr .= "</div>"; // post-title
            $journalPost->htmlStr .= "<p class='journal-navigation'><br> [ ".$previousEntryStr." ".$nextEntryStr." ]</p>";
            // $journalPost->htmlStr = "<p class='crying-pink' style='text-align:right; margin-bottom: 10px;'><br><span class='magenta'>[ #".$journalPost->id"]." ]</span> [ ".$journalPost->date"]." ]</p>";


        }
        return $journalPost;
    }
    return NULL;
}

