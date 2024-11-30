<?php
class EmployerJobPostController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function create_job_post() {
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

        // Get user_id from session
        $user_id = $_SESSION['user_id'];

        // Collect job post data
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

        // Validate required fields
        if (empty($business_name) || empty($descriptions) || empty($work_positions)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Missing required fields'
            ]);
            return;
        }

        // Prepare SQL statement
        $stmt = $this->conn->prepare("INSERT INTO employer_job_posts 
            (user_id, business_name, descriptions, work_schedule, skills_required, 
            experience, employment_type, work_positions, company_email, 
            contact_number, locations, collar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("isssssssssss", 
            $user_id, $business_name, $descriptions, $work_schedule, 
            $skills_required, $experience, $employment_type, $work_positions, 
            $company_email, $contact_number, $locations, $collar);

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

    public function update_job_post() {
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
        $post_id = $_POST['post_id'] ?? '';

        // First, verify the job post belongs to the user
        $stmt = $this->conn->prepare("SELECT * FROM employer_job_posts 
            WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Job post not found or unauthorized'
            ]);
            return;
        }

        // Collect updated job post data
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

        // Prepare update SQL statement
        $stmt = $this->conn->prepare("UPDATE employer_job_posts 
            SET business_name=?, descriptions=?, work_schedule=?, 
            skills_required=?, experience=?, employment_type=?, 
            work_positions=?, company_email=?, contact_number=?, 
            locations=?, collar=?, status='pending' 
            WHERE id = ?");
        
        $stmt->bind_param("sssssssssssi", 
            $business_name, $descriptions, $work_schedule, 
            $skills_required, $experience, $employment_type, 
            $work_positions, $company_email, $contact_number, 
            $locations, $collar, $post_id);

        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Job post updated successfully. Pending admin approval.'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to update job post: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }

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
        $post_id = $_POST['post_id'] ?? '';

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

// In admin.php or employer.php
public function get_approved_employer_job_posts() {
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

    // Fetch approved job posts for the logged-in user
    $stmt = $this->conn->prepare("SELECT * FROM employer_job_posts WHERE user_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $job_posts = [];
    while ($row = $result->fetch_assoc()) {
        $job_posts[] = $row;
    }

    // Always return a structured response
    echo json_encode([
        'success' => true, 
        'job_posts' => $job_posts
    ]);

    $stmt->close();
}

}
?>