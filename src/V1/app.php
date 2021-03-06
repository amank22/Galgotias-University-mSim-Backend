<?php

error_reporting(E_ALL);
ini_set("display_errors","On");

use SOURCEPATH\V1\Models\MainModel;
use SOURCEPATH\V1\Models\DbHandler;

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $app = \Slim\Slim::getInstance();
    $headers = $app->request->headers("Authorization");
    $response = array();

    // Verifying Authorization Header
    if (isset($headers)) {
        $db = new DbHandler();

        // get the api key
        $api_key = $headers;
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            MainModel::resultreached($app, $response["message"], $response["error"]);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL) {
                $user_id = $user["id"];
            }
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        MainModel::resultreached($app, $response["message"], $response["error"]);
        $app->stop();
    }
}


require_once 'UserRedirect.php';
require_once 'NewsRedirect.php';
require_once 'WebSimRedirect.php';
