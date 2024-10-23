<?php

//Step 1
class RegLogController
{

    public function register() 
    {

        global $conn;

        //register function
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $profession = $_POST['profession'] ?? '';

        $response = [];

        // Validate input
        if (empty($username) || empty($email) || empty($password) || empty($profession)) {
            $response = ['success' => false, 'message' => 'Username, email, password ,and profession cannot be empty'];
            echo json_encode($response);
            return;
        }

        // Check if the email already exists
        $stmt = $conn->prepare('SELECT * FROM reglog WHERE email =?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response = ['success' => false, 'message' => 'Email already exists'];
            echo json_encode($response);
            return;
        }

        // Insert into Database
        $stmt = $conn->prepare('INSERT INTO reglog (username, email, password, profession) VALUES (?, ?, ?, ?)');
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }

        $stmt->bind_param('ssss', $username, $email, $password, $profession);

        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'User registered successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to register user: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    // User login function
    public function login()
    {
        global $conn;

        // Sanitize input
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = htmlspecialchars(trim($_POST['password'] ?? ''));

        $response = [];

        // Validate input
        if (empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Email and password cannot be empty'];
            echo json_encode($response);
            return;
        }

        // Check if the email exists
        $stmt = $conn->prepare('SELECT * FROM reglog WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
            echo json_encode($response);
            return;
        }

        $user = $result->fetch_assoc();

        // Compare the password directly (since we're not hashing)
        if ($password === $user['password']) {
            $response = ['success' => true, 'message' => 'Login successful'];
        } else {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
        }

        echo json_encode($response);
    }
}
?>
