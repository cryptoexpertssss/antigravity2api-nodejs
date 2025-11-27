import React, { useEffect } from "react";
import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import HomePage from "./pages/HomePage";
import ArticlePage from "./pages/ArticlePage";
import CategoryPage from "./pages/CategoryPage";
import CasinosPage from "./pages/CasinosPage";
import CasinoDetailPage from "./pages/CasinoDetailPage";
import LoginPage from "./pages/LoginPage";
import AdminDashboard from "./pages/admin/AdminDashboard";
import AdminArticles from "./pages/admin/AdminArticles";
import AdminCategories from "./pages/admin/AdminCategories";
import AdminCasinos from "./pages/admin/AdminCasinos";
import AdminReviews from "./pages/admin/AdminReviews";
import AdminAffiliateLinks from "./pages/admin/AdminAffiliateLinks";
import AdminAds from "./pages/admin/AdminAds";
import { setupAxiosInterceptors, isAuthenticated } from "./utils/auth";
import "./App.css";

// Protected Route Component
const ProtectedRoute = ({ children }) => {
  return isAuthenticated() ? children : <Navigate to="/login" replace />;
};

function App() {
  useEffect(() => {
    setupAxiosInterceptors();
  }, []);

  return (
    <div className="App">
      <BrowserRouter>
        <Routes>
          {/* Public Routes */}
          <Route path="/" element={<HomePage />} />
          <Route path="/article/:slug" element={<ArticlePage />} />
          <Route path="/category/:categoryId" element={<CategoryPage />} />
          <Route path="/casinos" element={<CasinosPage />} />
          <Route path="/casino/:casinoId" element={<CasinoDetailPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<LoginPage />} />
          
          {/* Protected Admin Routes */}
          <Route path="/admin" element={<ProtectedRoute><AdminDashboard /></ProtectedRoute>} />
          <Route path="/admin/articles" element={<ProtectedRoute><AdminArticles /></ProtectedRoute>} />
          <Route path="/admin/categories" element={<ProtectedRoute><AdminCategories /></ProtectedRoute>} />
          <Route path="/admin/casinos" element={<ProtectedRoute><AdminCasinos /></ProtectedRoute>} />
          <Route path="/admin/reviews" element={<ProtectedRoute><AdminReviews /></ProtectedRoute>} />
          <Route path="/admin/affiliate-links" element={<ProtectedRoute><AdminAffiliateLinks /></ProtectedRoute>} />
          <Route path="/admin/ads" element={<ProtectedRoute><AdminAds /></ProtectedRoute>} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
