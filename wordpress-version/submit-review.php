<?php
require_once 'config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    redirect('user-login.php?error=login_required');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $casino_id = (int)$_POST['casino_id'];
    $rating = (float)$_POST['rating'];
    $title = sanitize($_POST['title']);
    $comment = sanitize($_POST['comment']);
    $pros = isset($_POST['pros']) ? array_filter(array_map('trim', $_POST['pros'])) : [];
    $cons = isset($_POST['cons']) ? array_filter(array_map('trim', $_POST['cons'])) : [];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (casino_id, user_name, rating, title, comment, pros, cons, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $casino_id,
            $_SESSION['user_username'],
            $rating,
            $title,
            $comment,
            json_encode($pros),
            json_encode($cons)
        ]);
        
        $success = 'Review submitted successfully! It will be visible after admin approval.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Redirect back to casino page
if ($success) {
    $_SESSION['review_success'] = $success;
    redirect('casino-detail.php?id=' . $_POST['casino_id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Review - GamingToday</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { margin-bottom: 30px; }
        .error { background: #fee; border: 1px solid #fcc; padding: 10px; border-radius: 8px; color: #c00; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        button { padding: 12px 24px; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        button:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Submit Review</h1>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <p>Go back to casino page to submit review</p>
    </div>
</body>
</html>
