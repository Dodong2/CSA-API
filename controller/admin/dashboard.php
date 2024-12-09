<?php

class Hiring_DetailsController
{

    //Insert fucntion to na dito i-insert mga hiring details sa database
    public function hiring_details() {
        global $conn;

        $business_name = $_POST['business_name'] ?? '';
        $descriptions = $_POST['descriptions'] ?? '';
        $work_positions = $_POST['work_positions'] ?? '';
        $company_email = $_POST['company_email'] ?? '';
        $contact_number = $_POST['contact_number'] ?? '';
        $slots = $_POST['slots'] ?? '';
        $locations = $_POST['locations'] ?? '';
        $collar = $_POST['collar'] ?? '';

        //validate input
        if (empty($business_name) || empty($descriptions) || empty($work_positions) || empty($company_email) || empty($contact_number) || empty($slots) || empty($locations) || empty($collar)) {
            $response = ['success' => false, 'message' => 'All fields are required.'];
            echo json_encode($response);
            return;
        }

        //Insert data sa database
        $stmt = $conn->prepare('INSERT INTO admin (business_name, descriptions, work_positions, company_email, contact_number, slots, locations, collar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        //check yung preparation statement kung successful
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error: ' . $conn->error];
            echo json_encode($response);
            return;
        }

        //Bind parameters
        $stmt->bind_param('ssssssss', $business_name, $descriptions, $work_positions, $company_email, $contact_number, $slots, $locations, $collar);
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Hiring Details created successfully'];
        } else {
            $response = ['success' => false, 'message' => 'failed to create Hiring Details: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    //Get all Details dito kukunin yung mga details na nakalagays sa database
    public function get_details() {
        global $conn;

        $result = $conn->query('SELECT * FROM admin');
        $admin = [];

        while($row = $result->fetch_assoc()) {
            $admin[] = $row;
        }

        $response = ['success' => true, 'details' => $admin];
        echo json_encode($response);
    }

    //Update details by ID function an dito ay edit ang mga details
    public function update_details() {
        global $conn;

        $id = $_POST['id'] ?? '';
        $business_name = $_POST['business_name'] ?? '';
        $descriptions = $_POST['descriptions'] ?? '';
        $work_positions = $_POST['work_positions'] ?? '';
        $company_email = $_POST['company_email'] ?? '';
        $contact_number = $_POST['contact_number'] ?? '';
        $slots = $_POST['slots'] ?? '';
        $locations = $_POST['locations'] ?? '';
        $collar = $_POST['collar'] ?? '';

        //Validate input
        if (empty($id) || empty($business_name) || empty($descriptions) || empty($work_positions) || empty($company_email) || empty($contact_number) || empty($slots) || empty($locations) || empty($collar)) {
            $response = ['success' => false, 'message' => 'All fields are required.'];
            echo json_encode($response);
            return;
        }

        //update data in the database
        $stmt = $conn->prepare('UPDATE admin SET business_name = ?, descriptions = ?, work_positions = ?, company_email = ?, contact_number = ?, slots = ?, locations = ?, collar =? WHERE id = ?');
        if ($stmt === false) {
            $response = ['success' => false, 'message' => 'SQL prepare error' . $conn->error];
            echo json_encode($response);
            return;
        }

        //Bind Parameters para sa security and interaction sa database
        $stmt->bind_param('ssssssssi', $business_name, $descriptions, $work_positions, $company_email, $contact_number, $slots, $locations, $collar, $id);
        if($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Details updated successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to Update details: ' . $stmt->error];
        }

        $stmt->close();
        echo json_encode($response);
    }

    //Delete function na syempre dedelete yung mga existing data
    public function delete_details() {
        global $conn;
        $id = $_POST['id'] ?? '';

        if(empty($id)) {
            $response = ['success' => false, 'message' => 'ID is required'];
            echo json_encode($response);
            return;
        }

        $stmt = $conn->prepare('DELETE FROM admin WHERE id = ?');
        $stmt->bind_param('i', $id);

        if($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Details deleted successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to delete details'];
        }

        $stmt->close();
        echo json_encode($response);
    }
} 

class Count_CollarsController {
    //get count collar para alam kung ilang pink collars
    public function get_pink() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as pink_collar FROM admin WHERE collar = 'pink collar'");

        $row = $result->fetch_assoc();
        
        if ($row) {
            $response = ['success' => 'true', 'pink_collar' => $row['pink_collar']];
        } else {
            $response = ['success' => 'false', 'message' => 'No data found.'];
        }
        echo json_encode($response);
    }

    //get count collar para alam kung ilang green collars
    public function get_green() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as green_collar FROM admin WHERE collar = 'green collar'");
        $row = $result->fetch_assoc();

        if($row) {
            $response = ['success' => 'true', 'green_collar' => $row['green_collar']];
        } else {
            $response = ['success' => 'false', 'message' => 'No data found.'];
        }
        echo json_encode(($response));
    }

    //get count collar para alam kung ilang white collars
    public function get_white() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as white_collar FROM admin WHERE collar = 'white collar'");
        $row = $result->fetch_assoc();

        if($row) {
            $response = ['success' => 'true', 'white_collar' => $row['white_collar']];
        } else {
            $response = ['success' => 'false', 'message' => 'No data found.'];
        }
        echo json_encode(($response));
    }

    //get count collar para alam kung ilang blue collars
    public function get_blue() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as blue_collar FROM admin WHERE collar = 'blue collar'");
        $row = $result->fetch_assoc();
    
        if($row) {
            $response = ['success' => 'true', 'blue_collar' => $row['blue_collar']];
        } else {
            $response = ['success' => 'false', 'message' => 'No data found.'];
        }
        echo json_encode(($response));
    }

    //get count collar para alam kung ilang grey collars
    public function get_grey() {
        global $conn;
        $result = $conn->query("SELECT COUNT(*) as grey_collar FROM admin WHERE collar = 'grey collar'");
        $row = $result->fetch_assoc();
            
        if($row) {
            $response = ['success' => 'true', 'grey_collar' => $row['grey_collar']];
        } else {
            $response = ['success' => 'false', 'message' => 'No data found.'];
        }
        echo json_encode(($response));
    }
}


?>
