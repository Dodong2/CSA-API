<?php
class EmployerJobPostController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    //insert ng imployer
    // public function create_job_post() {
    //     // Start session to access user_id
    //     session_start();
        
    //     // Check if user is logged in
    //     if (!isset($_SESSION['user_id'])) {
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'User not logged in'
    //         ]);
    //         return;
    //     }

    //     // Get user_id from session
    //     $user_id = $_SESSION['user_id'];

    //     // Collect job post data
    //     $business_name = $_POST['business_name'] ?? '';
    //     $descriptions = $_POST['descriptions'] ?? '';
    //     $work_schedule = $_POST['work_schedule'] ?? '';
    //     $skills_required = $_POST['skills_required'] ?? '';
    //     $experience = $_POST['experience'] ?? '';
    //     $employment_type = $_POST['employment_type'] ?? '';
    //     $work_positions = $_POST['work_positions'] ?? '';
    //     $company_email = $_POST['company_email'] ?? '';
    //     $contact_number = $_POST['contact_number'] ?? '';
    //     $locations = $_POST['locations'] ?? '';
    //     $collar = $_POST['collar'] ?? '';

    //     // Validate required fields
    //     if (empty($business_name) || empty($descriptions) || empty($work_positions)) {
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'Missing required fields'
    //         ]);
    //         return;
    //     }

    //     // Prepare SQL statement
    //     $stmt = $this->conn->prepare("INSERT INTO employer_job_posts 
    //         (user_id, business_name, descriptions, work_schedule, skills_required, 
    //         experience, employment_type, work_positions, company_email, 
    //         contact_number, locations, collar) 
    //         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
    //     $stmt->bind_param("isssssssssss", 
    //         $user_id, $business_name, $descriptions, $work_schedule, 
    //         $skills_required, $experience, $employment_type, $work_positions, 
    //         $company_email, $contact_number, $locations, $collar);

    //     if ($stmt->execute()) {
    //         echo json_encode([
    //             'success' => true, 
    //             'message' => 'Job post submitted successfully. Pending admin approval.',
    //             'post_id' => $stmt->insert_id
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'Failed to submit job post: ' . $stmt->error
    //         ]);
    //     }
    //     $stmt->close();
    // }


    public function create_job_post() {
        session_start();
    
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'User not logged in'
            ]);
            return;
        }
    
        $user_id = $_SESSION['user_id'];
        $business_name = $_POST['business_name'] ?? '';
        $descriptions = $_POST['descriptions'] ?? '';
        $work_schedule = $_POST['work_schedule'] ?? '';
        $skills_required = $_POST['skills_required'] ?? '';
        $experience = $_POST['experience'] ?? '';
        $employment_type = $_POST['employment_type'] ?? '';
        $work_positions = $_POST['work_positions'] ?? '';
        $company_email = $_POST['company_email'] ?? '';
        $contact_number = $_POST['contact_number'] ?? '';
        $locations = $_POST['locations'] ?? '';
        $collar = $_POST['collar'] ?? '';
    
        if (empty($business_name) || empty($descriptions) || empty($work_positions)) {
            echo json_encode([
                'success' => false,
                'message' => 'Missing required fields'
            ]);
            return;
        }
    
        // Handle file uploads
        $upload_dir = 'uploads/';
        $business_permit_path = '';
        $valid_id_path = '';
    
        if (isset($_FILES['business_permit_path']) && $_FILES['business_permit_path']['error'] === UPLOAD_ERR_OK) {
            $business_permit_path = $upload_dir . basename($_FILES['business_permit_path']['name']);
            move_uploaded_file($_FILES['business_permit_path']['tmp_name'], $business_permit_path);
        }
    
        if (isset($_FILES['valid_id_path']) && $_FILES['valid_id_path']['error'] === UPLOAD_ERR_OK) {
            $valid_id_path = $upload_dir . basename($_FILES['valid_id_path']['name']);
            move_uploaded_file($_FILES['valid_id_path']['tmp_name'], $valid_id_path);
        }
    
        $stmt = $this->conn->prepare("INSERT INTO employer_job_posts 
            (user_id, business_name, descriptions, work_schedule, skills_required, 
            experience, employment_type, work_positions, company_email, 
            contact_number, locations, collar, business_permit_path, valid_id_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param(
            "isssssssssssss", 
            $user_id, $business_name, $descriptions, $work_schedule, $skills_required,
            $experience, $employment_type, $work_positions, $company_email,
            $contact_number, $locations, $collar, $business_permit_path, $valid_id_path
        );
    
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Job post submitted successfully. Pending admin approval.',
                'post_id' => $stmt->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to submit job post: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }
    

    
    //get ng post details ng employer
    public function get_employer_job_posts() {
        // Start session to access user_id
        session_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'User not logged in'
            ]);
            return;
        }

        $user_id = $_SESSION['user_id'];

        // Fetch job posts for the logged-in user
        $stmt = $this->conn->prepare("SELECT * FROM employer_job_posts 
            WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $job_posts = [];
        while ($row = $result->fetch_assoc()) {
            $job_posts[] = $row;
        }

        echo json_encode([
            'success' => true, 
            'job_posts' => $job_posts
        ]);
        $stmt->close();
    }
    
    //delete job post ng employer
    public function delete_job_post() {
        // Start session to access user_id
        session_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false, 
                'message' => 'User not logged in'
            ]);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $post_id = $_POST['id'] ?? '';

        // Prepare delete SQL statement
        $stmt = $this->conn->prepare("DELETE FROM employer_job_posts 
            WHERE id = ? AND user_id = ?");
        
        $stmt->bind_param("ii", $post_id, $user_id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Job post deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to delete job post: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }

    // get yung mga n approved ng employer In admin.php or employer.php
    public function get_approved_employer_job_posts() {
        session_start();
    
        // Check if user is logged in and has admin privileges
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            echo json_encode([
                'success' => true, 
                'message' => 'admin'
            ]);
            return;
        }
    
        // Fetch ALL approved job posts, not filtered by user_id
        $stmt = $this->conn->prepare("SELECT * FROM employer_job_posts WHERE status = 'approved' ORDER BY created_at DESC");
        $stmt->execute();
        $result = $stmt->get_result();
    
        $job_posts = [];
        while ($row = $result->fetch_assoc()) {
            $job_posts[] = $row;
        }
    
        echo json_encode([
            'success' => true, 
            'job_posts' => $job_posts
        ]);
    
        $stmt->close();
    }

    //all around pwede sa admin
    //get yung mga job na naka post para sa joblist basta approve
    public function get_joblist() {
        global $conn;

        $result = $conn->query("SELECT * FROM employer_job_posts WHERE status = 'approved'");
        $joblist = [];

        while($row = $result->fetch_assoc()) {
            $joblist[] = $row;
        }

        $response = ['success' => true, 'joblists' => $joblist];
        echo json_encode($response);
    }


}
?>