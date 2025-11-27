import React, { useState } from "react";
import { Link } from "react-router-dom";

const Navbar = ({ categories = [] }) => {
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  return (
    <nav style={{
      background: 'white',
      borderBottom: '1px solid #e5e7eb',
      position: 'sticky',
      top: 0,
      zIndex: 50,
      boxShadow: '0 1px 3px rgba(0,0,0,0.05)'
    }}>
      <div className="container" style={{
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        padding: '1rem 1.5rem'
      }}>
        {/* Logo */}
        <Link 
          to="/" 
          data-testid="nav-logo"
          style={{
            fontSize: '1.75rem',
            fontWeight: '800',
            color: '#1a1a1a',
            fontFamily: 'Merriweather, Georgia, serif'
          }}
        >
          Gaming<span style={{ color: '#2563eb' }}>Today</span>
        </Link>

        {/* Desktop Menu */}
        <div style={{
          display: 'flex',
          gap: '2rem',
          alignItems: 'center'
        }} className="desktop-menu">
          <Link 
            to="/" 
            data-testid="nav-home"
            style={{
              fontWeight: '600',
              fontSize: '0.95rem',
              color: '#4b5563',
              transition: 'color 0.2s ease'
            }}
            onMouseEnter={(e) => e.target.style.color = '#2563eb'}
            onMouseLeave={(e) => e.target.style.color = '#4b5563'}
          >
            Home
          </Link>
          <Link 
            to="/casinos" 
            data-testid="nav-casinos"
            style={{
              fontWeight: '600',
              fontSize: '0.95rem',
              color: '#4b5563',
              transition: 'color 0.2s ease'
            }}
            onMouseEnter={(e) => e.target.style.color = '#2563eb'}
            onMouseLeave={(e) => e.target.style.color = '#4b5563'}
          >
            Casino Rankings
          </Link>
          {categories.slice(0, 2).map((cat) => (
            <Link 
              key={cat.id}
              to={`/category/${cat.id}`}
              data-testid={`nav-category-${cat.id}`}
              style={{
                fontWeight: '600',
                fontSize: '0.95rem',
                color: '#4b5563',
                transition: 'color 0.2s ease'
              }}
              onMouseEnter={(e) => e.target.style.color = '#2563eb'}
              onMouseLeave={(e) => e.target.style.color = '#4b5563'}
            >
              {cat.name}
            </Link>
          ))}
          <Link 
            to="/login" 
            data-testid="nav-login"
            style={{
              fontWeight: '600',
              fontSize: '0.95rem',
              color: '#4b5563',
              transition: 'color 0.2s ease'
            }}
            onMouseEnter={(e) => e.target.style.color = '#2563eb'}
            onMouseLeave={(e) => e.target.style.color = '#4b5563'}
          >
            Login
          </Link>
          <Link 
            to="/register" 
            data-testid="nav-register"
            style={{
              padding: '0.6rem 1.5rem',
              fontSize: '0.875rem',
              background: '#2563eb',
              color: 'white',
              borderRadius: '8px',
              fontWeight: '600',
              transition: 'background 0.2s ease'
            }}
            onMouseEnter={(e) => e.target.style.background = '#1d4ed8'}
            onMouseLeave={(e) => e.target.style.background = '#2563eb'}
          >
            Sign Up
          </Link>
          <Link 
            to="/admin" 
            data-testid="nav-admin"
            style={{
              padding: '0.5rem 1.25rem',
              fontSize: '0.75rem',
              color: '#9ca3af',
              border: '1px solid #e5e7eb',
              borderRadius: '6px',
              fontWeight: '500'
            }}
          >
            Admin
          </Link>
        </div>

        {/* Mobile Menu Button */}
        <button 
          onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
          data-testid="mobile-menu-btn"
          className="mobile-menu-btn"
          style={{
            display: 'none',
            background: 'none',
            border: 'none',
            fontSize: '1.5rem',
            cursor: 'pointer',
            color: '#1a1a1a'
          }}
        >
          ☰
        </button>
      </div>

      {/* Mobile Menu */}
      {mobileMenuOpen && (
        <div className="mobile-menu" style={{
          background: 'white',
          borderTop: '1px solid #e5e7eb',
          padding: '1rem'
        }}>
          <div style={{ display: 'flex', flexDirection: 'column', gap: '1rem' }}>
            <Link to="/" style={{ padding: '0.5rem', fontWeight: '600', color: '#4b5563' }}>Home</Link>
            <Link to="/casinos" style={{ padding: '0.5rem', fontWeight: '600', color: '#4b5563' }}>Casino Rankings</Link>
            {categories.map((cat) => (
              <Link key={cat.id} to={`/category/${cat.id}`} style={{ padding: '0.5rem', fontWeight: '600', color: '#4b5563' }}>
                {cat.name}
              </Link>
            ))}
            <Link to="/admin" className="btn btn-primary" style={{ marginTop: '0.5rem' }}>Admin</Link>
          </div>
        </div>
      )}

      <style>{`
        @media (max-width: 768px) {
          .desktop-menu {
            display: none !important;
          }
          .mobile-menu-btn {
            display: block !important;
          }
        }
      `}</style>
    </nav>
  );
};

export default Navbar;
