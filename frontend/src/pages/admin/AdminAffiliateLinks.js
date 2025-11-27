import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import AffiliateLinkForm from "../../components/admin/AffiliateLinkForm";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminAffiliateLinks = () => {
  const [links, setLinks] = useState([]);
  const [casinos, setCasinos] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editingLink, setEditingLink] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [linksRes, casinosRes] = await Promise.all([
        axios.get(`${API}/affiliate-links`),
        axios.get(`${API}/casinos`)
      ]);
      setLinks(linksRes.data);
      setCasinos(casinosRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
      toast.error("Failed to load affiliate links");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (linkId) => {
    if (!window.confirm("Are you sure you want to delete this affiliate link?")) return;
    
    try {
      await axios.delete(`${API}/affiliate-links/${linkId}`);
      toast.success("Affiliate link deleted successfully");
      fetchData();
    } catch (error) {
      console.error("Error deleting link:", error);
      toast.error("Failed to delete affiliate link");
    }
  };

  const handleEdit = (link) => {
    setEditingLink(link);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingLink(null);
    fetchData();
  };

  const getCasinoName = (casinoId) => {
    const casino = casinos.find(c => c.id === casinoId);
    return casino ? casino.name : 'General Link';
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
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-affiliate-title">Affiliate Links</h1>
          <Button onClick={() => setShowForm(true)} data-testid="create-affiliate-link-btn">
            + Create Affiliate Link
          </Button>
        </div>

        {showForm && (
          <AffiliateLinkForm 
            link={editingLink}
            casinos={casinos}
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
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>NAME</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>CASINO</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>URL</th>
                <th style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>CLICKS</th>
                <th style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>STATUS</th>
                <th style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              {links.map((link, index) => (
                <tr 
                  key={link.id}
                  data-testid={`affiliate-link-row-${link.id}`}
                  style={{
                    borderBottom: index !== links.length - 1 ? '1px solid #e5e7eb' : 'none'
                  }}
                >
                  <td style={{ padding: '1rem' }}>
                    <div style={{ fontWeight: '600' }}>{link.name}</div>
                    {link.description && (
                      <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>{link.description}</div>
                    )}
                  </td>
                  <td style={{ padding: '1rem', color: '#4b5563' }}>
                    {getCasinoName(link.casino_id)}
                  </td>
                  <td style={{ padding: '1rem' }}>
                    <a href={link.url} target="_blank" rel="noopener noreferrer" style={{ color: '#2563eb', fontSize: '0.875rem' }}>
                      {link.url.substring(0, 50)}...
                    </a>
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '1.125rem' }}>
                    {link.clicks}
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <span style={{
                      display: 'inline-block',
                      padding: '0.25rem 0.75rem',
                      borderRadius: '20px',
                      fontSize: '0.75rem',
                      fontWeight: '600',
                      background: link.is_active ? '#d1fae5' : '#fee2e2',
                      color: link.is_active ? '#065f46' : '#991b1b'
                    }}>
                      {link.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <button
                      onClick={() => handleEdit(link)}
                      data-testid={`edit-affiliate-${link.id}`}
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
                      onClick={() => handleDelete(link.id)}
                      data-testid={`delete-affiliate-${link.id}`}
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

          {links.length === 0 && (
            <div style={{ padding: '3rem', textAlign: 'center', color: '#6b7280' }}>
              <p>No affiliate links found. Create your first link!</p>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
};

export default AdminAffiliateLinks;
