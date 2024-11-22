<?php

require 'config.php';
require 'controller/user/reglog.php';
require 'controller/admin/dashboard.php';


$RegLogController = new RegLogController();
$Hiring_DetailesController = new Hiring_DetailsController();
$Count_CollarsController = new Count_CollarsController();

$action = $_GET['action'] ?? '';

switch($action) {
    case 'register':
        $RegLogController->register();
        break;
    case 'login':
        $RegLogController->login();
        break;
    case 'insert':
        $Hiring_DetailesController->hiring_details();
        break;
    case 'get':
        $Hiring_DetailesController->get_details();
        break;
    case 'update':
        $Hiring_DetailesController->update_details();
        break;
    case 'delete':
        $Hiring_DetailesController->delete_details();
        break;
    case 'pink_collars':
        $Count_CollarsController->get_pink();
        break;
    case 'green_collars':
        $Count_CollarsController->get_green();
        break;
    case 'white_collars':
        $Count_CollarsController->get_white();
        break;
    case 'blue_collars':
        $Count_CollarsController->get_blue();
        break;
    case 'grey_collars':
        $Count_CollarsController->get_grey();
        break;
    default: 
    echo json_encode(['success' => false, 'message' => 'Invalid Action']);
    break;
}

?>