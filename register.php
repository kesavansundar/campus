<?php
// Database connection details
$host = "localhost";
$db_name = "campus_recruitment";
$username = "root";
$password = "";

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper function to check if a string ends with a specific suffix
function endsWith($string, $suffix) {
    return substr($string, -strlen($suffix)) === $suffix;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Create connection
        $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve form data and sanitize
        $user_name = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : null;
        $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : null;
        $mobile = isset($_POST['mobile']) ? htmlspecialchars(trim($_POST['mobile'])) : null;
        $department = isset($_POST['department']) ? htmlspecialchars(trim($_POST['department'])) : null;
        $shift = isset($_POST['shift']) ? htmlspecialchars(trim($_POST['shift'])) : null;
        $section = isset($_POST['section']) ? htmlspecialchars(trim($_POST['section'])) : null;
        $password = isset($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : null;
        $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : null;

        // Ensure email ends with bhc.edu.in
        if (!endsWith($email, "@bhc.edu.in")) {
            echo "<script>alert('Email must end with bhc.edu.in');</script>";
        } elseif ($user_name && $email && $mobile && $department && $shift && $section && $password && $gender) {
            // Check if email already exists
            $sql = "SELECT * FROM students WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':email' => $email]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_user) {
                echo "<script>alert('Email is already registered. Please use a different email.');</script>";
            } else {
                // Insert data into the database
                $sql = "INSERT INTO students (username, email, mobile, department, shift, section, password, gender)
                        VALUES (:username, :email, :mobile, :department, :shift, :section, :password, :gender)";

                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':username' => $user_name,
                    ':email' => $email,
                    ':mobile' => $mobile,
                    ':department' => $department,
                    ':shift' => $shift,
                    ':section' => $section,
                    ':password' => $password,
                    ':gender' => $gender,
                ]);

                echo "<script>alert('Registration successful! Redirecting to login page.'); window.location.href='login.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Please fill in all the fields.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function validateEmail() {
            const emailField = document.getElementById('email');
            const email = emailField.value;
            if (!email.endsWith('@bhc.edu.in')) {
                alert('Invalid Email ');
                emailField.focus();
                return false;
            }
            return true;
        }

        function validateForm(event) {
            if (!validateEmail()) {
                event.preventDefault();
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%; /* Adjust the width to make it smaller */
            max-width: 350px; /* Reduce the max-width to make it smaller */
            margin: auto; /* Center the container */
}


        h2 {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        label {
            font-weight: 600;
            margin-top: 15px;
            display: block;
            color: #555;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45A049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="" onsubmit="validateForm(event)">
            <label for="username">User Name</label>
            <input type="text" id="username" name="username" required>

            <label for="email">College Mail ID</label>
            <input type="email" id="email" name="email" required>

            <label for="mobile">Mobile Number</label>
            <input type="text" id="mobile" name="mobile" required>

            <label for="department">Department</label>
            <input type="text" id="department" name="department" required>

            <label for="shift">Shift</label>
            <select id="shift" name="shift" required>
                <option value="First Shift">First Shift</option>
                <option value="Second Shift">Second Shift</option>
            </select>

            <label for="section">Section</label>
            <select id="section" name="section" required>
                <option value="Aided">Aided</option>
                <option value="SF (A)">SF (A)</option>
                <option value="SF (B)">SF (B)</option>
                <option value="SF (C)">SF (C)</option>
                <option value="SF (D)">SF (D)</option>
                <option value="SF (E)">SF (E)</option>
            </select>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <button type="submit">Register</button>
        </form>
        <p class="mt-4 text-gray-600">
            Already registered? <a href="login.php" class="text-blue-600 hover:underline">Login here</a>.
        </p>
    </div>
    
</body>
</html>
