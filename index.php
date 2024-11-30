<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(0);

function cors() {
    if(isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Max-Age: 86400');
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
          header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
          header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        exit(0);
      }
}
cors();
header('Content-Type: application/json');
require 'router.php';



?>