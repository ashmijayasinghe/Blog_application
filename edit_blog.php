<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Blog post ID is missing in the URL.");
}

$post_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM blogpost WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Error: Post not found in the database.");
}

if ($post['user_id'] != $_SESSION['user_id']) {
    die("Access Denied: You do not have permission to edit blogs created by other users.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $update_stmt = $conn->prepare("UPDATE blogpost SET title = ?, content = ? WHERE id = ?");
        $update_stmt->bind_param("ssi", $title, $content, $post_id);
        
        if ($update_stmt->execute()) {
            // THE FIX: Perfectly formatted URL with the status trigger
            header("Location: blog.php?id=" . $post_id . "&status=updated");
            exit();
        } else {
            $message = "<p style='color: red;'>Failed to update post.</p>";
        }
        $update_stmt->close();
    } else {
        $message = "<p style='color: red;'>All fields are required!</p>";
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog Post</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 0; }
        .form-container { max-width: 640px; margin: 50px auto; padding: 32px; background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); border: 1px solid #e5e7eb; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 14px; box-sizing: border-box; border: 1px solid #d1d5db; border-radius: 10px; font-size: 0.95rem; }
        .form-group textarea { height: 180px; resize: vertical; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #3858E9; box-shadow: 0 0 0 3px rgba(56, 88, 233, 0.15); }
        .btn { background-color: #b08d57; color: white; border: none; padding: 10px 16px; border-radius: 6px; font-size: 0.95rem; cursor: pointer; font-weight: 600; transition: all 0.2s ease; }
        .btn:hover { background-color: #9d7a48; }
        .cancel-link { margin-left: 15px; text-decoration: none; color: #666; font-weight: 600; }
        .cancel-link:hover { color: #b08d57; text-decoration: underline; }
        .cancel-link:hover { color: #111827; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit Your Blog Post</h2>
        <?php echo $message; ?>
        <form action="edit_blog.php?id=<?php echo $post_id; ?>" method="POST">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
            </div>
            <div class="form-group">
                <label>Content</label>
                <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>
            <button type="submit" class="btn">Update Post</button>
            <a href="blog.php?id=<?php echo $post_id; ?>" class="cancel-link">Cancel</a>
        </form>
    </div>

</body>
</html>