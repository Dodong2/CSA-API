<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload for PHPMailer

class RegLogController {
    private $conn;
    private $mail;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->mail = new PHPMailer(true);
    }

    // Generate 4-digit OTP
    private function generateOTP() {
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    // Send OTP via Gmail
    private function sendOTPEmail($email, $otp) {
        try {
            // SMTP Configuration (replace with your Gmail SMTP settings)
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'careersearchagency@gmail.com';
            $this->mail->Password = 'asae gozf kbdj mvfz'; // Use App Password, not regular password
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Port = 587;

            // Email Content
            $this->mail->setFrom('your_verified_gmail@gmail.com', 'Career Seach Agency');
            $this->mail->addAddress($email);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Your Registration OTP';
            $this->mail->Body = "Your 4-digit verification code is: <b>$otp</b> note: his OTP is confidential and valid for the next [time limit, e.g., 10 minutes].";

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // Log error or handle appropriately
            return false;
        }
    }

    public function register() {
        $username = $_POST['username'] ?? ''; 
        $email = $_POST['email'] ?? ''; 
        $password = $_POST['password'] ?? ''; 
    
        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username, email, password cannot be empty']);
            return;
        }
    
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format']);
            return;
        }
    
        $stmt = $this->conn->prepare('SELECT * FROM reglog WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            return;
        }
    
        $otp = $this->generateOTP();
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare('INSERT INTO temp_registration (username, email, password, otp, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssss', $username, $email, $hashedPassword, $otp);
    
        if ($stmt->execute()) {
            if ($this->sendOTPEmail($email, $otp)) {
                echo json_encode(['success' => true, 'message' => 'OTP sent to your email', 'email' => $email]);
            } else {
                $this->conn->query("DELETE FROM temp_registration WHERE email = ?", $email);
                echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register: ' . $stmt->error]);
        }
    }

    public function verifyRegistration() {
        $email = $_POST['email'] ?? '';
        $otp = $_POST['otp'] ?? '';

        // Verify OTP
        $stmt = $this->conn->prepare('SELECT * FROM temp_registration WHERE email = ? AND otp = ? AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
        $stmt->bind_param('ss', $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // OTP is valid, get registration details
            $row = $result->fetch_assoc();

            // Insert into main registration table
            $stmt = $this->conn->prepare('INSERT INTO reglog (username, email, password) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $row['username'], $row['email'], $row['password']);
            
            if ($stmt->execute()) {
                // Remove temp registration entry
                $stmt = $this->conn->prepare('DELETE FROM temp_registration WHERE email = ?');
                $stmt->bind_param('s', $email);
                $stmt->execute();

                echo json_encode([
                    'success' => true, 
                    'message' => 'Registration completed successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to complete registration'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid or expired OTP'
            ]);
        }
    }


    public function login() {
        $input = $_POST['input'] ?? '';
        $password = $_POST['password'] ?? '';
    
        // Validate inputs
        if (empty($input) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password cannot be empty']);
            return;
        }
    
        // Check if the email exists
        $stmt = $this->conn->prepare('SELECT id, password, email, username FROM reglog WHERE email = ? OR username = ?');
        $stmt->bind_param('ss', $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashedPassword = $row['password'];
    
            // Verify the password
            if (password_verify($password, $hashedPassword)) {
                // Store user information in session
                session_start();
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['username'] = $row['username'];

                echo json_encode([
                    'success' => true, 
                    'message' => 'Login successful', 
                    'user_id' => $row['id']
                ]);
            }  else {
                echo json_encode(['success' => false, 'message' => 'Invalid username/email or password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid username/email or password']);
        }
    
        $stmt->close();
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
}
?>