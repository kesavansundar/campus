<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "campus_recruitment"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert company data into the database when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $contact_email = $_POST['contact_email'];
    $description = $_POST['description'];
    $website_url = $_POST['website_url'];
    $established_year = $_POST['established_year'];
    $eligibility_criteria = $_POST['eligibility_criteria'];
    $available_jobs = explode(',', $_POST['available_jobs']); // Split job roles into an array

    // Handle the image upload
    $company_image = null;
    if (isset($_FILES['company_image']) && $_FILES['company_image']['error'] == 0) {
        $company_image = file_get_contents($_FILES['company_image']['tmp_name']);
    }

    // Insert company data into the `companies` table
    $sql = "INSERT INTO companies (company_name, location, contact_email, description, website_url, established_year, company_image)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssis", $company_name, $location, $contact_email, $description, $website_url, $established_year, $company_image);

        if ($stmt->execute()) {
            // Get the last inserted company ID
            $company_id = $stmt->insert_id;

            // Insert eligibility criteria
            $criteria_sql = "INSERT INTO eligibility_criteria (company_id, criteria_description) VALUES (?, ?)";
            if ($criteria_stmt = $conn->prepare($criteria_sql)) {
                $criteria_stmt->bind_param("is", $company_id, $eligibility_criteria);
                $criteria_stmt->execute();
                $criteria_stmt->close();
            }

            // Insert available job roles
            $role_sql = "INSERT INTO available_roles (company_id, role_name) VALUES (?, ?)";
            if ($role_stmt = $conn->prepare($role_sql)) {
                foreach ($available_jobs as $role) {
                    $role = trim($role); // Remove whitespace
                    $role_stmt->bind_param("is", $company_id, $role);
                    $role_stmt->execute();
                }
                $role_stmt->close();
            }

            echo "<script>alert('Company details added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding company details!');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error preparing the SQL query.');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .dashboard-btn {
            display: block;
            text-align: center;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }
        .dashboard-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Company Details</h2>
        <form method="POST" action="admin_index.html" enctype="multipart/form-data">
            <label for="company_name">Company Name</label>
            <input type="text" id="company_name" name="company_name" required>

            <label for="location">Location</label>
            <input type="text" id="location" name="location" required>

            <label for="contact_email">Contact Email</label>
            <input type="email" id="contact_email" name="contact_email" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="website_url">Website URL</label>
            <input type="text" id="website_url" name="website_url">

            <label for="established_year">Established Year</label>
            <input type="number" id="established_year" name="established_year">

            <label for="eligibility_criteria">Eligibility Criteria</label>
            <textarea id="eligibility_criteria" name="eligibility_criteria" rows="3" required></textarea>

            <label for="available_jobs">Available Job Roles (Comma-separated)</label>
            <textarea id="available_jobs" name="available_jobs" rows="3" required></textarea>

            <label for="company_image">Company Image</label>
            <input type="file" id="company_image" name="company_image">

            <button type="submit">Submit</button>
            <a href="admin_dashboard.php" class="dashboard-btn">Admin Dashboard</a>
        </form>
    </div>
</body>
</html>
