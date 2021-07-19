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

// -------------------------------------------------------------- //
// PHP7 requires the strict type declaration to be the very first //
// thing to be included in a script                               //
// -------------------------------------------------------------- //
declare(strict_types=1);

// ------- NON STATIC REQUEST START HERE -------
// THE POCKET_PHP FRAMEWORK IS CONFIGURED FROM THIS SELF DOCUMENTING FILE
require_once("configure.php"); // <-------------
// ---------------------------------------------

include(CORE_DIR."HTTPRequest.php");
include(CORE_DIR."templateEngine.php");
include(CORE_DIR."database.php");


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
        throw new Exception ("You are banned.", 202);


    //  The request is a login attempt
    if ($request->requestedFile == (LOGIN_CONTROLLER) )
    {
        // Allow users to login/out only if sessions are enabled
        if (!SESSIONS_ENABLED)
            throw new Exception("Sessions are currently disabled", 404);

        // Client is already logged in
        if ($request->sessionStatus == SESSION_STATUS::VALID_SESSION)
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);

        // No valid session for this client
        else if ($request->sessionStatus == SESSION_STATUS::NO_SESSION)
        {
            // Login attempts must be sent by POST
            if ($request->requestType != "POST")
            {
                // If the request is GET but empty it's most likely a request for the login page
                // and not an error
                if (empty($request->GET["email"]) || empty($request->GET["password"]))
                    dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
                // If it's a GET login form with actual data it's definately a script trying to
                // fug with the server, throw an error and call the cyber police
                $request->errorMsg = "Login forms must be submitted as POST requests.";
                dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
                // throw new Exception ("Login data must be sent as a POST request.", 404);
            }

            if (ENFORCE_LOGIN_CAPTCHA)
            {
            // Check if the captcha is set / included, $_SESSION['captcha'] is set
            // by calling PROJECT_URL/getCaptcha to prevent the user from skipping
            // the login page using a script
                if (empty($request->POST["login_captcha"]) || !isset($_SESSION["login_captcha"]))
                {
                    $request->errorMsg = "Captcha must be solved.";
                    session_destroy();
                    dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
                    exit();
                }
            }
            if (!empty($request->POST["email"]) && !empty($request->POST["password"]))
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
                    $request->accountSessionID = $loginRequest->accountSessionID;
                    // Pass control to the login controller, now with valid login data
                    dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
                }
            }
        }
        // Login request is empty
        else
        {
            $request->errorMsg = "Both email and password must be provided.";
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $request);
        }
    }

    // Client requests to logout, destroy session data and redirect to homepage
    if ( $request->requestedFile == (LOGOUT_CONTROLLER))
    {
        // Allow users to login/out only if sessions are enabled
        if (!SESSIONS_ENABLED)
            throw new Exception("Sessions are currently disabled", 404);

        // If the user has an active session update the DB before destroying it
        if ($request->sessionStatus == SESSION_STATUS::VALID_SESSION)
        {
            // Update the db to reflect the account is logged out and available for logging in
            $db = (new SQLiteConnection())->getDB();
            if ($db != NULL)
            {
                $result = $db->prepare("UPDATE accounts SET logged_in=0, logout_type='CLEAN_LOGOUT' WHERE email=:email");
                $result->bindParam(":email", $request->accountEmail);
                $result->execute();
            }
        }

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
    include(CONTROLLERS_DIR.$file);
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
    $email = $requestData->POST["email"];
    $password = $requestData->POST["password"];
    $captcha = $requestData->POST["login_captcha"];

    // Only process the captcha if ENFORCE_LOGIN_CAPTCHA is set to true and the captcha isn't empty
    if (ENFORCE_LOGIN_CAPTCHA)
    {
        if (!empty($captcha))
        {
            // Validate captcha
            if ($_SESSION["login_captcha"] != $captcha)
            {
                $requestData->errorMsg = "Invalid captcha.";
                session_destroy();
                dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
                exit();
            }
        }
        else
        {
            $requestData->errorMsg = "Captcha can't be empty.";
            session_destroy();
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
            exit();
        }

    }

    // No empty fields
    if (!empty($email) && !empty($password))
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $verification = validateLoginData($requestData);
            if ($verification != NULL) // Login successful
            {

                if ($verification->accountLoggedIn == 1) // Account is flagged as already online
                {
                    // Delete the original session file (stored in the database on successful login)
                    if (file_exists(SESSIONS_SAVE_PATH."/sess_".$verification->accountSessionID))
                        unlink(SESSIONS_SAVE_PATH."/sess_".$verification->accountSessionID);

                    // Flag the account as logged off
                    $db = (new SQLiteConnection())->getDB();
                    if ($db != NULL)
                    {
                        // Keep the account as logged in if the session is to be hijacked
                        //$result = $db->prepare("UPDATE accounts SET logged_in=0, logout_type='SESSION_HIJACKED' WHERE email=:email");
                        $result = $db->prepare("UPDATE accounts SET logout_type='SESSION_HIJACKED' WHERE email=:email");
                        $result->bindParam(":email", $verification->accountEmail);
                        $result->execute();
                    }

                    // If control flow continues past this function, the session will be hijacked by the new SID
                    // $requestData->errorMsg = "Account already in use.";
                    // session_destroy();
                    // dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
                    // exit();
                }

                $verification->accountSessionID = session_id();

                // These variables are to be included in the client's HTTPRequest instance and
                // stored in a session file in the directory specified by SESSION_SAVE_PATH
                // as sess_.session_id()
                $_SESSION["ID"] = $verification->accountID;
                $_SESSION["email"] = $verification->accountEmail;
                $_SESSION["last_login"] = $verification->accountLastLogin;
                $_SESSION["account_type"] = $verification->accountType;
                $_SESSION["login_ip"] = $verification->accountLoginIP;
                $_SESSION["session_start_time"] = $verification->accountSessionTime;
                $_SESSION["session_inactivity_time"] = $verification->accountInactivityTime;
                $_SESSION["solved_captcha"] = (ENFORCE_LOGIN_CAPTCHA ? $captcha : "");
                $verification->sessionStatus = SESSION_STATUS::VALID_SESSION;

                // During a session hijack, the session ID in the validateLoginData return object contains
                // the SID of the session that got hijacked, this is so the hijack procedure can manually
                // delete the session file
                $_SESSION["session_id"] = $verification->accountSessionID;

                return $verification;
            }
            else
            {
                $requestData->errorMsg = "Wrong email or password.";
                session_destroy();
                dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
                exit();
            }
        }
        else // Invalid form data
        {
            $requestData->errorMsg = "Invalid email or password.";
            session_destroy();
            dispatchRequest(LOGIN_CONTROLLER, CONTROLLER_ENTRY_FUNCTION, $requestData);
            exit();
        }
    }
    else // Missing login data
    {
        $requestData->errorMsg = "Please log in.";
        session_destroy();
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
        $result->bindparam(":vemail", $requestData->POST["email"]);
        $result->bindparam(":vpassword", $requestData->POST["password"]);
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
            $requestData->accountLoggedIn = $resultData[0]["logged_in"];
            $requestData->accountLogoutType = $resultData[0]["logout_type"];
            $requestData->accountSessionID = $resultData[0]["session_id"];

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
        if ($requestData->GET != NULL && is_array($requestData->GET))
        {
            $argumentsStr = "";
            foreach ($requestData->arguments as $key => $value)
            {
                $argumentsStr .= $key. "=>" .$value. ",";
            }
        }

        $result = $sqlite->prepare("INSERT INTO requests (ip, route, arguments, country, session_id) VALUES (:ip, :route, :arguments, :country, :session_id)");
        $result->bindparam(":ip", $requestData->ip);
        $result->bindparam(":route", $requestData->route);
        $result->bindparam(":arguments", $argumentsStr);
        $result->bindparam(":country", $requestData->ipCountry);
        $sid = session_id();
        $result->bindparam(":session_id", $sid);
        $result->execute();
    }
}

// UPDATE THE CLIENT'S LOGIN DATA TO THE DATABASE
// R: void
function updateAccountLogin($requestData) : void
{
    $sqlite = (new SQLiteConnection())->getDB();
    if ($sqlite != NULL)
    {
        $result = $sqlite->prepare("UPDATE accounts SET last_login=:last_login, last_login_ip=:last_login_ip, logged_in=1, session_id=:session_id, session_timestamp=:session_timestamp WHERE email=:email");
        $now = date('H:i:s m/d/y');
        $result->bindparam(":email", $requestData->accountEmail);
        $result->bindparam(":last_login", $now);
        $result->bindparam(":last_login_ip", $requestData->accountLoginIP);
        $result->bindparam(":session_timestamp", $requestData->accountSessionTime);
        $sid = session_id();
        $result->bindparam(":session_id", $sid);
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
