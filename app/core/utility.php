//  ___    ___ _______   ________   ___  ________      ________   _______  _________
// |\  \  /  /|\  ___ \ |\   ___  \|\  \|\   ___ \    |\   ___  \|\  ___ \|\___   ___\
// \ \  \/  / | \   __/|\ \  \\ \  \ \  \ \  \_|\ \   \ \  \\ \  \ \   __/\|___ \  \_|
//  \ \    / / \ \  \_|/_\ \  \\ \  \ \  \ \  \ \\ \   \ \  \\ \  \ \  \_|/__  \ \  \
//   /     \/   \ \  \_|\ \ \  \\ \  \ \  \ \  \_\\ \ __\ \  \\ \  \ \  \_|\ \  \ \  \
//  /  /\   \    \ \_______\ \__\\ \__\ \__\ \_______\\__\ \__\\ \__\ \_______\  \ \__\
// /__/ /\ __\    \|_______|\|__| \|__|\|__|\|_______\|__|\|__| \|__|\|_______|   \|__|
// |__|/ \|__|
// MIT Licensed. Use at your own risk.
// Visit xenid.net for more
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
// Homepage: https://xenid.net/projects/?nav=pocket_php

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
