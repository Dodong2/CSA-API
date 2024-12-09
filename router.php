<?php
require 'config.php';
require 'controller/user/reglog.php';
require 'controller/admin/dashboard.php';
require 'controller/user/employer.php';
require 'controller/admin/admin.php';


$RegLogController = new RegLogController();
$Hiring_DetailesController = new Hiring_DetailsController();
$Count_CollarsController = new Count_CollarsController();
$EmployerJobPostController = new EmployerJobPostController();
$AdminJobPostController = new AdminJobPostController();


$action = $_GET['action'] ?? '';

switch($action) {
    // user and employer
    case 'register':
        $RegLogController->register();
        break;
    case 'verify':
        $RegLogController->verifyRegistration();
        break;
    case 'login':
        $RegLogController->login();
        break;
    case 'logout':
        $RegLogController->logout();
        break;
    case 'create_post_employer':
        $EmployerJobPostController->create_job_post();
        break;
    case 'get_employer_post':
        $EmployerJobPostController->get_employer_job_posts();
        break;
    case 'delete_job_post':
        $EmployerJobPostController->delete_job_post();
        break;
    case 'get_approved_employer_posts':
        $EmployerJobPostController->get_approved_employer_job_posts();
        break;
    case 'joblist':
        $EmployerJobPostController->get_joblist();
        break;
    //Admin
    case 'get_pending':
        $AdminJobPostController->get_pending_job_posts();
        break;
    case 'approve_job':
        $AdminJobPostController->approve_job_post();
        break;
    case 'reject_job':
        $AdminJobPostController->reject_job_post();
        break;
    case 'get_approve':
        $AdminJobPostController->get_approved_job_posts();
        break;
    case 'get_approve_collar':
        $AdminJobPostController->get_approved_collar();
        break;
    case 'get_approve_reject':
        $AdminJobPostController->get_post_percentages();
        break;
    case 'delete_detail':
        $AdminJobPostController-> admin_delete_details();
        break;
    case 'update_job':
        $AdminJobPostController->update_job_post();
        break;
    case 'update_list':
        $AdminJobPostController->get_job_post_details();
        break;
    case 'get_reject':
        $AdminJobPostController->get_reject();
        break;
    // other admin task
    case 'insert':
        $Hiring_DetailesController->hiring_details();
        break;
    case 'get':
        $Hiring_DetailesController->get_details();
        break;
    case 'update':
        $Hiring_DetailesController->update_details();
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