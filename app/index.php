<?php
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
// Author:  AlexHG @ XENOBYTE.XYZ
// License: MIT License
// Website: https://XENOBYTE.XYZ

// -------------------------------------------------------------- //
// PHP7 requires the strict type declaration to be the very first //
// thing to be included in a script                               //
// -------------------------------------------------------------- //
declare(strict_types=1);
session_start();

// ------- NON STATIC REQUEST START HERE -------
// THE POCKET_PHP FRAMEWORK IS CONFIGURED FROM THIS SELF DOCUMENTING FILE
require_once("configure.php"); // <-------------
// ---------------------------------------------

include(CORE."HTTPRequest.php");
include(CORE."templateEngine.php");
include(CORE."database.php");


// MAKE SURE TO CONFIGURE YOUR WEB SERVER TO SERVE REQUESTS ASKING FOR
// STATIC CONTENT DIRECTLY (SEE INCLUDED NGINX CONFIGURATION)
// R: void
processRequest();
function processRequest() : void
{
    $request = new HTTPRequest($_SERVER["REQUEST_URI"]);

    // If FORCE_HTTPS is enabled redirect the client to the HTTPS port
    if (FORCE_HTTPS && !$request->https)
    {
        $HTTPS_URL = str_replace('http://', 'https://', PROJECT_URL);
        header("Location: ".$HTTPS_URL);
    }
    if (TRACK_REQUESTS)
        saveRequest($request);
    if (ENFORCE_BANS && checkForBan($request->ip))
        throw new Exception ("You are banned.", 666);

    //  The request is a login attempt
    if ( $request->requestedFile == (LOGIN_CONTROLLER))
    {
        // Client is already logged in
        if ($request->sessionStatus == SESSION_STATUS::VALID_SESSION)
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);

        // No valid session for this client
        else if ($request->sessionStatus == SESSION_STATUS::NO_SESSION)
        {
            if (!empty($request->arguments["email"]) && !empty($request->arguments["password"]))
            {
                // Validate the provided login data
                $loginRequest = validateLogInAttempt($request);
                // Successfully logged in, add login data to the HTTPRequest object
                if ($loginRequest != NULL)
                {
                    $request->accountID = $loginRequest->accountID;
                    $request->accountEmail = $loginRequest->accountEmail;
                    $request->accountLastLogin = $loginRequest->accountLastLogin;
                    $request->accountType = $loginRequest->accountType;
                    // Pass control to the login controller, now with valid login data
                    dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
                }
            }
            // Login request is empty
            else
            {
                $request->errorMsg = "Both email and password must be provided.";
                dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
            }
        }
    }
    // Client requests to logout, destroy session data and redirect to homepage
    if ( $request->requestedFile == (LOGOUT_CONTROLLER))
    {
        session_destroy();
        header("Location: ".PROJECT_URL."login");
        exit();
    }
    // The request isn't a login or logout attempt, its not a static file and references
    // an existing controller in the controllers folder, call the controller's entry function
    dispatchRequest($request->requestedFile, CONTROLLER_ENTRY_FUNCTION, $request);
}


// ROUTES THE PROCESSED REQUEST TO ITS RESPECTIVE CONTROLLER
// THERE MUST BE A PHP FILE WITH A MATCHING NAME TO THE
// REQUESTED FILE. FOR EXAMPLE, LOCALHOST/HOME WOULD ROUTE
// TO A APP/CONTROLLERS/ROUTE.PHP
// THIS IS THE FINAL STEP IN THE RESPONSE PROCESS AND WILL
// END THE SCRIPT IF THE CONTROLLER DOESN'T
// R: void
function dispatchRequest($file, $function, $parameters) : void
{
    include(CONTROLLERS.$file);
    if (empty($function))
        call_user_func($file, $parameters);
    else
        call_user_func($function, $parameters);

    exit();
}

// VALIDATES A LOGIN ATTEMPT, APPROPRIATELY REDIRECTS THE CLIENT AND KILLS THE SCRIPT
// R: void
function validateLoginAttempt($requestData) : ?HTTPRequest
{
    $email = $requestData->arguments["email"];
    $password = $requestData->arguments["password"];

    // No empty fields
    if (!empty($email) && !empty($password))
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $verification = validateLoginData($requestData);
            if ($verification != NULL) // Login successful
            {
                // These variables are to be included in the HTTPRequest instance
                // You can add or remove them here
                $_SESSION["ID"] = $verification->accountID;
                $_SESSION["email"] = $verification->accountEmail;
                $_SESSION["last_login"] = $verification->accountLastLogin;
                $_SESSION["account_type"] = $verification->accountType;
                $_SESSION["login_ip"] = $verification->accountLoginIP;
                $_SESSION["session_start_time"] = $verification->accountSessionTime;
                $_SESSION["session_inactivity_time"] = $verification->accountInactivityTime;
                $verification->sessionStatus = SESSION_STATUS::VALID_SESSION;

                return $verification;
            }
            else
            {
                $requestData->errorMsg = "Wrong email or password.";
                dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
                exit();
            }
        }
        else // Invalid form data
        {
            $requestData->errorMsg = "Invalid email or password.";
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
            exit();
        }
    }
    else // Missing login data
    {
        $requestData->errorMsg = "Please log in.";
        dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
        exit();
    }

}

// VALIDATES THE LOGIN DATA WITH THE SAVED DATA IN THE INTERNAL DATABASE, "accounts" TABLE
// R: HTTPRequest with added data or NULL on error
function validateLoginData($requestData) : ?HTTPRequest
{
    $sqlite = new SQLiteConnection();
    $sqlite = $sqlite->getDB();
    if ($sqlite != NULL)
    {
        $result = $sqlite->prepare("SELECT * FROM accounts WHERE password=:vpassword AND email=:vemail LIMIT 1");
        $result->bindparam(":vemail", $requestData->arguments["email"]);
        $result->bindparam(":vpassword", $requestData->arguments["password"]);
        $result->execute();
        $resultData = $result->fetchAll();
        if (!empty($resultData))
        {
            $requestData->accountLastLogin = $resultData[0]["last_login"];
            $requestData->accountType = $resultData[0]["account_type"];
            $requestData->accountID = $resultData[0]["id"];
            $requestData->accountEmail = $resultData[0]["email"];
            $requestData->accountLoginIP = $requestData->ip;
            $requestData->accountSessionTime = time();
            $requestData->accountInactivityTime = time();

            // Update the account with the latest login time
            updateAccountLogin($requestData);
            return $requestData;
        }
        else
        {
            print("ERROR");
            return NULL;
        }
    }
    else
        return NULL;

}

// SAVE THE CLIENT'S REQUEST TO THE DATABASE (TRACK_REQUESTS MUST BE SET TO TRUE)
// R: void
function saveRequest($requestData) : void
{
    $sqlite = new SQLiteConnection();
    $sqlite = $sqlite->getDB();
    if ($sqlite != NULL)
    {
        if ($requestData->arguments != NULL && is_array($requestData->arguments))
        {
            $argumentsStr = "";
            foreach ($requestData->arguments as $key => $value)
            {
                $argumentsStr .= $key. "=>" .$value. ",";
            }
        }

        $result = $sqlite->prepare("INSERT INTO requests (ip, route, arguments, country) VALUES (:ip, :route, :arguments, :country)");
        $result->bindparam(":ip", $requestData->ip);
        $result->bindparam(":route", $requestData->route);
        $result->bindparam(":arguments", $argumentsStr);
        $result->bindparam(":country", $requestData->ipCountry);
        $result->execute();
    }
}

// UPDATE THE CLIENT'S LOGIN DATA TO THE DATABASE
// R: void
function updateAccountLogin($requestData) : void
{
    $sqlite = new SQLiteConnection();
    $sqlite = $sqlite->getDB();
    if ($sqlite != NULL)
    {
        $result = $sqlite->prepare("UPDATE accounts SET last_login=:last_login, last_login_ip=:last_login_ip WHERE email=:email");
        $now = date('H:i:s m/d/y');
        $result->bindparam(":email", $requestData->accountEmail);
        $result->bindparam(":last_login", $now);
        $result->bindparam(":last_login_ip", $requestData->accountLoginIP);
        $result->execute();
    }
}

// CHECK IF THE REQUEST'S IP IS IN THE BAN LIST
// R: bool
function checkForBan($ip) : bool
{
    $sqlite = new SQLiteConnection();
    $sqlite = $sqlite->getDB();
    if ($sqlite != NULL)
    {
        $query = $sqlite->prepare("SELECT * from banned WHERE ip=:ip LIMIT 1");
        $query->bindparam(":ip", $ip);
        $query->execute();
        $resultData = $query->fetchAll();
        if (!empty($resultData))
        {
            if ($resultData[0]["active"] == 1)
                return true;
            else
                return false;
        }
    }
}
