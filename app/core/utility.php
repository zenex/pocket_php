//           _______  _        _______  _______  _______  _______  _______     _        _______ _________
// |\     /|(  ____ \( (    /|(  ____ \(  ____ )(  ___  )(  ____ \(  ____ \   ( (    /|(  ____ \\__   __/
// ( \   / )| (    \/|  \  ( || (    \/| (    )|| (   ) || (    \/| (    \/   |  \  ( || (    \/   ) (
//  \ (_) / | (__    |   \ | || (_____ | (____)|| (___) || |      | (__       |   \ | || (__       | |
//   ) _ (  |  __)   | (\ \) |(_____  )|  _____)|  ___  || |      |  __)      | (\ \) ||  __)      | |
//  / ( ) \ | (      | | \   |      ) || (      | (   ) || |      | (         | | \   || (         | |
// ( /   \ )| (____/\| )  \  |/\____) || )      | )   ( || (____/\| (____/\ _ | )  \  || (____/\   | |
// |/     \|(_______/|/    )_)\_______)|/       |/     \|(_______/(_______/(_)|/    )_)(_______/   )_(
// Author: AlexHG @ xenspace.net
// License: MIT. Use at your own risk.
//      ___         ___           ___           ___           ___                       ___         ___           ___
//     /  /\       /  /\         /  /\         /__/|         /  /\          ___        /  /\       /__/\         /  /\
//    /  /::\     /  /::\       /  /:/        |  |:|        /  /:/_        /  /\      /  /::\      \  \:\       /  /::\
//   /  /:/\:\   /  /:/\:\     /  /:/         |  |:|       /  /:/ /\      /  /:/     /  /:/\:\      \__\:\     /  /:/\:\
//  /  /:/~/:/  /  /:/  \:\   /  /:/  ___   __|  |:|      /  /:/ /:/_    /  /:/     /  /:/~/:/  ___ /  /::\   /  /:/~/:/
// /__/:/ /:/  /__/:/ \__\:\ /__/:/  /  /\ /__/\_|:|____ /__/:/ /:/ /\  /  /::\    /__/:/ /:/  /__/\  /:/\:\ /__/:/ /:/
// \  \:\/:/   \  \:\ /  /:/ \  \:\ /  /:/ \  \:\/:::::/ \  \:\/:/ /:/ /__/:/\:\   \  \:\/:/   \  \:\/:/__\/ \  \:\/:/
//  \  \::/     \  \:\  /:/   \  \:\  /:/   \  \::/~~~~   \  \::/ /:/  \__\/  \:\   \  \::/     \  \::/       \  \::/
//   \  \:\      \  \:\/:/     \  \:\/:/     \  \:\        \  \:\/:/        \  \:\   \  \:\      \  \:\        \  \:\
//    \  \:\      \  \::/       \  \::/       \  \:\        \  \::/          \__\/    \  \:\      \  \:\        \  \:\
//     \__\/       \__\/         \__\/         \__\/         \__\/                     \__\/       \__\/         \__\/
//
// Blazing fast MVC implementation for PHP7+
// Homepage: https://xenspace.net/projects/?nav=pocket_php



<?php
require_once(__DIR__."/../configure.php");


// REQUEST A QR CODE FROM GOOGLE.COM
function generate_googleQR($content, $size, $error = NULL)
{
    $width = $height = $size;
    // handle up to 30% data loss, or "L" (7%), "M" (15%), "Q" (25%)
    if (empty($error))
        $error  = "H";
    $border = 1;
    $code = $content;
    echo "<img src=\"http://chart.googleapis.com/chart?"."chs={$width}x{$height}&cht=qr&chld=$error|$border&chl=$code\" />";
}


// REPLACE ALL ITERATIONS OF A STRING IN A FILE
// R: string
function replaceVariables($file, $data)
{
    $template = file_get_contents($file);
    if (empty($data))
        return $template;
    foreach ($data as $key => $value)
        $template = str_replace('{{'.$key.'}}', $value, $template);
    return $template;
}
