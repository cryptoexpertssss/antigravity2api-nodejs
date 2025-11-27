import React, { useEffect, useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const HomePage = () => {
  const [articles, setArticles] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const [articlesRes, categoriesRes] = await Promise.all([
        axios.get(`${API}/articles?status=published&limit=6`),
        axios.get(`${API}/categories`)
      ]);
      setArticles(articlesRes.data);
      setCategories(categoriesRes.data);
    } catch (error) {
      console.error("Error fetching data:", error);
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

  if (loading) {
    return <div className="loading">Loading...</div>;
  }

  const featuredArticle = articles[0];
  const regularArticles = articles.slice(1);

  return (
    <div className="homepage">
      <Navbar categories={categories} />
      
      {/* Hero Section */}
      {featuredArticle && (
        <div className="hero-section" style={{
          background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
          padding: '4rem 0',
          marginBottom: '3rem'
        }}>
          <div className="container">
            <div className="hero-content" style={{
              display: 'grid',
              gridTemplateColumns: '1fr 1fr',
              gap: '3rem',
              alignItems: 'center'
            }}>
              <div>
                <span style={{
                  display: 'inline-block',
                  padding: '0.5rem 1rem',
                  background: 'rgba(255,255,255,0.2)',
                  borderRadius: '20px',
                  color: 'white',
                  fontSize: '0.875rem',
                  fontWeight: '600',
                  marginBottom: '1rem'
                }}>Featured Story</span>
                <h1 style={{
                  fontSize: '3rem',
                  fontWeight: '800',
                  color: 'white',
                  marginBottom: '1rem',
                  lineHeight: '1.1'
                }}>{featuredArticle.title}</h1>
                <p style={{
                  fontSize: '1.125rem',
                  color: 'rgba(255,255,255,0.9)',
                  marginBottom: '2rem'
                }}>{featuredArticle.excerpt}</p>
                <Link 
                  to={`/article/${featuredArticle.slug}`}
                  data-testid="featured-article-link"
                  style={{
                    display: 'inline-block',
                    padding: '1rem 2rem',
                    background: 'white',
                    color: '#667eea',
                    borderRadius: '50px',
                    fontWeight: '700',
                    fontSize: '1rem',
                    transition: 'all 0.3s ease'
                  }}
                  onMouseEnter={(e) => {
                    e.target.style.transform = 'translateY(-2px)';
                    e.target.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
                  }}
                  onMouseLeave={(e) => {
                    e.target.style.transform = 'translateY(0)';
                    e.target.style.boxShadow = 'none';
                  }}
                >
                  Read Full Story →
                </Link>
              </div>
              {featuredArticle.featured_image && (
                <div style={{
                  borderRadius: '16px',
                  overflow: 'hidden',
                  boxShadow: '0 20px 40px rgba(0,0,0,0.3)'
                }}>
                  <img 
                    src={`${BACKEND_URL}${featuredArticle.featured_image}`}
                    alt={featuredArticle.title}
                    style={{
                      width: '100%',
                      height: '400px',
                      objectFit: 'cover'
                    }}
                  />
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* Latest Articles */}
      <div className="container" style={{ marginBottom: '4rem' }}>
        <div style={{
          display: 'flex',
          justifyContent: 'space-between',
          alignItems: 'center',
          marginBottom: '2rem'
        }}>
          <h2 style={{ fontSize: '2rem', fontWeight: '700' }}>Latest News</h2>
          <Link to="/casinos" className="btn btn-secondary" data-testid="view-casinos-btn">
            View Casino Rankings
          </Link>
        </div>

        <div style={{
          display: 'grid',
          gridTemplateColumns: 'repeat(auto-fill, minmax(350px, 1fr))',
          gap: '2rem'
        }}>
          {regularArticles.map((article) => (
            <article key={article.id} className="card" data-testid={`article-card-${article.id}`}>
              {article.featured_image && (
                <img 
                  src={`${BACKEND_URL}${article.featured_image}`}
                  alt={article.title}
                  style={{
                    width: '100%',
                    height: '200px',
                    objectFit: 'cover'
                  }}
                />
              )}
              <div style={{ padding: '1.5rem' }}>
                <div style={{
                  fontSize: '0.875rem',
                  color: '#6b7280',
                  marginBottom: '0.5rem'
                }}>
                  {formatDate(article.created_at)} • {article.author}
                </div>
                <h3 style={{
                  fontSize: '1.25rem',
                  fontWeight: '700',
                  marginBottom: '0.75rem'
                }}>
                  <Link to={`/article/${article.slug}`} data-testid={`article-title-${article.id}`}>
                    {article.title}
                  </Link>
                </h3>
                <p style={{
                  color: '#4b5563',
                  fontSize: '0.95rem',
                  marginBottom: '1rem'
                }}>{article.excerpt}</p>
                <Link 
                  to={`/article/${article.slug}`}
                  data-testid={`read-more-${article.id}`}
                  style={{
                    color: '#2563eb',
                    fontWeight: '600',
                    fontSize: '0.9rem'
                  }}
                >
                  Read More →
                </Link>
              </div>
            </article>
          ))}
        </div>
      </div>

      <Footer />
    </div>
  );
};

export default HomePage;
