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
            $rating = (float)$_POST['rating'];
            $bonus = sanitize($_POST['bonus']);
            $description = sanitize($_POST['description']);
            $affiliate_link = sanitize($_POST['affiliate_link']);
            $logo_url = sanitize($_POST['logo_url']);
            
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare("INSERT INTO casinos (name, rating, bonus, description, affiliate_link, logo_url) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt->execute([$name, $rating, $bonus, $description, $affiliate_link, $logo_url])) {
                    $success = 'Casino added successfully!';
                }
            } else {
                $stmt = $pdo->prepare("UPDATE casinos SET name=?, rating=?, bonus=?, description=?, affiliate_link=?, logo_url=? WHERE id=?");
                if ($stmt->execute([$name, $rating, $bonus, $description, $affiliate_link, $logo_url, $id])) {
                    $success = 'Casino updated successfully!';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM casinos WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Casino deleted successfully!';
            }
        }
    }
}

$casinos = $pdo->query("SELECT * FROM casinos ORDER BY rating DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Casinos - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Casino Rankings</h1>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2>Add New Casino</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Casino Name</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Rating (1-5)</label>
                        <input type="number" step="0.1" min="1" max="5" name="rating" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Welcome Bonus</label>
                        <input type="text" name="bonus" placeholder="e.g. 100% up to $500" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Affiliate Link</label>
                        <input type="url" name="affiliate_link" placeholder="https://" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Logo URL</label>
                        <input type="url" name="logo_url" placeholder="https://" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Casino</button>
                </form>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <h2>All Casinos</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Rating</th>
                            <th>Bonus</th>
                            <th>Affiliate Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($casinos as $casino): ?>
                        <tr>
                            <td><?php if($casino['logo_url']): ?><img src="<?= htmlspecialchars($casino['logo_url']) ?>" style="width:50px; height:50px; object-fit:contain;"><?php endif; ?></td>
                            <td><?= htmlspecialchars($casino['name']) ?></td>
                            <td>⭐ <?= $casino['rating'] ?></td>
                            <td><?= htmlspecialchars($casino['bonus']) ?></td>
                            <td><?php if($casino['affiliate_link']): ?><a href="<?= htmlspecialchars($casino['affiliate_link']) ?>" target="_blank">Link</a><?php endif; ?></td>
                            <td>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this casino?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $casino['id'] ?>">
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