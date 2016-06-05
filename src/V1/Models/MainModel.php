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

namespace SOURCEPATH\V1\Models;

require_once 'DbHandler.php';
require_once 'PassHash.php';

/**
 * Description of MainModel
 *
 * @author aman
 */
class MainModel {

    /**
     * Verifying required params posted or not
     */
    static function verifyRequiredParams($required_fields) {
        $error = false;
        $error_fields = "";
        // $request_params = array();
        $request_params = $_REQUEST;
        // Handling PUT request params
        if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $app = \Slim\Slim::getInstance();
            parse_str($app->request()->getBody(), $request_params);
        }
        foreach ($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                $error = true;
                $error_fields .= $field . ', ';
            }
        }
        if ($error) {
            // Required field(s) are missing or empty
            return 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        } else {
            return 'done';
        }
    }

    static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Email address is not valid';
        } else {
            return 'done';
        }
    }

    static function writelog($app, $message) {
        $app->log->addDebug($message, array($app->request()));
    }

    static function resultreached($app, $result, $error) {
        if ($error != FALSE) {
            MainModel::writelog($app, 'failed:' . $result);
            $app->render(400, array('error' => TRUE, 'result' => $result));
            die();
        } else {
            MainModel::writelog($app, 'Success:' . $result);
            $app->render(200, array('error' => FALSE, 'result' => $result));
        }
    }

    static function addresult($result, $string, $text) {
        if ($result) {
            $string.=$text;
        } else {
            $string.=$text . ' can\'t be ';
        }
        return $string;
    }
}
