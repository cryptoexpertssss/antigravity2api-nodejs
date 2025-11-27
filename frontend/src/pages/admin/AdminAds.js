import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import AdForm from "../../components/admin/AdForm";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminAds = () => {
  const [ads, setAds] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editingAd, setEditingAd] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchAds();
  }, []);

  const fetchAds = async () => {
    try {
      const response = await axios.get(`${API}/ads`);
      setAds(response.data);
    } catch (error) {
      console.error("Error fetching ads:", error);
      toast.error("Failed to load advertisements");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (adId) => {
    if (!window.confirm("Are you sure you want to delete this advertisement?")) return;
    
    try {
      await axios.delete(`${API}/ads/${adId}`);
      toast.success("Advertisement deleted successfully");
      fetchAds();
    } catch (error) {
      console.error("Error deleting ad:", error);
      toast.error("Failed to delete advertisement");
    }
  };

  const handleEdit = (ad) => {
    setEditingAd(ad);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingAd(null);
    fetchAds();
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
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-ads-title">Advertisements</h1>
          <Button onClick={() => setShowForm(true)} data-testid="create-ad-btn">
            + Create Advertisement
          </Button>
        </div>

        {showForm && (
          <AdForm 
            ad={editingAd}
            onClose={handleFormClose}
          />
        )}

        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fill, minmax(350px, 1fr))',
          gap: '1.5rem'
        }}>
          {ads.map((ad) => (
            <div 
              key={ad.id}
              data-testid={`ad-card-${ad.id}`}
              style={{
                background: 'white',
                borderRadius: '12px',
                overflow: 'hidden',
                boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
                border: '1px solid #e5e7eb'
              }}
            >
              <img 
                src={ad.image_url}
                alt={ad.alt_text}
                style={{
                  width: '100%',
                  height: '200px',
                  objectFit: 'cover'
                }}
              />
              <div style={{ padding: '1.5rem' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '0.5rem' }}>
                  <h3 style={{ fontSize: '1.125rem', fontWeight: '700' }}>{ad.name}</h3>
                  <span style={{
                    padding: '0.25rem 0.75rem',
                    borderRadius: '20px',
                    fontSize: '0.75rem',
                    fontWeight: '600',
                    background: ad.is_active ? '#d1fae5' : '#fee2e2',
                    color: ad.is_active ? '#065f46' : '#991b1b'
                  }}>
                    {ad.is_active ? 'Active' : 'Inactive'}
                  </span>
                </div>
                <div style={{ fontSize: '0.875rem', color: '#6b7280', marginBottom: '0.75rem' }}>
                  Position: <strong>{ad.position}</strong>
                </div>
                <div style={{ fontSize: '0.875rem', color: '#6b7280', marginBottom: '0.75rem' }}>
                  Link: <a href={ad.link_url} target="_blank" rel="noopener noreferrer" style={{ color: '#2563eb' }}>
                    {ad.link_url.substring(0, 40)}...
                  </a>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem', marginBottom: '1rem' }}>
                  <div style={{ padding: '0.5rem', background: '#f9fafb', borderRadius: '6px', textAlign: 'center' }}>
                    <div style={{ fontSize: '0.75rem', color: '#6b7280' }}>Impressions</div>
                    <div style={{ fontSize: '1.25rem', fontWeight: '700' }}>{ad.impressions}</div>
                  </div>
                  <div style={{ padding: '0.5rem', background: '#f9fafb', borderRadius: '6px', textAlign: 'center' }}>
                    <div style={{ fontSize: '0.75rem', color: '#6b7280' }}>Clicks</div>
                    <div style={{ fontSize: '1.25rem', fontWeight: '700' }}>{ad.clicks}</div>
                  </div>
                </div>
                <div style={{ display: 'flex', gap: '0.5rem' }}>
                  <button
                    onClick={() => handleEdit(ad)}
                    data-testid={`edit-ad-${ad.id}`}
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
                    onClick={() => handleDelete(ad.id)}
                    data-testid={`delete-ad-${ad.id}`}
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
            </div>
          ))}
        </div>

        {ads.length === 0 && (
          <div style={{
            background: 'white',
            borderRadius: '12px',
            padding: '3rem',
            textAlign: 'center',
            color: '#6b7280',
            boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
            border: '1px solid #e5e7eb'
          }}>
            <p>No advertisements found. Create your first ad!</p>
          </div>
        )}
      </div>
    </AdminLayout>
  );
};

export default AdminAds;
