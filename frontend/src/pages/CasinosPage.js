import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const CasinosPage = () => {
  const [casinos, setCasinos] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [casinosRes, categoriesRes] = await Promise.all([
        axios.get(`${API}/casinos`),
        axios.get(`${API}/categories`)
      ]);
      setCasinos(casinosRes.data);
      setCategories(categoriesRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  return (
    <div>
      <Navbar categories={categories} />
      
      <div className="container-wide" style={{ padding: '3rem 1.5rem' }}>
        <div style={{ marginBottom: '3rem', textAlign: 'center' }}>
          <h1 style={{ fontSize: '3rem', fontWeight: '800', marginBottom: '1rem' }} data-testid="casinos-page-title">
            Best Online Gambling Sites 2025
          </h1>
          <p style={{ fontSize: '1.125rem', color: '#6b7280', maxWidth: '800px', margin: '0 auto' }}>
            Discover the top-rated online casinos with exclusive bonuses, expert reviews, and trusted gaming experiences.
          </p>
        </div>

        {/* Casino Rankings Table */}
        <div style={{
          background: 'white',
          borderRadius: '16px',
          overflow: 'hidden',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead style={{ background: '#f9fafb', borderBottom: '2px solid #e5e7eb' }}>
              <tr>
                <th style={{ padding: '1.25rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280', width: '80px' }}>RANK</th>
                <th style={{ padding: '1.25rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>CASINO</th>
                <th style={{ padding: '1.25rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>OFFER</th>
                <th style={{ padding: '1.25rem', textAlign: 'left', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280' }}>FEATURES</th>
                <th style={{ padding: '1.25rem', textAlign: 'center', fontWeight: '700', fontSize: '0.875rem', color: '#6b7280', width: '150px' }}>ACTION</th>
              </tr>
            </thead>
            <tbody>
              {casinos.map((casino, index) => (
                <tr 
                  key={casino.id}
                  data-testid={`casino-row-${casino.id}`}
                  style={{
                    borderBottom: index !== casinos.length - 1 ? '1px solid #e5e7eb' : 'none',
                    transition: 'background 0.2s ease'
                  }}
                  onMouseEnter={(e) => e.currentTarget.style.background = '#f9fafb'}
                  onMouseLeave={(e) => e.currentTarget.style.background = 'white'}
                >
                  <td style={{ padding: '1.5rem' }}>
                    <div style={{
                      width: '40px',
                      height: '40px',
                      borderRadius: '50%',
                      background: casino.rank <= 3 ? '#fbbf24' : '#e5e7eb',
                      display: 'flex',
                      alignItems: 'center',
                      justifyContent: 'center',
                      fontWeight: '700',
                      color: casino.rank <= 3 ? '#78350f' : '#4b5563'
                    }}>
                      {casino.rank}
                    </div>
                  </td>
                  <td style={{ padding: '1.5rem' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '1rem' }}>
                      <img 
                        src={casino.logo_url}
                        alt={casino.name}
                        data-testid={`casino-logo-${casino.id}`}
                        style={{
                          width: '80px',
                          height: '50px',
                          objectFit: 'contain',
                          borderRadius: '8px'
                        }}
                      />
                      <div>
                        <div style={{ fontWeight: '700', fontSize: '1rem', marginBottom: '0.25rem' }} data-testid={`casino-name-${casino.id}`}>
                          {casino.name}
                        </div>
                        {casino.rating && (
                          <div style={{ color: '#fbbf24', fontSize: '0.875rem' }}>
                            {'★'.repeat(Math.floor(casino.rating))}
                          </div>
                        )}
                      </div>
                    </div>
                  </td>
                  <td style={{ padding: '1.5rem' }}>
                    <div style={{ fontWeight: '700', fontSize: '1.125rem', color: '#059669', marginBottom: '0.25rem' }} data-testid={`casino-offer-${casino.id}`}>
                      {casino.offer_title}
                    </div>
                    <div style={{ fontSize: '0.875rem', color: '#6b7280' }}>
                      {casino.offer_details}
                    </div>
                    {casino.promo_code && (
                      <div style={{ marginTop: '0.5rem' }}>
                        <span style={{
                          display: 'inline-block',
                          padding: '0.25rem 0.75rem',
                          background: '#fef3c7',
                          color: '#92400e',
                          borderRadius: '6px',
                          fontSize: '0.75rem',
                          fontWeight: '700'
                        }} data-testid={`casino-promo-${casino.id}`}>
                          Code: {casino.promo_code}
                        </span>
                      </div>
                    )}
                  </td>
                  <td style={{ padding: '1.5rem' }}>
                    <ul style={{ listStyle: 'none', padding: 0, margin: 0 }}>
                      {casino.features.slice(0, 3).map((feature, idx) => (
                        <li 
                          key={idx}
                          data-testid={`casino-feature-${casino.id}-${idx}`}
                          style={{
                            fontSize: '0.875rem',
                            color: '#4b5563',
                            marginBottom: '0.25rem',
                            paddingLeft: '1.25rem',
                            position: 'relative'
                          }}
                        >
                          <span style={{
                            position: 'absolute',
                            left: 0,
                            color: '#10b981'
                          }}>✓</span>
                          {feature}
                        </li>
                      ))}
                    </ul>
                  </td>
                  <td style={{ padding: '1.5rem', textAlign: 'center' }}>
                    <Link
                      to={`/casino/${casino.id}`}
                      data-testid={`casino-detail-btn-${casino.id}`}
                      style={{
                        display: 'inline-block',
                        padding: '0.75rem 1.5rem',
                        background: '#6b7280',
                        color: 'white',
                        borderRadius: '50px',
                        fontWeight: '700',
                        fontSize: '0.875rem',
                        textDecoration: 'none',
                        transition: 'all 0.2s ease',
                        whiteSpace: 'nowrap',
                        marginBottom: '0.5rem'
                      }}
                      onMouseEnter={(e) => {
                        e.target.style.background = '#4b5563';
                      }}
                      onMouseLeave={(e) => {
                        e.target.style.background = '#6b7280';
                      }}
                    >
                      VIEW DETAILS
                    </Link>
                    <br />
                    <a 
                      href={casino.claim_link}
                      target="_blank"
                      rel="noopener noreferrer"
                      data-testid={`casino-claim-btn-${casino.id}`}
                      style={{
                        display: 'inline-block',
                        padding: '0.75rem 1.5rem',
                        background: '#2563eb',
                        color: 'white',
                        borderRadius: '50px',
                        fontWeight: '700',
                        fontSize: '0.875rem',
                        textDecoration: 'none',
                        transition: 'all 0.2s ease',
                        whiteSpace: 'nowrap'
                      }}
                      onMouseEnter={(e) => {
                        e.target.style.background = '#1d4ed8';
                        e.target.style.transform = 'translateY(-2px)';
                        e.target.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.3)';
                      }}
                      onMouseLeave={(e) => {
                        e.target.style.background = '#2563eb';
                        e.target.style.transform = 'translateY(0)';
                        e.target.style.boxShadow = 'none';
                      }}
                    >
                      CLAIM OFFER
                    </a>
                    {casino.review_link && (
                      <div style={{ marginTop: '0.5rem' }}>
                        <Link 
                          to={casino.review_link}
                          style={{
                            fontSize: '0.75rem',
                            color: '#6b7280',
                            textDecoration: 'underline'
                          }}
                        >
                          Read Review
                        </Link>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {casinos.length === 0 && (
          <div style={{ textAlign: 'center', padding: '4rem 0', color: '#6b7280' }}>
            <p>No casino listings available at the moment.</p>
          </div>
        )}
      </div>

      <Footer />
    </div>
  );
};

export default CasinosPage;
