<?php
class AdminJobPostController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // Get all pending job posts
    public function get_pending_job_posts() {
        // Add admin authentication check here
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
}
?>