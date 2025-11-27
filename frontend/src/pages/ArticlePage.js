import React, { useEffect, useState } from "react";
import axios from "axios";
import { useParams, Link } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const ArticlePage = () => {
  const { slug } = useParams();
  const [article, setArticle] = useState(null);
  const [category, setCategory] = useState(null);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchArticle();
    fetchCategories();
  }, [slug]);

  const fetchCategories = async () => {
    try {
      const response = await axios.get(`${API}/categories`);
      setCategories(response.data);
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  };

  const fetchArticle = async () => {
    try {
      const response = await axios.get(`${API}/articles/slug/${slug}`);
      setArticle(response.data);
      
      // Fetch category info
      const catResponse = await axios.get(`${API}/categories/${response.data.category_id}`);
      setCategory(catResponse.data);
    } catch (error) {
      console.error("Error fetching article:", error);
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

  if (!article) {
    return (
      <div>
        <Navbar categories={categories} />
        <div className="container" style={{ padding: '4rem 1.5rem', textAlign: 'center' }}>
          <h1>Article not found</h1>
          <Link to="/" className="btn btn-primary" style={{ marginTop: '2rem' }}>Go Home</Link>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div>
      <Navbar categories={categories} />
      
      <article style={{ maxWidth: '900px', margin: '0 auto', padding: '3rem 1.5rem' }}>
        {/* Breadcrumb */}
        <div style={{ marginBottom: '2rem', fontSize: '0.9rem', color: '#6b7280' }}>
          <Link to="/" style={{ color: '#6b7280' }}>Home</Link>
          <span style={{ margin: '0 0.5rem' }}>/</span>
          {category && (
            <>
              <Link to={`/category/${category.id}`} style={{ color: '#6b7280' }}>
                {category.name}
              </Link>
              <span style={{ margin: '0 0.5rem' }}>/</span>
            </>
          )}
          <span>{article.title}</span>
        </div>

        {/* Featured Image */}
        {article.featured_image && (
          <div style={{
            marginBottom: '2rem',
            borderRadius: '16px',
            overflow: 'hidden',
            boxShadow: '0 8px 24px rgba(0,0,0,0.1)'
          }}>
            <img 
              src={`${BACKEND_URL}${article.featured_image}`}
              alt={article.title}
              data-testid="article-featured-image"
              style={{
                width: '100%',
                height: '500px',
                objectFit: 'cover'
              }}
            />
          </div>
        )}

        {/* Title */}
        <h1 style={{
          fontSize: '3rem',
          fontWeight: '800',
          marginBottom: '1rem',
          lineHeight: '1.2'
        }} data-testid="article-title">{article.title}</h1>

        {/* Meta */}
        <div style={{
          display: 'flex',
          gap: '1.5rem',
          marginBottom: '2rem',
          paddingBottom: '2rem',
          borderBottom: '1px solid #e5e7eb',
          color: '#6b7280',
          fontSize: '0.95rem'
        }}>
          <div data-testid="article-author">By {article.author}</div>
          <div data-testid="article-date">{formatDate(article.created_at)}</div>
          {category && (
            <Link to={`/category/${category.id}`} style={{ color: '#2563eb', fontWeight: '600' }}>
              {category.name}
            </Link>
          )}
        </div>

        {/* Content */}
        <div 
          data-testid="article-content"
          style={{
            fontSize: '1.125rem',
            lineHeight: '1.8',
            color: '#1f2937'
          }}
          dangerouslySetInnerHTML={{ __html: article.content }}
        />

        {/* Tags */}
        {article.tags && article.tags.length > 0 && (
          <div style={{ marginTop: '3rem', paddingTop: '2rem', borderTop: '1px solid #e5e7eb' }}>
            <div style={{ fontWeight: '600', marginBottom: '1rem' }}>Tags:</div>
            <div style={{ display: 'flex', gap: '0.5rem', flexWrap: 'wrap' }}>
              {article.tags.map((tag, index) => (
                <span 
                  key={index}
                  data-testid={`article-tag-${index}`}
                  style={{
                    padding: '0.5rem 1rem',
                    background: '#f3f4f6',
                    borderRadius: '20px',
                    fontSize: '0.875rem',
                    color: '#4b5563'
                  }}
                >
                  {tag}
                </span>
              ))}
            </div>
          </div>
        )}
      </article>

      <Footer />
    </div>
  );
};

export default ArticlePage;
