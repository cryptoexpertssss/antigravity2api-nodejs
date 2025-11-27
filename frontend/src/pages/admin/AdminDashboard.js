import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import AdminLayout from "../../components/AdminLayout";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdminDashboard = () => {
  const [stats, setStats] = useState({
    articles: 0,
    categories: 0,
    casinos: 0
  });

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const [articlesRes, categoriesRes, casinosRes] = await Promise.all([
        axios.get(`${API}/articles`),
        axios.get(`${API}/categories`),
        axios.get(`${API}/casinos`)
      ]);
      setStats({
        articles: articlesRes.data.length,
        categories: categoriesRes.data.length,
        casinos: casinosRes.data.length
      });
    } catch (error) {
      console.error("Error fetching stats:", error);
    }
  };

  const statCards = [
    { title: 'Total Articles', value: stats.articles, link: '/admin/articles', color: '#3b82f6' },
    { title: 'Categories', value: stats.categories, link: '/admin/categories', color: '#8b5cf6' },
    { title: 'Casino Listings', value: stats.casinos, link: '/admin/casinos', color: '#10b981' }
  ];

  return (
    <AdminLayout>
      <div>
        <h1 style={{ fontSize: '2.5rem', fontWeight: '800', marginBottom: '2rem' }} data-testid="admin-dashboard-title">
          Dashboard
        </h1>

        {/* Stats Grid */}
        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fit, minmax(280px, 1fr))',
          gap: '1.5rem',
          marginBottom: '3rem'
        }}>
          {statCards.map((stat, index) => (
            <Link 
              key={index}
              to={stat.link}
              data-testid={`stat-card-${index}`}
              style={{
                background: 'white',
                borderRadius: '12px',
                padding: '2rem',
                boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
                border: '1px solid #e5e7eb',
                transition: 'all 0.3s ease',
                textDecoration: 'none'
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.transform = 'translateY(-4px)';
                e.currentTarget.style.boxShadow = '0 12px 24px rgba(0,0,0,0.1)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.transform = 'translateY(0)';
                e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.05)';
              }}
            >
              <div style={{ color: '#6b7280', fontSize: '0.95rem', marginBottom: '0.5rem' }}>
                {stat.title}
              </div>
              <div style={{ fontSize: '3rem', fontWeight: '800', color: stat.color }}>
                {stat.value}
              </div>
            </Link>
          ))}
        </div>

        {/* Quick Actions */}
        <div style={{
          background: 'white',
          borderRadius: '12px',
          padding: '2rem',
          boxShadow: '0 4px 12px rgba(0,0,0,0.05)',
          border: '1px solid #e5e7eb'
        }}>
          <h2 style={{ fontSize: '1.5rem', fontWeight: '700', marginBottom: '1.5rem' }}>
            Quick Actions
          </h2>
          <div style={{ display: 'flex', gap: '1rem', flexWrap: 'wrap' }}>
            <Link to="/admin/articles" className="btn btn-primary" data-testid="manage-articles-btn">
              Manage Articles
            </Link>
            <Link to="/admin/categories" className="btn btn-secondary" data-testid="manage-categories-btn">
              Manage Categories
            </Link>
            <Link to="/admin/casinos" className="btn btn-secondary" data-testid="manage-casinos-btn">
              Manage Casinos
            </Link>
            <Link to="/" className="btn btn-secondary" data-testid="view-site-btn">
              View Site
            </Link>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
};

export default AdminDashboard;
