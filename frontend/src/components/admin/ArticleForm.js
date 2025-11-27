import React, { useState, useEffect, useRef } from "react";
import axios from "axios";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const ArticleForm = ({ article, categories, onClose }) => {
  const [formData, setFormData] = useState({
    title: "",
    slug: "",
    content: "",
    excerpt: "",
    category_id: "",
    author: "",
    featured_image: "",
    status: "published",
    meta_description: "",
    tags: []
  });
  const [uploading, setUploading] = useState(false);
  const [tagInput, setTagInput] = useState("");

  useEffect(() => {
    if (article) {
      setFormData(article);
    }
  }, [article]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleContentChange = (e) => {
    setFormData(prev => ({ ...prev, content: e.target.value }));
  };

  const insertFormatting = (tag) => {
    const textarea = document.getElementById('article-content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = formData.content.substring(start, end);
    const before = formData.content.substring(0, start);
    const after = formData.content.substring(end);
    
    let formatted = '';
    switch(tag) {
      case 'h1':
        formatted = `${before}<h1>${selectedText}</h1>${after}`;
        break;
      case 'h2':
        formatted = `${before}<h2>${selectedText}</h2>${after}`;
        break;
      case 'h3':
        formatted = `${before}<h3>${selectedText}</h3>${after}`;
        break;
      case 'bold':
        formatted = `${before}<strong>${selectedText}</strong>${after}`;
        break;
      case 'italic':
        formatted = `${before}<em>${selectedText}</em>${after}`;
        break;
      case 'p':
        formatted = `${before}<p>${selectedText}</p>${after}`;
        break;
      case 'ul':
        formatted = `${before}<ul><li>${selectedText}</li></ul>${after}`;
        break;
      case 'link':
        const url = prompt('Enter URL:');
        if (url) formatted = `${before}<a href="${url}">${selectedText}</a>${after}`;
        else formatted = formData.content;
        break;
      default:
        formatted = formData.content;
    }
    setFormData(prev => ({ ...prev, content: formatted }));
  };

  const handleImageUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formDataObj = new FormData();
    formDataObj.append('file', file);

    setUploading(true);
    try {
      const response = await axios.post(`${API}/upload`, formDataObj);
      setFormData(prev => ({ ...prev, featured_image: response.data.url }));
      toast.success("Image uploaded successfully");
    } catch (error) {
      console.error("Error uploading image:", error);
      toast.error("Failed to upload image");
    } finally {
      setUploading(false);
    }
  };

  const handleAddTag = () => {
    if (tagInput.trim() && !formData.tags.includes(tagInput.trim())) {
      setFormData(prev => ({ ...prev, tags: [...prev.tags, tagInput.trim()] }));
      setTagInput("");
    }
  };

  const handleRemoveTag = (tagToRemove) => {
    setFormData(prev => ({ ...prev, tags: prev.tags.filter(tag => tag !== tagToRemove) }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      if (article) {
        await axios.put(`${API}/articles/${article.id}`, formData);
        toast.success("Article updated successfully");
      } else {
        await axios.post(`${API}/articles`, formData);
        toast.success("Article created successfully");
      }
      onClose();
    } catch (error) {
      console.error("Error saving article:", error);
      toast.error("Failed to save article");
    }
  };

  return (
    <div style={{
      position: 'fixed',
      top: 0,
      left: 0,
      right: 0,
      bottom: 0,
      background: 'rgba(0,0,0,0.5)',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      zIndex: 1000,
      padding: '2rem',
      overflowY: 'auto'
    }}>
      <div style={{
        background: 'white',
        borderRadius: '16px',
        maxWidth: '900px',
        width: '100%',
        maxHeight: '90vh',
        overflow: 'auto',
        padding: '2rem'
      }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
          <h2 style={{ fontSize: '1.75rem', fontWeight: '700' }}>
            {article ? 'Edit Article' : 'Create Article'}
          </h2>
          <button
            onClick={onClose}
            data-testid="close-form-btn"
            style={{
              background: 'none',
              border: 'none',
              fontSize: '1.5rem',
              cursor: 'pointer',
              color: '#6b7280'
            }}
          >
            ×
          </button>
        </div>

        <form onSubmit={handleSubmit}>
          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Title *</label>
            <input
              type="text"
              name="title"
              value={formData.title}
              onChange={handleChange}
              required
              data-testid="article-title-input"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Slug *</label>
            <input
              type="text"
              name="slug"
              value={formData.slug}
              onChange={handleChange}
              required
              data-testid="article-slug-input"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Category *</label>
            <select
              name="category_id"
              value={formData.category_id}
              onChange={handleChange}
              required
              data-testid="article-category-select"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            >
              <option value="">Select a category</option>
              {categories.map(cat => (
                <option key={cat.id} value={cat.id}>{cat.name}</option>
              ))}
            </select>
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Author *</label>
            <input
              type="text"
              name="author"
              value={formData.author}
              onChange={handleChange}
              required
              data-testid="article-author-input"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Excerpt *</label>
            <textarea
              name="excerpt"
              value={formData.excerpt}
              onChange={handleChange}
              required
              data-testid="article-excerpt-input"
              rows="3"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem',
                resize: 'vertical'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Content *</label>
            
            {/* Simple HTML Editor Toolbar */}
            <div style={{ 
              display: 'flex', 
              gap: '0.5rem', 
              marginBottom: '0.5rem',
              padding: '0.5rem',
              background: '#f9fafb',
              borderRadius: '8px',
              flexWrap: 'wrap'
            }}>
              <button type="button" onClick={() => insertFormatting('h1')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer', fontWeight: '700' }}>H1</button>
              <button type="button" onClick={() => insertFormatting('h2')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer', fontWeight: '700' }}>H2</button>
              <button type="button" onClick={() => insertFormatting('h3')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer', fontWeight: '700' }}>H3</button>
              <button type="button" onClick={() => insertFormatting('p')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer' }}>P</button>
              <button type="button" onClick={() => insertFormatting('bold')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer', fontWeight: '700' }}>B</button>
              <button type="button" onClick={() => insertFormatting('italic')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer', fontStyle: 'italic' }}>I</button>
              <button type="button" onClick={() => insertFormatting('ul')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer' }}>List</button>
              <button type="button" onClick={() => insertFormatting('link')} style={{ padding: '0.5rem', background: 'white', border: '1px solid #e5e7eb', borderRadius: '4px', cursor: 'pointer' }}>Link</button>
            </div>

            <textarea
              id="article-content"
              value={formData.content}
              onChange={handleContentChange}
              required
              data-testid="article-content-input"
              rows="15"
              placeholder="Write your article content with HTML tags..."
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '0.95rem',
                fontFamily: 'monospace',
                resize: 'vertical'
              }}
            />
            <p style={{ fontSize: '0.75rem', color: '#6b7280', marginTop: '0.5rem' }}>
              Use the toolbar buttons or write HTML directly. Preview will show formatted content.
            </p>
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Featured Image</label>
            <input
              type="file"
              accept="image/*"
              onChange={handleImageUpload}
              data-testid="article-image-input"
              style={{ marginBottom: '0.5rem' }}
            />
            {uploading && <p style={{ color: '#6b7280', fontSize: '0.875rem' }}>Uploading...</p>}
            {formData.featured_image && (
              <img 
                src={`${BACKEND_URL}${formData.featured_image}`}
                alt="Preview"
                style={{ maxWidth: '200px', marginTop: '0.5rem', borderRadius: '8px' }}
              />
            )}
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Tags</label>
            <div style={{ display: 'flex', gap: '0.5rem', marginBottom: '0.5rem' }}>
              <input
                type="text"
                value={tagInput}
                onChange={(e) => setTagInput(e.target.value)}
                onKeyPress={(e) => e.key === 'Enter' && (e.preventDefault(), handleAddTag())}
                data-testid="article-tag-input"
                placeholder="Add a tag"
                style={{
                  flex: 1,
                  padding: '0.5rem',
                  border: '1px solid #e5e7eb',
                  borderRadius: '8px'
                }}
              />
              <button
                type="button"
                onClick={handleAddTag}
                data-testid="add-tag-btn"
                style={{
                  padding: '0.5rem 1rem',
                  background: '#3b82f6',
                  color: 'white',
                  border: 'none',
                  borderRadius: '8px',
                  cursor: 'pointer',
                  fontWeight: '600'
                }}
              >
                Add
              </button>
            </div>
            <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
              {formData.tags.map((tag, index) => (
                <span
                  key={index}
                  data-testid={`tag-${index}`}
                  style={{
                    padding: '0.5rem 1rem',
                    background: '#f3f4f6',
                    borderRadius: '20px',
                    fontSize: '0.875rem',
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.5rem'
                  }}
                >
                  {tag}
                  <button
                    type="button"
                    onClick={() => handleRemoveTag(tag)}
                    data-testid={`remove-tag-${index}`}
                    style={{
                      background: 'none',
                      border: 'none',
                      cursor: 'pointer',
                      color: '#6b7280',
                      fontWeight: '700'
                    }}
                  >
                    ×
                  </button>
                </span>
              ))}
            </div>
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Meta Description</label>
            <textarea
              name="meta_description"
              value={formData.meta_description}
              onChange={handleChange}
              data-testid="article-meta-input"
              rows="2"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Status *</label>
            <select
              name="status"
              value={formData.status}
              onChange={handleChange}
              required
              data-testid="article-status-select"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            >
              <option value="published">Published</option>
              <option value="draft">Draft</option>
            </select>
          </div>

          <div style={{ display: 'flex', gap: '1rem', justifyContent: 'flex-end' }}>
            <button
              type="button"
              onClick={onClose}
              data-testid="cancel-btn"
              style={{
                padding: '0.75rem 1.5rem',
                background: '#f3f4f6',
                color: '#1a1a1a',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              Cancel
            </button>
            <button
              type="submit"
              data-testid="submit-article-btn"
              style={{
                padding: '0.75rem 1.5rem',
                background: '#3b82f6',
                color: 'white',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              {article ? 'Update' : 'Create'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default ArticleForm;
