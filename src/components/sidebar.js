import React, { useRef, useState } from 'react';
import './sidebarStyles.css';

/**
 * Sidebar Component
 * 
 * A responsive sidebar navigation component that collapses to icons on desktop
 * and expands on hover, with a mobile-friendly slide-in menu.
 * 
 * Features:
 * - Auto-collapsible sidebar that expands on hover
 * - Mobile slide-in navigation with toggle button
 * - Colorful category icons
 * - Category counters
 * - Smooth transitions and hover effects
 * 
 * @returns {JSX.Element} Sidebar component with navigation links
 */
const Sidebar = () => {
  const [isMobileOpen, setIsMobileOpen] = useState(false);
  const sidebarRef = useRef(null);
  
  // Toggle sidebar on mobile
  const toggleMobileSidebar = () => {
    setIsMobileOpen(!isMobileOpen);
  };
  
  // Close sidebar when clicking overlay
  const closeMobileSidebar = () => {
    if (isMobileOpen) {
      setIsMobileOpen(false);
    }
  };
  
  // Close sidebar when clicking a link
  const handleLinkClick = () => {
    if (isMobileOpen) {
      setIsMobileOpen(false);
    }
  };

  return (
    <>
      {/* Mobile sidebar toggle button */}
      <button 
        className="lg:hidden fixed left-4 bottom-4 z-50 bg-blue-500 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg"
        onClick={toggleMobileSidebar}
        aria-label="Toggle navigation menu"
      >
        <i className={`fas ${isMobileOpen ? 'fa-times' : 'fa-bars'}`}></i>
      </button>
      
      {/* Sidebar */}
      <aside 
        ref={sidebarRef}
        className={`sidebar fixed top-[59px] left-0 h-[calc(100vh-59px)] overflow-y-auto z-40 transition-all duration-300 group
          w-[60px] hover:w-[240px]
          ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}`
        }
      >
        <nav className="py-4" role="navigation" aria-label="Main navigation">
          <div className="space-y-1">
            <a 
              href="/index.html" 
              className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]" 
              aria-current="page"
              onClick={handleLinkClick}
            >
              <div className="w-8 h-8 flex items-center justify-center">
                <svg className="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M12 2L2 9.5V22h20V9.5L12 2zm7 18H5v-9.5l7-5.25 7 5.25V20z"/>
                </svg>
              </div>
              <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Home</span>
            </a>
            <a 
              href="/favorites.html" 
              className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
              onClick={handleLinkClick}
            >
              <div className="w-8 h-8 flex items-center justify-center">
                <svg className="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                </svg>
              </div>
              <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Favorites</span>
              <span className="ml-auto bg-purple-700 px-2 py-0.5 rounded-full text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">0</span>
            </a>
            <a 
              href="/recent.html" 
              className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
              onClick={handleLinkClick}
            >
              <div className="w-8 h-8 flex items-center justify-center">
                <svg className="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                </svg>
              </div>
              <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Recently Played</span>
            </a>
            <a 
              href="/new-games.html" 
              className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
              onClick={handleLinkClick}
            >
              <div className="w-8 h-8 flex items-center justify-center">
                <svg className="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20 4v12h-1.34l1.91 1.91A2.01 2.01 0 0022 16V4c0-1.1-.9-2-2-2H4c-1.1 0-1.99.9-1.99 2L2 16c0 1.1.9 2 2 2h11.17l-.83-.83V18H4V6h16v10h-1v2h1zm-7 6l-4 4h3v6h2v-6h3l-4-4z"/>
                </svg>
              </div>
              <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">New Games</span>
              <span className="ml-auto bg-blue-500 px-2 py-0.5 rounded-full text-xs text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">12</span>
            </a>

            <div className="pt-4 pb-2">
              <div className="px-4 py-2 text-xs font-semibold text-gray-400 uppercase opacity-0 group-hover:opacity-100 transition-opacity duration-300">Categories</div>
              <a 
                href="/categories/action.html" 
                className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
                onClick={handleLinkClick}
              >
                <div className="w-8 h-8 flex items-center justify-center">
                  <div className="w-4 h-4 bg-orange-500 rounded"></div>
                </div>
                <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Action</span>
                <span className="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">42</span>
              </a>
              <a 
                href="/categories/racing.html" 
                className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
                onClick={handleLinkClick}
              >
                <div className="w-8 h-8 flex items-center justify-center">
                  <svg className="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.85 7h10.29l1.04 3H5.81l1.04-3zM19 17H5v-5h14v5z M7.5 14.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z M16.5 14.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5 1.5-.67 1.5-1.5-.67-1.5-1.5-1.5z"/>
                  </svg>
                </div>
                <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Racing</span>
                <span className="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">28</span>
              </a>
              <a 
                href="/categories/sports.html" 
                className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
                onClick={handleLinkClick}
              >
                <div className="w-8 h-8 flex items-center justify-center">
                  <div className="w-4 h-4 bg-cyan-500 rounded-full"></div>
                </div>
                <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Sports</span>
                <span className="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">35</span>
              </a>
              <a 
                href="/categories/shooter.html" 
                className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
                onClick={handleLinkClick}
              >
                <div className="w-8 h-8 flex items-center justify-center">
                  <svg className="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M21 6h-3.17L16 4h-6v2h5.12l1.83 2H21v12H5v-9H3v9c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM8 14c0 2.76 2.24 5 5 5s5-2.24 5-5-2.24-5-5-5-5 2.24-5 5zm5-3c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3zM5 6h3V4H5V1H3v3H0v2h3v3h2z"/>
                  </svg>
                </div>
                <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Shooter</span>
                <span className="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">24</span>
              </a>
              <a 
                href="/categories/cards.html" 
                className="flex items-center px-4 py-3 text-white hover:bg-[#3861BB]"
                onClick={handleLinkClick}
              >
                <div className="w-8 h-8 flex items-center justify-center">
                  <div className="w-4 h-4 bg-purple-500 rounded-sm transform rotate-12"></div>
                </div>
                <span className="sidebar-item opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap ml-3 font-medium">Cards</span>
                <span className="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300">18</span>
              </a>
            </div>
          </div>
        </nav>
      </aside>

      {/* Mobile sidebar overlay */}
      <div 
        className={`fixed inset-0 bg-black/50 z-30 lg:hidden ${isMobileOpen ? 'block' : 'hidden'}`}
        onClick={closeMobileSidebar}
      ></div>
    </>
  );
};

export default Sidebar; 