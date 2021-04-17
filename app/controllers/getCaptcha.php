<?php
// ██████╗  ██████╗  ██████╗██╗  ██╗███████╗████████╗     ██████╗ ██╗  ██╗██████╗
// ██╔══██╗██╔═══██╗██╔════╝██║ ██╔╝██╔════╝╚══██╔══╝     ██╔══██╗██║  ██║██╔══██╗
// ██████╔╝██║   ██║██║     █████╔╝ █████╗     ██║        ██████╔╝███████║██████╔╝
// ██╔═══╝ ██║   ██║██║     ██╔═██╗ ██╔══╝     ██║        ██╔═══╝ ██╔══██║██╔═══╝
// ██║     ╚██████╔╝╚██████╗██║  ██╗███████╗   ██║███████╗██║     ██║  ██║██║
// ╚═╝      ╚═════╝  ╚═════╝╚═╝  ╚═╝╚══════╝   ╚═╝╚══════╝╚═╝     ╚═╝  ╚═╝╚═╝
// ─┐ ┬┌─┐┌┐┌┌─┐┌┐ ┬ ┬┌┬┐┌─┐ ─┐ ┬┬ ┬┌─┐
// ┌┴┬┘├┤ ││││ │├┴┐└┬┘ │ ├┤  ┌┴┬┘└┬┘┌─┘
// ┴ └─└─┘┘└┘└─┘└─┘ ┴  ┴ └─┘o┴ └─ ┴ └─┘
// Author:  SENEX @ XENOBYTE.XYZ
// License: MIT License
// Website: https://xenobyte.xyz/projects/?nav=pocket_php

require_once(CONFIG);
require_once(CORE."HTTPRequest.php");

// AS SPECIFIED IN THE CONFIGURATION.PHP FILE, ALL CONTROLLER
// FILES. IN ADDITION TO BEING NAMED AFTER THE DESIRED FILE TO
// BE SERVED, MUST INCLUDE A FUNCTION NAMED 'ENTRY( HTTPOBJECT )'
// TO BE CONSIDERED A VALID POCKET_PHP CONTROLLER

// This function returns a png image containing a captcha string that is also saved server side as
// a $_SESSION variable, note that the $_SESSION["captcha"] var is reserved for the internal login
// service, to generate a captcha image for other forms simply store it in a different $_SERVER variable
function entry($requestData)
{
    if (!ENFORCE_LOGIN_CAPTCHA)
        exit();
    $img = imagecreatetruecolor(200, 50) or die('Cannot Initialize new GD image stream');;
    imageantialias($img, true);
    $colors = [];
    $red = rand(125, 175);
    $green = rand(125, 175);
    $blue = rand(125, 175);

    for ($i = 0; $i < 5; $i++)
        $colors[] = imagecolorallocate($img, $red - $i*20, $green - $i*20, $blue - $i*20);

    imagefill($img, 0, 0, $colors[0]);

    for ($i = 0; $i < 10; $i++)
    {
        imagesetthickness($img, rand(2, 10));
        $lineColor = $colors[rand(1,4)];
        imagerectangle($img, rand(-10,190), rand(-10,10), rand(-10,190), rand(40,60), $lineColor);
    }


    $black = imagecolorallocate($img, 0, 0, 0);
    $white = imagecolorallocate($img, 255, 255, 255);
    $textColor = [$black, $white];

    // Defined in app/configure.php
    $fonts = [CAPTCHA_FONT_1, CAPTCHA_FONT_2, CAPTCHA_FONT_3, CAPTCHA_FONT_4];

    $captchaLen = 6;
    // Generate the captcha random string
    $input = "ABCDEFGHJKLMNPQRSTUVWXYZ0123456789";
    $len = strlen($input);
    $captchaStr = '';
    for ($i = 0; $i < $captchaLen; $i++) {
        $randChar = $input[mt_rand(0, $len - 1)];
        $captchaStr .= $randChar;
    }

    $_SESSION["login_captcha"] = $captchaStr;

    for ($i = 0; $i < $captchaLen; $i++)
    {
        $letterSpace = 170/$captchaLen;
        $initial = 15;
        imagettftext($img, 24, rand(-15,15), $initial + ($i*$letterSpace),
                     rand(25,45), $textColor[rand(0,1)], $fonts[array_rand($fonts)],
                     $captchaStr[$i]);
    }

    // Return the image to the browser
    header('Content-type: image/png');
    imagepng($img);
    imagedestroy($img);

}
