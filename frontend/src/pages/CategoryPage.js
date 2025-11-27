import React, { useEffect, useState } from "react";
import axios from "axios";
import { useParams, Link } from "react-router-dom";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const CategoryPage = () => {
  const { categoryId } = useParams();
  const [category, setCategory] = useState(null);
  const [articles, setArticles] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchData();
  }, [categoryId]);

  const fetchData = async () => {
    try {
      const [categoryRes, articlesRes, categoriesRes] = await Promise.all([
        axios.get(`${API}/categories/${categoryId}`),
        axios.get(`${API}/articles?category_id=${categoryId}&status=published`),
        axios.get(`${API}/categories`)
      ]);
      setCategory(categoryRes.data);
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

  return (
    <div>
      <Navbar categories={categories} />
      
      <div className="container" style={{ padding: '3rem 1.5rem' }}>
        {/* Category Header */}
        <div style={{ marginBottom: '3rem' }}>
          <div style={{ marginBottom: '1rem', fontSize: '0.9rem', color: '#6b7280' }}>
            <Link to="/" style={{ color: '#6b7280' }}>Home</Link>
            <span style={{ margin: '0 0.5rem' }}>/</span>
            <span>{category?.name}</span>
          </div>
          <h1 style={{ fontSize: '3rem', fontWeight: '800', marginBottom: '1rem' }} data-testid="category-title">
            {category?.name}
          </h1>
          {category?.description && (
            <p style={{ fontSize: '1.125rem', color: '#6b7280' }}>{category.description}</p>
          )}
        </div>

        {/* Articles Grid */}
        {articles.length === 0 ? (
          <div style={{ textAlign: 'center', padding: '4rem 0', color: '#6b7280' }}>
            <p>No articles found in this category.</p>
          </div>
        ) : (
          <div style={{
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fill, minmax(350px, 1fr))',
            gap: '2rem'
          }}>
            {articles.map((article) => (
              <article key={article.id} className="card" data-testid={`category-article-${article.id}`}>
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
                    <Link to={`/article/${article.slug}`}>
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
        )}
      </div>

      <Footer />
    </div>
  );
};

export default CategoryPage;
