<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* layouts/base.twig */
class __TwigTemplate_272e0e341db71b75b43db5c9856dded7993d7ef2dcb062b032ff766b5d599dbe extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        yield "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 6
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_title", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_title"] ?? null), "Welcome")) : ("Welcome")), "html", null, true);
        yield " - Sonice Online Games</title>
    <meta name=\"description\" content=\"";
        // line 7
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_description", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_description"] ?? null), "Play free online games at Sonice.Games")) : ("Play free online games at Sonice.Games")), "html", null, true);
        yield "\">
    <link rel=\"icon\" type=\"image/png\" href=\"";
        // line 8
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/images/icons/logo.png\">
    
    <!-- Open Graph / Facebook -->
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"https://sonice.online";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["page_url"] ?? null), "html", null, true);
        yield "\">
    <meta property=\"og:title\" content=\"";
        // line 13
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_title", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_title"] ?? null), "Welcome")) : ("Welcome")), "html", null, true);
        yield " - Sonice Online Games\">
    <meta property=\"og:description\" content=\"";
        // line 14
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_description", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_description"] ?? null), "Play free online games at Sonice.Games")) : ("Play free online games at Sonice.Games")), "html", null, true);
        yield "\">
    <meta property=\"og:image\" content=\"https://sonice.online/assets/images/icons/logo.png\">

    <!-- Twitter -->
    <meta property=\"twitter:card\" content=\"summary_large_image\">
    <meta property=\"twitter:url\" content=\"https://sonice.online";
        // line 19
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["page_url"] ?? null), "html", null, true);
        yield "\">
    <meta property=\"twitter:title\" content=\"";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_title", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_title"] ?? null), "Welcome")) : ("Welcome")), "html", null, true);
        yield " - Sonice Online Games\">
    <meta property=\"twitter:description\" content=\"";
        // line 21
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("page_description", $context)) ? (Twig\Extension\CoreExtension::default(($context["page_description"] ?? null), "Play free online games at Sonice.Games")) : ("Play free online games at Sonice.Games")), "html", null, true);
        yield "\">
    <meta property=\"twitter:image\" content=\"https://sonice.online/assets/images/icons/logo.png\">

    <link rel=\"stylesheet\" href=\"";
        // line 24
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/css/style.css\">
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <!-- Font Awesome icon library -->
    <link rel=\"stylesheet\" href=\"";
        // line 27
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/fontawesome/css/all.min.css\">
    <link rel=\"stylesheet\" href=\"";
        // line 28
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/css/auth.css\">
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js\"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark': '#0F1012',
                        'dark-lighter': '#1A1B1F',
                        'blue-primary': '#0EA5E9',
                        'blue-secondary': '#38BDF8',
                        'blue-bright': '#7DD3FC',
                        'purple-primary': '#7C3AED',
                        'gray-custom': '#2A2B31',
                        'sidebar-blue': '#152a69',
                        'sidebar-hover': '#1d3a8f'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>
<body class=\"min-h-screen flex flex-col bg-dark text-white\">
";
        // line 61
        if (( !array_key_exists("categories", $context) || (null === ($context["categories"] ?? null)))) {
            // line 62
            yield "    ";
            $context["categories"] = [];
        }
        // line 64
        yield "    <!-- Header -->
    <header class=\"fixed top-0 left-0 right-0 z-30 bg-dark-lighter bg-opacity-90 backdrop-blur-sm border-b border-gray-800\">
        <div class=\"container mx-auto px-4 h-16 flex items-center justify-between\">
            <!-- Logo -->
            <a href=\"";
        // line 68
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/public/\" class=\"flex items-center space-x-2\">
                <img src=\"";
        // line 69
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/images/icons/logo.png\" alt=\"Sonice.Games\" class=\"h-10 w-10 rounded-full object-cover\">
                <span class=\"text-2xl font-bold text-white\">Sonice<span class=\"text-blue-500\">.Games</span></span>
            </a>
            <!-- Search Bar -->
            <div class=\"flex-1 max-w-2xl mx-8\">
                <div class=\"relative\">
                    <input type=\"search\" placeholder=\"Search games...\"
                           class=\"w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner\">
                    <button class=\"absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition\">
                        <i class=\"fas fa-arrow-right\"></i>
                    </button>
                </div>
            </div>
            <!-- 新用户系统入口 -->
            <div class=\"flex items-center space-x-2\">
                <button id=\"customLoginBtn\" class=\"btn-primary\">登录</button>
                <button id=\"customRegisterBtn\" class=\"btn-secondary\">注册</button>
                <button id=\"customLogoutBtn\" class=\"btn-danger hidden\">退出</button>
            </div>
        </div>
    </header>
    <div class=\"flex flex-1 min-h-0 pt-16\">
        ";
        // line 91
        yield from         $this->loadTemplate("partials/sidebar.twig", "layouts/base.twig", 91)->unwrap()->yield(CoreExtension::merge($context, ["categories" => ($context["categories"] ?? null), "base_url" => ($context["base_url"] ?? null)]));
        // line 92
        yield "        <div class=\"flex-1 flex flex-col min-h-0\">
            <main class=\"flex-1 gradient-blue\">
                <div class=\"container mx-auto px-4 py-8\">
                    ";
        // line 95
        yield from $this->unwrap()->yieldBlock('content', $context, $blocks);
        // line 96
        yield "                </div>
            </main>
            <footer class=\"w-full bg-dark-lighter text-gray-200 pt-12 pb-6 mt-0 border-t border-gray-800 pl-16 sm:pl-16 md:pl-16 lg:pl-16 xl:pl-16 2xl:pl-16\">
                <div class=\"container mx-auto px-4 flex flex-col lg:flex-row lg:justify-between gap-8\">
                    <!-- Logo & About -->
                    <div class=\"flex-1 min-w-[220px] mb-8 lg:mb-0\">
                        <div class=\"flex items-center mb-4\">
                            <img src=\"";
        // line 103
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/images/icons/logo.png\" alt=\"Sonice.Games\" class=\"h-10 w-10 rounded-full mr-2\">
                            <span class=\"text-2xl font-bold text-white\">Sonice<span class=\"text-blue-400\">.Games</span></span>
                        </div>
                        <p class=\"mb-4 text-sm text-gray-300\">Play the best online games for free. New games added daily!</p>
                        <div class=\"flex space-x-4 text-xl\">
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-facebook-f\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-twitter\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-instagram\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-discord\"></i></a>
                        </div>
                    </div>
                    <!-- Categories -->
                    <div class=\"flex-1 min-w-[180px] mb-8 lg:mb-0\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Categories</h4>
                        <ul class=\"space-y-2 text-sm text-gray-300\">
                            <li><a href=\"";
        // line 118
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/category/action\" class=\"hover:text-blue-400\">Action Games</a></li>
                            <li><a href=\"";
        // line 119
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/category/puzzle\" class=\"hover:text-blue-400\">Puzzle Games</a></li>
                            <li><a href=\"";
        // line 120
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/category/racing\" class=\"hover:text-blue-400\">Racing Games</a></li>
                            <li><a href=\"";
        // line 121
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/category/sports\" class=\"hover:text-blue-400\">Sports Games</a></li>
                            <li><a href=\"";
        // line 122
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/category/shooter\" class=\"hover:text-blue-400\">Shooter Games</a></li>
                        </ul>
                    </div>
                    <!-- Quick Links -->
                    <div class=\"flex-1 min-w-[180px] mb-8 lg:mb-0\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Quick Links</h4>
                        <ul class=\"space-y-2 text-sm text-gray-300\">
                            <li><a href=\"";
        // line 129
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/about\" class=\"hover:text-blue-400\">About Us</a></li>
                            <li><a href=\"";
        // line 130
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/contact\" class=\"hover:text-blue-400\">Contact</a></li>
                            <li><a href=\"";
        // line 131
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/privacy\" class=\"hover:text-blue-400\">Privacy Policy</a></li>
                            <li><a href=\"";
        // line 132
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/terms\" class=\"hover:text-blue-400\">Terms of Service</a></li>
                        </ul>
                    </div>
                    <!-- Newsletter -->
                    <div class=\"flex-1 min-w-[220px]\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Newsletter</h4>
                        <p class=\"mb-2 text-sm text-gray-300\">Subscribe to get updates on new games and features.</p>
                        <form class=\"flex flex-col space-y-2\">
                            <input type=\"email\" placeholder=\"Your email address\" class=\"px-3 py-2 rounded-full bg-[#233a6b] border-none text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400\">
                            <button type=\"submit\" class=\"bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-full transition\">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class=\"container mx-auto px-4 mt-8 border-t border-blue-900 pt-4 text-center text-xs text-gray-400\">
                    © 2023-2024 Sonice.Games. All rights reserved.
                </div>
            </footer>
        </div>
    </div>
    <!-- 只保留合并弹窗和通知 -->
    ";
        // line 152
        yield from         $this->loadTemplate("partials/auth-modals.twig", "layouts/base.twig", 152)->unwrap()->yield($context);
        // line 153
        yield "    <!-- 统一用户弹窗HTML -->
    <div id=\"userModal\" class=\"fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 hidden z-50\">
      <div class=\"bg-white rounded-lg p-8 max-w-md w-full relative\">
        <button type=\"button\" id=\"closeUserModal\" class=\"absolute top-4 right-4 text-gray-400 hover:text-blue-600 text-2xl\">
          <i class=\"fas fa-times\"></i>
        </button>
        <div id=\"userModalContent\"></div>
      </div>
    </div>
    <!-- Scripts -->
    <script src=\"";
        // line 163
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/js/auth.js\"></script>
    <script src=\"";
        // line 164
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/js/app.js\"></script>
    <!-- 统一用户系统JS（支持登录、注册、找回密码、邮箱验证） -->
    <script>
    // 工具函数
    function showUserModal(html) {
      document.getElementById('userModalContent').innerHTML = html;
      document.getElementById('userModal').classList.remove('hidden');
    }
    function closeUserModal() {
      document.getElementById('userModal').classList.add('hidden');
    }
    document.getElementById('closeUserModal').onclick = closeUserModal;
    // 登录弹窗
    document.getElementById('customLoginBtn').onclick = function() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">登录</h2>
        <form id=\"loginForm\">
          <input type=\"text\" name=\"username\" placeholder=\"用户名\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"password\" name=\"password\" placeholder=\"密码\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">登录</button>
          <div class=\"text-center mt-2\">
            <a href=\"#\" id=\"showForgot\">忘记密码？</a>
          </div>
          <div id=\"loginError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('loginForm').onsubmit = handleLogin;
      document.getElementById('showForgot').onclick = function(e) {
        e.preventDefault();
        showForgotForm();
      };
    };
    // 注册弹窗
    document.getElementById('customRegisterBtn').onclick = function() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">注册</h2>
        <form id=\"registerForm\">
          <input type=\"text\" name=\"username\" placeholder=\"用户名\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"email\" name=\"email\" placeholder=\"邮箱\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"password\" name=\"password\" placeholder=\"密码\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">注册</button>
          <div id=\"registerError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('registerForm').onsubmit = handleRegister;
    };
    // 忘记密码弹窗
    function showForgotForm() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">找回密码</h2>
        <form id=\"forgotForm\">
          <input type=\"email\" name=\"email\" placeholder=\"注册邮箱\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">发送重置邮件</button>
          <div id=\"forgotError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('forgotForm').onsubmit = handleForgot;
    }
    // 退出登录
    document.getElementById('customLogoutBtn').onclick = async function() {
      await fetch('/api/logout.php', {method: 'POST'});
      location.reload();
    };
    // 登录处理
    async function handleLogin(e) {
      e.preventDefault();
      const form = e.target;
      const username = form.username.value.trim();
      const password = form.password.value;
      const errorDiv = document.getElementById('loginError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, password})
      });
      const data = await res.json();
      if (data.success) {
        location.reload();
      } else {
        errorDiv.innerText = data.message || '登录失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 注册处理
    async function handleRegister(e) {
      e.preventDefault();
      const form = e.target;
      const username = form.username.value.trim();
      const email = form.email.value.trim();
      const password = form.password.value;
      const errorDiv = document.getElementById('registerError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/register.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, email, password})
      });
      const data = await res.json();
      if (data.success) {
        showUserModal(`<div class=\"text-green-600 text-center py-8\">注册成功，请前往邮箱激活账号！</div>`);
      } else {
        errorDiv.innerText = data.message || '注册失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 找回密码处理
    async function handleForgot(e) {
      e.preventDefault();
      const form = e.target;
      const email = form.email.value.trim();
      const errorDiv = document.getElementById('forgotError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/forgot.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email})
      });
      const data = await res.json();
      if (data.success) {
        showUserModal(`<div class=\"text-green-600 text-center py-8\">重置邮件已发送，请查收邮箱！</div>`);
      } else {
        errorDiv.innerText = data.message || '发送失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 登录态检测（可选：页面加载时显示/隐藏按钮）
    fetch('/api/userinfo.php').then(r=>r.json()).then(data=>{
      if(data.logged_in){
        document.getElementById('customLoginBtn').classList.add('hidden');
        document.getElementById('customRegisterBtn').classList.add('hidden');
        document.getElementById('customLogoutBtn').classList.remove('hidden');
      }else{
        document.getElementById('customLoginBtn').classList.remove('hidden');
        document.getElementById('customRegisterBtn').classList.remove('hidden');
        document.getElementById('customLogoutBtn').classList.add('hidden');
      }
    });
    </script>
    <!-- 保留头像选择弹窗 -->
    <div id=\"avatar-picker-modal\" class=\"modal-backdrop hidden\">
      <div class=\"modal-content max-w-lg mx-auto p-8 rounded-2xl bg-white text-center relative\">
        <button onclick=\"hideAvatarPickerModal()\" class=\"absolute right-4 top-4 text-gray-400 hover:text-blue-600 text-2xl\"><i class=\"fas fa-times\"></i></button>
        <h2 class=\"text-2xl font-bold mb-4 text-blue-700 flex items-center justify-center\"><i class=\"fas fa-random mr-2\"></i>选择你的新头像</h2>
        <div id=\"avatar-picker-list\" class=\"grid grid-cols-3 gap-4 mb-6\"></div>
        <button onclick=\"refreshAvatarPicker()\" class=\"btn-secondary mb-2\"><i class=\"fas fa-sync-alt mr-2\"></i>再换一批</button>
        <div class=\"text-gray-400 text-xs\">头像由 DiceBear API 随机生成</div>
      </div>
    </div>
</body>
</html> 
 ";
        return; yield '';
    }

    // line 95
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "layouts/base.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable()
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array (  453 => 95,  293 => 164,  289 => 163,  277 => 153,  275 => 152,  252 => 132,  248 => 131,  244 => 130,  240 => 129,  230 => 122,  226 => 121,  222 => 120,  218 => 119,  214 => 118,  196 => 103,  187 => 96,  185 => 95,  180 => 92,  178 => 91,  153 => 69,  149 => 68,  143 => 64,  139 => 62,  137 => 61,  101 => 28,  97 => 27,  91 => 24,  85 => 21,  81 => 20,  77 => 19,  69 => 14,  65 => 13,  61 => 12,  54 => 8,  50 => 7,  46 => 6,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>{{page_title|default('Welcome')}} - Sonice Online Games</title>
    <meta name=\"description\" content=\"{{page_description|default('Play free online games at Sonice.Games')}}\">
    <link rel=\"icon\" type=\"image/png\" href=\"{{ base_url }}/assets/images/icons/logo.png\">
    
    <!-- Open Graph / Facebook -->
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"https://sonice.online{{page_url}}\">
    <meta property=\"og:title\" content=\"{{page_title|default('Welcome')}} - Sonice Online Games\">
    <meta property=\"og:description\" content=\"{{page_description|default('Play free online games at Sonice.Games')}}\">
    <meta property=\"og:image\" content=\"https://sonice.online/assets/images/icons/logo.png\">

    <!-- Twitter -->
    <meta property=\"twitter:card\" content=\"summary_large_image\">
    <meta property=\"twitter:url\" content=\"https://sonice.online{{page_url}}\">
    <meta property=\"twitter:title\" content=\"{{page_title|default('Welcome')}} - Sonice Online Games\">
    <meta property=\"twitter:description\" content=\"{{page_description|default('Play free online games at Sonice.Games')}}\">
    <meta property=\"twitter:image\" content=\"https://sonice.online/assets/images/icons/logo.png\">

    <link rel=\"stylesheet\" href=\"{{ base_url }}/assets/css/style.css\">
    <script src=\"https://cdn.tailwindcss.com\"></script>
    <!-- Font Awesome icon library -->
    <link rel=\"stylesheet\" href=\"{{ base_url }}/assets/fontawesome/css/all.min.css\">
    <link rel=\"stylesheet\" href=\"{{ base_url }}/assets/css/auth.css\">
    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js\"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark': '#0F1012',
                        'dark-lighter': '#1A1B1F',
                        'blue-primary': '#0EA5E9',
                        'blue-secondary': '#38BDF8',
                        'blue-bright': '#7DD3FC',
                        'purple-primary': '#7C3AED',
                        'gray-custom': '#2A2B31',
                        'sidebar-blue': '#152a69',
                        'sidebar-hover': '#1d3a8f'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #60a5fa 100%) !important;
            color: #ffffff;
        }
        .content-wrapper {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }
    </style>
</head>
<body class=\"min-h-screen flex flex-col bg-dark text-white\">
{% if categories is not defined or categories is null %}
    {% set categories = [] %}
{% endif %}
    <!-- Header -->
    <header class=\"fixed top-0 left-0 right-0 z-30 bg-dark-lighter bg-opacity-90 backdrop-blur-sm border-b border-gray-800\">
        <div class=\"container mx-auto px-4 h-16 flex items-center justify-between\">
            <!-- Logo -->
            <a href=\"{{ base_url }}/public/\" class=\"flex items-center space-x-2\">
                <img src=\"{{ base_url }}/assets/images/icons/logo.png\" alt=\"Sonice.Games\" class=\"h-10 w-10 rounded-full object-cover\">
                <span class=\"text-2xl font-bold text-white\">Sonice<span class=\"text-blue-500\">.Games</span></span>
            </a>
            <!-- Search Bar -->
            <div class=\"flex-1 max-w-2xl mx-8\">
                <div class=\"relative\">
                    <input type=\"search\" placeholder=\"Search games...\"
                           class=\"w-full px-5 py-2 bg-[#233a6b] border-none rounded-full text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent shadow-inner\">
                    <button class=\"absolute right-2 top-1/2 -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center transition\">
                        <i class=\"fas fa-arrow-right\"></i>
                    </button>
                </div>
            </div>
            <!-- 新用户系统入口 -->
            <div class=\"flex items-center space-x-2\">
                <button id=\"customLoginBtn\" class=\"btn-primary\">登录</button>
                <button id=\"customRegisterBtn\" class=\"btn-secondary\">注册</button>
                <button id=\"customLogoutBtn\" class=\"btn-danger hidden\">退出</button>
            </div>
        </div>
    </header>
    <div class=\"flex flex-1 min-h-0 pt-16\">
        {% include 'partials/sidebar.twig' with {'categories': categories, 'base_url': base_url} %}
        <div class=\"flex-1 flex flex-col min-h-0\">
            <main class=\"flex-1 gradient-blue\">
                <div class=\"container mx-auto px-4 py-8\">
                    {% block content %}{% endblock %}
                </div>
            </main>
            <footer class=\"w-full bg-dark-lighter text-gray-200 pt-12 pb-6 mt-0 border-t border-gray-800 pl-16 sm:pl-16 md:pl-16 lg:pl-16 xl:pl-16 2xl:pl-16\">
                <div class=\"container mx-auto px-4 flex flex-col lg:flex-row lg:justify-between gap-8\">
                    <!-- Logo & About -->
                    <div class=\"flex-1 min-w-[220px] mb-8 lg:mb-0\">
                        <div class=\"flex items-center mb-4\">
                            <img src=\"{{ base_url }}/assets/images/icons/logo.png\" alt=\"Sonice.Games\" class=\"h-10 w-10 rounded-full mr-2\">
                            <span class=\"text-2xl font-bold text-white\">Sonice<span class=\"text-blue-400\">.Games</span></span>
                        </div>
                        <p class=\"mb-4 text-sm text-gray-300\">Play the best online games for free. New games added daily!</p>
                        <div class=\"flex space-x-4 text-xl\">
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-facebook-f\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-twitter\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-instagram\"></i></a>
                            <a href=\"#\" class=\"footer-social\"><i class=\"fab fa-discord\"></i></a>
                        </div>
                    </div>
                    <!-- Categories -->
                    <div class=\"flex-1 min-w-[180px] mb-8 lg:mb-0\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Categories</h4>
                        <ul class=\"space-y-2 text-sm text-gray-300\">
                            <li><a href=\"{{ base_url }}/category/action\" class=\"hover:text-blue-400\">Action Games</a></li>
                            <li><a href=\"{{ base_url }}/category/puzzle\" class=\"hover:text-blue-400\">Puzzle Games</a></li>
                            <li><a href=\"{{ base_url }}/category/racing\" class=\"hover:text-blue-400\">Racing Games</a></li>
                            <li><a href=\"{{ base_url }}/category/sports\" class=\"hover:text-blue-400\">Sports Games</a></li>
                            <li><a href=\"{{ base_url }}/category/shooter\" class=\"hover:text-blue-400\">Shooter Games</a></li>
                        </ul>
                    </div>
                    <!-- Quick Links -->
                    <div class=\"flex-1 min-w-[180px] mb-8 lg:mb-0\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Quick Links</h4>
                        <ul class=\"space-y-2 text-sm text-gray-300\">
                            <li><a href=\"{{ base_url }}/about\" class=\"hover:text-blue-400\">About Us</a></li>
                            <li><a href=\"{{ base_url }}/contact\" class=\"hover:text-blue-400\">Contact</a></li>
                            <li><a href=\"{{ base_url }}/privacy\" class=\"hover:text-blue-400\">Privacy Policy</a></li>
                            <li><a href=\"{{ base_url }}/terms\" class=\"hover:text-blue-400\">Terms of Service</a></li>
                        </ul>
                    </div>
                    <!-- Newsletter -->
                    <div class=\"flex-1 min-w-[220px]\">
                        <h4 class=\"text-lg font-bold mb-3 text-white\">Newsletter</h4>
                        <p class=\"mb-2 text-sm text-gray-300\">Subscribe to get updates on new games and features.</p>
                        <form class=\"flex flex-col space-y-2\">
                            <input type=\"email\" placeholder=\"Your email address\" class=\"px-3 py-2 rounded-full bg-[#233a6b] border-none text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400\">
                            <button type=\"submit\" class=\"bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-full transition\">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class=\"container mx-auto px-4 mt-8 border-t border-blue-900 pt-4 text-center text-xs text-gray-400\">
                    © 2023-2024 Sonice.Games. All rights reserved.
                </div>
            </footer>
        </div>
    </div>
    <!-- 只保留合并弹窗和通知 -->
    {% include 'partials/auth-modals.twig' %}
    <!-- 统一用户弹窗HTML -->
    <div id=\"userModal\" class=\"fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 hidden z-50\">
      <div class=\"bg-white rounded-lg p-8 max-w-md w-full relative\">
        <button type=\"button\" id=\"closeUserModal\" class=\"absolute top-4 right-4 text-gray-400 hover:text-blue-600 text-2xl\">
          <i class=\"fas fa-times\"></i>
        </button>
        <div id=\"userModalContent\"></div>
      </div>
    </div>
    <!-- Scripts -->
    <script src=\"{{ base_url }}/assets/js/auth.js\"></script>
    <script src=\"{{ base_url }}/assets/js/app.js\"></script>
    <!-- 统一用户系统JS（支持登录、注册、找回密码、邮箱验证） -->
    <script>
    // 工具函数
    function showUserModal(html) {
      document.getElementById('userModalContent').innerHTML = html;
      document.getElementById('userModal').classList.remove('hidden');
    }
    function closeUserModal() {
      document.getElementById('userModal').classList.add('hidden');
    }
    document.getElementById('closeUserModal').onclick = closeUserModal;
    // 登录弹窗
    document.getElementById('customLoginBtn').onclick = function() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">登录</h2>
        <form id=\"loginForm\">
          <input type=\"text\" name=\"username\" placeholder=\"用户名\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"password\" name=\"password\" placeholder=\"密码\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">登录</button>
          <div class=\"text-center mt-2\">
            <a href=\"#\" id=\"showForgot\">忘记密码？</a>
          </div>
          <div id=\"loginError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('loginForm').onsubmit = handleLogin;
      document.getElementById('showForgot').onclick = function(e) {
        e.preventDefault();
        showForgotForm();
      };
    };
    // 注册弹窗
    document.getElementById('customRegisterBtn').onclick = function() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">注册</h2>
        <form id=\"registerForm\">
          <input type=\"text\" name=\"username\" placeholder=\"用户名\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"email\" name=\"email\" placeholder=\"邮箱\" class=\"w-full mb-3 p-2 border rounded\" required>
          <input type=\"password\" name=\"password\" placeholder=\"密码\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">注册</button>
          <div id=\"registerError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('registerForm').onsubmit = handleRegister;
    };
    // 忘记密码弹窗
    function showForgotForm() {
      showUserModal(`
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">找回密码</h2>
        <form id=\"forgotForm\">
          <input type=\"email\" name=\"email\" placeholder=\"注册邮箱\" class=\"w-full mb-3 p-2 border rounded\" required>
          <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">发送重置邮件</button>
          <div id=\"forgotError\" class=\"text-red-500 text-center mt-2 hidden\"></div>
        </form>
      `);
      document.getElementById('forgotForm').onsubmit = handleForgot;
    }
    // 退出登录
    document.getElementById('customLogoutBtn').onclick = async function() {
      await fetch('/api/logout.php', {method: 'POST'});
      location.reload();
    };
    // 登录处理
    async function handleLogin(e) {
      e.preventDefault();
      const form = e.target;
      const username = form.username.value.trim();
      const password = form.password.value;
      const errorDiv = document.getElementById('loginError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, password})
      });
      const data = await res.json();
      if (data.success) {
        location.reload();
      } else {
        errorDiv.innerText = data.message || '登录失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 注册处理
    async function handleRegister(e) {
      e.preventDefault();
      const form = e.target;
      const username = form.username.value.trim();
      const email = form.email.value.trim();
      const password = form.password.value;
      const errorDiv = document.getElementById('registerError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/register.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({username, email, password})
      });
      const data = await res.json();
      if (data.success) {
        showUserModal(`<div class=\"text-green-600 text-center py-8\">注册成功，请前往邮箱激活账号！</div>`);
      } else {
        errorDiv.innerText = data.message || '注册失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 找回密码处理
    async function handleForgot(e) {
      e.preventDefault();
      const form = e.target;
      const email = form.email.value.trim();
      const errorDiv = document.getElementById('forgotError');
      errorDiv.classList.add('hidden');
      errorDiv.innerText = '';
      const res = await fetch('/api/forgot.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({email})
      });
      const data = await res.json();
      if (data.success) {
        showUserModal(`<div class=\"text-green-600 text-center py-8\">重置邮件已发送，请查收邮箱！</div>`);
      } else {
        errorDiv.innerText = data.message || '发送失败';
        errorDiv.classList.remove('hidden');
      }
    }
    // 登录态检测（可选：页面加载时显示/隐藏按钮）
    fetch('/api/userinfo.php').then(r=>r.json()).then(data=>{
      if(data.logged_in){
        document.getElementById('customLoginBtn').classList.add('hidden');
        document.getElementById('customRegisterBtn').classList.add('hidden');
        document.getElementById('customLogoutBtn').classList.remove('hidden');
      }else{
        document.getElementById('customLoginBtn').classList.remove('hidden');
        document.getElementById('customRegisterBtn').classList.remove('hidden');
        document.getElementById('customLogoutBtn').classList.add('hidden');
      }
    });
    </script>
    <!-- 保留头像选择弹窗 -->
    <div id=\"avatar-picker-modal\" class=\"modal-backdrop hidden\">
      <div class=\"modal-content max-w-lg mx-auto p-8 rounded-2xl bg-white text-center relative\">
        <button onclick=\"hideAvatarPickerModal()\" class=\"absolute right-4 top-4 text-gray-400 hover:text-blue-600 text-2xl\"><i class=\"fas fa-times\"></i></button>
        <h2 class=\"text-2xl font-bold mb-4 text-blue-700 flex items-center justify-center\"><i class=\"fas fa-random mr-2\"></i>选择你的新头像</h2>
        <div id=\"avatar-picker-list\" class=\"grid grid-cols-3 gap-4 mb-6\"></div>
        <button onclick=\"refreshAvatarPicker()\" class=\"btn-secondary mb-2\"><i class=\"fas fa-sync-alt mr-2\"></i>再换一批</button>
        <div class=\"text-gray-400 text-xs\">头像由 DiceBear API 随机生成</div>
      </div>
    </div>
</body>
</html> 
 ", "layouts/base.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\layouts\\base.twig");
    }
}
