<?php
require_once '../config.php';
requireLogin();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $id = $_POST['id'] ?? null;
            $name = sanitize($_POST['name']);
            $url = sanitize($_POST['url']);
            $description = sanitize($_POST['description']);
            $category = sanitize($_POST['category']);
            $status = sanitize($_POST['status']);
            
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare("INSERT INTO affiliate_links (name, url, description, category, status) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $url, $description, $category, $status])) {
                    $success = 'Affiliate link added successfully!';
                }
            } else {
                $stmt = $pdo->prepare("UPDATE affiliate_links SET name=?, url=?, description=?, category=?, status=? WHERE id=?");
                if ($stmt->execute([$name, $url, $description, $category, $status, $id])) {
                    $success = 'Affiliate link updated successfully!';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM affiliate_links WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Affiliate link deleted successfully!';
            }
        }
    }
}

$links = $pdo->query("SELECT * FROM affiliate_links ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Affiliate Links - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Affiliate Links</h1>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2>Add New Affiliate Link</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Link Name</label>
                        <input type="text" name="name" required class="form-control" placeholder="e.g. Bet365, 888Casino">
                    </div>
                    <div class="form-group">
                        <label>Affiliate URL</label>
                        <input type="url" name="url" required class="form-control" placeholder="https://youraffiliatelink.com">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control">
                            <option value="casino">Casino</option>
                            <option value="sports">Sports Betting</option>
                            <option value="poker">Poker</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Affiliate Link</button>
                </form>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <h2>All Affiliate Links</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>URL</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($links as $link): ?>
                        <tr>
                            <td><?= $link['id'] ?></td>
                            <td><?= htmlspecialchars($link['name']) ?></td>
                            <td><a href="<?= htmlspecialchars($link['url']) ?>" target="_blank" style="font-size:12px;"><?= substr(htmlspecialchars($link['url']), 0, 50) ?>...</a></td>
                            <td><?= htmlspecialchars($link['category']) ?></td>
                            <td><span style="color: <?= $link['status'] === 'active' ? 'green' : 'red' ?>"><?= ucfirst($link['status']) ?></span></td>
                            <td><?= date('M j, Y', strtotime($link['created_at'])) ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this link?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $link['id'] ?>">
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