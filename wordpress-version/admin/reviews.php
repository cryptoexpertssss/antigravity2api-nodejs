<?php
require_once '../config.php';
requireLogin();

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'approve') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE reviews SET status='approved' WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Review approved!';
            }
        } elseif ($_POST['action'] === 'reject') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE reviews SET status='rejected' WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Review rejected!';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Review deleted!';
            }
        }
    }
}

$reviews = $pdo->query("SELECT r.*, c.name as casino_name, u.username FROM reviews r LEFT JOIN casinos c ON r.casino_id=c.id LEFT JOIN users u ON r.user_id=u.id ORDER BY r.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reviews - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>User Reviews</h1>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Casino</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($reviews as $review): ?>
                        <tr>
                            <td><?= $review['id'] ?></td>
                            <td><?= htmlspecialchars($review['casino_name']) ?></td>
                            <td><?= htmlspecialchars($review['username']) ?></td>
                            <td>⭐ <?= $review['rating'] ?></td>
                            <td><?= htmlspecialchars(substr($review['review_text'], 0, 100)) ?>...</td>
                            <td>
                                <span style="color: <?= $review['status'] === 'approved' ? 'green' : ($review['status'] === 'pending' ? 'orange' : 'red') ?>">
                                    <?= ucfirst($review['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M j, Y', strtotime($review['created_at'])) ?></td>
                            <td>
                                <?php if($review['status'] !== 'approved'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                    <button type="submit" class="btn btn-sm">Approve</button>
                                </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this review?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $review['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>