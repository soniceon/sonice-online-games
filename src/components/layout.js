import React from 'react';
import Navbar from './navbar';
import Sidebar from './sidebar';
import Footer from './footer';

/**
 * Layout Component - The main layout that combines navbar, sidebar, content area and footer
 * @param {Object} props - Component props
 * @param {React.ReactNode} props.children - Content to be rendered in the main area
 * @returns {JSX.Element} The layout component
 */
const Layout = ({ children }) => {
  return (
    <div className="flex flex-col min-h-screen bg-[#274FD0]">
      {/* Navbar (红色框) */}
      <Navbar />
      
      <div className="flex flex-1 pt-[56px]">
        {/* Sidebar (绿色框) */}
        <Sidebar />
        
        {/* Main Content Area */}
        <main className="flex-1 ml-16 transition-all duration-300 p-6">
          {/* Content passed as children */}
          {children}
        </main>
      </div>
      
      {/* Footer (绿色底部栏) */}
      <Footer />
    </div>
  );
};

export default Layout; 