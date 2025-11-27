<?php
require_once '../config.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $id = $_POST['id'] ?? null;
            $title = sanitize($_POST['title']);
            $code = $_POST['code']; // Don't sanitize HTML code
            $position = sanitize($_POST['position']);
            $status = sanitize($_POST['status']);
            
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare("INSERT INTO ads (title, code, position, status) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$title, $code, $position, $status])) {
                    $success = 'Ad added successfully!';
                }
            } else {
                $stmt = $pdo->prepare("UPDATE ads SET title=?, code=?, position=?, status=? WHERE id=?");
                if ($stmt->execute([$title, $code, $position, $status, $id])) {
                    $success = 'Ad updated successfully!';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM ads WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Ad deleted successfully!';
            }
        }
    }
}

$ads = $pdo->query("SELECT * FROM ads ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Advertisements - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Advertisements</h1>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2>Add New Advertisement</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Ad Title (for reference)</label>
                        <input type="text" name="title" required class="form-control" placeholder="e.g. Sidebar Banner, Header Ad">
                    </div>
                    <div class="form-group">
                        <label>Ad Code (HTML/JavaScript)</label>
                        <textarea name="code" class="form-control" rows="6" required placeholder="Paste your ad code here (Google AdSense, banner HTML, etc.)"></textarea>
                        <small>Paste Google AdSense code, banner HTML, or any ad network code</small>
                    </div>
                    <div class="form-group">
                        <label>Position</label>
                        <select name="position" class="form-control">
                            <option value="header">Header (Top of page)</option>
                            <option value="sidebar">Sidebar (Right side)</option>
                            <option value="article-top">Article Top (Before content)</option>
                            <option value="article-middle">Article Middle</option>
                            <option value="article-bottom">Article Bottom (After content)</option>
                            <option value="footer">Footer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Advertisement</button>
                </form>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <h2>All Advertisements</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ads as $ad): ?>
                        <tr>
                            <td><?= $ad['id'] ?></td>
                            <td><?= htmlspecialchars($ad['title']) ?></td>
                            <td><?= htmlspecialchars($ad['position']) ?></td>
                            <td><span style="color: <?= $ad['status'] === 'active' ? 'green' : 'red' ?>"><?= ucfirst($ad['status']) ?></span></td>
                            <td><?= date('M j, Y', strtotime($ad['created_at'])) ?></td>
                            <td>
                                <button onclick="viewCode(<?= $ad['id'] ?>)" class="btn btn-sm">View Code</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this ad?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $ad['id'] ?>">
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