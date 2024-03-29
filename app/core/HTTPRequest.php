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

abstract class SESSION_STATUS
{
    const NO_SESSION = 0;
    const ID_SESSION = 1;
    const VALID_SESSION = 2;
}

// THIS OBJECT WILL BE PASSED DOWN TO THE CONTROLLER, IT ENCAPSULATES ALL RELEVANT
// SECTIONS OF THE CLIENT'S REQUEST, IT IS RESPONSIBLE FOR VALIDATING THE FILE
// REQUESTED BY THE CLIENT, WILL THROW AN EXCEPTION IF THE REQUEST IS INVALID
class HTTPRequest
{
    // DEPRECATED IN 2.2
    // public $ipCountry = NULL;

    public $ip = NULL;
    public $userAgent = NULL;
    public $route = NULL;
    public $GET = NULL; // Will always contain the $_GET arguments
    public $POST = NULL;
    public $cookies = NULL;
    public $requestType = NULL;
    public $requestedFile = NULL;
    public $sessionStatus = NULL;
    public $errorMsg = NULL; // To be filled by index and subsequently processed by the controller
    public $https = false;

    // These variables are populated by the validateLoginAttempt function
    // THEY REQUIRE SESSIONS_ENABLED TO WORK
    public $accountLastLogin = NULL;
    public $accountType = NULL;
    public $accountID = NULL;
    public $accountEmail = NULL;
    public $accountUsername = NULL;
    public $accountSessionTime = NULL; // Session starting time, used by SESSION_DURATION to limit client session time
    public $accountSessionID = NULL;
    public $accountInactivityTime = NULL; // Updated with every client activity, will close the session if more than SESSION_INACTIVITY_TOLERANCE has passed
    public $accountLoginIP = NULL;
    public $accountLoggedIn = NULL;
    public $accountLogoutType = NULL;

    // EXTRACT AND PROCESS THE REQUESTS CONTENTS, ENSURE THE CONTROLLER FILE EXISTS
    // AND THE REQUEST'S VALIDITY
    function __construct($url)
    {
        // Validate and track the client's IP
        $this->ip = $this->getRequestIP();

        // The geoip service reaches its limit very fast, premium required
        //$this->ipCountry = $this->traceIP();

        // Process the requested URL
        // If the request is empty (servername.com/)
        if ($url == "/")
            $this->route = HOMEPAGE; // defined in configure.php


        else // Not empty
        {
            $url = ltrim($url, "/");
            if (strpos($url, '?')) // Check for URL arguments
                $url = strstr($url, '?', true);

            // If the end of the string is a slash, discard it
            $this->route = $url;
            while (substr($url, -1) == "/")
                $url = rtrim($url, "/");

            // Check for more than one subdirectory
            $slashCount = substr_count($url, '/');
            if ($slashCount > 1)
                throw new Exception("Only one nested subdirectory is supported.", 0);
            else
                $this->route = $url;

            // Incorrectly requested login attempt (potential inactivity log out)
            if (strpos($url, "login"))
                $this->route = "login";
        }

        // Match the requested file within the file system, if the request wasn't nested test it as a string
        if (!is_array($this->route))
        {
            // NOTE: Despite being an outdated practice, a lot of clients still request the favicon.ico directly from
            // the site's root (website.com_favicon.ico), simple map the request to the appropriate location for
            // static content
            // if ($this->route == "favicon.ico")
            if (file_exists(CONTROLLERS_DIR.$this->route.".php"))
                $this->requestedFile = $this->route.".php";
            else
                throw new Exception("File: ". $this->route.".php" ." does not exist.", 404);
        }
        else
        {
            // First item is an empty string, second item is the subfolder
            // third item is the target source file.
            if (count($this->route) > 2)
                throw new Exception("URL nesting can't go deeper than 1 directory.", 0);

            if (file_exists(CONTROLLERS_DIR.$this->route[0])) // Check if the subfolder exists
            {
                if ($this->route[1] != "" && $this->route[1] != "/") // Check for empty or wrongly formatted string
                {
                    // Check if the file requested exists
                    if (file_exists(CONTROLLERS_DIR.$this->route[0]."/".$this->route[1].".php"))
                        $this->requestedFile = $this->route[0]."/".$this->route[1].".php";
                    else
                        throw new Exception("Nested file: ". $this->route[1].".php" ." does not exist.", 404);
                }
            }
            else
                throw new Exception("Subfolder: ". $this->route[0]." does not exist.", 404);

            throw new Exception("URL route invalid.", 0);
        }

        // Extra client data
        $this->userAgent = $_SERVER['HTTP_USER_AGENT']??NULL;

        // Get the request type and arguments
        if ($_SERVER['REQUEST_METHOD'] == 'GET')
        {
            $this->requestType = 'GET';
            $this->GET = $_GET;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->requestType = 'POST';
            $this->GET = $_GET;
            $this->POST = $_POST;
        }

        // Check the requests protocol
        if ((!empty($_SERVER['HTTPS'])) && $_SERVER["HTTPS"] != 'off')
        {
            $this->https = true;
        }
        // Check the session status
        if (SESSIONS_ENABLED && isset($_SESSION["ID"]))
        {
            // Check the account login status, if there was a succesful login attempt while the account was already in use by
            // a different system the account will be flagged as offline despite being currently in use, if this is the case
            // then original session should be forcefully expired to reflect the fact that the account is now logged off
            $db = (new SQLiteConnection())->getDB();
            if ($db != NULL)
            {
                $result = $db->prepare("SELECT logged_in FROM accounts WHERE email=:email LIMIT 1");
                $result->bindParam(":email", $_SESSION["email"]);
                $result->execute();
                $data = $result->fetchAll();
                if ($data && $data[0][0] == 1)
                {
                    //print("<br> VALID SESSION");
                }
                else
                {
                    session_destroy();
                    header("Location: ".PROJECT_URL."login");
                    exit();

                }
            }


            $this->sessionStatus = SESSION_STATUS::VALID_SESSION;
            $this->accountID = $_SESSION["ID"];
            $this->accountEmail = $_SESSION["email"];
            $this->accountLastLogin = $_SESSION["last_login"];
            $this->accountType = $_SESSION["account_type"];
            $this->accountSessionTime = $_SESSION["session_start_time"];
            $this->accountLoginIP = $_SESSION["login_ip"];
            $this->accountSessionID = session_id();
            $this->accountUsername = $_SESSION["username"];

            // Check if the session is still valid
            if (isset($_SESSION["session_start_time"]) && null != SESSION_MAX_DURATION)
            {
                // Check if the last saved session activity time is bigger than the duration
                if ( (SESSION_MAX_DURATION > 0) && ((time() - (int)$_SESSION['session_inactivity_time']) > SESSION_MAX_DURATION) )
                {
                    // The client's session has expired, redirect to the login page

                    $db = (new SQLiteConnection())->getDB();
                    if ($db != NULL)
                    {
                        $result = $db->prepare("UPDATE accounts SET logged_in=0, logout_type='SESSION_MAX_DURATION' WHERE email=:email");
                        $result->bindParam(":email", $this->accountEmail);
                        $result->execute();
                    }

                    session_destroy();
                    header("Location: ".PROJECT_URL."login");
                    exit();
                }

            }
            // Check for inactivity timeouts
            if (isset($_SESSION['session_inactivity_time']) && null != SESSION_INACTIVITY_TOLERANCE)
            {
                // Check if the last saved session activity time is bigger than the duration
                if ( (SESSION_INACTIVITY_TOLERANCE > 0) && (time() - (int)$_SESSION['session_inactivity_time']) > SESSION_INACTIVITY_TOLERANCE)
                {
                    // The client's session has expired, redirect to the login page
                    $db = (new SQLiteConnection())->getDB();
                    if ($db != NULL)
                    {
                        $result = $db->prepare("UPDATE accounts SET logged_in=0, logout_type='SESSION_INACTIVITY' WHERE email=:email");
                        $result->bindParam(":email", $this->accountEmail);
                        $result->execute();
                    }

                    session_destroy();
                    header("Location: ".PROJECT_URL."login");
                    exit();
                }
            }
            // Update the inactivity timer
            $_SESSION["session_inacitivty_time"] = time();
        }
        else
            $this->sessionStatus= SESSION_STATUS::NO_SESSION;

        // Encapsulate all retrieved cookies
        $this->cookies = $_COOKIE;
    }

    // TRACK THE REQUESTS IP
    // R: string with IP
    private function getRequestIP() : ?string
    {
        // check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP']) && $this->validateIP($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check for IPs passing through proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // check if multiple ips exist in var
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
                $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($iplist as $ip) {
                    if ($this->validateIP($ip))
                        return $ip;
                }
            } else {
                if ($this->validateIP($_SERVER['HTTP_X_FORWARDED_FOR']))
                    return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED']) && $this->validateIP($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && $this->validateIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && $this->validateIP($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        if (!empty($_SERVER['HTTP_FORWARDED']) && $this->validateIP($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];

        // return unreliable ip since all else failed
        return $_SERVER['REMOTE_ADDR'];
    }

    // NOTE 28/09/21
    // The geoip service reached it's limit and now throws undefined value warnings
    // either buy premium or find a way to get geolocation data from elsewhere

    // GET THE REQUEST'S LOCATION, REQUIRES CURL AND USES THE GEOPLUGIN WEB API
    // R: void
    // private function traceIP() : void
    // {
    //     // localhost request
    //     if ($this->ip == "127.0.0.1" || $this->ip == "::1")
    //         return;
    //     // Geoplugin Returns NULL if its requested to process localhost (127.0.0.1)
    //     $url = "http://www.geoplugin.net/json.gp?ip=".$this->ip;
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 2); //timeout in seconds
    //     $data = curl_exec($ch);
    //     curl_close($ch);
    //     // var_dump($data);

    //     $locationData = json_decode($data, true);
    //     if ($locationData && defined($locationData['geoplugin_countryName']) && !empty($locationData['geoplugin_countryName']))
    //         $this->ipCountry = $locationData['geoplugin_countryName'];
    // }

    // CHECKS FOR INVALID IP RANGES
    // R: bool
    private function validateIP($ip) : bool
    {
        if (strtolower($ip) === 'unknown')
            return false;

        // generate ipv4 network address
        $ip = ip2long($ip);

        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf('%u', $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) return false;
            if ($ip >= 167772160 && $ip <= 184549375) return false;
            if ($ip >= 2130706432 && $ip <= 2147483647) return false;
            if ($ip >= 2851995648 && $ip <= 2852061183) return false;
            if ($ip >= 2886729728 && $ip <= 2887778303) return false;
            if ($ip >= 3221225984 && $ip <= 3221226239) return false;
            if ($ip >= 3232235520 && $ip <= 3232301055) return false;
            if ($ip >= 4294967040) return false;
        }
        return true;
    }
}
