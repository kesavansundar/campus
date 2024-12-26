<?php
// At the top of your file, add error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "campus_recruitment";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $email = $_POST['email'];
        
        // First, let's check if the prepare statement works
        $sql = "SELECT admin_id FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Now bind the parameter
        if (!$stmt->bind_param("s", $email)) {
            throw new Exception("Binding parameters failed: " . $stmt->error);
        }
        
        // Execute the query
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Prepare the update statement
            $update_sql = "UPDATE admin SET reset_token = ?, reset_expiry = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            
            if ($update_stmt === false) {
                throw new Exception("Prepare update failed: " . $conn->error);
            }
            
            if (!$update_stmt->bind_param("sss", $token, $expiry, $email)) {
                throw new Exception("Binding update parameters failed: " . $update_stmt->error);
            }
            
            if (!$update_stmt->execute()) {
                throw new Exception("Update execute failed: " . $update_stmt->error);
            }

            // Send reset email
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            $to = $email;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: " . $reset_link;
            $headers = "From: noreply@yourwebsite.com";

            if(mail($to, $subject, $message, $headers)) {
                echo "<div class='message success'>Password reset instructions have been sent to your email.</div>";
            } else {
                throw new Exception("Error sending email.");
            }
            
            $update_stmt->close();
        } else {
            echo "<div class='message error'>Email address not found.</div>";
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        echo "<div class='message error'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</div>";
        // Log the error for debugging
        error_log("Password reset error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ... (keep the same CSS as before) ... */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Reset Password</h2>
            <p>Enter your email to receive a password reset link</p>
        </div>

        <form method="POST" action="">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit">Send Reset Link</button>
        </form>

        <div class="back-to-login">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</body>
</html>