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

use PDO;
use SOURCEPATH\V1\Models\Send_Mail;
use SOURCEPATH\V1\Models\PassHash;

/**
 * Description of DbHandler
 *
 * @author aman
 */
class DbHandler {

    private $conn;

    function __construct() {
        // opening db connection
        $this->conn = DBConnect::getdb();
    }

    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($name, $email, $password) {
        // First check if user already existed in db
        if (!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
            // Generating API key
            $api_key = $this->generateApiKey();
            // insert query
            $params = array(':name' => $name, ':email' => $email,
                ':ph' => $password_hash, ':apik' => $api_key);
            $stmt = $this->conn->prepare('INSERT INTO users(name, email, password_hash, api_key)'
                    . ' values(:name, :email, :ph, :apik)');
            $result = $stmt->execute($params);
            // Check for successful insertion
            if ($result) {
                // $base_url = $_SERVER['HTTP_HOST'];
                // $to = $email;
                // $subject = "G-Quasar Registration";
                // $body = 'You have been registered with GU mSim App Panel.</br>You can now login with following
                //  credentials:</br>username:'.$email.'</br>password:'.$password.'</br>';
                // Send_Mail::Send_Mail($to, $subject, $body, $name, 'register');
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }
    }
    
    
    /**
     * Creating new Student
     * @param String $adm_no Student Admission
     * @param String $name Student full name
     * @param String $email Student email id
     * @param String $gcm_id Student GCM id
     */
    public function registerStudent($adm_no,$name, $email, $gcm_id) {
        // First check if user already existed in db
        if (!$this->isStudentExists($adm_no)) {
            // insert query
            $params = array(':admno' => $adm_no,':name' => $name, ':email' => $email,
                ':gcm' => $gcm_id);
            $stmt = $this->conn->prepare('INSERT INTO students(adm_no,name, email,gcm_id)'
                    . ' values(:admno,:name, :email, :gcm)');
            $result = $stmt->execute($params);
            // Check for successful insertion
            if ($result) {
                // User successfully inserted
                return USER_CREATED_SUCCESSFULLY;
            } else {
                // Failed to create user
                return USER_CREATE_FAILED;
            }
        } else {
            // User with same email already existed in the db
            return USER_ALREADY_EXISTED;
        }
    }

    public function Generate($password) {
        $password_hash = PassHash::hash($password);
        // Generating API key
        $api_key = $this->generateCApiKey();
        return $password_hash . '{:}' . $api_key;
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $params = array(':email' => $email);
        $stmt = $this->conn->prepare('SELECT password_hash FROM users WHERE email = :email');
        $stmt->execute($params);
        //$stmt->store_result();
        if ($stmt->rowCount() > 0) {
            // Found user with the email
            // Now verify the password
            $password_hash = $stmt->fetch(PDO::FETCH_OBJ);
            $stmt->closeCursor();
            if (PassHash::check_password($password_hash->password_hash, $password)) {
                // User password is correct
                return USER_LOGEDIN_SUCCESSFULLY;
            } else {
                // user password is incorrect
                return USER_LOGIN_INCORRECT;
            }
        } else {
            $stmt->closeCursor();
            // user not existed with the email
            return USER_NOT_EXIST;
        }
    }
    
    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $params = array(':email' => $email);
        $query = $this->conn->prepare('SELECT id FROM users WHERE email = :email');
        //  $query->bindColumn('email', $email);
        $query->execute($params);
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }
    
    /**
     * Checking for duplicate student by Admission number
     * @param String $adm_no Admission no. to check in db
     * @return boolean
     */
    private function isStudentExists($admno) {
        $params = array(':admno' => $admno);
        $query = $this->conn->prepare('SELECT email FROM students WHERE adm_no = :admno');
        $query->execute($params);
        if ($query->rowCount() == 0) {
            return false;
        }
        return true;
    }

    public function getUserByid($id) {
        $params = array(':id' => $id);
        $query = $this->conn->prepare('SELECT name,email FROM users WHERE id = :id');
        $query->execute($params);
        if ($query) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $params = array(':id' => $user_id);
        $query = $this->conn->prepare('SELECT api_key FROM users WHERE id = :id');
        $query->execute($params);
        if ($query) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            return NULL;
        }
    }

    public function getHashById($user_id) {
        $params = array(':id' => $user_id);
        $query = $this->conn->prepare('SELECT password_hash FROM users WHERE id = :id');
        $query->execute($params);
        if ($query) {
            $user = $query->fetch(PDO::FETCH_OBJ);
            return $user->password_hash;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $params = array(':api' => $api_key);
        $query = $this->conn->prepare('SELECT id FROM users WHERE api_key = :api');
        $query->execute($params);
        if ($query) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            return NULL;
        }
    }

    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $params = array(':api' => $api_key);
        $query = $this->conn->prepare('SELECT id FROM users WHERE api_key = :api');
        $query->execute($params);
        return $query->rowCount() > 0;
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        $key = md5(uniqid(rand(), true));
        if ($this->isValidApiKey($key)) {
            //key already exists
            $this->generateApiKey();
        } else {
            return $key;
        }
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $params = array(':email' => $email);
        $query = $this->conn->prepare('SELECT name, email, api_key,created_at FROM users WHERE email = :email');
        $query->execute($params);
        if ($query) {
            $user = $query->fetch(PDO::FETCH_ASSOC);
            return $user;
        } else {
            return NULL;
        }
    }


}
