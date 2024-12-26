<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(10px);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: #2d3748;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #718096;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #718096;
        }

        .form-group input {
            width: 100%;
            padding: 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            color: #e53e3e;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-align: center;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #5a67d8;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1rem;
        }

        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "campus_recruitment";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $error_message = '';

    // Check if form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get email and password from POST request
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Prepare SQL query to fetch user data
        $sql = "SELECT * FROM admin WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("s", $email);
            
            // Execute the query
            $stmt->execute();
            
            // Get result
            $result = $stmt->get_result();
            
            // Check if the user exists
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Check if the password matches
                if ($password == $user['password']) {
                    // Redirect to the dashboard page
                    header("Location: admin_index.html");
                    exit;
                } else {
                    $error_message = "Invalid email or password!";
                }
            } else {
                $error_message = "Invalid email or password!";
            }
            
            $stmt->close();
        } else {
            $error_message = "Error preparing the query: " . $conn->error;
        }
    }

    // Close the connection
    $conn->close();
    ?>

    <div class="container">
        <div class="login-header">
            <h2>Welcome Back</h2>
            <p>Please sign in to continue</p>
        </div>
        
        <form method="POST" action="">
            <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email address" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Sign In</button>

            <div class="forgot-password">
                <a href="forgetpass.php">Forgot your password?</a>
            </div>
        </form>
    </div>
</body>
</html>