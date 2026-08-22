<?php
// 1. Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Require authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if an ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Blog post ID is missing.");
}

$post_id = intval($_GET['id']);

// 3. Fetch the post from the database (using the lowercase 'blogpost' table)
$stmt = $conn->prepare("SELECT blogpost.*, user.username FROM blogpost 
                        JOIN user ON blogpost.user_id = user.id 
                        WHERE blogpost.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: The requested blog post does not exist.");
}

$post = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($post['title']); ?> - Off the Pages</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: #fafafa; 
            color: #1e1e1e; 
        }

        /* Navigation */
        nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            padding: 16px 40px; 
            background-color: #b08d57; 
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

        /* Container */
        .container { 
            max-width: 900px; 
            margin: 60px auto; 
            padding: 0 40px; 
        }

        /* Article Header */
        .article-header {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #eee;
        }

        .article-header h1 {
            font-size: 2.8rem;
            font-weight: 600;
            color: #111;
            margin-bottom: 16px;
            line-height: 1.2;
            font-family: 'Poppins', sans-serif;
        }

        .article-meta {
            font-size: 0.95rem;
            color: #999;
            font-weight: 500;
        }

        .article-meta strong {
            color: #111;
        }

        /* Article Content */
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 50px;
        }

        /* Actions */
        .article-actions {
            padding-top: 30px;
            border-top: 2px solid #eee;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn { 
            padding: 12px 20px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 0.9rem;
            border-radius: 8px; 
            display: inline-block; 
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }

        .btn:hover { 
            transform: translateY(-2px);
        }

        .btn-edit { 
            background-color: #f7efe2; 
            color: #b08d57;
        }

        .btn-edit:hover {
            background-color: #f0e6d2;
            color: #9d7a48;
        }

        .btn-delete { 
            background-color: #ffe5e5;
            color: #c85a5a;
        }

        .btn-delete:hover {
            background-color: #ffd6d6;
            color: #b84545;
        }

        .btn-back {
            background-color: #f5f5f5;
            color: #666;
        }

        .btn-back:hover {
            background-color: #efefef;
            color: #111;
        }

        .success-msg {
            background-color: #e5f8ed;
            color: #2d7a5f;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2d7a5f;
            font-family: 'Poppins', sans-serif;
        }

        /* Responsive */
        @media (max-width: 640px) {
            nav { padding: 12px 20px; }
            .nav-right { gap: 15px; }
            .container { padding: 0 20px; }
            .article-header h1 { font-size: 1.8rem; }
            .article-actions { flex-direction: column; }
            .btn { width: 100%; text-align: center; }
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

    <div class="container">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="success-msg">
                ✅ Success! Your blog post has been successfully updated.
            </div>
        <?php endif; ?>

        <div class="article-header">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="article-meta">
                By <strong><?php echo htmlspecialchars($post['username']); ?></strong> 
                on <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
            </div>
        </div>
        
        <div class="article-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <div class="article-actions">
            <a href="blogs.php" class="btn btn-back">← Back to Community</a>
            
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                <a href="edit_blog.php?id=<?php echo $post['id']; ?>" class="btn btn-edit">✏️ Edit</a>
                <a href="delete_blog.php?id=<?php echo $post['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this blog post?');">🗑️ Delete</a>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
