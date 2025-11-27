import React, { useEffect, useState } from "react";
import axios from "axios";
import AdminLayout from "../../components/AdminLayout";
import { Button } from "../../components/ui/button";
import { toast } from "sonner";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminReviews = () => {
  const [reviews, setReviews] = useState([]);
  const [casinos, setCasinos] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("pending");

  useEffect(() => {
    fetchData();
  }, [filter]);

  const fetchData = async () => {
    try {
      const [reviewsRes, casinosRes] = await Promise.all([
        axios.get(`${API}/reviews${filter ? `?status=${filter}` : ""}`),
        axios.get(`${API}/casinos`)
      ]);
      setReviews(reviewsRes.data);
      setCasinos(casinosRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
      toast.error("Failed to load reviews");
    } finally {
      setLoading(false);
    }
  };

  const getCasinoName = (casinoId) => {
    const casino = casinos.find(c => c.id === casinoId);
    return casino ? casino.name : 'Unknown';
  };

  const handleStatusChange = async (reviewId, status) => {
    try {
      await axios.put(`${API}/reviews/${reviewId}/status?status=${status}`);
      toast.success(`Review ${status} successfully`);
      fetchData();
    } catch (error) {
      console.error("Error updating review:", error);
      toast.error("Failed to update review");
    }
  };

  const handleDelete = async (reviewId) => {
    if (!window.confirm("Are you sure you want to delete this review?")) return;
    
    try {
      await axios.delete(`${API}/reviews/${reviewId}`);
      toast.success("Review deleted successfully");
      fetchData();
    } catch (error) {
      console.error("Error deleting review:", error);
      toast.error("Failed to delete review");
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
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
          <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="admin-reviews-title">User Reviews</h1>
          <div style={{ display: 'flex', gap: '0.5rem' }}>
            <button
              onClick={() => setFilter("pending")}
              data-testid="filter-pending"
              style={{
                padding: '0.5rem 1rem',
                background: filter === "pending" ? '#3b82f6' : '#f3f4f6',
                color: filter === "pending" ? 'white' : '#1a1a1a',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              Pending
            </button>
            <button
              onClick={() => setFilter("approved")}
              data-testid="filter-approved"
              style={{
                padding: '0.5rem 1rem',
                background: filter === "approved" ? '#3b82f6' : '#f3f4f6',
                color: filter === "approved" ? 'white' : '#1a1a1a',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              Approved
            </button>
            <button
              onClick={() => setFilter("rejected")}
              data-testid="filter-rejected"
              style={{
                padding: '0.5rem 1rem',
                background: filter === "rejected" ? '#3b82f6' : '#f3f4f6',
                color: filter === "rejected" ? 'white' : '#1a1a1a',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              Rejected
            </button>
            <button
              onClick={() => setFilter("")}
              data-testid="filter-all"
              style={{
                padding: '0.5rem 1rem',
                background: filter === "" ? '#3b82f6' : '#f3f4f6',
                color: filter === "" ? 'white' : '#1a1a1a',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontWeight: '600'
              }}
            >
              All
            </button>
          </div>
        </div>

        <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
          {reviews.map((review) => (
            <div 
              key={review.id}
              data-testid={`review-admin-${review.id}`}
              style={{
                background: 'white',
                borderRadius: '12px',
                padding: '1.5rem',
                boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
                border: '1px solid #e5e7eb'
              }}
            >
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '1rem' }}>
                <div style={{ flex: 1 }}>
                  <div style={{ display: 'flex', gap: '1rem', alignItems: 'center', marginBottom: '0.5rem' }}>
                    <h3 style={{ fontSize: '1.25rem', fontWeight: '700' }}>{review.title}</h3>
                    <div style={{ color: '#fbbf24', fontSize: '1rem' }}>
                      {'★'.repeat(Math.floor(review.rating))} {review.rating}
                    </div>
                  </div>
                  <div style={{ fontSize: '0.875rem', color: '#6b7280', marginBottom: '0.5rem' }}>
                    Casino: <strong>{getCasinoName(review.casino_id)}</strong> | 
                    By: <strong>{review.user_name}</strong> | 
                    Date: {formatDate(review.created_at)}
                  </div>
                  <p style={{ color: '#4b5563', marginBottom: '1rem' }}>{review.comment}</p>
                  
                  {(review.pros.length > 0 || review.cons.length > 0) && (
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', fontSize: '0.875rem' }}>
                      {review.pros.length > 0 && (
                        <div>
                          <strong style={{ color: '#059669' }}>Pros:</strong>
                          <ul style={{ margin: '0.5rem 0', paddingLeft: '1.5rem' }}>
                            {review.pros.map((pro, idx) => (
                              <li key={idx}>{pro}</li>
                            ))}
                          </ul>
                        </div>
                      )}
                      {review.cons.length > 0 && (
                        <div>
                          <strong style={{ color: '#dc2626' }}>Cons:</strong>
                          <ul style={{ margin: '0.5rem 0', paddingLeft: '1.5rem' }}>
                            {review.cons.map((con, idx) => (
                              <li key={idx}>{con}</li>
                            ))}
                          </ul>
                        </div>
                      )}
                    </div>
                  )}
                </div>

                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem', marginLeft: '1rem' }}>
                  <span style={{
                    padding: '0.5rem 1rem',
                    borderRadius: '20px',
                    fontSize: '0.75rem',
                    fontWeight: '600',
                    textAlign: 'center',
                    background: review.status === 'approved' ? '#d1fae5' : review.status === 'rejected' ? '#fee2e2' : '#fef3c7',
                    color: review.status === 'approved' ? '#065f46' : review.status === 'rejected' ? '#991b1b' : '#92400e'
                  }}>
                    {review.status.toUpperCase()}
                  </span>
                  
                  {review.status === 'pending' && (
                    <>
                      <button
                        onClick={() => handleStatusChange(review.id, 'approved')}
                        data-testid={`approve-review-${review.id}`}
                        style={{
                          padding: '0.5rem 1rem',
                          background: '#10b981',
                          color: 'white',
                          border: 'none',
                          borderRadius: '6px',
                          cursor: 'pointer',
                          fontSize: '0.875rem',
                          fontWeight: '600'
                        }}
                      >
                        Approve
                      </button>
                      <button
                        onClick={() => handleStatusChange(review.id, 'rejected')}
                        data-testid={`reject-review-${review.id}`}
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
                        Reject
                      </button>
                    </>
                  )}
                  
                  <button
                    onClick={() => handleDelete(review.id)}
                    data-testid={`delete-review-${review.id}`}
                    style={{
                      padding: '0.5rem 1rem',
                      background: '#6b7280',
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

        {reviews.length === 0 && (
          <div style={{
            background: 'white',
            borderRadius: '12px',
            padding: '3rem',
            textAlign: 'center',
            color: '#6b7280',
            boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
            border: '1px solid #e5e7eb'
          }}>
            <p>No {filter} reviews found.</p>
          </div>
        )}
      </div>
    </AdminLayout>
  );
};

export default AdminReviews;
