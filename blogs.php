<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Fetch all dynamic user blogs
$sql = "SELECT blogpost.*, user.username 
        FROM blogpost 
        JOIN user ON blogpost.user_id = user.id 
        ORDER BY blogpost.created_at DESC";
        
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Community Blogs - Off the Pages</title>
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
        .btn-primary { 
            background-color: #ffffff; 
            color: #b08d57 !important; 
            padding: 10px 20px; 
            border-radius: 8px; 
            font-weight: 600; 
            font-size: 0.9rem; 
            text-decoration: none; 
            border: none; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            font-family: 'Poppins', sans-serif;
        }
        .btn-primary:hover { 
            background-color: #f5f1ed; 
            color: #9d7a48 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
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

        /* Container and Grid */
        .container { 
            max-width: 1200px; 
            margin: 60px auto; 
            padding: 0 40px; 
        }
        .section-header h2 {
            font-size: 2rem;
            font-weight: 600;
            color: #111;
            margin-bottom: 40px;
            font-family: 'Cormorant Garamond', serif;
            letter-spacing: 0.5px;
        }
        .blogs-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); 
            gap: 28px;
        }
        .blog-card { 
            background: #ffffff; 
            padding: 28px; 
            border-radius: 12px; 
            display: flex; 
            flex-direction: column; 
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: none;
        }
        .blog-link { text-decoration: none; color: inherit; display: block; }
        .blog-link .read-more-btn { display: inline-block; }
        .blog-link:focus, .blog-link:hover { outline: none; }
        .blog-card:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
            transform: translateY(-6px);
        }
        .blog-card h2 { 
            margin: 0 0 12px 0; 
            font-size: 1.3rem; 
            font-weight: 600;
            color: #111;
            font-family: 'Poppins', sans-serif;
            line-height: 1.4;
        }
        .blog-card .meta { 
            font-size: 0.85rem; 
            color: #999; 
            margin-bottom: 16px; 
            font-weight: 500;
        }
        .blog-card p { 
            line-height: 1.6; 
            flex-grow: 1; 
            margin-bottom: 20px;
            color: #666;
            font-size: 0.95rem;
        }
        .read-more-btn { 
            color: #b08d57; 
            text-decoration: none; 
            font-weight: 600; 
            background: #f7efe2; 
            padding: 8px 14px; 
            font-size: 0.85rem;
            border-radius: 6px; 
            display: inline-block; 
            width: fit-content; 
            transition: all 0.2s ease;
        }
        .read-more-btn:hover { 
            background: #f0e6d2;
            color: #9d7a48;
        }

        /* Empty State */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-banner { height: 350px; }
            .hero-content h1 { font-size: 2.8rem; }
            .blogs-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 640px) {
            .hero-banner { height: 280px; }
            .hero-content h1 { font-size: 2rem; }
            nav { padding: 12px 20px; }
            .nav-right { gap: 15px; }
            .container { padding: 0 20px; margin: 40px auto; }
            .blogs-grid { grid-template-columns: 1fr; }
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
                <a href="create_blog.php" class="btn-primary">+ Create Post</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn-primary">Get Started</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Banner -->
    <div class="hero-banner">
        <div class="hero-content">
            <h1>Community Blogs</h1>
        </div>
    </div>

    <div class="container">
        <div class="section-header">
            <h2>Latest Posts</h2>
        </div>
        <div class="blogs-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($post = $result->fetch_assoc()): ?>
                    <a href="blog.php?id=<?php echo $post['id']; ?>" class="blog-link" aria-label="Read full post <?php echo htmlspecialchars($post['title']); ?>">
                        <div class="blog-card">
                            <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                            <div class="meta">By <strong><?php echo htmlspecialchars($post['username']); ?></strong> | <?php echo date('M d, Y', strtotime($post['created_at'])); ?></div>
                            <p><?php echo substr(htmlspecialchars($post['content']), 0, 120) . '...'; ?></p>
                            <span class="read-more-btn">Read More →</span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No posts yet. Be the first to share your story!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>