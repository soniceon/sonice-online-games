<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Sonice Online Games</title>
    <meta name="description" content="About Sonice.Games - Learn more about our free online gaming platform">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .sidebar-blue { background-color: #152a69; }
        .sidebar-hover { background-color: #1d3a8f; }
        .ml-14 { margin-left: 3.5rem; }
        .ml-56 { margin-left: 14rem; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-dark text-white">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-30 bg-black bg-opacity-90 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Logo -->
            <a href="../index.php" class="flex items-center space-x-2">
                <img src="assets/images/icons/logo.png" alt="Sonice.Games" class="h-10 w-10 rounded-full object-cover">
                <span class="text-2xl font-bold text-white">Sonice<span class="text-blue-500">.Games</span></span>
            </a>
            <!-- Navigation -->
            <div class="flex items-center space-x-4">
                <a href="../index.php" class="text-white hover:text-blue-400 transition-colors">Home</a>
                <a href="test-game.php" class="text-white hover:text-blue-400 transition-colors">Games</a>
                <a href="about.php" class="text-blue-400 font-semibold">About</a>
                <a href="contact.php" class="text-white hover:text-blue-400 transition-colors">Contact</a>
            </div>
        </div>
    </header>

    <div class="flex flex-1 min-h-0 pt-16">
        <!-- Sidebar -->
        <nav id="sidebar" class="group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden">
            <div class="flex-1 py-2 overflow-y-auto" style="scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;">
                <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
                <ul class="mt-2">
                    <!-- Home -->
                    <li>
                        <a href="../index.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-home text-2xl" style="color:#3b82f6;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Home</span>
                        </a>
                    </li>
                    <!-- Games -->
                    <li>
                        <a href="test-game.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-gamepad text-2xl" style="color:#ef476f;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">Games</span>
                        </a>
                    </li>
                    <!-- About -->
                    <li>
                        <a href="about.php" class="flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover">
                            <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;">
                                <i class="fa-solid fa-info-circle text-2xl" style="color:#06d6a0;"></i>
                            </span>
                            <span class="ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">About</span>
                        </a>
                    </li>
                </ul>
                <!-- Quick Links -->
                <div class="w-full py-4 flex flex-col items-center justify-center gap-2">
                    <a href="../index.php" class="flex items-center justify-center mb-2">
                        <span style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;">
                            <img src="assets/images/icons/logo.png" alt="Sonice Games" class="w-8 h-8 transition-all duration-200" />
                        </span>
                    </a>
                    <div class="flex flex-col items-center w-full opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <span class="mt-2 text-lg font-bold text-white whitespace-nowrap">
                            Sonice<span class="text-blue-400">.Games</span>
                        </span>
                        <p class="text-xs text-gray-200 text-center whitespace-nowrap mb-2">
                            Play the best online games for free
                        </p>
                        <div class="w-full mt-2">
                            <ul class="space-y-1 text-center">
                                <li><a href="about.php" class="hover:text-blue-300 text-blue-400 font-semibold">About Us</a></li>
                                <li><a href="contact.php" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="privacy.php" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="terms.php" class="hover:text-blue-300 text-gray-300">Terms of Service</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div id="mainContent" class="flex-1 flex flex-col min-h-0 ml-14 transition-all duration-300">
            <main class="flex-1">
                <div class="w-full px-8 py-8">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="../index.php" class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Home
                        </a>
                    </div>

                    <!-- About Content -->
                    <div class="max-w-4xl mx-auto">
                        <h1 class="text-4xl font-bold text-white mb-8">About Sonice.Games</h1>
                        
                        <!-- Mission Statement -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-4">Our Mission</h2>
                            <p class="text-lg text-gray-200 leading-relaxed">
                                At Sonice.Games, we believe that gaming should be accessible to everyone. Our mission is to provide a free, high-quality gaming platform where players can enjoy a diverse collection of games without any barriers or restrictions.
                            </p>
                        </div>

                        <!-- What We Offer -->
                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                                <i class="fas fa-gamepad text-4xl text-blue-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold text-white mb-3">Free Gaming</h3>
                                <p class="text-gray-300">Access to hundreds of free online games without registration or payment requirements.</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                                <i class="fas fa-mobile-alt text-4xl text-green-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold text-white mb-3">Cross-Platform</h3>
                                <p class="text-gray-300">Play on any device - desktop, tablet, or mobile - with responsive design.</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                                <i class="fas fa-shield-alt text-4xl text-purple-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold text-white mb-3">Safe & Secure</h3>
                                <p class="text-gray-300">Family-friendly content with no downloads or installations required.</p>
                            </div>
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6">
                                <i class="fas fa-sync text-4xl text-orange-400 mb-4"></i>
                                <h3 class="text-2xl font-semibold text-white mb-3">Regular Updates</h3>
                                <p class="text-gray-300">New games added daily to keep the gaming experience fresh and exciting.</p>
                            </div>
                        </div>

                        <!-- Game Categories -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Game Categories</h2>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <i class="fas fa-hourglass text-3xl text-blue-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Idle Games</h4>
                                    <p class="text-gray-300 text-sm">Relaxing incremental games</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-building text-3xl text-green-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Tycoon Games</h4>
                                    <p class="text-gray-300 text-sm">Build and manage empires</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-seedling text-3xl text-yellow-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Farming Games</h4>
                                    <p class="text-gray-300 text-sm">Grow and harvest crops</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-mouse-pointer text-3xl text-red-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Clicker Games</h4>
                                    <p class="text-gray-300 text-sm">Simple yet addictive clicking</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-gem text-3xl text-purple-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Mining Games</h4>
                                    <p class="text-gray-300 text-sm">Dig for resources and treasures</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-chess text-3xl text-indigo-400 mb-3"></i>
                                    <h4 class="text-lg font-semibold text-white mb-2">Strategy Games</h4>
                                    <p class="text-gray-300 text-sm">Test your tactical skills</p>
                                </div>
                            </div>
                        </div>

                        <!-- Call to Action -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                            <h2 class="text-3xl font-bold text-white mb-4">Ready to Start Gaming?</h2>
                            <p class="text-xl text-blue-100 mb-6">Join thousands of players enjoying free games daily!</p>
                            <a href="../index.php" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Start Playing Now
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // 侧边栏展开时推开内容区
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        if (sidebar && mainContent) {
            sidebar.addEventListener('mouseenter', () => {
                mainContent.classList.remove('ml-14');
                mainContent.classList.add('ml-56');
            });
            sidebar.addEventListener('mouseleave', () => {
                mainContent.classList.remove('ml-56');
                mainContent.classList.add('ml-14');
            });
        }
    </script>
</body>
</html> 