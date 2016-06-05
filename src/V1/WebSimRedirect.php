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

//include_once dirname(__FILE__) . "/../../lib/Snoopy.class.php";
require_once dirname(__FILE__) . '/../../lib/simple_html_dom.php';

use SOURCEPATH\V1\Models\MainModel;
use SOURCEPATH\V1\Models\SimHelper;


$app->get('/sim/captcha', function() use ($app) {
    $captchaurl = 'http://182.71.87.38/iSIM/Student/capimage';
    $loginurl = 'http://182.71.87.38/iSIM/Login';
    $simHelper = new SimHelper();
    $result = $simHelper->login_get($app,$loginurl, $captchaurl);
    if (isset($result['error'])) {
        MainModel::resultreached($app, 'Error in Captcha.Please Retry', TRUE);
    } else {
        $app->render(200, ["params"=>$result]);
    }
});

$app->post('/sim/login', function() use ($app) {
    // check for required params
    $para = ['username', 'password', 'captcha'];
    $result1 = MainModel::verifyRequiredParams($para);
    if ($result1 != 'done') {
        MainModel::resultreached($app, $result1, true);
    }
    $user = $app->request->post('username');
    $pass = $app->request->post('password');
    $captcha = $app->request->post('captcha');
    $loginurl = 'http://182.71.87.38/iSIM/Login';
    $simHelper = new SimHelper();
    $params;//get parameters from post request
    $loginparm = $simHelper->create_login_params($params, $user, $pass, $captcha);
    $result = $simHelper->login_post($loginurl, $loginparm);
    if ($result == false) {
        MainModel::resultreached($app, 'Error Logging in.Please Retry', TRUE);
    } else {
        $app->render(200, $result);
    }
});
