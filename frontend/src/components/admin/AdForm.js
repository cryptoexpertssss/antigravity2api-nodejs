import React, { useState, useEffect } from "react";
import axios from "axios";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdForm = ({ ad, onClose }) => {
  const [formData, setFormData] = useState({
    name: "",
    position: "sidebar",
    image_url: "",
    link_url: "",
    alt_text: "",
    is_active: true,
    start_date: "",
    end_date: ""
  });
  const [uploading, setUploading] = useState(false);

  useEffect(() => {
    if (ad) {
      setFormData({
        name: ad.name,
        position: ad.position,
        image_url: ad.image_url,
        link_url: ad.link_url,
        alt_text: ad.alt_text,
        is_active: ad.is_active,
        start_date: ad.start_date ? new Date(ad.start_date).toISOString().split('T')[0] : "",
        end_date: ad.end_date ? new Date(ad.end_date).toISOString().split('T')[0] : ""
      });
    }
  }, [ad]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({ 
      ...prev, 
      [name]: type === 'checkbox' ? checked : value 
    }));
  };

  const handleImageUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formDataObj = new FormData();
    formDataObj.append('file', file);

    setUploading(true);
    try {
      const response = await axios.post(`${API}/upload`, formDataObj);
      setFormData(prev => ({ ...prev, image_url: `${BACKEND_URL}${response.data.url}` }));
      toast.success("Image uploaded successfully");
    } catch (error) {
      console.error("Error uploading image:", error);
      toast.error("Failed to upload image");
    } finally {
      setUploading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    const submitData = {
      ...formData,
      start_date: formData.start_date ? new Date(formData.start_date).toISOString() : null,
      end_date: formData.end_date ? new Date(formData.end_date).toISOString() : null
    };
    
    try {
      if (ad) {
        await axios.put(`${API}/ads/${ad.id}`, submitData);
        toast.success("Advertisement updated successfully");
      } else {
        await axios.post(`${API}/ads`, submitData);
        toast.success("Advertisement created successfully");
      }
      onClose();
    } catch (error) {
      console.error("Error saving ad:", error);
      toast.error("Failed to save advertisement");
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
        maxWidth: '700px',
        width: '100%',
        maxHeight: '90vh',
        overflow: 'auto',
        padding: '2rem'
      }}>
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
          <h2 style={{ fontSize: '1.75rem', fontWeight: '700' }}>
            {ad ? 'Edit Advertisement' : 'Create Advertisement'}
          </h2>
          <button
            onClick={onClose}
            data-testid="close-ad-form"
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
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Name *</label>
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
              data-testid="ad-name-input"
              placeholder="e.g., Homepage Banner"
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
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Position *</label>
            <select
              name="position"
              value={formData.position}
              onChange={handleChange}
              required
              data-testid="ad-position-select"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            >
              <option value="header">Header</option>
              <option value="sidebar">Sidebar</option>
              <option value="footer">Footer</option>
              <option value="in-content">In-Content</option>
            </select>
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Ad Image *</label>
            <input
              type="file"
              accept="image/*"
              onChange={handleImageUpload}
              data-testid="ad-image-input"
              style={{ marginBottom: '0.5rem' }}
            />
            {uploading && <p style={{ color: '#6b7280', fontSize: '0.875rem' }}>Uploading...</p>}
            {formData.image_url && (
              <img 
                src={formData.image_url}
                alt="Preview"
                style={{ maxWidth: '300px', marginTop: '0.5rem', borderRadius: '8px' }}
              />
            )}
            <input
              type="url"
              name="image_url"
              value={formData.image_url}
              onChange={handleChange}
              required
              data-testid="ad-image-url-input"
              placeholder="Or enter image URL"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem',
                marginTop: '0.5rem'
              }}
            />
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Link URL *</label>
            <input
              type="url"
              name="link_url"
              value={formData.link_url}
              onChange={handleChange}
              required
              data-testid="ad-link-url-input"
              placeholder="https://example.com"
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
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Alt Text *</label>
            <input
              type="text"
              name="alt_text"
              value={formData.alt_text}
              onChange={handleChange}
              required
              data-testid="ad-alt-text-input"
              placeholder="Description of the ad image"
              style={{
                width: '100%',
                padding: '0.75rem',
                border: '1px solid #e5e7eb',
                borderRadius: '8px',
                fontSize: '1rem'
              }}
            />
          </div>

          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', marginBottom: '1.5rem' }}>
            <div>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Start Date (Optional)</label>
              <input
                type="date"
                name="start_date"
                value={formData.start_date}
                onChange={handleChange}
                data-testid="ad-start-date-input"
                style={{
                  width: '100%',
                  padding: '0.75rem',
                  border: '1px solid #e5e7eb',
                  borderRadius: '8px',
                  fontSize: '1rem'
                }}
              />
            </div>
            <div>
              <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>End Date (Optional)</label>
              <input
                type="date"
                name="end_date"
                value={formData.end_date}
                onChange={handleChange}
                data-testid="ad-end-date-input"
                style={{
                  width: '100%',
                  padding: '0.75rem',
                  border: '1px solid #e5e7eb',
                  borderRadius: '8px',
                  fontSize: '1rem'
                }}
              />
            </div>
          </div>

          <div style={{ marginBottom: '1.5rem' }}>
            <label style={{ display: 'flex', alignItems: 'center', gap: '0.5rem', cursor: 'pointer' }}>
              <input
                type="checkbox"
                name="is_active"
                checked={formData.is_active}
                onChange={handleChange}
                data-testid="ad-active-checkbox"
                style={{ width: '1.25rem', height: '1.25rem', cursor: 'pointer' }}
              />
              <span style={{ fontWeight: '600' }}>Active</span>
            </label>
          </div>

          <div style={{ display: 'flex', gap: '1rem', justifyContent: 'flex-end' }}>
            <button
              type="button"
              onClick={onClose}
              data-testid="cancel-ad-btn"
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
              data-testid="submit-ad-btn"
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
              {ad ? 'Update' : 'Create'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default AdForm;
