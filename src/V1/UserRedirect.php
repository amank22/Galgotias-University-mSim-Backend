<?php

/*
 * The MIT License
 *
 * Copyright 2015 aman.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

use SOURCEPATH\V1\Models\MainModel;
use SOURCEPATH\V1\Models\DbHandler;

/**
 * User Registration
 * url - /register
 * method - POST
 * params - name, email, password
 */

$app->map('/user/register', function () use ($app) {
    if ($app->request()->isPost()) {
        $app->pass();
    } else {
        MainModel::resultreached($app, 'Please send a post request with proper parameters', true);
    }
})->via('GET', 'POST');

$app->post('/user/register', function() use ($app) {
    // check for required params
    $result1 = MainModel::verifyRequiredParams(array('name', 'email', 'password'));
    if ($result1 != 'done') {
        MainModel::resultreached($app, $result1, true);
    }
    // reading post params
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $password = $app->request->post('password');
    // validating email address
    $result = MainModel::validateEmail($email);
    if ($result != 'done') {
        MainModel::resultreached($app, $result, true);
    }
    $db = new DbHandler();
    $res = $db->createUser($name, $email, $password);
    if ($res == USER_CREATED_SUCCESSFULLY) {
            $response['error'] = false;
            $response['message'] = 'Registration Success';
            $response['name'] = $name;
            $response['email'] = $email;
            MainModel::writelog($app, 'Success:' . implode('-', $response));
            $app->render(200, $response);
    } else if ($res == USER_CREATE_FAILED) {
        MainModel::resultreached($app, 'User Creation Failed', true);
    } else if ($res == USER_ALREADY_EXISTED) {
        MainModel::resultreached($app, 'User Already Exists', false);
    }
});


/**
 * User Login
 * url - /login
 * method - POST
 * params - email, password
 */
$app->post('/user/login', function() use ($app) {
    // check for required params
    $a = MainModel::verifyRequiredParams(array('email', 'password'));
    if ($a != 'done') {
        MainModel::resultreached($app, $a, true);
    }
    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();
    $db = new DbHandler();
    // check for correct email and password
    $result = $db->checkLogin($email, $password);
    if ($result == USER_LOGEDIN_SUCCESSFULLY) {
        // get the user by email
        $user = $db->getUserByEmail($email);
        if ($user != NULL) {
            $response["error"] = false;
            $response['name'] = $user['name'];
            $response['email'] = $user['email'];
            $response['apiKey'] = $user['api_key'];
            MainModel::writelog($app, 'Success:' . $result);
            $app->render(200, $response);
        } else {
            // unknown error occurred
            $response['error'] = true;
            $response['message'] = "An error occurred.System Error.Please try again";
            MainModel::resultreached($app, $response['message'], $response["error"]);
        }
    } else if ($result == USER_LOGIN_INCORRECT) {
        // user credentials are wrong
        $response['error'] = false;
        $response['message'] = 'Login failed. Incorrect credentials';
        MainModel::resultreached($app, $response['message'], $response["error"]);
    } else if ($result == USER_NOT_EXIST) {
        // user credentials are wrong
        $response['error'] = false;
        $response['message'] = 'Login failed. User doesnot exist';
        MainModel::resultreached($app, $response['message'], $response["error"]);
    } else {
        $response['error'] = true;
        $response['message'] = 'Login failed.System Error';
        MainModel::resultreached($app, $response['message'], $response["error"]);
    }
});

$app->post('/student/register', function() use ($app) {
    // check for required params
    $result1 = MainModel::verifyRequiredParams(array('adm_no','name','email','gcm_id'));
    if ($result1 != 'done') {
        MainModel::resultreached($app, $result1, true);
    }
    // reading post params
    $name = $app->request->post('name');
    $email = $app->request->post('email');
    $adm_no = $app->request->post('adm_no');
    $gcm_id = $app->request->post('gcm_id');
    
    $db = new DbHandler();
    $res = $db->registerStudent($adm_no,$name, $email, $gcm_id);
    if ($res == USER_CREATED_SUCCESSFULLY) {
            $response['error'] = false;
            $response['message'] = 'Student GCM Registration Success';
            MainModel::writelog($app, 'Success GCM:' . implode('-', $response));
            $app->render(200, $response);
    } else if ($res == USER_CREATE_FAILED) {
        MainModel::resultreached($app, 'Student GCM Registration Failed', true);
    } else if ($res == USER_ALREADY_EXISTED) {
        MainModel::resultreached($app, 'Student Already Registered', false);
    }
});