import React from 'react';

/**
 * 网站底部组件
 * 包含网站logo、About Us、Quick Links和Categories部分
 */
const Footer = () => {
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

export default Footer; 