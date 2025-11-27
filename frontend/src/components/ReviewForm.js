import React, { useState } from "react";
import axios from "axios";
import { toast } from "sonner";
import { Button } from "./ui/button";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

const ReviewForm = ({ casinoId, casinoName, onClose }) => {
  const [formData, setFormData] = useState({
    user_name: "",
    user_avatar: "",
    rating: 5,
    title: "",
    comment: "",
    pros: [""],
    cons: [""]
  });
  const [submitting, setSubmitting] = useState(false);
  const [uploadingAvatar, setUploadingAvatar] = useState(false);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleAvatarUpload = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const formDataObj = new FormData();
    formDataObj.append('file', file);

    setUploadingAvatar(true);
    try {
      const response = await axios.post(`${API}/upload`, formDataObj);
      setFormData(prev => ({ ...prev, user_avatar: response.data.url }));
      toast.success("Profile picture uploaded successfully");
    } catch (error) {
      console.error("Error uploading avatar:", error);
      toast.error("Failed to upload profile picture");
    } finally {
      setUploadingAvatar(false);
    }
  };

  const handleProChange = (index, value) => {
    const newPros = [...formData.pros];
    newPros[index] = value;
    setFormData(prev => ({ ...prev, pros: newPros }));
  };

  const handleConChange = (index, value) => {
    const newCons = [...formData.cons];
    newCons[index] = value;
    setFormData(prev => ({ ...prev, cons: newCons }));
  };

  const addPro = () => {
    setFormData(prev => ({ ...prev, pros: [...prev.pros, ""] }));
  };

  const addCon = () => {
    setFormData(prev => ({ ...prev, cons: [...prev.cons, ""] }));
  };

  const removePro = (index) => {
    setFormData(prev => ({ ...prev, pros: prev.pros.filter((_, i) => i !== index) }));
  };

  const removeCon = (index) => {
    setFormData(prev => ({ ...prev, cons: prev.cons.filter((_, i) => i !== index) }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);

    const reviewData = {
      casino_id: casinoId,
      user_name: formData.user_name,
      user_avatar: formData.user_avatar || null,
      rating: parseFloat(formData.rating),
      title: formData.title,
      comment: formData.comment,
      pros: formData.pros.filter(p => p.trim() !== ""),
      cons: formData.cons.filter(c => c.trim() !== "")
    };

    try {
      await axios.post(`${API}/reviews`, reviewData);
      toast.success("Review submitted! It will be visible after admin approval.");
      onClose();
    } catch (error) {
      console.error("Error submitting review:", error);
      toast.error("Failed to submit review. Please try again.");
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div style={{
      background: 'white',
      borderRadius: '12px',
      padding: '2rem',
      marginBottom: '2rem',
      border: '2px solid #3b82f6'
    }}>
      <h3 style={{ fontSize: '1.5rem', fontWeight: '700', marginBottom: '1.5rem' }}>
        Write a Review for {casinoName}
      </h3>

      <form onSubmit={handleSubmit}>
        <div style={{ marginBottom: '1.5rem' }}>
          <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Your Name *</label>
          <input
            type="text"
            name="user_name"
            value={formData.user_name}
            onChange={handleChange}
            required
            data-testid="review-name-input"
            placeholder="Enter your name"
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #e5e7eb',
              borderRadius: '8px',
              fontSize: '1rem'
            }}
          />
        </div>

        <div style={{ marginBottom: '1.5rem' }}>
          <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Rating *</label>
          <select
            name="rating"
            value={formData.rating}
            onChange={handleChange}
            required
            data-testid="review-rating-select"
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #e5e7eb',
              borderRadius: '8px',
              fontSize: '1rem'
            }}
          >
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Very Good</option>
            <option value="3">3 - Good</option>
            <option value="2">2 - Fair</option>
            <option value="1">1 - Poor</option>
          </select>
        </div>

        <div style={{ marginBottom: '1.5rem' }}>
          <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Review Title *</label>
          <input
            type="text"
            name="title"
            value={formData.title}
            onChange={handleChange}
            required
            data-testid="review-title-input"
            placeholder="Summarize your experience"
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #e5e7eb',
              borderRadius: '8px',
              fontSize: '1rem'
            }}
          />
        </div>

        <div style={{ marginBottom: '1.5rem' }}>
          <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600' }}>Your Review *</label>
          <textarea
            name="comment"
            value={formData.comment}
            onChange={handleChange}
            required
            data-testid="review-comment-input"
            rows="5"
            placeholder="Share your detailed experience..."
            style={{
              width: '100%',
              padding: '0.75rem',
              border: '1px solid #e5e7eb',
              borderRadius: '8px',
              fontSize: '1rem',
              resize: 'vertical'
            }}
          />
        </div>

        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1.5rem', marginBottom: '1.5rem' }}>
          {/* Pros */}
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600', color: '#059669' }}>Pros (Optional)</label>
            {formData.pros.map((pro, index) => (
              <div key={index} style={{ display: 'flex', gap: '0.5rem', marginBottom: '0.5rem' }}>
                <input
                  type="text"
                  value={pro}
                  onChange={(e) => handleProChange(index, e.target.value)}
                  data-testid={`review-pro-${index}`}
                  placeholder="Add a positive point"
                  style={{
                    flex: 1,
                    padding: '0.5rem',
                    border: '1px solid #e5e7eb',
                    borderRadius: '8px'
                  }}
                />
                {formData.pros.length > 1 && (
                  <button
                    type="button"
                    onClick={() => removePro(index)}
                    style={{
                      padding: '0.5rem',
                      background: '#ef4444',
                      color: 'white',
                      border: 'none',
                      borderRadius: '8px',
                      cursor: 'pointer'
                    }}
                  >
                    ×
                  </button>
                )}
              </div>
            ))}
            <button
              type="button"
              onClick={addPro}
              data-testid="add-pro-btn"
              style={{
                padding: '0.5rem 1rem',
                background: '#10b981',
                color: 'white',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontSize: '0.875rem',
                fontWeight: '600',
                marginTop: '0.5rem'
              }}
            >
              + Add Pro
            </button>
          </div>

          {/* Cons */}
          <div>
            <label style={{ display: 'block', marginBottom: '0.5rem', fontWeight: '600', color: '#dc2626' }}>Cons (Optional)</label>
            {formData.cons.map((con, index) => (
              <div key={index} style={{ display: 'flex', gap: '0.5rem', marginBottom: '0.5rem' }}>
                <input
                  type="text"
                  value={con}
                  onChange={(e) => handleConChange(index, e.target.value)}
                  data-testid={`review-con-${index}`}
                  placeholder="Add a negative point"
                  style={{
                    flex: 1,
                    padding: '0.5rem',
                    border: '1px solid #e5e7eb',
                    borderRadius: '8px'
                  }}
                />
                {formData.cons.length > 1 && (
                  <button
                    type="button"
                    onClick={() => removeCon(index)}
                    style={{
                      padding: '0.5rem',
                      background: '#ef4444',
                      color: 'white',
                      border: 'none',
                      borderRadius: '8px',
                      cursor: 'pointer'
                    }}
                  >
                    ×
                  </button>
                )}
              </div>
            ))}
            <button
              type="button"
              onClick={addCon}
              data-testid="add-con-btn"
              style={{
                padding: '0.5rem 1rem',
                background: '#ef4444',
                color: 'white',
                border: 'none',
                borderRadius: '8px',
                cursor: 'pointer',
                fontSize: '0.875rem',
                fontWeight: '600',
                marginTop: '0.5rem'
              }}
            >
              + Add Con
            </button>
          </div>
        </div>

        <div style={{ display: 'flex', gap: '1rem', justifyContent: 'flex-end' }}>
          <Button
            type="button"
            onClick={onClose}
            variant="secondary"
            data-testid="cancel-review-btn"
          >
            Cancel
          </Button>
          <Button
            type="submit"
            disabled={submitting}
            data-testid="submit-review-btn"
          >
            {submitting ? 'Submitting...' : 'Submit Review'}
          </Button>
        </div>
      </form>
    </div>
  );
};

export default ReviewForm;
