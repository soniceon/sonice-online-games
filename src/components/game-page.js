import React, { useState } from 'react';

/**
 * GamePage Component - Displays a game with its details, controls, and recommendations
 * @param {Object} props - Component props
 * @param {Object} props.game - Game data object
 * @returns {JSX.Element} The game page component
 */
const GamePage = ({ game = {} }) => {
  // Fallback values if game is not provided
  const {
    title = '{{game.title}}',
    categories = ['{{game.categories}}'],
    controls = '{{game.controls}}',
    description = '{{game.description}}',
    iframeUrl = 'about:blank',
    rating = 4.5,
    ratingCount = 128,
    recommendedGames = []
  } = game;

  // State for rating stars
  const [userRating, setUserRating] = useState(0);
  const [hoverRating, setHoverRating] = useState(0);
  const [comment, setComment] = useState('');

  // Mock comments
  const [comments] = useState([
    {
      id: 1,
      user: 'GameLover123',
      avatar: '/assets/images/default-avatar.png',
      date: '2 days ago',
      text: 'This game is amazing! I really love the graphics and gameplay. Highly recommended to everyone who enjoys this genre.'
    },
    {
      id: 2,
      user: 'ProGamer42',
      avatar: '/assets/images/default-avatar.png',
      date: '5 days ago',
      text: 'The controls are intuitive and the challenges are just right. Been playing for hours!'
    }
  ]);

  const handleRatingHover = (rating) => {
    setHoverRating(rating);
  };

  const handleRatingLeave = () => {
    setHoverRating(0);
  };

  const handleRatingClick = (rating) => {
    setUserRating(rating);
    // In a real app, you would send this to an API
    console.log(`User rated: ${rating}`);
  };

  const handleCommentSubmit = (e) => {
    e.preventDefault();
    if (comment.trim()) {
      console.log(`New comment: ${comment}`);
      // In a real app, you would send this to an API
      setComment('');
    }
  };

  return (
    <div className="rounded-xl overflow-hidden bg-[rgba(0,0,0,0.2)] backdrop-blur-md border border-white/10 p-8">
      {/* Game Title */}
      <div className="text-center mb-6">
        <h1 className="inline-block text-3xl font-bold px-6 py-2 rounded-lg bg-gradient-to-r from-blue-500/80 to-blue-400/80 text-white shadow-lg backdrop-blur-md border border-white/10">
          {title}
        </h1>
      </div>

      {/* Game Categories */}
      <div className="flex justify-center flex-wrap gap-2 mb-4">
        {categories.map((category, index) => (
          <span
            key={index}
            className="px-4 py-1 rounded-full text-white font-medium text-sm bg-gradient-to-r from-purple-600/80 to-purple-500/80 shadow-md"
          >
            {category}
          </span>
        ))}
      </div>

      {/* Game Description */}
      <div className="text-center mb-6">
        <p className="text-white text-lg bg-black/30 p-2 rounded-xl inline-block">
          Play various games for free on sonice.online. Thanks for your support and sharing!
        </p>
      </div>

      {/* Game Iframe */}
      <div className="w-full bg-black/50 rounded-lg overflow-hidden relative mb-8" style={{ paddingBottom: '56.25%' }}>
        <iframe
          src={iframeUrl}
          className="absolute top-0 left-0 w-full h-full"
          frameBorder="0"
          allowFullScreen
          title={title}
        ></iframe>
      </div>

      {/* Game Controls */}
      <div className="bg-[rgba(255,255,255,0.1)] backdrop-blur-md rounded-xl p-6 mb-8">
        <h2 className="text-white text-xl font-semibold mb-3">Game Controls</h2>
        <p className="text-white/90 mb-4">{controls}</p>
        
        <h3 className="text-white text-md mt-3 mb-2">Tips for Better Gaming Experience:</h3>
        <ul className="list-disc pl-5 text-white text-sm opacity-80">
          <li>Use a mouse for better precision in aiming games</li>
          <li>For racing games, keyboard arrow keys provide better control</li>
          <li>Press F11 for fullscreen gameplay experience</li>
        </ul>
      </div>

      {/* Game Overview */}
      <div className="bg-[rgba(255,255,255,0.1)] backdrop-blur-md rounded-xl p-6 mb-8">
        <h2 className="text-white text-xl font-semibold mb-3">Game Overview</h2>
        <p className="text-white/90 mb-6">{description}</p>
        
        <h3 className="text-white text-md mb-3">Key Features:</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
          <div className="bg-white/20 p-3 rounded flex items-center">
            <span className="mr-2">🎮</span> Intuitive controls for all skill levels
          </div>
          <div className="bg-white/20 p-3 rounded flex items-center">
            <span className="mr-2">🏆</span> Progressive difficulty for continued challenge
          </div>
          <div className="bg-white/20 p-3 rounded flex items-center">
            <span className="mr-2">🌈</span> Vibrant graphics and engaging sound effects
          </div>
          <div className="bg-white/20 p-3 rounded flex items-center">
            <span className="mr-2">⏱️</span> Quick game sessions perfect for casual play
          </div>
        </div>
        
        <div className="flex flex-wrap gap-2">
          {categories.map((category, index) => (
            <span
              key={index}
              className="px-3 py-1 rounded-full text-white text-sm bg-white/20 backdrop-blur-sm"
            >
              {category}
            </span>
          ))}
        </div>
      </div>

      {/* Recommended Games */}
      <div className="mb-8">
        <h2 className="text-white text-xl font-semibold mb-6">You May Also Like</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {[1, 2, 3].map((item) => (
            <div key={item} className="bg-white/10 rounded-xl overflow-hidden backdrop-blur-sm border border-white/10 transition-transform hover:-translate-y-1">
              <div className="relative">
                <img
                  src={`https://via.placeholder.com/300x200?text=Game+${item}`}
                  alt={`Recommended Game ${item}`}
                  className="w-full h-40 object-cover"
                />
                <button className="absolute top-2 right-2 bg-white/10 hover:bg-white/20 p-2 rounded-full">
                  <svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                </button>
              </div>
              <div className="p-4">
                <h3 className="text-white font-medium mb-1">{"{{rec_game.title}}"}</h3>
                <p className="text-white/60 text-sm mb-3">{"{{rec_game.category}}"}</p>
                <a href="#" className="inline-block bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg text-white text-sm transition-colors">
                  Play Now
                </a>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Rating Section */}
      <div className="bg-[rgba(255,255,255,0.1)] backdrop-blur-md rounded-xl p-6 mb-8 text-center">
        <h2 className="text-white text-xl font-semibold mb-4">Rate This Game</h2>
        <div className="flex justify-center gap-2 mb-4">
          {[1, 2, 3, 4, 5].map((star) => (
            <span
              key={star}
              className={`text-3xl cursor-pointer ${
                (hoverRating || userRating) >= star ? 'text-yellow-400' : 'text-white/30'
              }`}
              onMouseEnter={() => handleRatingHover(star)}
              onMouseLeave={handleRatingLeave}
              onClick={() => handleRatingClick(star)}
            >
              ★
            </span>
          ))}
        </div>
        <div className="text-white/70 text-sm">
          Average Rating: <span>{rating}</span>/5 (<span>{ratingCount}</span> votes)
        </div>
      </div>

      {/* Comments Section */}
      <div className="mb-8">
        <h2 className="text-white text-xl font-semibold mb-4">Game Comments</h2>
        
        {/* Comment Form */}
        <div className="bg-white/10 rounded-lg p-4 mb-6">
          <textarea
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            className="w-full p-3 bg-white/20 text-white border border-white/20 rounded-lg focus:outline-none focus:border-blue-500 mb-3"
            rows="4"
            placeholder="Share your thoughts about this game..."
          ></textarea>
          <div className="flex justify-between items-center">
            <div className="text-sm text-white/70">Please be respectful in your comments</div>
            <button
              onClick={handleCommentSubmit}
              className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
            >
              Submit Comment
            </button>
          </div>
        </div>
        
        {/* Comments List */}
        <div className="space-y-4">
          {comments.map((comment) => (
            <div key={comment.id} className="bg-white/10 p-4 rounded-lg">
              <div className="flex justify-between items-center mb-2">
                <div className="flex items-center">
                  <img src={comment.avatar} alt={comment.user} className="w-8 h-8 rounded-full mr-2" />
                  <div className="font-medium text-white">{comment.user}</div>
                </div>
                <div className="text-sm text-white/50">{comment.date}</div>
              </div>
              <p className="text-white/80">{comment.text}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default GamePage; 