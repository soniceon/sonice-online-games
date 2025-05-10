import React from 'react';
import { createRoot } from 'react-dom/client';
import Layout from './components/layout';
import GamePage from './components/game-page';

// 示例游戏数据
const gameData = {
  id: 'game123',
  title: '{{game.title}}',
  slug: 'example-game',
  description: '{{game.description}}',
  image: 'https://via.placeholder.com/800x450?text=Game+Preview',
  iframeUrl: 'about:blank',
  controls: 'Use WASD to move and mouse to aim. Press SPACE to jump and SHIFT to run.',
  categories: ['Action', 'Adventure'],
  rating: 4.5,
  ratingCount: 128,
  recommendedGames: [
    {
      id: 'rec1',
      title: 'Recommended Game 1',
      slug: 'recommended-game-1',
      image: 'https://via.placeholder.com/300x200?text=Game+1',
      category: 'Action'
    },
    {
      id: 'rec2',
      title: 'Recommended Game 2',
      slug: 'recommended-game-2',
      image: 'https://via.placeholder.com/300x200?text=Game+2',
      category: 'Puzzle'
    },
    {
      id: 'rec3',
      title: 'Recommended Game 3',
      slug: 'recommended-game-3',
      image: 'https://via.placeholder.com/300x200?text=Game+3',
      category: 'Racing'
    }
  ]
};

/**
 * App Component - The main application component
 * @returns {JSX.Element} The app component
 */
const App = () => {
  return (
    <Layout>
      <GamePage game={gameData} />
    </Layout>
  );
};

// 渲染应用到DOM
const container = document.getElementById('root');
const root = createRoot(container);
root.render(<App />);

export default App; 