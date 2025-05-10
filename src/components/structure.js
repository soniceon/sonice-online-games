/**
 * 网站组件结构文件
 * 包含:
 * 1. 导航栏组件 (Navbar)
 * 2. 侧边栏组件 (Sidebar)
 * 3. 底部栏组件 (Footer)
 * 4. 布局组件 (Layout)
 */

import React, { useState } from 'react';

// 导航栏组件
export const Navbar = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const handleSearchChange = (e) => {
    setSearchQuery(e.target.value);
  };

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    console.log('Search query:', searchQuery);
  };

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  return (
    <nav className="fixed top-0 left-0 right-0 z-40 navbar">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex justify-between items-center h-full">
          {/* Logo */}
          <div className="flex items-center">
            <a href="/" className="flex items-center">
              <img 
                src="/src/assets/icons/logo.png" 
                alt="Sonice" 
                className="h-8 w-8"
              />
              <span className="text-white font-bold text-xl ml-2">Sonice</span>
              <span className="text-blue-400 font-bold text-xl ml-1">Games</span>
            </a>
          </div>

          {/* 中央搜索框 */}
          <div className="flex-1 flex justify-center">
            <form onSubmit={handleSearchSubmit} className="relative hidden md:block w-full max-w-md">
              <input
                type="text"
                placeholder="Search games..."
                value={searchQuery}
                onChange={handleSearchChange}
                className="search-input w-full"
              />
            </form>
          </div>

          {/* 右侧登录按钮 */}
          <div className="flex items-center">
            <button className="login-button flex items-center">
              <span className="mr-1">Login</span>
              <i className="fas fa-chevron-down text-xs ml-1"></i>
            </button>

            {/* 移动端菜单按钮 */}
            <button 
              className="ml-4 text-white md:hidden"
              onClick={toggleMobileMenu}
            >
              <i className={`fas ${isMobileMenuOpen ? 'fa-times' : 'fa-bars'}`}></i>
            </button>
          </div>
        </div>
      </div>

      {/* 移动端菜单 */}
      <div className={`md:hidden bg-[#121212] border-t border-white/10 ${isMobileMenuOpen ? 'block' : 'hidden'}`}>
        <div className="py-3 px-4">
          <form onSubmit={handleSearchSubmit} className="mb-4">
            <input
              type="text"
              placeholder="Search games..."
              value={searchQuery}
              onChange={handleSearchChange}
              className="search-input w-full"
            />
          </form>
        </div>
      </div>
    </nav>
  );
};

// 侧边栏组件
export const Sidebar = () => {
  const [isMobileOpen, setIsMobileOpen] = useState(false);
  const [isExpanded, setIsExpanded] = useState(false);
  
  const toggleMobileSidebar = () => {
    setIsMobileOpen(!isMobileOpen);
  };
  
  const closeMobileSidebar = () => {
    setIsMobileOpen(false);
  };
  
  const toggleExpand = () => {
    setIsExpanded(!isExpanded);
    // 同时更新主内容区域的margin，保持布局一致
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
      if (!isExpanded) {
        mainContent.classList.add('sidebar-expanded');
      } else {
        mainContent.classList.remove('sidebar-expanded');
      }
    }
  };
  
  return (
    <>
      {/* Sidebar toggle button - 仅在移动设备上显示 */}
      <div className="fixed bottom-4 right-4 lg:hidden z-40">
        <button
          onClick={toggleMobileSidebar}
          className="bg-blue-600 text-white p-3 rounded-full shadow-lg"
          aria-label="Toggle sidebar"
        >
          <i className={`fas ${isMobileOpen ? 'fa-times' : 'fa-bars'}`}></i>
        </button>
      </div>
      
      {/* Sidebar */}
      <aside 
        className={`sidebar ${isMobileOpen ? 'show' : ''} ${isExpanded ? 'expanded' : ''}`}
        onMouseEnter={() => setIsExpanded(true)}
        onMouseLeave={() => setIsExpanded(false)}
      >
        <div className="flex flex-col h-full py-2">
          {/* Navigation Items */}
          <div className="flex-1">
            <div className="flex flex-col">
              {/* Home */}
              <a href="/index.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-home text-purple-500"></i>
                </div>
                <span className="text">Home</span>
              </a>
              
              {/* Favorites */}
              <a href="/favorites.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-heart text-red-500"></i>
                </div>
                <span className="text">Favorites</span>
                <span className="badge" style={{backgroundColor: 'rgb(168, 85, 247)'}}>0</span>
              </a>
              
              {/* Recently Played */}
              <a href="/recent.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-history text-blue-300"></i>
                </div>
                <span className="text">Recently Played</span>
              </a>
              
              {/* New Games */}
              <a href="/new-games.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-gamepad text-green-400"></i>
                </div>
                <span className="text">New Games</span>
                <span className="badge" style={{backgroundColor: 'rgb(59, 130, 246)'}}>12</span>
              </a>
              
              {/* Categories Header */}
              <div className="category-header">Categories</div>
              
              {/* Action */}
              <a href="/categories/action.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-fire text-orange-400"></i>
                </div>
                <span className="text">Action</span>
                <span className="count">42</span>
              </a>
              
              {/* Racing */}
              <a href="/categories/racing.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-car text-pink-400"></i>
                </div>
                <span className="text">Racing</span>
                <span className="count">28</span>
              </a>
              
              {/* Sports */}
              <a href="/categories/sports.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-futbol text-cyan-400"></i>
                </div>
                <span className="text">Sports</span>
                <span className="count">35</span>
              </a>
              
              {/* Shooter */}
              <a href="/categories/shooter.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-bullseye text-indigo-400"></i>
                </div>
                <span className="text">Shooter</span>
                <span className="count">24</span>
              </a>
              
              {/* Cards */}
              <a href="/categories/cards.html" className="sidebar-link">
                <div className="icon">
                  <i className="fas fa-square text-purple-400"></i>
                </div>
                <span className="text">Cards</span>
                <span className="count">18</span>
              </a>
            </div>
          </div>
        </div>
      </aside>

      {/* Mobile sidebar overlay */}
      <div 
        className={`fixed inset-0 bg-black/50 z-20 lg:hidden ${isMobileOpen ? 'block' : 'hidden'}`}
        onClick={closeMobileSidebar}
      ></div>
    </>
  );
};

// 底部栏组件
export const Footer = () => {
  return (
    <footer className="bg-[#1A1B1F] border-t border-white/10 mt-16">
      <div className="container mx-auto px-6 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Website Logo and Name Section */}
          <div>
            <div className="flex flex-col items-center text-center">
              <img 
                src="/src/assets/icons/logo.png" 
                alt="Sonice.online Logo" 
                className="h-16 w-16 mb-3 rounded-xl"
              />
              <h2 className="text-white text-xl font-bold mb-1 uppercase tracking-wider">SONICE.ONLINE</h2>
              <p className="text-white/70 text-sm">Your Best Gaming Experience</p>
            </div>
          </div>

          {/* About Section */}
          <div>
            <h3 className="text-white text-lg font-medium mb-4">About Us</h3>
            <p className="text-white/70 text-sm leading-relaxed">
              sonice.online is your premier destination for free online games. We offer a wide variety of games across different genres.
            </p>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-white text-lg font-medium mb-4">Quick Links</h3>
            <ul className="space-y-2">
              <li>
                <a href="/games.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  All Games
                </a>
              </li>
              <li>
                <a href="/categories.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Categories
                </a>
              </li>
              <li>
                <a href="/about.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  About Us
                </a>
              </li>
              <li>
                <a href="/contact.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Contact
                </a>
              </li>
            </ul>
          </div>

          {/* Categories */}
          <div>
            <h3 className="text-white text-lg font-medium mb-4">Categories</h3>
            <ul className="space-y-2">
              <li>
                <a href="/categories/action.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Action
                </a>
              </li>
              <li>
                <a href="/categories/puzzle.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Puzzle
                </a>
              </li>
              <li>
                <a href="/categories/racing.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Racing
                </a>
              </li>
              <li>
                <a href="/categories/sports.html" className="text-white/70 hover:text-white text-sm transition-colors">
                  Sports
                </a>
              </li>
            </ul>
          </div>
        </div>

        {/* Copyright */}
        <div className="border-t border-white/10 mt-8 pt-8 text-center">
          <p className="text-white/50 text-sm">
            &copy; {new Date().getFullYear()} sonice.online. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
};

// 布局组件 - 集成导航栏、侧边栏和内容区域
export const Layout = ({ children }) => {
  return (
    <div className="flex flex-col min-h-screen">
      {/* Navbar */}
      <Navbar />
      
      <div className="flex-1 pt-[59px]">
        {/* Sidebar */}
        <Sidebar />
        
        {/* Main Content Area */}
        <main className="main-content p-6">
          {/* Content passed as children */}
          {children}
        </main>
      </div>
      
      {/* Footer */}
      <Footer />
    </div>
  );
}; 