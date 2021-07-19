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
    // No point in generating the captcha if sessions are disabled
    if (!SESSIONS_ENABLED)
        exit();

    $img = imagecreatetruecolor(150, 50) or die('Cannot Initialize new GD image stream');
    imageantialias($img, true);
    $colors = [];
    $red = rand(100, 175);
    $green = rand(100, 175);
    $blue = rand(100, 175);
    $colorPool = 5;
    $colorOffset = 20;

    // Mix the colors to prevent easy "eyedroppper" value recognition
    for ($i = 0; $i < $colorPool; $i++)
        $colors[] = imagecolorallocate($img, $red - $i*$colorOffset, $green - $i*$colorOffset, $blue - $i*$colorOffset);

    // Fill the background color with the first mix
    imagefill($img, 0, 0, $colors[0]);

    // Draw random rectangles on top to further obfuscate the characters from the background
    for ($i = 0; $i < 10; $i++)
    {
        imagesetthickness($img, rand(2, 10));
        $lineColor = $colors[rand(1,4)];
        imagerectangle($img, rand(-10,190), rand(-10,10), rand(-10,190), rand(40,60), $lineColor);
    }

    // Limit character colors to black & white, this way they will never conflict with the background
    $black = imagecolorallocate($img, 0, 0, 0);
    $white = imagecolorallocate($img, 255, 255, 255);
    $textColor = [$black, $white];

    // Defined in app/configure.php
    $fonts = [CAPTCHA_FONT_1, CAPTCHA_FONT_2, CAPTCHA_FONT_3, CAPTCHA_FONT_4];
    $captchaLen = LOGIN_CAPTCHA_LENGTH;
    // Limit the potential characters to those in the array
    $input = "ABCDEFGHJKLMNPQRSTUVWXYZ0123456789";
    $len = strlen($input);
    $captchaStr = '';
    for ($i = 0; $i < $captchaLen; $i++) {
        $randChar = $input[mt_rand(0, $len - 1)];
        $captchaStr .= $randChar;
    }

    $_SESSION["login_captcha"] = $captchaStr;

    // Draw the characters
    for ($i = 0; $i < $captchaLen; $i++)
    {
        $letterSpace = 120/$captchaLen;
        $initial = 25;
        imagettftext($img, 24, rand(-15,15), $initial + ($i*$letterSpace),
                     rand(25,45), $textColor[rand(0,1)], $fonts[array_rand($fonts)],
                     $captchaStr[$i]);
    }
    // Add a watermark like a true SHILL
    imagettftext($img, 6, 0, 2, 48, $white, CAPTCHA_FONT_3, "powered by xenobyte.xyz");
    // Make sure no output has been sent to the client yet, it will conflict with the image data and crash
    ob_end_clean();
    // Return the image to the browser
    header('Content-type: image/png');
    imagepng($img);
    imagedestroy($img);
}
