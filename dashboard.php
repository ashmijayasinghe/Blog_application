<?php
// Ensure session is started only once to prevent errors
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'db.php';

// Security Check: Kick out anyone who isn't logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Default values to prevent "Undefined variable" crashes
$username = "User";
$posts_result = null;

// 1. Fetch Username safely
$user_stmt = $conn->prepare("SELECT username FROM user WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_res = $user_stmt->get_result();
if ($user_row = $user_res->fetch_assoc()) {
    $username = $user_row['username'];
}
$user_stmt->close();

// 2. Fetch Posts safely
$posts_stmt = $conn->prepare("SELECT * FROM blogpost WHERE user_id = ? ORDER BY created_at DESC");
$posts_stmt->bind_param("i", $user_id);
$posts_stmt->execute();
$posts_result = $posts_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Off the Pages</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            background-color: #fcfcfc; 
            margin: 0; 
            color: #333; 
        }
        
        /* 🚀 The Upgraded Premium Navigation Bar */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 15px 50px; 
            background-color: #b08d57; /* The solid gold/brown background */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-left a { 
            text-decoration: none; 
            color: #ffffff; 
            font-family: "Georgia", serif; /* Matches the editorial serif font in your image */
            font-size: 1.4rem; 
            letter-spacing: 0.5px;
        }
        .nav-right { 
            display: flex; 
            align-items: center; 
        }
        .nav-right a.nav-link { 
            color: #ffffff; 
            margin-left: 30px; 
            text-decoration: none; 
            font-size: 0.95rem; 
            font-weight: 500;
            opacity: 0.9;
            transition: opacity 0.2s ease;
        }
        .nav-right a.nav-link:hover { 
            opacity: 1; 
            text-decoration: underline;
            text-underline-offset: 4px;
        }
        
        /* The White Inverted Button */
        .btn-create { 
            background-color: #ffffff; 
            color: #b08d57 !important; 
            padding: 8px 20px; 
            border-radius: 4px; 
            text-decoration: none; 
            font-size: 0.9rem; 
            font-weight: 600; 
            margin-left: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .btn-create:hover { 
            background-color: #f9f9f9; 
            transform: translateY(-1px);
        }

        /* Dashboard Header Area */
        .dashboard-header { 
            padding: 80px 50px; 
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600&q=80');
            background-size: cover; 
            background-position: center; 
            color: #fff; 
        }
        .dashboard-header h1 { font-family: "Georgia", serif; font-size: 3rem; margin: 0; }

        /* The Main Content Layout */
        .main-container { max-width: 1000px; margin: -50px auto 40px auto; padding: 0 20px; }
        
        /* Side-by-Side Posts Grid */
        .posts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .blog-card { 
            background: #fff; 
            border: 1px solid #eee; 
            padding: 25px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            transition: transform 0.2s ease;
        }
        .blog-card:hover { transform: translateY(-3px); }
        
        .action-btn { 
            font-size: 0.8rem; text-decoration: none; padding: 6px 14px; 
            border-radius: 4px; font-weight: 600; margin-top: 15px; 
            display: inline-block; transition: background 0.2s;
        }
        .edit-link { color: #b08d57; background: #fdfaf5; border: 1px solid #f2e6d3; margin-right: 10px; }
        .edit-link:hover { background: #f5eedf; }
        
        .delete-link { color: #c85a5a; background: #fff5f5; border: 1px solid #fbdcdc; }
        .delete-link:hover { background: #ffe6e6; }
    </style>
</head>
<body>

    <nav>
        <div class="nav-left">
            <a href="index.php">◆ Off the Pages</a>
        </div>
        <div class="nav-right">
            <a href="blogs.php" class="nav-link">Community Blogs</a>
            <a href="dashboard.php" class="nav-link" style="opacity: 1; font-weight: bold;">Dashboard</a>
            <a href="logout.php" class="nav-link">Logout</a>
            <a href="create_blog.php" class="btn-create">+ Create Post</a>
        </div>
    </nav>

    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($username); ?>.</h1>
    </div>

    <div class="main-container">
        <h2 style="font-family: Georgia, serif; margin-bottom: 25px; color: #222;">Your Contributions</h2>
        
        <div class="posts-grid">
            <?php if ($posts_result && $posts_result->num_rows > 0): ?>
                <?php while($post = $posts_result->fetch_assoc()): ?>
                    <div class="blog-card">
                        <h3 style="margin-top:0; color: #111;"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p style="color:#888; font-size: 0.85rem; margin-bottom: 20px;">
                            Published on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                        </p>
                        <a href="edit_blog.php?id=<?php echo $post['id']; ?>" class="action-btn edit-link">Edit Story</a>
                        <a href="delete_blog.php?id=<?php echo $post['id']; ?>" class="action-btn delete-link" onclick="return confirm('Are you sure you want to delete this story?');">Delete</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; background: #fff; padding: 40px; text-align: center; border-radius: 8px; border: 1px dashed #ccc;">
                    <p style="color: #666; font-size: 1.1rem;">You haven't published any stories yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
<?php 
// Clean up the database connection safely
if (isset($posts_stmt)) $posts_stmt->close(); 
?>