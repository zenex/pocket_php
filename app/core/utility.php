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


// REQUEST A QR CODE FROM GOOGLE.COM
function generate_googleQR($content, $size, $error = NULL) : void
{
    $width = $height = $size;
    // handle up to 30% data loss, or "L" (7%), "M" (15%), "Q" (25%)
    if (empty($error))
        $error  = "H";
    $border = 1;
    $code = $content;
    echo "<img src=\"http://chart.googleapis.com/chart?"."chs={$width}x{$height}&cht=qr&chld=$error|$border&chl=$code\" />";
}


function replaceValues($string, $prefix, $postfix, $values)
{
    if (empty($string) || empty($values))
        return $string;

    if (is_array($values))
    {
        foreach ($values as $key => $data)
            $string = str_replace($prefix . $key . $postfix, $data, $string);
    }

    return $string;
}

// Recursively deletes a directory and all its contents, including subdirectories
// NOTE: This can take an unacceptable amount of time based on the contents of the dir
// it could be better to just format the path and do something like shell_exec("rm -rf " . $dir);
function delDir($path)
{
    //print("<br>Deleting ".$path);
    if (!is_dir($path))
        return;

    // The last character in the path string should be a separator '/'
    if (substr($path, strlen($path) - 1, 1) != '/')
        $path .= '/';

    $dirContents = glob($path.'*');
    if (!empty($dirContents))
    {
        foreach ($dirContents as $file)
        {
            // If the item is a folder, recursively call this function to delete its contents before proceeding
            if (is_dir($file))
            {
                //print("<br><br>Deleting subdir ".$file);
                delDir($file);
            }
            else
            {
                //print("<br><br>Deleting file ".$file);
                unlink($file);
            }
        }
    }
    rmdir($path);
}

function newDir($path, $permissions = 0777)
{
    if (!file_exists($path))
        if (mkdir($path, $permissions, true))
            return true;

    return false;
}
