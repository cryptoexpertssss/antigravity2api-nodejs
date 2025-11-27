import React from "react";
import { Link } from "react-router-dom";

const Footer = () => {
  const currentYear = new Date().getFullYear();

  return (
    <footer style={{
      background: '#1f2937',
      color: 'white',
      marginTop: '4rem'
    }}>
      <div className="container" style={{ padding: '3rem 1.5rem' }}>
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))',
          gap: '2rem',
          marginBottom: '2rem'
        }}>
          {/* About */}
          <div>
            <h3 style={{ fontSize: '1.5rem', fontWeight: '700', marginBottom: '1rem' }}>
              Gaming<span style={{ color: '#3b82f6' }}>Today</span>
            </h3>
            <p style={{ color: '#9ca3af', fontSize: '0.95rem', lineHeight: '1.6' }}>
              Your trusted source for gambling news, casino reviews, and expert insights since 2025.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h4 style={{ fontSize: '1.125rem', fontWeight: '600', marginBottom: '1rem' }}>Quick Links</h4>
            <ul style={{ listStyle: 'none', padding: 0 }}>
              <li style={{ marginBottom: '0.5rem' }}>
                <Link to="/" style={{ color: '#9ca3af', fontSize: '0.95rem' }}>Home</Link>
              </li>
              <li style={{ marginBottom: '0.5rem' }}>
                <Link to="/casinos" style={{ color: '#9ca3af', fontSize: '0.95rem' }}>Casino Rankings</Link>
              </li>
              <li style={{ marginBottom: '0.5rem' }}>
                <Link to="/admin" style={{ color: '#9ca3af', fontSize: '0.95rem' }}>Admin Panel</Link>
              </li>
            </ul>
          </div>

          {/* Disclaimer */}
          <div>
            <h4 style={{ fontSize: '1.125rem', fontWeight: '600', marginBottom: '1rem' }}>Responsible Gaming</h4>
            <p style={{ color: '#9ca3af', fontSize: '0.875rem', lineHeight: '1.6' }}>
              Please gamble responsibly. If you or someone you know has a gambling problem, call 1-800-GAMBLER.
            </p>
          </div>
        </div>

        <div style={{
          paddingTop: '2rem',
          borderTop: '1px solid #374151',
          textAlign: 'center',
          color: '#9ca3af',
          fontSize: '0.875rem'
        }}>
          <p>© {currentYear} GamingToday. All rights reserved. 21+ Only.</p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
