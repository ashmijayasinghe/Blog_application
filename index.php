<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Off the Pages - Home</title>
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
        .nav-left a { text-decoration: none; color: #ffffff; font-weight: 400; font-size: 1.15rem; letter-spacing: 1px; font-family: 'Cormorant Garamond', serif; }
        .nav-right { display: flex; align-items: center; gap: 28px; }
        .nav-right a { color: #f5f1ed; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: 0.3s; font-family: 'Poppins', sans-serif; }
        .nav-right a:hover { color: #ffffff; }
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
            height: 550px;
            background: linear-gradient(135deg, #3d3d3d 0%, #2a2a2a 100%);
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
            max-width: 800px;
            padding: 40px;
        }
        .hero-content h1 {
            font-size: 4.5rem;
            font-weight: 400;
            margin-bottom: 16px;
            letter-spacing: 2px;
            font-family: 'Cormorant Garamond', serif;
            line-height: 1.2;
        }
        .hero-content p {
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        /* Section Header */
        .section-header {
            max-width: 1200px;
            margin: 60px auto 45px auto;
            padding: 0 40px;
        }
        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 8px;
            padding-bottom: 12px;
            border-bottom: 3px solid #b08d57;
            display: inline-block;
        }

        /* Card Grid */
        .card-grid {
            max-width: 1200px;
            margin: 0 auto 70px auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .card:hover {
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
            transform: translateY(-6px);
        }
        .card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #e8e8e8;
        }
        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .card-content h3 {
            font-size: 1rem;
            line-height: 1.5;
            margin: 0 0 12px 0;
            color: #111;
            font-weight: 600;
        }
        .card-meta {
            font-size: 0.8rem;
            color: #999;
            margin-bottom: 12px;
            font-weight: 500;
        }
        .read-more-btn {
            background-color: #b08d57;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-block;
            width: fit-content;
            margin-top: auto;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }
        .read-more-btn:hover {
            background-color: #9d7a48;
            transform: translateX(4px);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .card-grid { grid-template-columns: repeat(2, 1fr); }
            .hero-banner { height: 450px; }
            .hero-content h1 { font-size: 3.5rem; }
        }
        @media (max-width: 640px) {
            .card-grid { grid-template-columns: 1fr; }
            .hero-banner { height: 350px; }
            .hero-content h1 { font-size: 2.5rem; }
            .hero-content p { font-size: 1rem; }
            nav { padding: 12px 20px; }
            .nav-right { gap: 15px; }
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
            <h1>Off the Pages</h1>
            <p>Words, beats, life, art & inspiration</p>
        </div>
    </div>

    <!-- Featured Section -->
    <div class="section-header">
        <h2>Featured Stories</h2>
    </div>

    <div class="card-grid">
        <div class="card">
            <img src="https://picsum.photos/600/200?random=1" alt="DJ Event" class="card-image">
            <div class="card-content">
                <div class="card-meta">Music & Culture</div>
                <h3>How Hip-Hop Can Help a Nation in Crisis</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=2" alt="Meeting" class="card-image">
            <div class="card-content">
                <div class="card-meta">Music Industry</div>
                <h3>Inside The B-Side Program at Carnegie Hall</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=3" alt="Group of teens" class="card-image">
            <div class="card-content">
                <div class="card-meta">Community</div>
                <h3>The Need for a Third Space: Teen Space</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=4" alt="Slam Team" class="card-image">
            <div class="card-content">
                <div class="card-meta">Spoken Word</div>
                <h3>2026 Slam Season Crowns the DMV Youth Slam Team</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>
    </div>

    <!-- Art Tutorials Section -->
    <div class="section-header">
        <h2>Art Tutorials</h2>
    </div>

    <div class="card-grid">
        <div class="card">
            <img src="https://picsum.photos/600/200?random=5" alt="Drawing" class="card-image">
            <div class="card-content">
                <div class="card-meta">Visual Arts</div>
                <h3>Mastering Digital Illustration: A Beginner's Guide</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=6" alt="Painting" class="card-image">
            <div class="card-content">
                <div class="card-meta">Painting</div>
                <h3>Acrylic Painting Techniques for Expressive Art</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=7" alt="Design" class="card-image">
            <div class="card-content">
                <div class="card-meta">Design</div>
                <h3>Graphic Design Principles for Modern Creators</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=8" alt="Sculpture" class="card-image">
            <div class="card-content">
                <div class="card-meta">Sculpture</div>
                <h3>3D Art: From Concept to Creation</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>
    </div>

    <!-- College Prep Section -->
    <div class="section-header">
        <h2>College Preparation</h2>
    </div>

    <div class="card-grid">
        <div class="card">
            <img src="https://picsum.photos/600/200?random=9" alt="College" class="card-image">
            <div class="card-content">
                <div class="card-meta">Education</div>
                <h3>Choosing the Right College for Your Future</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=10" alt="SAT" class="card-image">
            <div class="card-content">
                <div class="card-meta">Test Prep</div>
                <h3>SAT and ACT: Complete Preparation Strategy</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=11" alt="Essay" class="card-image">
            <div class="card-content">
                <div class="card-meta">Essay Writing</div>
                <h3>Crafting Compelling College Application Essays</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=12" alt="Scholarship" class="card-image">
            <div class="card-content">
                <div class="card-meta">Scholarships</div>
                <h3>Finding and Winning Scholarships: Your Complete Guide</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>
    </div>

    <!-- Academics & Life Skills -->
    <div class="section-header">
        <h2>Academic Excellence & Life Skills</h2>
    </div>

    <div class="card-grid">
        <div class="card">
            <img src="https://picsum.photos/600/200?random=13" alt="Study" class="card-image">
            <div class="card-content">
                <div class="card-meta">Study Tips</div>
                <h3>Effective Study Techniques That Actually Work</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=14" alt="Leadership" class="card-image">
            <div class="card-content">
                <div class="card-meta">Leadership</div>
                <h3>Building Leadership Skills in Youth</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=15" alt="Career" class="card-image">
            <div class="card-content">
                <div class="card-meta">Career Development</div>
                <h3>Finding Your Passion: Career Exploration Guide</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>

        <div class="card">
            <img src="https://picsum.photos/600/200?random=16" alt="Mindfulness" class="card-image">
            <div class="card-content">
                <div class="card-meta">Wellness</div>
                <h3>Mental Health and Mindfulness for Students</h3>
                <a href="#" class="read-more-btn">Read More</a>
            </div>
        </div>
    </div>

</body>
</html>