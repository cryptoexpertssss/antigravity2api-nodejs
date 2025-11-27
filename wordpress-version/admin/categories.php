<?php
require_once '../config.php';
requireLogin();

$success = '';
$error = '';

// Handle Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            if ($stmt->execute([$name, $description])) {
                $success = 'Category added successfully!';
            } else {
                $error = 'Failed to add category.';
            }
        } elseif ($_POST['action'] === 'edit') {
            $id = (int)$_POST['id'];
            $name = sanitize($_POST['name']);
            $description = sanitize($_POST['description']);
            
            $stmt = $pdo->prepare("UPDATE categories SET name=?, description=? WHERE id=?");
            if ($stmt->execute([$name, $description, $id])) {
                $success = 'Category updated successfully!';
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
            if ($stmt->execute([$id])) {
                $success = 'Category deleted successfully!';
            }
        }
    }
}

// Get all categories
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h1>Categories</h1>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add Category Form -->
            <div style="background: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
                <h2>Add New Category</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </form>
            </div>
            
            <!-- Categories List -->
            <div style="background: white; padding: 30px; border-radius: 12px;">
                <h2>All Categories</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td>
                                <button onclick="editCategory(<?= $cat['id'] ?>, '<?= addslashes($cat['name']) ?>', '<?= addslashes($cat['description']) ?>')" class="btn btn-sm">Edit</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
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
    
    <script>
    function editCategory(id, name, description) {
        if(confirm('Edit this category?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="${id}">
                <input type="text" name="name" value="${name}" required>
                <textarea name="description">${description}</textarea>
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>