import React, { useState } from 'react';

/**
 * Navbar Component - The top navigation bar with logo, search and login
 * @returns {JSX.Element} The navbar component
 */
const Navbar = () => {
  const [searchQuery, setSearchQuery] = useState('');
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false);

  const handleSearchChange = (e) => {
    setSearchQuery(e.target.value);
  };

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    console.log('Searching for:', searchQuery);
    // Add actual search logic here
  };

  const toggleUserMenu = () => {
    setIsUserMenuOpen(!isUserMenuOpen);
  };

  return (
    <div className="navbar-container">
      <header className="bg-black h-14 w-full fixed top-0 left-0 right-0 z-50">
        <div className="max-w-full px-6 h-full flex items-center justify-between">
          {/* Logo */}
          <a href="/" className="flex items-center">
            <img 
              src="/src/assets/icons/logo.png" 
              alt="Sonice Logo" 
              className="w-7 h-7 mr-2" 
              onError={(e) => {e.target.src = 'https://via.placeholder.com/28?text=SG'; e.target.onerror = null;}} 
            />
            <span className="text-lg font-bold whitespace-nowrap">
              <span className="text-white">Sonice</span>&nbsp;<span className="text-blue-primary">Games</span>
            </span>
          </a>

          {/* Search Bar */}
          <div className="flex-1 max-w-lg mx-8">
            <form onSubmit={handleSearchSubmit} className="w-full">
              <div className="relative">
                <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <svg className="w-4 h-4 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                  </svg>
                </div>
                <input 
                  type="search" 
                  className="block w-full p-2 pl-10 text-sm text-white border border-gray-600 rounded-full bg-gray-700 focus:ring-blue-500 focus:border-blue-500" 
                  placeholder="Search games..." 
                  value={searchQuery}
                  onChange={handleSearchChange}
                  required 
                />
              </div>
            </form>
          </div>

          {/* User Menu */}
          <div className="flex items-center">
            <div className="flex items-center mr-2">
              <svg className="w-5 h-5 text-gray-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 4 1 8l4 4m10-8 4 4-4 4M11 1 9 15"/>
              </svg>
            </div>
            <div className="relative">
              <button 
                onClick={toggleUserMenu}
                className="flex items-center text-white text-sm hover:opacity-90 transition-opacity" 
              >
                Login <i className="fas fa-chevron-down ml-1 text-xs"></i>
              </button>
              
              {/* Dropdown Menu */}
              {isUserMenuOpen && (
                <div className="absolute right-0 mt-2 w-48 bg-dark-lighter rounded-md shadow-lg py-1 z-50 border border-gray-700">
                  <a href="/login" className="block px-4 py-2 text-sm text-white hover:bg-gray-700">Sign In</a>
                  <a href="/register" className="block px-4 py-2 text-sm text-white hover:bg-gray-700">Create Account</a>
                  <div className="border-t border-gray-700 my-1"></div>
                  <a href="/help" className="block px-4 py-2 text-sm text-white hover:bg-gray-700">Help</a>
                </div>
              )}
            </div>
          </div>
        </div>
      </header>
      {/* Blue border line */}
      <div className="w-full h-[3px] bg-blue-primary fixed top-14 left-0 right-0 z-50"></div>
    </div>
  );
};

export default Navbar; 