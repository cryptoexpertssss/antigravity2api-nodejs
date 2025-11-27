import React from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import HomePage from "./pages/HomePage";
import ArticlePage from "./pages/ArticlePage";
import CategoryPage from "./pages/CategoryPage";
import CasinosPage from "./pages/CasinosPage";
import CasinoDetailPage from "./pages/CasinoDetailPage";
import AdminDashboard from "./pages/admin/AdminDashboard";
import AdminArticles from "./pages/admin/AdminArticles";
import AdminCategories from "./pages/admin/AdminCategories";
import AdminCasinos from "./pages/admin/AdminCasinos";
import AdminReviews from "./pages/admin/AdminReviews";
import AdminAffiliateLinks from "./pages/admin/AdminAffiliateLinks";
import AdminAds from "./pages/admin/AdminAds";
import "./App.css";

function App() {
  return (
    <div className="App">
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/article/:slug" element={<ArticlePage />} />
          <Route path="/category/:categoryId" element={<CategoryPage />} />
          <Route path="/casinos" element={<CasinosPage />} />
          <Route path="/casino/:casinoId" element={<CasinoDetailPage />} />
          <Route path="/admin" element={<AdminDashboard />} />
          <Route path="/admin/articles" element={<AdminArticles />} />
          <Route path="/admin/categories" element={<AdminCategories />} />
          <Route path="/admin/casinos" element={<AdminCasinos />} />
          <Route path="/admin/reviews" element={<AdminReviews />} />
          <Route path="/admin/affiliate-links" element={<AdminAffiliateLinks />} />
          <Route path="/admin/ads" element={<AdminAds />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;
