import React from 'react';

/**
 * LogoWithText Component
 * 
 * A component that displays the site logo with text
 * 
 * @param {Object} props Component props
 * @param {string} props.className Additional classes to apply
 * @returns {JSX.Element} The logo with text component
 */
const LogoWithText = ({ className = '' }) => {
  return (
    <div className={`flex flex-col items-center justify-center ${className}`}>
      <div className="w-24 h-24 relative">
        <img 
          src="/src/assets/icons/logo.png" 
          alt="Sonice Games Logo" 
          className="w-full h-full object-contain"
        />
      </div>
      <div className="text-center mt-2">
        <h2 className="text-white text-lg font-bold">sonice.online</h2>
        <p className="text-gray-400 text-xs">Gaming platform</p>
      </div>
    </div>
  );
};

export default LogoWithText; 