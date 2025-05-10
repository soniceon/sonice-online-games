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

/* partials/auth-modals.twig */
class __TwigTemplate_b36072fdc0ce0af3e5a99b586708d62f25196a394e9744f0c3e42a20abcfc4f3 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        yield "<!-- Auth Modal (Login/Register Tab) -->
<div id=\"authModal\" class=\"fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden\">
    <div class=\"bg-white rounded-lg p-8 max-w-md w-full relative\">
        <button type=\"button\" id=\"closeAuthModalBtn\" class=\"absolute top-4 right-4 text-gray-400 hover:text-blue-600 text-2xl\">
            <i class=\"fas fa-times\"></i>
        </button>
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">登录 / 注册</h2>
        <!-- 登录表单 -->
        <form id=\"loginForm\">
            <input type=\"email\" name=\"email\" placeholder=\"Email\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"password\" placeholder=\"Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">登录</button>
        </form>
        <div class=\"mt-4 text-center\">
            <button type=\"button\" onclick=\"showAuthModal('register')\" class=\"text-blue-600 underline\">没有账号？注册</button>
        </div>
        <!-- 注册表单（可选扩展） -->
        <form id=\"registerForm\" class=\"hidden mt-6\">
            <input type=\"text\" name=\"username\" placeholder=\"Username\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"email\" name=\"email\" placeholder=\"Email\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"password\" placeholder=\"Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"confirm_password\" placeholder=\"Confirm Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">注册</button>
        </form>
        <div class=\"mt-4 text-center\">
            <button type=\"button\" onclick=\"showAuthModal('login')\" class=\"text-blue-600 underline\">已有账号？登录</button>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id=\"forgotPasswordModal\" class=\"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden\">
    <div class=\"bg-dark-lighter rounded-lg p-6 w-full max-w-md\">
        <div class=\"flex justify-between items-center mb-6\">
            <h2 class=\"text-2xl font-bold\">Reset Password</h2>
            <button type=\"button\" class=\"close-modal text-gray-400 hover:text-white\" onclick=\"closeModal('forgotPasswordModal')\">
                <i class=\"fas fa-times\"></i>
            </button>
        </div>
        <form id=\"forgotPasswordForm\" class=\"space-y-6\" autocomplete=\"off\">
            <div>
                <label for=\"resetEmail\" class=\"block text-sm font-medium mb-2\">Email</label>
                <input type=\"email\" id=\"resetEmail\" name=\"email\" required autocomplete=\"off\"
                       class=\"w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500\">
            </div>
            <div id=\"resetError\" class=\"text-red-500 text-sm hidden\"></div>
            <button type=\"submit\" class=\"w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors\">
                Send Reset Link
            </button>
            <div class=\"text-center\">
                <a href=\"#\" id=\"backToLogin\" class=\"text-sm text-blue-400 hover:text-blue-300\" onclick=\"event.preventDefault();switchTab('login');closeModal('forgotPasswordModal')\">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id=\"toast\" class=\"fixed top-4 right-4 px-4 py-2 rounded-lg text-white transform translate-y-[-100%] opacity-0 transition-all duration-300 z-50\">
    <span id=\"toastMessage\"></span>
</div> ";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "partials/auth-modals.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo()
    {
        return array ();
    }

    public function getSourceContext()
    {
        return new Source("<!-- Auth Modal (Login/Register Tab) -->
<div id=\"authModal\" class=\"fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden\">
    <div class=\"bg-white rounded-lg p-8 max-w-md w-full relative\">
        <button type=\"button\" id=\"closeAuthModalBtn\" class=\"absolute top-4 right-4 text-gray-400 hover:text-blue-600 text-2xl\">
            <i class=\"fas fa-times\"></i>
        </button>
        <h2 class=\"text-2xl font-bold mb-4 text-center text-blue-700\">登录 / 注册</h2>
        <!-- 登录表单 -->
        <form id=\"loginForm\">
            <input type=\"email\" name=\"email\" placeholder=\"Email\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"password\" placeholder=\"Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">登录</button>
        </form>
        <div class=\"mt-4 text-center\">
            <button type=\"button\" onclick=\"showAuthModal('register')\" class=\"text-blue-600 underline\">没有账号？注册</button>
        </div>
        <!-- 注册表单（可选扩展） -->
        <form id=\"registerForm\" class=\"hidden mt-6\">
            <input type=\"text\" name=\"username\" placeholder=\"Username\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"email\" name=\"email\" placeholder=\"Email\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"password\" placeholder=\"Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <input type=\"password\" name=\"confirm_password\" placeholder=\"Confirm Password\" class=\"w-full mb-3 p-2 border rounded\" required>
            <button type=\"submit\" class=\"w-full bg-blue-600 text-white py-2 rounded\">注册</button>
        </form>
        <div class=\"mt-4 text-center\">
            <button type=\"button\" onclick=\"showAuthModal('login')\" class=\"text-blue-600 underline\">已有账号？登录</button>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id=\"forgotPasswordModal\" class=\"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden\">
    <div class=\"bg-dark-lighter rounded-lg p-6 w-full max-w-md\">
        <div class=\"flex justify-between items-center mb-6\">
            <h2 class=\"text-2xl font-bold\">Reset Password</h2>
            <button type=\"button\" class=\"close-modal text-gray-400 hover:text-white\" onclick=\"closeModal('forgotPasswordModal')\">
                <i class=\"fas fa-times\"></i>
            </button>
        </div>
        <form id=\"forgotPasswordForm\" class=\"space-y-6\" autocomplete=\"off\">
            <div>
                <label for=\"resetEmail\" class=\"block text-sm font-medium mb-2\">Email</label>
                <input type=\"email\" id=\"resetEmail\" name=\"email\" required autocomplete=\"off\"
                       class=\"w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500\">
            </div>
            <div id=\"resetError\" class=\"text-red-500 text-sm hidden\"></div>
            <button type=\"submit\" class=\"w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors\">
                Send Reset Link
            </button>
            <div class=\"text-center\">
                <a href=\"#\" id=\"backToLogin\" class=\"text-sm text-blue-400 hover:text-blue-300\" onclick=\"event.preventDefault();switchTab('login');closeModal('forgotPasswordModal')\">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id=\"toast\" class=\"fixed top-4 right-4 px-4 py-2 rounded-lg text-white transform translate-y-[-100%] opacity-0 transition-all duration-300 z-50\">
    <span id=\"toastMessage\"></span>
</div> ", "partials/auth-modals.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\partials\\auth-modals.twig");
    }
}
