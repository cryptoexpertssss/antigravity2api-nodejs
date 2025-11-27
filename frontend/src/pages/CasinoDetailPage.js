import React, { useEffect, useState } from "react";
import axios from "axios";
import { useParams, Link } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";
import ReviewList from "../components/ReviewList";
import ReviewForm from "../components/ReviewForm";
import { Button } from "../components/ui/button";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const CasinoDetailPage = () => {
  const { casinoId } = useParams();
  const [casino, setCasino] = useState(null);
  const [categories, setCategories] = useState([]);
  const [affiliateLinks, setAffiliateLinks] = useState([]);
  const [showReviewForm, setShowReviewForm] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, [casinoId]);

  const fetchData = async () => {
    try {
      const [casinoRes, categoriesRes, linksRes] = await Promise.all([
        axios.get(`${API}/casinos/${casinoId}`),
        axios.get(`${API}/categories`),
        axios.get(`${API}/affiliate-links?casino_id=${casinoId}`)
      ]);
      setCasino(casinoRes.data);
      setCategories(categoriesRes.data);
      setAffiliateLinks(linksRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
    } finally {
      setLoading(false);
    }
  };

  const handleAffiliateClick = async (linkId) => {
    try {
      await axios.post(`${API}/affiliate-links/${linkId}/click`);
    } catch (error) {
      console.error("Error tracking click:", error);
    }
  };

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  if (!casino) {
    return (
      <div>
        <Navbar categories={categories} />
        <div className="container" style={{ padding: '4rem 1.5rem', textAlign: 'center' }}>
          <h1>Casino not found</h1>
          <Link to="/casinos" className="btn btn-primary" style={{ marginTop: '2rem' }}>Back to Casinos</Link>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div>
      <Navbar categories={categories} />
      
      <div className="container" style={{ padding: '3rem 1.5rem' }}>
        {/* Casino Header */}
        <div style={{
          background: 'white',
          borderRadius: '16px',
          padding: '2rem',
          marginBottom: '2rem',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <div style={{ display: 'grid', gridTemplateColumns: '200px 1fr auto', gap: '2rem', alignItems: 'center' }}>
            <img 
              src={casino.logo_url}
              alt={casino.name}
              data-testid="casino-detail-logo"
              style={{
                width: '200px',
                height: '120px',
                objectFit: 'contain',
                borderRadius: '12px'
              }}
            />
            <div>
              <div style={{ display: 'flex', alignItems: 'center', gap: '1rem', marginBottom: '0.5rem' }}>
                <h1 style={{ fontSize: '2.5rem', fontWeight: '800' }} data-testid="casino-detail-name">
                  {casino.name}
                </h1>
                <div style={{
                  padding: '0.5rem 1rem',
                  background: '#fbbf24',
                  borderRadius: '50px',
                  fontWeight: '700',
                  fontSize: '1.25rem'
                }}>#{casino.rank}</div>
              </div>
              <div style={{ color: '#fbbf24', fontSize: '1.5rem', marginBottom: '1rem' }}>
                {'★'.repeat(Math.floor(casino.rating))}
              </div>
              <div style={{ fontSize: '1.5rem', fontWeight: '700', color: '#059669', marginBottom: '0.5rem' }}>
                {casino.offer_title}
              </div>
              <p style={{ fontSize: '1rem', color: '#6b7280' }}>{casino.offer_details}</p>
            </div>
            <a 
              href={casino.claim_link}
              target="_blank"
              rel="noopener noreferrer"
              data-testid="casino-detail-claim-btn"
              style={{
                display: 'inline-block',
                padding: '1rem 2rem',
                background: '#2563eb',
                color: 'white',
                borderRadius: '50px',
                fontWeight: '700',
                fontSize: '1.125rem',
                textDecoration: 'none',
                transition: 'all 0.2s ease',
                whiteSpace: 'nowrap'
              }}
              onMouseEnter={(e) => {
                e.target.style.background = '#1d4ed8';
                e.target.style.transform = 'translateY(-2px)';
                e.target.style.boxShadow = '0 8px 20px rgba(37, 99, 235, 0.3)';
              }}
              onMouseLeave={(e) => {
                e.target.style.background = '#2563eb';
                e.target.style.transform = 'translateY(0)';
                e.target.style.boxShadow = 'none';
              }}
            >
              CLAIM OFFER
            </a>
          </div>
        </div>

        {/* Image Gallery */}
        {casino.images && casino.images.length > 0 && (
          <div style={{
            background: 'white',
            borderRadius: '16px',
            padding: '2rem',
            marginBottom: '2rem',
            boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
            border: '1px solid #e5e7eb'
          }}>
            <h2 style={{ fontSize: '1.75rem', fontWeight: '700', marginBottom: '1.5rem' }}>Gallery</h2>
            <div style={{ 
              display: 'grid', 
              gridTemplateColumns: 'repeat(auto-fill, minmax(250px, 1fr))', 
              gap: '1rem' 
            }}>
              {casino.images.map((image, idx) => (
                <img 
                  key={idx}
                  src={`${BACKEND_URL}${image}`}
                  alt={`${casino.name} ${idx + 1}`}
                  data-testid={`casino-gallery-${idx}`}
                  style={{
                    width: '100%',
                    height: '200px',
                    objectFit: 'cover',
                    borderRadius: '12px',
                    cursor: 'pointer',
                    transition: 'transform 0.2s ease'
                  }}
                  onMouseEnter={(e) => e.target.style.transform = 'scale(1.05)'}
                  onMouseLeave={(e) => e.target.style.transform = 'scale(1)'}
                />
              ))}
            </div>
          </div>
        )}

        {/* Features */}
        <div style={{
          background: 'white',
          borderRadius: '16px',
          padding: '2rem',
          marginBottom: '2rem',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <h2 style={{ fontSize: '1.75rem', fontWeight: '700', marginBottom: '1.5rem' }}>Features</h2>
          <ul style={{ listStyle: 'none', padding: 0, display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '1rem' }}>
            {casino.features.map((feature, idx) => (
              <li 
                key={idx}
                data-testid={`casino-feature-${idx}`}
                style={{
                  padding: '1rem',
                  background: '#f9fafb',
                  borderRadius: '8px',
                  paddingLeft: '2.5rem',
                  position: 'relative'
                }}
              >
                <span style={{
                  position: 'absolute',
                  left: '1rem',
                  color: '#10b981',
                  fontWeight: '700',
                  fontSize: '1.25rem'
                }}>✓</span>
                {feature}
              </li>
            ))}
          </ul>
        </div>

        {/* Affiliate Links */}
        {affiliateLinks.length > 0 && (
          <div style={{
            background: 'white',
            borderRadius: '16px',
            padding: '2rem',
            marginBottom: '2rem',
            boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
            border: '1px solid #e5e7eb'
          }}>
            <h2 style={{ fontSize: '1.75rem', fontWeight: '700', marginBottom: '1.5rem' }}>Partner Links</h2>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
              {affiliateLinks.map((link) => (
                <a
                  key={link.id}
                  href={link.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={() => handleAffiliateClick(link.id)}
                  data-testid={`affiliate-link-${link.id}`}
                  style={{
                    padding: '1rem',
                    background: '#f9fafb',
                    borderRadius: '8px',
                    textDecoration: 'none',
                    color: '#2563eb',
                    fontWeight: '600',
                    transition: 'all 0.2s ease',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center'
                  }}
                  onMouseEnter={(e) => e.currentTarget.style.background = '#e5e7eb'}
                  onMouseLeave={(e) => e.currentTarget.style.background = '#f9fafb'}
                >
                  <div>
                    <div style={{ fontWeight: '700', marginBottom: '0.25rem' }}>{link.name}</div>
                    {link.description && (
                      <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>{link.description}</div>
                    )}
                  </div>
                  <span style={{ fontSize: '1.25rem' }}>→</span>
                </a>
              ))}
            </div>
          </div>
        )}

        {/* Reviews Section */}
        <div style={{
          background: 'white',
          borderRadius: '16px',
          padding: '2rem',
          marginBottom: '2rem',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '2rem' }}>
            <h2 style={{ fontSize: '1.75rem', fontWeight: '700' }}>User Reviews</h2>
            <Button onClick={() => setShowReviewForm(true)} data-testid="write-review-btn">
              Write a Review
            </Button>
          </div>

          {showReviewForm && (
            <ReviewForm 
              casinoId={casinoId}
              casinoName={casino.name}
              onClose={() => setShowReviewForm(false)}
            />
          )}

          <ReviewList casinoId={casinoId} />
        </div>
      </div>

      <Footer />
    </div>
  );
};

export default CasinoDetailPage;
