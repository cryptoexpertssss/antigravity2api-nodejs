import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import ArticleForm from "../../components/admin/ArticleForm";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminArticles = () => {
  const [articles, setArticles] = useState([]);
  const [categories, setCategories] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editingArticle, setEditingArticle] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [articlesRes, categoriesRes] = await Promise.all([
        axios.get(`${API}/articles`),
        axios.get(`${API}/categories`)
      ]);
      setArticles(articlesRes.data);
      setCategories(categoriesRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
      toast.error("Failed to load articles");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (articleId) => {
    if (!window.confirm("Are you sure you want to delete this article?")) return;
    
    try {
      await axios.delete(`${API}/articles/${articleId}`);
      toast.success("Article deleted successfully");
      fetchData();
    } catch (error) {
      console.error("Error deleting article:", error);
      toast.error("Failed to delete article");
    }
  };

  const handleEdit = (article) => {
    setEditingArticle(article);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingArticle(null);
    fetchData();
  };

  const getCategoryName = (categoryId) => {
    const category = categories.find(c => c.id === categoryId);
    return category ? category.name : 'Unknown';
  };

  if (loading) {
    return (
      <AdminLayout>
        <div className="loading">Loading...</div>
      </AdminLayout>
    );
  }

  return (
    <AdminLayout>
      <div>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-articles-title">Articles</h1>
          <Button onClick={() => setShowForm(true)} data-testid="create-article-btn">
            + Create Article
          </Button>
        </div>

        {showForm && (
          <ArticleForm 
            article={editingArticle}
            categories={categories}
            onClose={handleFormClose}
          />
        )}

        <div style={{
          background: 'white',
          borderRadius: '12px',
          overflow: 'hidden',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead style={{ background: '#f9fafb', borderBottom: '2px solid #e5e7eb' }}>
              <tr>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>TITLE</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>CATEGORY</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>AUTHOR</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>STATUS</th>
                <th style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              {articles.map((article, index) => (
                <tr 
                  key={article.id}
                  data-testid={`article-row-${article.id}`}
                  style={{
                    borderBottom: index !== articles.length - 1 ? '1px solid #e5e7eb' : 'none'
                  }}
                >
                  <td style={{ padding: '1rem' }}>
                    <div style={{ fontWeight: '600', marginBottom: '0.25rem' }} data-testid={`article-title-${article.id}`}>
                      {article.title}
                    </div>
                    <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>{article.slug}</div>
                  </td>
                  <td style={{ padding: '1rem', color: '#4b5563' }}>
                    {getCategoryName(article.category_id)}
                  </td>
                  <td style={{ padding: '1rem', color: '#4b5563' }}>
                    {article.author}
                  </td>
                  <td style={{ padding: '1rem' }}>
                    <span style={{
                      display: 'inline-block',
                      padding: '0.25rem 0.75rem',
                      borderRadius: '20px',
                      fontSize: '0.75rem',
                      fontWeight: '600',
                      background: article.status === 'published' ? '#d1fae5' : '#fef3c7',
                      color: article.status === 'published' ? '#065f46' : '#92400e'
                    }}>
                      {article.status}
                    </span>
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <button
                      onClick={() => handleEdit(article)}
                      data-testid={`edit-article-${article.id}`}
                      style={{
                        padding: '0.5rem 1rem',
                        marginRight: '0.5rem',
                        background: '#3b82f6',
                        color: 'white',
                        border: 'none',
                        borderRadius: '6px',
                        cursor: 'pointer',
                        fontSize: '0.875rem',
                        fontWeight: '600'
                      }}
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDelete(article.id)}
                      data-testid={`delete-article-${article.id}`}
                      style={{
                        padding: '0.5rem 1rem',
                        background: '#ef4444',
                        color: 'white',
                        border: 'none',
                        borderRadius: '6px',
                        cursor: 'pointer',
                        fontSize: '0.875rem',
                        fontWeight: '600'
                      }}
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {articles.length === 0 && (
            <div style={{ padding: '3rem', textAlign: 'center', color: '#6b7280' }}>
              <p>No articles found. Create your first article!</p>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
};

export default AdminArticles;
