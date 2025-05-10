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

/* partials/sidebar.twig */
class __TwigTemplate_249927d31c84010c6cfc332da92fec400ee1465497aa9e67ce8409e45ba19317 extends Template
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
        // line 1
        $macros["icons"] = $this->macros["icons"] = $this->loadTemplate("partials/icons.twig", "partials/sidebar.twig", 1)->unwrap();
        // line 2
        yield "
";
        // line 3
        if (( !array_key_exists("categories", $context) || (null === ($context["categories"] ?? null)))) {
            // line 4
            yield "    ";
            $context["categories"] = [];
        }
        // line 6
        yield "
<!-- Sidebar -->
<nav id=\"sidebar\" class=\"group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden\">
    <!-- 顶部LOGO，与导航栏对齐 -->
    <div class=\"flex items-center justify-center mt-2 mb-2\" style=\"height:48px;\">
        <a href=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/\" class=\"flex items-center justify-center\">
            <img src=\"";
        // line 12
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/assets/images/icons/logo.png\" alt=\"Sonice Games\" style=\"width:32px;height:32px;\">
        </a>
    </div>
    <div class=\"flex-1 py-2 overflow-y-auto\" style=\"scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;\">
        <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
        <ul class=\"mt-2\">
            <!-- 首页 -->
            <li>
                <a href=\"";
        // line 20
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-home text-2xl\" style=\"color:#3b82f6;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Home</span>
                </a>
            </li>
            <!-- 收藏 -->
            <li>
                <a href=\"";
        // line 29
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/favorites\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-heart text-2xl\" style=\"color:#ef476f;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Favorites</span>
                </a>
            </li>
            <!-- 最近游玩 -->
            <li>
                <a href=\"";
        // line 38
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
        yield "/recently-played\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-history text-2xl\" style=\"color:#06d6a0;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Recently Played</span>
                </a>
            </li>
        </ul>
        <!-- 分类 -->
        <div class=\"mt-2\">
            <h3 class=\"px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity duration-200\">Categories</h3>
            <ul class=\"mt-2\">
                ";
        // line 50
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(($context["categories"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 51
            yield "                <li>
                    <a href=\"";
            // line 52
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["base_url"] ?? null), "html", null, true);
            yield "/category/";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "slug", [], "any", false, false, false, 52), "html", null, true);
            yield "\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                        <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                            <i class=\"";
            // line 54
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "icon", [], "any", false, false, false, 54), "html", null, true);
            yield " text-2xl\" style=\"color: ";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "color", [], "any", false, false, false, 54), "html", null, true);
            yield ";\"></i>
                        </span>
                        <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">";
            // line 56
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(CoreExtension::getAttribute($this->env, $this->source, $context["category"], "name", [], "any", false, false, false, 56), "html", null, true);
            yield "</span>
                    </a>
                </li>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 60
        yield "            </ul>
        </div>
    </div>
    <!-- 侧边栏底部栏：蓝色渐变背景，平铺，版权与主站一致 -->
    <div class=\"sidebar-footer w-full py-4 flex flex-col items-center justify-center\"
         style=\"background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%); background-repeat: repeat;\">
        <span class=\"text-xs text-gray-200\">© 2024 Sonice.Games</span>
    </div>
</nav> ";
        return; yield '';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName()
    {
        return "partials/sidebar.twig";
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
        return array (  141 => 60,  131 => 56,  124 => 54,  117 => 52,  114 => 51,  110 => 50,  95 => 38,  83 => 29,  71 => 20,  60 => 12,  56 => 11,  49 => 6,  45 => 4,  43 => 3,  40 => 2,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% import \"partials/icons.twig\" as icons %}

{% if categories is not defined or categories is null %}
    {% set categories = [] %}
{% endif %}

<!-- Sidebar -->
<nav id=\"sidebar\" class=\"group fixed left-0 top-16 bottom-0 h-[calc(100vh-4rem)] w-14 hover:w-56 bg-sidebar-blue flex flex-col z-20 transition-all duration-300 ease-in-out overflow-hidden\">
    <!-- 顶部LOGO，与导航栏对齐 -->
    <div class=\"flex items-center justify-center mt-2 mb-2\" style=\"height:48px;\">
        <a href=\"{{ base_url }}/\" class=\"flex items-center justify-center\">
            <img src=\"{{ base_url }}/assets/images/icons/logo.png\" alt=\"Sonice Games\" style=\"width:32px;height:32px;\">
        </a>
    </div>
    <div class=\"flex-1 py-2 overflow-y-auto\" style=\"scrollbar-width:none; -ms-overflow-style:none; overflow-y:scroll;\">
        <style>.overflow-y-auto::-webkit-scrollbar { display:none!important; width:0!important; height:0!important; background:transparent!important; }</style>
        <ul class=\"mt-2\">
            <!-- 首页 -->
            <li>
                <a href=\"{{ base_url }}/\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-home text-2xl\" style=\"color:#3b82f6;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Home</span>
                </a>
            </li>
            <!-- 收藏 -->
            <li>
                <a href=\"{{ base_url }}/favorites\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-heart text-2xl\" style=\"color:#ef476f;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Favorites</span>
                </a>
            </li>
            <!-- 最近游玩 -->
            <li>
                <a href=\"{{ base_url }}/recently-played\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                    <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                        <i class=\"fa-solid fa-history text-2xl\" style=\"color:#06d6a0;\"></i>
                    </span>
                    <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">Recently Played</span>
                </a>
            </li>
        </ul>
        <!-- 分类 -->
        <div class=\"mt-2\">
            <h3 class=\"px-2 text-xs font-semibold text-gray-300 uppercase tracking-wider opacity-0 group-hover:opacity-100 transition-opacity duration-200\">Categories</h3>
            <ul class=\"mt-2\">
                {% for category in categories %}
                <li>
                    <a href=\"{{ base_url }}/category/{{ category.slug }}\" class=\"flex items-center px-0 hover:px-4 rounded-lg transition-all duration-200 hover:bg-sidebar-hover\">
                        <span style=\"width:48px;height:48px;display:flex;align-items:center;justify-content:center;margin-left:8px;padding:0;box-sizing:border-box;\">
                            <i class=\"{{ category.icon }} text-2xl\" style=\"color: {{ category.color }};\"></i>
                        </span>
                        <span class=\"ml-2 text-gray-100 font-medium text-base opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap\">{{ category.name }}</span>
                    </a>
                </li>
                {% endfor %}
            </ul>
        </div>
    </div>
    <!-- 侧边栏底部栏：蓝色渐变背景，平铺，版权与主站一致 -->
    <div class=\"sidebar-footer w-full py-4 flex flex-col items-center justify-center\"
         style=\"background: linear-gradient(90deg, #1e3c72 0%, #2a5298 100%); background-repeat: repeat;\">
        <span class=\"text-xs text-gray-200\">© 2024 Sonice.Games</span>
    </div>
</nav> ", "partials/sidebar.twig", "C:\\xampp\\htdocs\\sonice-online-games-new\\templates\\partials\\sidebar.twig");
    }
}
