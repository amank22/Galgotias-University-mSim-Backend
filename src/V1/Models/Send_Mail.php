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

use Mandrill;
/**
 * Description of Send_Mail
 *
 * @author aman
 */
use SOURCEPATH\V1\Models\MainModel;

class Send_Mail {

    static function Send_Mail($to, $subject, $body, $user, $template, $async = false) {
        $url = parse_url(getenv("DATABASE_URL"));
        $api = $url["MANDRILL_APIKEY"];
        $from = "noreply@donatio.com";
        try {
            $mandrill = new Mandrill($api);
            $message = array(
                'subject' => $subject,
                'from_email' => $from,
                'from_name' => 'Donatio',
                'to' => array(
                    array(
                        'email' => $to,
                        'name' => $user,
                        'type' => 'to'
                    )
                ),
                'important' => true,
                'tags' => array('register'),
                'merge_vars' => array(array(
                        'rcpt' => $to,
                        'vars' =>
                        array(
                            array(
                                'name' => 'USERNAME',
                                'content' => $user)
            ))));
            $template_content = array(
                array(
                    'name' => 'main',
                    'content' => 'Hi *|USERNAME|* ,<br/>' . $body),
                array(
                    'name' => 'header',
                    'content' => $subject)
            );

            $ip_pool = 'Main Pool';
            $result=$mandrill->messages->sendTemplate($template, $template_content, $message, $async, $ip_pool);
            $app = \Slim\Slim::getInstance();
            MainModel::writelog($app, 'Success:Mail Send-->'.$result);
           // print_r($result);
        } catch (Mandrill_Error $e) {
            // Mandrill errors are thrown as exceptions
            echo $e->getMessage();
            $app = \Slim\Slim::getInstance();
            MainModel::resultreached($app, 'A mail error occurred: '
                    . get_class($e) . ' - ' . $e->getMessage(), true);
        }
    }

}
