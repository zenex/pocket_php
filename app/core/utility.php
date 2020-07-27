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


// REPLACE ALL ITERATIONS OF A STRING IN A FILE
// R: string
function replaceVariables($file, $data) : ?string
{
    $template = file_get_contents($file);
    if (empty($data))
        return $template;
    foreach ($data as $key => $value)
        $template = str_replace('{{'.$key.'}}', $value, $template);
    return $template;
}

