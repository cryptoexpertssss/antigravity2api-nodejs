<?php
require_once '../config.php';
requireLogin();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $success = "Article deleted successfully!";
        } elseif ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $title = sanitize($_POST['title']);
            $slug = sanitize($_POST['slug']);
            $content = $_POST['content']; // Allow HTML
            $excerpt = sanitize($_POST['excerpt']);
            $category_id = (int)$_POST['category_id'];
            $author = sanitize($_POST['author']);
            $status = sanitize($_POST['status']);
            
            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, excerpt, category_id, author, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $author, $status]);
                $success = "Article created successfully!";
            } else {
                $stmt = $pdo->prepare("UPDATE articles SET title=?, slug=?, content=?, excerpt=?, category_id=?, author=?, status=? WHERE id=?");
                $stmt->execute([$title, $slug, $content, $excerpt, $category_id, $author, $status, $_POST['id']]);
                $success = "Article updated successfully!";
            }
        }
    }
}

// Get articles
$articles = $pdo->query("SELECT a.*, c.name as category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Articles - Admin</title>
    <?php include 'header.php'; ?>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h1>Manage Articles</h1>
                <button onclick="openModal()" class="btn btn-primary">+ New Article</button>
            </div>

            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($articles as $article): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($article['title']) ?></strong><br><small><?= htmlspecialchars($article['slug']) ?></small></td>
                        <td><?= htmlspecialchars($article['category_name']) ?></td>
                        <td><?= htmlspecialchars($article['author']) ?></td>
                        <td><span style="background: <?= $article['status']=='published' ? '#d1fae5' : '#fef3c7' ?>; padding: 4px 12px; border-radius: 12px; font-size: 12px;"><?= $article['status'] ?></span></td>
                        <td><?= date('M j, Y', strtotime($article['created_at'])) ?></td>
                        <td>
                            <button onclick='editArticle(<?= json_encode($article) ?>)' class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this article?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="articleModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Create Article</h2>
            <form method="POST">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="id" id="articleId">
                
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" id="title" required>
                </div>
                
                <div class="form-group">
                    <label>Slug *</label>
                    <input type="text" name="slug" id="slug" required>
                </div>
                
                <div class="form-group">
                    <label>Category *</label>
                    <select name="category_id" id="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Author *</label>
                    <input type="text" name="author" id="author" value="<?= $_SESSION['admin_name'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Excerpt *</label>
                    <textarea name="excerpt" id="excerpt" rows="3" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Content * (HTML allowed)</label>
                    <textarea name="content" id="content" rows="10" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="status" required>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Article</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('articleModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Create Article';
            document.getElementById('formAction').value = 'create';
            document.querySelector('form').reset();
        }
        
        function closeModal() {
            document.getElementById('articleModal').classList.remove('active');
        }
        
        function editArticle(article) {
            document.getElementById('articleModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit Article';
            document.getElementById('formAction').value = 'update';
            document.getElementById('articleId').value = article.id;
            document.getElementById('title').value = article.title;
            document.getElementById('slug').value = article.slug;
            document.getElementById('category_id').value = article.category_id;
            document.getElementById('author').value = article.author;
            document.getElementById('excerpt').value = article.excerpt;
            document.getElementById('content').value = article.content;
            document.getElementById('status').value = article.status;
        }
        
        // Auto-generate slug from title
        document.getElementById('title').addEventListener('input', function() {
            if(document.getElementById('formAction').value === 'create') {
                document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
            }
        });
    </script>
</body>
</html>
