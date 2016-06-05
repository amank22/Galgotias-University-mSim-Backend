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
 * Description of NewsDbHandler
 *
 * @author aman
 */
namespace SOURCEPATH\V1\Models;

use PDO;

class NewsDbHandler {

    private $conn;

    function __construct() {
        // opening db connection
        $this->conn = DBConnect::getdb();
    }

    /**
     * Creating new News entry in the db
     */
    public function createNews($user_id, $note, $image_url,$name) {
        $params = array(':uid' => $user_id,
            ':note' => $note, ':url' => $image_url);
        $stmt = $this->conn->prepare('INSERT INTO news(userid,note,image_url)'
                . ' values(:uid,:note,:url)');
        $result = $stmt->execute($params);
        if (!$result) {
            return 'ERROR-INSERTING';
        }
        $gcmresult=$this->send_notification($name,$note, $image_url);
        return $gcmresult;
    }
    
       /**
     * Creating new News entry in the db
     */
    public function AddTopic($user_id,$name) {
       if($user_id!=2) return NULL;
        $gcmresult=$this->send_topic($name);
        return $gcmresult;
    }
    
    /**
     * Get All the News
     */
    public function getAllNews($startid){
        $params = array(':sid'=>$startid);
        $stmt=  $this->conn->prepare('SELECT news.id,news.note,news.image_url,users.name,'
                . 'users.email FROM news LEFT JOIN users ON news.userid=users.id WHERE news.id>= :sid');
        $result = $stmt->execute($params);
        $response = array();
        $response["news"] = array();
        if ($result) {
            while ($news = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tmp = array();
                $tmp["id"] = $news["id"];
                $tmp["note"] = $news["note"];
                $tmp["image_url"] = $news["image_url"];
                $tmp["author"] = $news["name"];
                $tmp["author_email"] = $news["email"];
                array_push($response["news"], $tmp);
            }
            return $response;
        } else {
            return NULL;
        }
    }

    
    /**
     * Sending Push Notification
     */
    public function send_notification($topic, $message, $image) {
        // include config
        define('GOOGLE_API_KEY', 'AIzaSyC9_nYsE-U_ziOzSfbzKCjJatT5r5LkXcA');
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        $msg = array
            (
                    'message'       => $message,
                    'image'         => $image,
                    'systemmsg'     => false
            );
        $fields = array(
            'to' => '/topics/'.$topic,
            'data' => $msg,
        );
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            return 'ERROR-INSERTING';
        }
 
        // Close connection
        curl_close($ch);
        return $result;
    }
 
    /**
     * Sending Push Notification
     */
    public function send_topic($topic) {
        // include config
        define('GOOGLE_API_KEY', 'AIzaSyC9_nYsE-U_ziOzSfbzKCjJatT5r5LkXcA');
 
        // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        $msg = array
            (
                    'message'       => $topic,
                    'systemmsg'         => true
            );
        $fields = array(
            'to' => '/topics/Admin',
            'data' => $msg,
        );
 
        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
 
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
 
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            return 'ERROR-INSERTING';
        }
 
        // Close connection
        curl_close($ch);
        return $result;
    }
 
    
}
