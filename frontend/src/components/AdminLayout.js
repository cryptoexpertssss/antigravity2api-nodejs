import React from "react";
import { Link, useLocation } from "react-router-dom";

const AdminLayout = ({ children }) => {
  const location = useLocation();

  const isActive = (path) => location.pathname === path;

  const menuItems = [
    { path: '/admin', label: 'Dashboard', icon: '📋' },
    { path: '/admin/articles', label: 'Articles', icon: '📝' },
    { path: '/admin/categories', label: 'Categories', icon: '📂' },
    { path: '/admin/casinos', label: 'Casinos', icon: '🎰' },
    { path: '/admin/reviews', label: 'Reviews', icon: '⭐' },
    { path: '/admin/affiliate-links', label: 'Affiliate Links', icon: '🔗' },
    { path: '/admin/ads', label: 'Advertisements', icon: '📢' }
  ];

  return (
    <div className="admin-layout">
      {/* Sidebar */}
      <aside className="admin-sidebar">
        <div style={{ marginBottom: '2rem' }}>
          <h2 style={{ fontSize: '1.5rem', fontWeight: '800' }}>
            Gaming<span style={{ color: '#3b82f6' }}>Today</span>
          </h2>
          <p style={{ fontSize: '0.875rem', color: '#9ca3af', marginTop: '0.5rem' }}>Admin Panel</p>
        </div>

        <nav>
          <ul style={{ listStyle: 'none', padding: 0 }}>
            {menuItems.map((item) => (
              <li key={item.path} style={{ marginBottom: '0.5rem' }}>
                <Link
                  to={item.path}
                  data-testid={`admin-nav-${item.label.toLowerCase()}`}
                  style={{
                    display: 'flex',
                    alignItems: 'center',
                    gap: '0.75rem',
                    padding: '0.75rem 1rem',
                    borderRadius: '8px',
                    color: isActive(item.path) ? '#3b82f6' : '#d1d5db',
                    background: isActive(item.path) ? 'rgba(59, 130, 246, 0.1)' : 'transparent',
                    fontWeight: isActive(item.path) ? '600' : '500',
                    fontSize: '0.95rem',
                    transition: 'all 0.2s ease',
                    textDecoration: 'none'
                  }}
                  onMouseEnter={(e) => {
                    if (!isActive(item.path)) {
                      e.target.style.background = 'rgba(255,255,255,0.05)';
                    }
                  }}
                  onMouseLeave={(e) => {
                    if (!isActive(item.path)) {
                      e.target.style.background = 'transparent';
                    }
                  }}
                >
                  <span style={{ fontSize: '1.25rem' }}>{item.icon}</span>
                  {item.label}
                </Link>
              </li>
            ))}
          </ul>
        </nav>

        <div style={{ marginTop: '3rem', paddingTop: '2rem', borderTop: '1px solid #374151' }}>
          <Link
            to="/"
            data-testid="back-to-site-link"
            style={{
              display: 'block',
              padding: '0.75rem 1rem',
              color: '#d1d5db',
              fontSize: '0.95rem',
              textDecoration: 'none',
              transition: 'color 0.2s ease'
            }}
            onMouseEnter={(e) => e.target.style.color = '#3b82f6'}
            onMouseLeave={(e) => e.target.style.color = '#d1d5db'}
          >
            ← Back to Site
          </Link>
        </div>
      </aside>

      {/* Main Content */}
      <main className="admin-content">
        {children}
      </main>
    </div>
  );
};

export default AdminLayout;
