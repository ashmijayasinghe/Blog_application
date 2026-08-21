<?php
// 1. Force PHP to display errors on the screen if something goes wrong
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Include the database connection file
require_once 'db.php';

$message = "";

// 3. Check if the user clicked the "Register" button
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);g
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if any field was left completely empty
    if (empty($username) || empty($email) || empty($password)) {
        $message = "<p style='color: red;'>All fields are required!</p>";
    } else {
        // Check if the username or email is already inside the database table
        $check_stmt = $conn->prepare("SELECT id FROM user WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Username or Email is already taken
            $message = "<p style='color: red;'>Error: Username or Email already exists. Try using a different one!</p>";
        } else {
            // Securely hash the password [cite: 9]
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user records into the table [cite: 9]
            $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "<p style='color: green;'>Registration successful! You can now log in.</p>";
            } else {
                $message = "<p style='color: red;'>Database Error: Failed to save user details.</p>";
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Off the Pages</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-image: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1400&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            margin: 0; 
            padding: 0; 
            color: #1f2937;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.65) 0%, rgba(0, 0, 0, 0.55) 100%);
            z-index: -1;
        }

        /* Navigation */
        nav { 
            background-color: transparent; 
            padding: 20px 40px; 
            display: flex;
            justify-content: center;
            gap: 40px;
        }
        nav a { 
            color: #ffffff; 
            text-decoration: none; 
            font-weight: 500;
            font-size: 0.95rem;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        nav a:hover { 
            color: #b08d57;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-container { 
            max-width: 420px; 
            width: 100%;
            padding: 40px; 
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px; 
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .form-header .logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.8rem;
            font-weight: 400;
            color: #b08d57;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        .form-container h2 { 
            margin: 0; 
            margin-bottom: 8px;
            font-size: 1.8rem;
            font-weight: 600;
            color: #111; 
            font-family: 'Poppins', sans-serif;
        }

        .form-container p {
            color: #666;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group { margin-bottom: 20px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #374151;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        .form-group input { 
            width: 100%; 
            padding: 12px 14px; 
            box-sizing: border-box; 
            border: 1px solid #d1d5db; 
            border-radius: 8px; 
            font-size: 0.95rem; 
            font-family: 'Poppins', sans-serif;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #b08d57; 
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(176, 141, 87, 0.1);
        }
        .btn { 
            background-color: #b08d57; 
            color: white; 
            border: none; 
            padding: 12px 16px; 
            width: 100%; 
            border-radius: 8px; 
            font-size: 0.95rem; 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        .btn:hover { 
            background-color: #c9a06f;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(176, 141, 87, 0.3);
        }

        .auth-link { 
            color: #b08d57; 
            text-decoration: none; 
            font-weight: 600; 
            transition: 0.3s;
        }
        .auth-link:hover { 
            color: #9d7a48;
        }

        .register-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9rem;
            color: #666;
        }

        .error-msg {
            background-color: #ffe5e5;
            color: #c85a5a;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c85a5a;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }

        .success-msg {
            background-color: #e5f8ed;
            color: #2d7a5f;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #2d7a5f;
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }

        @media (max-width: 640px) {
            nav { padding: 15px 20px; gap: 20px; }
            .form-container { padding: 30px 20px; }
            .form-container h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>

    <div class="main-content">
        <div class="form-container">
            <div class="form-header">
                <div class="logo">◆ Off the Pages</div>
                <h2>Join Us</h2>
                <p>Create an account to start sharing your stories</p>
            </div>
            
            <?php 
            if ($message && (strpos($message, 'red') !== false || strpos($message, 'Error') !== false)) {
                echo '<div class="error-msg">' . strip_tags($message) . '</div>';
            } elseif ($message && (strpos($message, 'green') !== false || strpos($message, 'successful') !== false)) {
                echo '<div class="success-msg">' . strip_tags($message) . '</div>';
            }
            ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Create a password">
                </div>
                <button type="submit" class="btn">Create Account</button>

                <div class="register-footer">
                    Already have an account? <a href="login.php" class="auth-link">Sign in</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>