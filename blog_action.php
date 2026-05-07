<?php
require_once 'connection.php';

$action = $_REQUEST['action'] ?? '';

if ($action == 'get_comments') {
    $blog_id = (int) $_GET['blog_id'];
    $result = mysqli_query($link, "SELECT * FROM blog_comments WHERE blog_id = $blog_id ORDER BY created_at DESC");
    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['created_at'] = date('d M Y, h:i A', strtotime($row['created_at']));
        $comments[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($comments);
    exit;
}

if ($action == 'post_comment') {
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Please login to post a comment.']);
        exit;
    }

    $blog_id = (int) $_POST['blog_id'];
    $user_id = (int) $_SESSION['user_id'];
    $user_name = mysqli_real_escape_string($link, $_SESSION['user_name']);
    
    // Fetch email from session or DB if not in session
    $email = '';
    $user_q = mysqli_query($link, "SELECT email FROM users WHERE id = $user_id");
    if($user_r = mysqli_fetch_assoc($user_q)) {
        $email = mysqli_real_escape_string($link, $user_r['email']);
    }

    $comment = mysqli_real_escape_string($link, $_POST['comment']);
    
    $sql = "INSERT INTO blog_comments (blog_id, user_name, email, comment) 
            VALUES ($blog_id, '$user_name', '$email', '$comment')";
    
    header('Content-Type: application/json');
    if (mysqli_query($link, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Comment posted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post comment.']);
    }
    exit;
}

if ($action == 'like') {
    $blog_id = (int) $_GET['blog_id'];
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $session_id = session_id();

    if ($user_id) {
        $check = mysqli_query($link, "SELECT * FROM blog_likes WHERE blog_id = $blog_id AND user_id = $user_id");
    } else {
        $check = mysqli_query($link, "SELECT * FROM blog_likes WHERE blog_id = $blog_id AND session_id = '$session_id'");
    }
    
    header('Content-Type: application/json');
    if (mysqli_num_rows($check) == 0) {
        if ($user_id) {
            mysqli_query($link, "INSERT INTO blog_likes (blog_id, user_id) VALUES ($blog_id, $user_id)");
        } else {
            mysqli_query($link, "INSERT INTO blog_likes (blog_id, session_id) VALUES ($blog_id, '$session_id')");
        }
        mysqli_query($link, "UPDATE blogs SET likes_count = (SELECT COUNT(*) FROM blog_likes WHERE blog_id = $blog_id) WHERE id = $blog_id");
        
        $res = mysqli_query($link, "SELECT likes_count FROM blogs WHERE id = $blog_id");
        $row = mysqli_fetch_assoc($res);
        
        echo json_encode(['status' => 'success', 'likes_count' => $row['likes_count']]);
    } else {
        echo json_encode(['status' => 'already_liked', 'message' => 'You already liked this post.']);
    }
    exit;
}
