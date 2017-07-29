<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SOURCEPATH\V1\Models;

use PDO;
use MongoClient;
include_once dirname(__FILE__) . '/../Define/Config.php';
/**
 * Description of DBConnect
 *
 * @author aman
 */
class DBConnect {

    public static function getdb() {
        static $db = null;
        if (null === $db) {
            $db = new PDO(DB_HOST.DB_NAME,DB_USERNAME,DB_PASSWORD);
            $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        return $db;
    }
    public static function get_mongo_db() {
        static $mdb = null;
        if (null === $mdb) {
        	try {
        	$options = array("connectTimeoutMS" => 30000);
            $mdb_connection = new MongoClient(M_DB_STRING, $options);
            $mdb = $mdb_connection->selectDB(M_DB_NAME);
            } catch (MongoConnectionException $e) {
			      echo "Cannot Connect to MongoDB";
		    }
        }
        return $mdb;
    }

}
