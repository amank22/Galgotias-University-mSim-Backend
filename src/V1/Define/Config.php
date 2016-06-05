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
/**
 * Database configuration
 */
$url=parse_url(getenv("JAWSDB_URL"));
$user=$url["user"];
$pass=$url["pass"];
$host=$url["host"];
$port=$url["port"];
$dbname=substr($url["path"],1);
define('DB_USERNAME', $user);
define('DB_PASSWORD', $pass);
define('DB_HOST', "mysql:host=$host;");
define('DB_PORT', "port=$port");
define('DB_NAME', "dbname=$dbname;");

define('USER_LOGEDIN_SUCCESSFULLY', 0);
define('USER_NOT_EXIST', 1);
define('USER_LOGIN_INCORRECT', 2);

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);