<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Sonice Online Games</title>
    <meta name="description" content="Terms of Service for Sonice.Games - Read our terms and conditions">
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
                                <li><a href="privacy.php" class="hover:text-blue-300 text-gray-300">Privacy Policy</a></li>
                                <li><a href="terms.php" class="hover:text-blue-300 text-blue-400 font-semibold">Terms of Service</a></li>
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

                    <!-- Terms of Service Content -->
                    <div class="max-w-4xl mx-auto">
                        <h1 class="text-4xl font-bold text-white mb-8">Terms of Service</h1>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <p class="text-lg text-gray-200 mb-6">
                                <strong>Last updated:</strong> August 19, 2024
                            </p>
                            <p class="text-lg text-gray-200 leading-relaxed">
                                By accessing and using Sonice.Games, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
                            </p>
                        </div>

                        <!-- Acceptance of Terms -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Acceptance of Terms</h2>
                            <p class="text-lg text-gray-200 leading-relaxed mb-4">
                                By using our gaming platform, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service. These terms apply to all visitors, users, and others who access or use the service.
                            </p>
                            <div class="bg-white/5 rounded-lg p-6">
                                <h3 class="text-xl font-semibold text-white mb-3">Key Points:</h3>
                                <ul class="text-gray-300 space-y-2 ml-6">
                                    <li>• You must be at least 13 years old to use our service</li>
                                    <li>• You are responsible for maintaining the security of your account</li>
                                    <li>• You agree not to use the service for any unlawful purpose</li>
                                    <li>• We reserve the right to modify these terms at any time</li>
                                </ul>
                            </div>
                        </div>

                        <!-- User Conduct -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">User Conduct</h2>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-check-circle text-3xl text-green-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">What You Can Do</h3>
                                    <ul class="text-gray-300 text-sm space-y-1">
                                        <li>• Play games freely</li>
                                        <li>• Create an account</li>
                                        <li>• Save favorites</li>
                                        <li>• Share with friends</li>
                                    </ul>
                                </div>
                                <div class="bg-white/5 rounded-lg p-6">
                                    <i class="fas fa-times-circle text-3xl text-red-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">What You Cannot Do</h3>
                                    <ul class="text-gray-300 text-sm space-y-1">
                                        <li>• Cheat or hack games</li>
                                        <li>• Harass other users</li>
                                        <li>• Upload malicious content</li>
                                        <li>• Violate copyright laws</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Intellectual Property -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Intellectual Property</h2>
                            <p class="text-lg text-gray-200 leading-relaxed mb-6">
                                The service and its original content, features, and functionality are and will remain the exclusive property of Sonice.Games and its licensors. The service is protected by copyright, trademark, and other laws.
                            </p>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <i class="fas fa-copyright text-4xl text-blue-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Copyright</h3>
                                    <p class="text-gray-300 text-sm">All content is protected by copyright laws</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-trademark text-4xl text-green-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Trademarks</h3>
                                    <p class="text-gray-300 text-sm">Sonice.Games is our registered trademark</p>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-shield-alt text-4xl text-purple-400 mb-3"></i>
                                    <h3 class="text-lg font-semibold text-white mb-2">Licenses</h3>
                                    <p class="text-gray-300 text-sm">Games are licensed from developers</p>
                                </div>
                            </div>
                        </div>

                        <!-- Limitation of Liability -->
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 mb-8">
                            <h2 class="text-3xl font-semibold text-white mb-6">Limitation of Liability</h2>
                            <p class="text-lg text-gray-200 leading-relaxed">
                                In no event shall Sonice.Games, nor its directors, employees, partners, agents, suppliers, or affiliates, be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses.
                            </p>
                        </div>

                        <!-- Contact Information -->
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-8 text-center">
                            <h2 class="text-3xl font-bold text-white mb-4">Questions About Terms?</h2>
                            <p class="text-xl text-blue-100 mb-6">Contact us if you have any questions about these terms of service</p>
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