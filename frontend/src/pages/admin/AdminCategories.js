import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import CategoryForm from "../../components/admin/CategoryForm";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminCategories = () => {
  const [categories, setCategories] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editingCategory, setEditingCategory] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      const response = await axios.get(`${API}/categories`);
      setCategories(response.data);
    } catch (error) {
      console.error("Error fetching categories:", error);
      toast.error("Failed to load categories");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (categoryId) => {
    if (!window.confirm("Are you sure you want to delete this category?")) return;
    
    try {
      await axios.delete(`${API}/categories/${categoryId}`);
      toast.success("Category deleted successfully");
      fetchCategories();
    } catch (error) {
      console.error("Error deleting category:", error);
      toast.error("Failed to delete category");
    }
  };

  const handleEdit = (category) => {
    setEditingCategory(category);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingCategory(null);
    fetchCategories();
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
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-categories-title">Categories</h1>
          <Button onClick={() => setShowForm(true)} data-testid="create-category-btn">
            + Create Category
          </Button>
        </div>

        {showForm && (
          <CategoryForm 
            category={editingCategory}
            onClose={handleFormClose}
          />
        )}

        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))',
          gap: '1.5rem'
        }}>
          {categories.map((category) => (
            <div 
              key={category.id}
              data-testid={`category-card-${category.id}`}
              style={{
                background: 'white',
                borderRadius: '12px',
                padding: '1.5rem',
                boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
                border: '1px solid #e5e7eb'
              }}
            >
              <h3 style={{ fontSize: '1.25rem', fontWeight: '700', marginBottom: '0.5rem' }} data-testid={`category-name-${category.id}`}>
                {category.name}
              </h3>
              <p style={{ fontSize: '0.875rem', color: '#6b7280', marginBottom: '0.5rem' }}>
                {category.slug}
              </p>
              {category.description && (
                <p style={{ fontSize: '0.95rem', color: '#4b5563', marginBottom: '1rem' }}>
                  {category.description}
                </p>
              )}
              <div style={{ display: 'flex', gap: '0.5rem', marginTop: '1rem' }}>
                <button
                  onClick={() => handleEdit(category)}
                  data-testid={`edit-category-${category.id}`}
                  style={{
                    flex: 1,
                    padding: '0.5rem 1rem',
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
                  onClick={() => handleDelete(category.id)}
                  data-testid={`delete-category-${category.id}`}
                  style={{
                    flex: 1,
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
              </div>
            </div>
          ))}
        </div>

        {categories.length === 0 && (
          <div style={{
            background: 'white',
            borderRadius: '12px',
            padding: '3rem',
            textAlign: 'center',
            color: '#6b7280',
            boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
            border: '1px solid #e5e7eb'
          }}>
            <p>No categories found. Create your first category!</p>
          </div>
        )}
      </div>
    </AdminLayout>
  );
};

export default AdminCategories;
