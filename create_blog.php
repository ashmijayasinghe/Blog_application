<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Security Guard: Kick out anyone who isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// When the user clicks "Publish"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($content)) {
        // Save to the database using the lowercase 'blogpost' table
        $stmt = $conn->prepare("INSERT INTO blogpost (user_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        
        if ($stmt->execute()) {
            // Success! Send them to the community blogs page to see their new post
            header("Location: blogs.php");
            exit();
        } else {
            $message = "<div class='error-msg'>Failed to publish post. Please try again.</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='error-msg'>All fields are required!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Post - Off the Pages</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #fafafa; 
            color: #1e1e1e; 
        }

        /* Top Navigation */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 16px 40px; 
            background-color: #b08d57; 
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        .nav-left a { 
            text-decoration: none; 
            color: #ffffff; 
            font-weight: 400; 
            font-size: 1.15rem; 
            letter-spacing: 1px; 
            font-family: 'Cormorant Garamond', serif; 
        }
        .nav-right { 
            display: flex; 
            align-items: center; 
            gap: 28px;
        }
        .nav-right a { 
            color: #f5f1ed; 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 500; 
            transition: 0.3s; 
            font-family: 'Poppins', sans-serif;
        }
        .nav-right a:hover { 
            color: #ffffff; 
        }

        /* Hero Banner */
        .hero-banner {
            width: 100%;
            height: 400px;
            background-image: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1400&q=80');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.45) 100%);
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 400;
            margin-bottom: 12px;
            letter-spacing: 1px;
            font-family: 'Cormorant Garamond', serif;
        }
        
        /* The Writing Form Canvas */
        .editor-container { 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 40px; 
            background: #ffffff; 
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        .editor-container h2 {
            font-family: 'Cormorant Garamond', serif;
            margin-top: 0;
            font-size: 2.2rem;
            font-weight: 400;
            color: #111;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        .form-group { margin-bottom: 25px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #444;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        .form-group input, .form-group textarea { 
            width: 100%; 
            padding: 12px 14px; 
            box-sizing: border-box; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #b08d57;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(176, 141, 87, 0.1);
        }
        .form-group textarea { height: 280px; resize: vertical; }
        
        .btn-publish { 
            background-color: #b08d57; 
            color: white; 
            border: none; 
            padding: 12px 24px; 
            border-radius: 8px; 
            font-size: 0.95rem; 
            cursor: pointer; 
            font-weight: 600; 
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        .btn-publish:hover { 
            background-color: #c9a06f; 
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(176, 141, 87, 0.3);
        }
        .cancel-link {
            margin-left: 20px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }
        .cancel-link:hover { 
            color: #b08d57;
        }
        
        .error-msg {
            background-color: #ffe5e5;
            color: #c85a5a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
            font-family: 'Poppins', sans-serif;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-banner { height: 350px; }
            .hero-content h1 { font-size: 2.8rem; }
        }
        @media (max-width: 640px) {
            .hero-banner { height: 280px; }
            .hero-content h1 { font-size: 2rem; }
            nav { padding: 12px 20px; }
            .nav-right { gap: 15px; }
            .editor-container { margin: 30px 20px; padding: 25px; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="nav-left">
            <a href="index.php">◆ Off the Pages</a>
        </div>
        <div class="nav-right">
            <a href="blogs.php">Community Blogs</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="hero-content">
            <h1>Share Your Story</h1>
        </div>
    </div>

    <div class="editor-container">
        <h2>Draft a New Story</h2>
        
        <?php echo $message; ?>

        <form action="create_blog.php" method="POST">
            <div class="form-group">
                <label>Post Title</label>
                <input type="text" name="title" placeholder="Give your story a catchy title..." required>
            </div>
            <div class="form-group">
                <label>Story Content</label>
                <textarea name="content" placeholder="Write your thoughts here..." required></textarea>
            </div>
            <button type="submit" class="btn-publish">Publish Post</button>
            <a href="blogs.php" class="cancel-link">Cancel</a>
        </form>
    </div>

</body>
</html>