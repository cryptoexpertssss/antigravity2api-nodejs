import React, { useEffect, useState } from "react";
import axios from "axios";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const AdBanner = ({ position }) => {
  const [ad, setAd] = useState(null);
  const [tracked, setTracked] = useState(false);

  useEffect(() => {
    fetchAd();
  }, [position]);

  useEffect(() => {
    if (ad && !tracked) {
      trackImpression();
      setTracked(true);
    }
  }, [ad]);

  const fetchAd = async () => {
    try {
      const response = await axios.get(`${API}/ads?position=${position}&active_only=true`);
      if (response.data.length > 0) {
        // Get random ad if multiple available
        const randomAd = response.data[Math.floor(Math.random() * response.data.length)];
        setAd(randomAd);
      }
    } catch (error) {
      console.error("Error fetching ad:", error);
    }
  };

  const trackImpression = async () => {
    if (!ad) return;
    try {
      await axios.post(`${API}/ads/${ad.id}/impression`);
    } catch (error) {
      console.error("Error tracking impression:", error);
    }
  };

  const handleClick = async () => {
    if (!ad) return;
    try {
      await axios.post(`${API}/ads/${ad.id}/click`);
    } catch (error) {
      console.error("Error tracking click:", error);
    }
  };

  if (!ad) return null;

  const containerStyles = {
    header: {
      width: '100%',
      maxWidth: '1200px',
      margin: '1rem auto',
      display: 'flex',
      justifyContent: 'center'
    },
    sidebar: {
      width: '300px',
      marginBottom: '1.5rem'
    },
    footer: {
      width: '100%',
      maxWidth: '1200px',
      margin: '2rem auto',
      display: 'flex',
      justifyContent: 'center'
    },
    'in-content': {
      width: '100%',
      margin: '2rem 0',
      display: 'flex',
      justifyContent: 'center'
    }
  };

  return (
    <div 
      style={containerStyles[position] || {}}
      data-testid={`ad-banner-${position}`}
    >
      <a
        href={ad.link_url}
        target="_blank"
        rel="noopener noreferrer sponsored"
        onClick={handleClick}
        data-testid={`ad-link-${ad.id}`}
        style={{
          display: 'block',
          textDecoration: 'none',
          position: 'relative',
          overflow: 'hidden',
          borderRadius: '8px',
          transition: 'transform 0.2s ease'
        }}
        onMouseEnter={(e) => e.currentTarget.style.transform = 'scale(1.02)'}
        onMouseLeave={(e) => e.currentTarget.style.transform = 'scale(1)'}
      >
        <img
          src={ad.image_url}
          alt={ad.alt_text}
          data-testid={`ad-image-${ad.id}`}
          style={{
            width: '100%',
            height: 'auto',
            display: 'block'
          }}
        />
        <div style={{
          position: 'absolute',
          top: '0.5rem',
          right: '0.5rem',
          padding: '0.25rem 0.5rem',
          background: 'rgba(0,0,0,0.6)',
          color: 'white',
          fontSize: '0.75rem',
          borderRadius: '4px'
        }}>
          Ad
        </div>
      </a>
    </div>
  );
};

export default AdBanner;
