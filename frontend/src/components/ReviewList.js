import React, { useEffect, useState } from "react";
import axios from "axios";
import { Button } from "./ui/button";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const ReviewList = ({ casinoId }) => {
  const [reviews, setReviews] = useState([]);
  const [page, setPage] = useState(1);
  const [totalCount, setTotalCount] = useState(0);
  const [loading, setLoading] = useState(true);
  const reviewsPerPage = 5;

  useEffect(() => {
    fetchReviews();
  }, [page, casinoId]);

  const fetchReviews = async () => {
    setLoading(true);
    try {
      const [reviewsRes, countRes] = await Promise.all([
        axios.get(`${API}/reviews?casino_id=${casinoId}&status=approved&page=${page}&limit=${reviewsPerPage}`),
        axios.get(`${API}/reviews/count?casino_id=${casinoId}&status=approved`)
      ]);
      setReviews(reviewsRes.data);
      setTotalCount(countRes.data.count);
    } catch (error) {
      console.error("Error fetching reviews:", error);
    } finally {
      setLoading(false);
    }
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric' 
    });
  };

  const totalPages = Math.ceil(totalCount / reviewsPerPage);

  if (loading && page === 1) {
    return <div style={{ textAlign: 'center', padding: '2rem', color: '#6b7280' }}>Loading reviews...</div>;
  }

  if (reviews.length === 0 && page === 1) {
    return (
      <div style={{ textAlign: 'center', padding: '2rem', color: '#6b7280' }}>
        <p>No reviews yet. Be the first to review!</p>
      </div>
    );
  }

  return (
    <div>
      {/* Reviews List */}
      <div style={{ display: 'flex', flexDirection: 'column', gap: '1.5rem' }}>
        {reviews.map((review) => (
          <div 
            key={review.id}
            data-testid={`review-${review.id}`}
            style={{
              padding: '1.5rem',
              background: '#f9fafb',
              borderRadius: '12px',
              border: '1px solid #e5e7eb'
            }}
          >
            {/* Review Header */}
            <div style={{ display: 'flex', gap: '1rem', marginBottom: '1rem' }}>
              {/* User Avatar */}
              <div style={{ flexShrink: 0 }}>
                {review.user_avatar ? (
                  <img 
                    src={`${BACKEND_URL}${review.user_avatar}`}
                    alt={review.user_name}
                    data-testid={`review-avatar-${review.id}`}
                    style={{
                      width: '60px',
                      height: '60px',
                      borderRadius: '50%',
                      objectFit: 'cover',
                      border: '2px solid #e5e7eb'
                    }}
                  />
                ) : (
                  <div 
                    data-testid={`review-default-avatar-${review.id}`}
                    style={{
                      width: '60px',
                      height: '60px',
                      borderRadius: '50%',
                      background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontSize: '1.5rem',
                      fontWeight: '700',
                      color: 'white'
                    }}
                  >
                    {review.user_name.charAt(0).toUpperCase()}
                  </div>
                )}
              </div>

              {/* Review Content */}
              <div style={{ flex: 1 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start', marginBottom: '0.5rem' }}>
                  <div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '0.25rem' }}>
                      <h3 style={{ fontSize: '1.25rem', fontWeight: '700' }} data-testid={`review-title-${review.id}`}>
                        {review.title}
                      </h3>
                      {review.is_verified && (
                        <span style={{
                          padding: '0.25rem 0.75rem',
                          background: '#d1fae5',
                          color: '#065f46',
                          borderRadius: '20px',
                          fontSize: '0.75rem',
                          fontWeight: '600'
                        }}>✓ Verified</span>
                      )}
                    </div>
                    <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>
                      By <strong>{review.user_name}</strong> • {formatDate(review.created_at)}
                    </div>
                  </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                <div style={{ color: '#fbbf24', fontSize: '1.25rem' }} data-testid={`review-rating-${review.id}`}>
                  {'★'.repeat(Math.floor(review.rating))}
                </div>
                <span style={{ fontWeight: '700', fontSize: '1.125rem' }}>{review.rating.toFixed(1)}</span>
              </div>
            </div>

            {/* Review Content */}
            <p style={{ color: '#4b5563', lineHeight: '1.6', marginBottom: '1rem' }} data-testid={`review-comment-${review.id}`}>
              {review.comment}
            </p>

            {/* Pros & Cons */}
            {(review.pros.length > 0 || review.cons.length > 0) && (
              <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
                {review.pros.length > 0 && (
                  <div>
                    <div style={{ fontWeight: '600', color: '#059669', marginBottom: '0.5rem' }}>Pros:</div>
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                      {review.pros.map((pro, idx) => (
                        <li key={idx} style={{ fontSize: '0.875rem', color: '#4b5563', marginBottom: '0.25rem', paddingLeft: '1.25rem', position: 'relative' }}>
                          <span style={{ position: 'absolute', left: 0, color: '#10b981' }}>✓</span>
                          {pro}
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
                {review.cons.length > 0 && (
                  <div>
                    <div style={{ fontWeight: '600', color: '#dc2626', marginBottom: '0.5rem' }}>Cons:</div>
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                      {review.cons.map((con, idx) => (
                        <li key={idx} style={{ fontSize: '0.875rem', color: '#4b5563', marginBottom: '0.25rem', paddingLeft: '1.25rem', position: 'relative' }}>
                          <span style={{ position: 'absolute', left: 0, color: '#ef4444' }}>✗</span>
                          {con}
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
              </div>
            )}
          </div>
        ))}
      </div>

      {/* Pagination */}
      {totalPages > 1 && (
        <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', gap: '1rem', marginTop: '2rem' }}>
          <Button
            onClick={() => setPage(page - 1)}
            disabled={page === 1}
            data-testid="prev-page-btn"
            variant="secondary"
          >
            Previous
          </Button>
          <span style={{ color: '#6b7280' }} data-testid="page-info">
            Page {page} of {totalPages}
          </span>
          <Button
            onClick={() => setPage(page + 1)}
            disabled={page === totalPages}
            data-testid="next-page-btn"
            variant="secondary"
          >
            Next
          </Button>
        </div>
      )}
    </div>
  );
};

export default ReviewList;
