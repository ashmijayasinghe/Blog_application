<?php
// Start the session at the very top (Requirement 1 & Step 8)
session_start();
require_once 'db.php';

$message = "";

// Step 6 Workflow: Check if user submitted credentials
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Database checks email
    $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Password Verify (Compares with the hashed password in DB)
        if (password_verify($password, $user['password'])) {
            // Session starts - save user details (Step 8)
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Redirect to dashboard (Step 6 Workflow)
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "<p style='color: red;'>Invalid password.</p>";
        }
    } else {
        $message = "<p style='color: red;'>No account found with that email.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Off the Pages</title>
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

        .login-footer {
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
                <h2>Welcome Back</h2>
                <p>Sign in to your account to continue</p>
            </div>
            
            <!-- Display error messages if login fails -->
            <?php 
            if ($message && strpos($message, 'red') !== false) {
                echo '<div class="error-msg">' . strip_tags($message) . '</div>';
            } elseif ($message) {
                echo '<div class="success-msg">' . strip_tags($message) . '</div>';
            }
            ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn">Sign In</button>

                <div class="login-footer">
                    Don't have an account? <a href="register.php" class="auth-link">Create one</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>