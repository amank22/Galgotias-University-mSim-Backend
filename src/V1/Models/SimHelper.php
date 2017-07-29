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

use SOURCEPATH\V1\Models\Snoopy;

class SimHelper {

    function __construct() {
        
    }

    function init($cookies = NULL) {
        $snoopy = new Snoopy;
        $snoopy->maxredirs = 0;
        $snoopy->agent = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/50.0.2661.102 Chrome/50.0.2661.102 Safari/537.36";
        if ($cookies != NULL) {
            $cok=explode('~',$cookies);
            $snoopy->cookies[$cok[0]] = $cok[1];
        }
        return $snoopy;
    }

    function login_get($app, $loginurl, $captchaurl) {
        $result = [];
        $snoopy = $this->init();
        if (!($snoopy->fetch($loginurl))) {
            $result['error'] = $snoopy->error;
            return $result;
        }
        $cookies = $this->get_cookies_array($app, $snoopy->headers);
        $dom = str_get_html($snoopy->results);
        foreach ($dom->find('input') as $element) {
            if (!($element->name === 'btnValue')) {
                $result[$element->name . '~' . $element->title] = $element->value;
            }
        }
        $result['captcha'] = $this->captcha_get($app, $snoopy, $captchaurl);
        $result['cookies'] = $cookies;
        return [$result];
    }

    private function captcha_get($app, $snoopy, $url) {
        if ($snoopy->fetch($url)) {
            $imgbase64 = chunk_split(base64_encode(explode('<!DOCTYPE', $snoopy->results, 2)[0]));
            return $imgbase64;
        }
        return NULL;
    }

    function login_post($loginurl, $formvars, $cookies) {
        $snoopy = $this->init($cookies);
        if (!$snoopy->submittext($loginurl, $formvars)) {
//            return false;
        }
    return ["data"=>$snoopy->results.'-----------'.implode('()',$snoopy->spostrequest)];
////    $html = str_get_html($content);
////    update_states($html);
//    echo $content;
//        if ($snoopy->response_code == 302) {
//            return true;
//        }

        return false;
    }

    function create_login_params($login_g, $username, $password, $code) {
        foreach ($login_g as $key => $value) {
            $karr = explode('~', $key);
            if ($key == 'captcha' || $key == 'cookies') {
                //ignoring
            } elseif ($key == 'username' || $key == 'password') {
                
            } elseif ($karr[1] === 'User ID') {
                $loginparm[$karr[0]] = $username;
            } elseif ($karr[1] === 'Password') {
                $loginparm[$karr[0]] = $password;
            } elseif (substr($karr[0], 0, 3) === 'txt') {
                $loginparm[$karr[0]] = $code; //captcha
            } else {
                $loginparm[$karr[0]] = $value;
            }
        }
        return $loginparm;
    }

//    private function update_states($dom) {
//        $states=[];
//        // Find all inputs
//        foreach ($dom->find('input') as $element) {
//            if ($element->name === '__VIEWSTATE' || $element->name === '__EVENTVALIDATION') {
//                $states[$element->name] = $element->value;
//            }
//        }
//        return $states;
//    }

    private function get_cookies_array($app, $headers) {
        $cookies = [];
        $values = '';
        while (list($key, $val) = each($headers)) {
            $values.=' ' . $val;
            if (strpos($val, 'Set-Cookie') !== false) {
                $va = str_replace('Set-Cookie:', '', $val);
                $c = array_slice(explode(";", $va), 0, -2);
                foreach ($c as $value) {
                    $pair = explode('=', $value);
                    $cookies[trim($pair[0])] = trim($pair[1]);
                }
            }
        }
        return $cookies;
    }

}
