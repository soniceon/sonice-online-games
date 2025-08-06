import React, { useEffect } from 'react';

/**
 * 游戏内容组件 - 用于展示游戏页面的主要内容
 * @param {Object} props 游戏数据
 * @returns {JSX.Element} 游戏内容组件
 */
const GameContent = ({ 
  title = "Game Title", 
  description = "Play this game online for free!",
  gameUrl = "",
  controls = "",
  categories = [],
  recommendedGames = []
}) => {
  
  useEffect(() => {
    // 初始化评分功能
    const stars = document.querySelectorAll('.star');
    let userRating = 0;

    const highlightStars = (rating) => {
      stars.forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        star.classList.toggle('active', starRating <= rating);
      });
    };

    const saveRating = (rating) => {
      // 模拟保存评分
      const avgRatingEl = document.getElementById('avgRating');
      const ratingCountEl = document.getElementById('ratingCount');
      
      let currentCount = parseInt(ratingCountEl.textContent);
      let currentAvg = parseFloat(avgRatingEl.textContent);
      
      // 计算新的平均分
      const newCount = currentCount + 1;
      const newAvg = ((currentAvg * currentCount) + rating) / newCount;
      
      // 更新显示
      avgRatingEl.textContent = newAvg.toFixed(1);
      ratingCountEl.textContent = newCount;
    };

    stars.forEach(star => {
      star.addEventListener('mouseover', () => {
        const rating = parseInt(star.dataset.rating);
        highlightStars(rating);
      });

      star.addEventListener('mouseout', () => {
        highlightStars(userRating);
      });

      star.addEventListener('click', () => {
        userRating = parseInt(star.dataset.rating);
        highlightStars(userRating);
        saveRating(userRating);
      });
    });
    
    // 清理事件监听
    return () => {
      stars.forEach(star => {
        star.removeEventListener('mouseover', () => {});
        star.removeEventListener('mouseout', () => {});
        star.removeEventListener('click', () => {});
      });
    };
  }, []);
  
  return (
    <div className="game-container">
      <div className="title-section">
        <h1>{title}</h1>
        
        <div className="categories-section">
          {categories.map((category, index) => (
            <span key={index} className="category-tag" style={{
              background: "linear-gradient(135deg, rgba(124, 58, 237, 0.8), rgba(139, 92, 246, 0.8))",
              boxShadow: "0 2px 8px rgba(124, 58, 237, 0.2)",
              border: "1px solid rgba(255, 255, 255, 0.1)",
              padding: "0.5rem 1rem",
              borderRadius: "20px",
              fontWeight: "500"
            }}>
              {category}
            </span>
          ))}
        </div>
        
        <div className="description-section">
          <p className="text-white text-lg">{description}</p>
        </div>
      </div>
      
      {/* 游戏窗口 */}
      <div className="game-wrapper">
        <iframe src={gameUrl} frameBorder="0" allowFullScreen></iframe>
      </div>

      {/* 游戏控制说明 */}
      <div className="game-controls">
        <h2>Game Controls</h2>
        <p>{controls}</p>
      </div>

      {/* 游戏概览 */}
      <div className="game-overview">
        <h2>Game Overview</h2>
        <p>{description}</p>
        
        <div className="game-categories">
          {categories.map((category, index) => (
            <span key={index} className="category-tag">{category}</span>
          ))}
        </div>
      </div>

      {/* 推荐游戏 */}
      <div className="recommended-games">
        <h2>You May Also Like</h2>
        <div className="games-grid">
          {recommendedGames.map((game, index) => (
            <div key={index} className="game-card">
              <img 
                src={game.image} 
                alt={game.title} 
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = '/assets/images/default-game.webp';
                }}
              />
              <div className="game-card-content">
                <h3>{game.title}</h3>
                <p>{game.description}</p>
                <a href={game.url} className="play-button">Play Now</a>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* 评分组件 */}
      <div className="rating-section">
        <h2 className="rating-title">Rate This Game</h2>
        <div className="rating-stars">
          <span className="star" data-rating="1">★</span>
          <span className="star" data-rating="2">★</span>
          <span className="star" data-rating="3">★</span>
          <span className="star" data-rating="4">★</span>
          <span className="star" data-rating="5">★</span>
        </div>
        <div className="rating-count">
          Average Rating: <span id="avgRating">4.5</span>/5
          (<span id="ratingCount">128</span> votes)
        </div>
      </div>
    </div>
  );
};

export default GameContent; 