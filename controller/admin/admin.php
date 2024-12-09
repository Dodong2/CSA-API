<?php
class AdminJobPostController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Get all pending job posts
    public function get_pending_job_posts() {
        $stmt = $this->conn->prepare('SELECT * FROM employer_job_posts WHERE status = "pending"');
        $stmt->execute();
        $result = $stmt->get_result();
    
        $pending_posts = [];
        while ($row = $result->fetch_assoc()) {
            $pending_posts[] = $row;
        }
    
        echo json_encode([
            'success' => true, 
            'pending_posts' => $pending_posts
        ]);
    }

    // Approve job post
    public function approve_job_post() {
        // Add admin authentication check here
        $post_id = $_POST['id'] ?? '';

        $stmt = $this->conn->prepare('UPDATE employer_job_posts SET status = "approved" WHERE id = ? AND status = "pending"');
        $stmt->bind_param('i', $post_id);
        
        if ($stmt->execute()) {
            // Optional: Move to main job posts table or add to public job listings
            echo json_encode([
                'success' => true, 
                'message' => 'Job post approved successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to approve job post: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }

    // Reject job post
    public function reject_job_post() {
        // Add admin authentication check here
        $post_id = $_POST['id'] ?? '';
        $reason = $_POST['reason'] ?? 'Not specified';

        $stmt = $this->conn->prepare('UPDATE employer_job_posts SET status = "rejected", rejection_reason = ? WHERE id = ? AND status = "pending"');
        $stmt->bind_param('si', $reason, $post_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Job post rejected successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to reject job post: ' . $stmt->error
            ]);
        }
        $stmt->close();
    }

    // Get approved public job posts
    public function get_approved_job_posts() {
        $stmt = $this->conn->prepare('SELECT * FROM employer_job_posts WHERE status = "approved"');
        $stmt->execute();
        $result = $stmt->get_result();

        $approved_posts = [];
        while ($row = $result->fetch_assoc()) {
            $approved_posts[] = $row;
        }

        echo json_encode([
            'success' => true, 
            'approved_posts' => $approved_posts
        ]);
    }

    //Get approved collars para sa graph
    public function get_approved_collar() {
        $stmt = $this->conn->prepare('
            SELECT collar, COUNT(*) AS count 
            FROM employer_job_posts 
            WHERE status = "approved" 
            GROUP BY collar
        ');
        $stmt->execute();
        $result = $stmt->get_result();
    
        $collars = [];
        while ($row = $result->fetch_assoc()) {
            $collars[] = $row;
        }
    
        echo json_encode([
            'success' => true, 
            'collars' => $collars
        ]);
    }
    

// Get approved and rejected count para sa graph
public function get_post_percentages() {
    $stmt = $this->conn->prepare('
    SELECT status, COUNT(*) AS count 
    FROM employer_job_posts 
    GROUP BY status
');
$stmt->execute();
$result = $stmt->get_result();

$counts = [];
while ($row = $result->fetch_assoc()) {
    $counts[$row['status']] = (int) $row['count'];
}

echo json_encode([
    'success' => true, 
    'counts' => $counts
]);
}


//Delete function na syempre dedelete yung mga existing data
public function admin_delete_details() {
        global $conn;
        $id = $_POST['id'] ?? '';

        if(empty($id)) {
            $response = ['success' => false, 'message' => 'ID is required'];
            echo json_encode($response);
            return;
        }

        $stmt = $conn->prepare('DELETE FROM employer_job_posts WHERE id = ?');
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Details deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete details'];
        }

        $stmt->close();
        echo json_encode($response);
    }


    public function update_job_post() {
        global $conn;
    
        // Retrieve input data
        $id = $_POST['id'] ?? ''; // Fetch ID from POST data
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
    
        // Validate input
        if (
            empty($id) || empty($business_name) || empty($descriptions) || empty($work_schedule) ||
            empty($skills_required) || empty($experience) || empty($employment_type) ||
            empty($work_positions) || empty($company_email) || empty($contact_number)
            || empty($locations) || empty($collar)
        ) {
            $response = ['success' => false, 'message' => 'All fields are required.'];
            echo json_encode($response);
            return;
        }
    
        // Update data in the database
        $stmt = $conn->prepare('
            UPDATE employer_job_posts 
            SET business_name = ?, descriptions = ?, work_schedule = ?, skills_required = ?, 
                experience = ?, employment_type = ?, work_positions = ?, company_email = ?, 
                contact_number = ?, locations = ?, collar = ? 
            WHERE id = ?
        ');
    
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }
    
        // Bind Parameters to prevent SQL Injection
        $stmt->bind_param(
            'sssssssssssi',
            $business_name, $descriptions, $work_schedule, $skills_required, 
            $experience, $employment_type, $work_positions, $company_email, 
            $contact_number, $locations, $collar, $id
        );
    
        // Execute statement and handle response
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Details updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to update details: ' . $stmt->error];
        }
    
        $stmt->close();
        echo json_encode($response);
    }
    

        //para lang sa mga rejected na job lists
        public function get_reject() {
            global $conn;
    
            $result = $conn->query("SELECT * FROM employer_job_posts WHERE status = 'rejected'");
            $jobreject = [];
    
            while($row = $result->fetch_assoc()) {
                $jobreject[] = $row;
            }
    
            $response = ['success' => true, 'jobreject' => $jobreject];
            echo json_encode($response);
        }
    

        public function get_job_post_details() {
            session_start();
        
            $id = $_GET['id'] ?? null;
        
            if (!$id) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No ID provided'
                ]);
                return;
            }
        
            $stmt = $this->conn->prepare("SELECT * FROM employer_job_posts WHERE id = ? AND status = 'approved'");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($row = $result->fetch_assoc()) {
                echo json_encode([
                    'success' => true, 
                    'jobPost' => $row
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Job post not found'
                ]);
            }
        
            $stmt->close();
        }

}






?>
