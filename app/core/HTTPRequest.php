
<?php

require_once(__DIR__."/../configure.php");

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
    public $ip = NULL;
    public $userAgent = NULL;
    public $ipCountry = NULL;
    public $route = NULL;
    public $arguments = NULL;
    public $requestType = NULL;
    public $requestedFile = NULL;
    public $sessionStatus = NULL;
    public $errorMsg = NULL; // To be filled by index and subsequently processed by the controller
    public $https = false;

    // These variabled are populated by the validateLoginAttempt function
    public $accountLastLogin = NULL;
    public $accountType = NULL;
    public $accountID = NULL;
    public $accountEmail = NULL;

    // EXTRACT AND PROCESS THE REQUESTS CONTENTS, ENSURE THE CONTROLLER FILE EXISTS
    // AND THE REQUEST'S VALIDITY
    function __construct($url)
    {
        // Validate and track the client's IP
        $this->ip = $this->getRequestIP();
        $this->ipCountry = $this->traceIP();

        // Process the requested URL
        // If the request is empty (servername.com/)
        if ($url == "/")
            $this->route = HOMEPAGE; // defined in configure.php
        else if (strpos($url, "robots.txt")) // Request is for robots.txt (probably a webbot)
        {
            // Redirect to the actual location of the robots.txt file so the webserver can serve it directly
            header("Location: ". PROJECT_URL.ROBOTS_TXT);
            exit();
        }

        // NOTE ABOUT MANUAL SSL CERTIFICATE AUTHENTICATION
        // Manually authenticating SSL certificates usually means returning a (provided) string from a very specific
        // server URL. Some sites (like ZeroSSL) will attempt to verify these strings through http://www.yourserver.com plus the ".well-known/acme-chellenge/"
        // specifier. THIS CAN FAIL IF YOUR SERVER ONLY HAS A VER 6 IP OR HAVEN'T CONFIGURED YOUR DNS REDIRECTS. /etc/nginx/sites-available/ must also be configured!
        // If you only have an IPv6 make sure to delete the AAAA records to allow for IPv4 HTTP + www extension to work.
        else if (strpos($url, ".well-known/acme-challenge/cR-7K-MrtkMjYJoknircDb-jugs8FfAxmRUXm5YsJWw")) // Request is for SSL certificate validation
        {
            // Redirect to the actual location of the robots.txt file so the webserver can serve it directly
            //header("Location: ". PROJECT_URL.SSL_VER_1_TXT);
            echo (SSL_VER_1_TXT);
            exit();
        }
        else if (strpos($url, ".well-known/acme-challenge/lvN5xeu63t-CxVEtDbUCuXNOT9kQIHud7ZPoFGFvImo")) // Request is for SSL certificate validation
        {
            // Redirect to the actual location of the robots.txt file so the webserver can serve it directl
            // header("Location: ". PROJECT_URL.SSL_VER_2_TXT);
            echo (SSL_VER_2_TXT);
            exit();
        }

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


            if (file_exists(CONTROLLERS.$this->route.".php"))
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

            if (file_exists(CONTROLLERS.$this->route[0])) // Check if the subfolder exists
            {
                if ($this->route[1] != "" && $this->route[1] != "/") // Check for empty or wrongly formatted string
                {
                    // Check if the file requested exists
                    if (file_exists(CONTROLLERS.$this->route[0]."/".$this->route[1].".php"))
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
            $this->arguments = $_GET;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $this->requestType = 'POST';
            $this->arguments = $_POST;
        }

        // Check the requests protocol
        if ((!empty($_SERVER['HTTPS'])) && $_SERVER["HTTPS"] != 'off')
        {
            $this->https = true;
        }
        // Check the session status
        if (isset($_SESSION["ID"]))
        {
            $this->sessionStatus= SESSION_STATUS::VALID_SESSION;
            $this->accountID = $_SESSION["ID"];
            $this->accountEmail = $_SESSION["email"];
            $this->accountLastLogin = $_SESSION["last_login"];
            $this->accountType = $_SESSION["account_type"];

        }
        else
            $this->sessionStatus= SESSION_STATUS::NO_SESSION;
    }

    // TRACK THE REQUESTS IP
    // R: string with IP
    private function getRequestIP()
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

    // GET THE REQUEST'S LOCATION, REQUIRES CURL AND USES THE GEOPLUGIN WEB API
    // R: void
    private function traceIP()
    {
        // localhost request
        if ($this->ip == "127.0.0.1" || $this->ip == "::1")
            return;
        // Geoplugin Returns NULL if its requested to process localhost (127.0.0.1)
        $url = "http://www.geoplugin.net/json.gp?ip=".$this->ip;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2); //timeout in seconds
        $data = curl_exec($ch);
        curl_close($ch);
        // var_dump($data);

        $locationData = json_decode($data, true);
        if ($locationData && $locationData['geoplugin_countryName'] != null)
            $this->ipCountry = $locationData['geoplugin_countryName'];
    }

    // CHECKS FOR INVALID IP RANGES
    // R: bool
    private function validateIP($ip)
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
