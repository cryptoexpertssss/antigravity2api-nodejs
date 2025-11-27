import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import CasinoForm from "../../components/admin/CasinoForm";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminCasinos = () => {
  const [casinos, setCasinos] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editingCasino, setEditingCasino] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCasinos();
  }, []);

  const fetchCasinos = async () => {
    try {
      const response = await axios.get(`${API}/casinos`);
      setCasinos(response.data);
    } catch (error) {
      console.error("Error fetching casinos:", error);
      toast.error("Failed to load casinos");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (casinoId) => {
    if (!window.confirm("Are you sure you want to delete this casino?")) return;
    
    try {
      await axios.delete(`${API}/casinos/${casinoId}`);
      toast.success("Casino deleted successfully");
      fetchCasinos();
    } catch (error) {
      console.error("Error deleting casino:", error);
      toast.error("Failed to delete casino");
    }
  };

  const handleEdit = (casino) => {
    setEditingCasino(casino);
    setShowForm(true);
  };

  const handleFormClose = () => {
    setShowForm(false);
    setEditingCasino(null);
    fetchCasinos();
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
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-casinos-title">Casino Listings</h1>
          <Button onClick={() => setShowForm(true)} data-testid="create-casino-btn">
            + Create Casino
          </Button>
        </div>

        {showForm && (
          <CasinoForm 
            casino={editingCasino}
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
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>RANK</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>NAME</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>OFFER</th>
                <th style={{ padding: '1rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>RATING</th>
                <th style={{ padding: '1rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              {casinos.map((casino, index) => (
                <tr 
                  key={casino.id}
                  data-testid={`casino-admin-row-${casino.id}`}
                  style={{
                    borderBottom: index !== casinos.length - 1 ? '1px solid #e5e7eb' : 'none'
                  }}
                >
                  <td style={{ padding: '1rem' }}>
                    <div style={{
                      width: '40px',
                      height: '40px',
                      borderRadius: '50%',
                      background: casino.rank <= 3 ? '#fbbf24' : '#e5e7eb',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontWeight: '700'
                    }}>
                      {casino.rank}
                    </div>
                  </td>
                  <td style={{ padding: '1rem' }}>
                    <div style={{ fontWeight: '600', marginBottom: '0.25rem' }} data-testid={`casino-admin-name-${casino.id}`}>
                      {casino.name}
                    </div>
                    {casino.is_featured && (
                      <span style={{
                        display: 'inline-block',
                        padding: '0.25rem 0.5rem',
                        background: '#fef3c7',
                        color: '#92400e',
                        borderRadius: '6px',
                        fontSize: '0.75rem',
                        fontWeight: '600'
                      }}>Featured</span>
                    )}
                  </td>
                  <td style={{ padding: '1rem', color: '#4b5563' }}>
                    <div style={{ fontWeight: '600', color: '#059669', marginBottom: '0.25rem' }}>
                      {casino.offer_title}
                    </div>
                    <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>
                      {casino.offer_details.substring(0, 50)}...
                    </div>
                  </td>
                  <td style={{ padding: '1rem' }}>
                    <div style={{ color: '#fbbf24', fontSize: '1rem' }}>
                      {'★'.repeat(Math.floor(casino.rating))}
                    </div>
                  </td>
                  <td style={{ padding: '1rem', textAlign: 'center' }}>
                    <button
                      onClick={() => handleEdit(casino)}
                      data-testid={`edit-casino-${casino.id}`}
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
                      onClick={() => handleDelete(casino.id)}
                      data-testid={`delete-casino-${casino.id}`}
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

          {casinos.length === 0 && (
            <div style={{ padding: '3rem', textAlign: 'center', color: '#6b7280' }}>
              <p>No casinos found. Create your first casino listing!</p>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
};

export default AdminCasinos;
