<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Sonice Online Games</title>
    <meta name="description" content="Privacy Policy for Sonice.Games - Learn how we protect your data">
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
                <a href="about.php" class="text-white hover:text-blue-400 transition-colors">About</a>
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
                                <li><a href="about.php" class="hover:text-blue-300 text-gray-300">About Us</a></li>
                                <li><a href="contact.php" class="hover:text-blue-300 text-gray-300">Contact</a></li>
                                <li><a href="privacy.php" class="hover:text-blue-300 text-blue-400 font-semibold">Privacy Policy</a></li>
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

                    <!-- Privacy Policy Content -->
                    <div class="max-w-4xl mx-auto">
                        <h1 class="text-4xl font-bold text-white mb-8">Privacy Policy</h1>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <p class="text-lg text-gray-200 mb-6">
                                <strong>Last updated:</strong> August 19, 2024
                            </p>
                            <p class="text-lg text-gray-200 leading-relaxed">
                                At Sonice.Games, we respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you visit our gaming platform.
                            </p>
                        </div>

                        <!-- Information We Collect -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Information We Collect</h2>
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-xl font-semibold text-white mb-3">Personal Information</h3>
                                    <ul class="text-gray-300 space-y-2 ml-6">
                                        <li>• Email address (if you choose to register)</li>
                                        <li>• Username (if you choose to register)</li>
                                        <li>• Game preferences and favorites</li>
                                    </ul>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-white mb-3">Usage Data</h3>
                                    <ul class="text-gray-300 space-y-2 ml-6">
                                        <li>• Games played and time spent</li>
                                        <li>• Browser type and device information</li>
                                        <li>• IP address and location data</li>
                                        <li>• Cookies and similar technologies</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- How We Use Your Information -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">How We Use Your Information</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-gamepad text-3xl text-blue-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Game Experience</h3>
                                    <p class="text-gray-300">Improve game performance and provide personalized recommendations</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-shield-alt text-3xl text-green-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Security</h3>
                                    <p class="text-gray-300">Protect against fraud and ensure platform security</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-chart-line text-3xl text-purple-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Analytics</h3>
                                    <p class="text-gray-300">Analyze usage patterns to improve our services</p>
                                </div>
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-bell text-3xl text-orange-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Communication</h3>
                                    <p class="text-gray-300">Send important updates and notifications</p>
                                </div>
                            </div>
                        </div>

                        <!-- Data Protection -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Data Protection</h2>
                            <p class="text-lg text-gray-200 leading-relaxed mb-6">
                                We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
                            </p>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <i class="fas fa-lock text-4xl text-blue-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Encryption</h3>
                                    <p class="text-gray-300">All data is encrypted in transit and at rest</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-user-shield text-4xl text-green-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Access Control</h3>
                                    <p class="text-gray-300">Limited access to personal data</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-eye-slash text-4xl text-purple-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Privacy First</h3>
                                    <p class="text-gray-300">We never sell your personal data</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                            <h2 class="text-3xl font-bold text-white mb-4">Questions About Privacy?</h2>
                            <p class="text-xl text-blue-100 mb-6">Contact us if you have any questions about this privacy policy</p>
                            <a href="contact.php" class="inline-block bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                                Contact Us
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